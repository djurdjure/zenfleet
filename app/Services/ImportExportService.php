<?php

namespace App\Services;

use App\Models\FuelType;
use App\Models\TransmissionType;
use App\Models\Vehicle;
use App\Models\VehicleStatus;
use App\Models\VehicleType;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use League\Csv\Reader;
use League\Csv\Statement;
use League\Csv\Writer;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ImportExportService
{
    /**
     * Génère un fichier CSV modèle pour l'importation.
     *
     * @param string $filename Nom du fichier à télécharger
     * @param array $headers En-têtes des colonnes
     * @param array $example Ligne d'exemple
     * @return StreamedResponse
     */
    public function generateCsvTemplate(string $filename, array $headers, array $example): StreamedResponse
    {
        return response()->streamDownload(function () use ($headers, $example) {
            $csv = Writer::createFromString();
            $csv->insertOne($headers);
            $csv->insertOne($example);
            echo $csv->toString();
        }, $filename, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Importe des véhicules à partir d'un fichier CSV.
     *
     * @param UploadedFile $file Fichier CSV uploadé
     * @param string $encoding Encodage du fichier ('auto', 'utf8', 'iso', 'windows')
     * @return array Résultats de l'importation
     */
    public function importVehiclesFromCsv(UploadedFile $file, string $encoding = 'auto', array $mappings = []): array
    {
        // Générer un identifiant unique pour cette importation
        $importId = Str::uuid()->toString();
        $fileName = $file->getClientOriginalName();

        // Stocker temporairement le fichier
        $path = $file->storeAs('imports/temp', $importId . '.csv');
        $fullPath = Storage::path($path);

        try {
            // Détecter et convertir l'encodage si nécessaire
            $detectedEncoding = $this->detectEncoding($fullPath, $encoding);
            $content = file_get_contents($fullPath);

            if ($detectedEncoding !== 'UTF-8') {
                $content = mb_convert_encoding($content, 'UTF-8', $detectedEncoding);
                file_put_contents($fullPath, $content);
            }

            // Charger le CSV avec League\CSV
            $csv = Reader::createFromPath($fullPath, 'r');
            $csv->setHeaderOffset(0);

            // Préparer les données de référence pour la validation
            $vehicleTypes = ($mappings["vehicle_types"] ?? VehicleType::pluck("id", "name"))->toArray();
            $fuelTypes = ($mappings["fuel_types"] ?? FuelType::pluck("id", "name"))->toArray();
            $transmissionTypes = ($mappings["transmission_types"] ?? TransmissionType::pluck("id", "name"))->toArray();
            $vehicleStatuses = ($mappings["vehicle_statuses"] ?? VehicleStatus::pluck("id", "name"))->toArray();

            // Traiter les enregistrements
            $records = Statement::create()->process($csv);
            $recordsArray = iterator_to_array($records);

            $successCount = 0;
            $errorRows = [];

            DB::beginTransaction();

            try {
                foreach ($recordsArray as $offset => $record) {
                    $lineNumber = $offset + 2; // +1 pour l'en-tête, +1 pour commencer à 1

                    // Nettoyer et préparer les données
                    $data = $this->prepareVehicleData($record, $vehicleTypes, $fuelTypes, $transmissionTypes, $vehicleStatuses);

                    // Valider les données
                    $validator = Validator::make($data, $this->getVehicleValidationRules());

                    if ($validator->fails()) {
                        // Enregistrer les erreurs
                        $errorRows[] = [
                            'line' => $lineNumber,
                            'errors' => $validator->errors()->all(),
                            'data' => $record,
                            'problematic_fields' => implode(',', array_keys($validator->errors()->toArray()))
                        ];
                    } else {
                        // Créer le véhicule
                        Vehicle::create($data);
                        $successCount++;
                    }
                }

                // Valider la transaction si tout s'est bien passé
                DB::commit();

                // Journaliser le résultat
                Log::info('Importation de véhicules terminée', [
                    'import_id' => $importId,
                    'file_name' => $fileName,
                    'success_count' => $successCount,
                    'error_count' => count($errorRows)
                ]);

                return [
                    'success_count' => $successCount,
                    'error_rows' => $errorRows,
                    'import_id' => $importId,
                    'file_name' => $fileName,
                    'encoding' => $detectedEncoding
                ];
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Erreur lors de l\'importation des véhicules', [
                    'import_id' => $importId,
                    'exception' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }
        } finally {
            // Nettoyer le fichier temporaire
            Storage::delete($path);
        }
    }

    /**
     * Exporte les lignes en erreur vers un fichier CSV.
     *
     * @param array $errorRows Lignes en erreur
     * @param string $importId Identifiant de l'importation
     * @return StreamedResponse
     */
    public function exportErrorRows(array $errorRows, string $importId): StreamedResponse
    {
        return response()->streamDownload(function () use ($errorRows) {
            $csv = Writer::createFromString();

            // Si aucune erreur, retourner un fichier vide avec en-têtes
            if (empty($errorRows)) {
                $csv->insertOne(['Aucune erreur à exporter']);
                echo $csv->toString();
                return;
            }

            // Récupérer les en-têtes à partir de la première ligne d'erreur
            $headers = array_keys($errorRows[0]['data']);
            $csv->insertOne($headers);

            // Ajouter les lignes en erreur
            foreach ($errorRows as $error) {
                $csv->insertOne(array_values($error['data']));
            }

            echo $csv->toString();
        }, 'erreurs_import_vehicules_' . substr($importId, 0, 8) . '.csv', [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="erreurs_import_vehicules_' . substr($importId, 0, 8) . '.csv"',
        ]);
    }

    /**
     * Détecte l'encodage d'un fichier CSV.
     *
     * @param string $filePath Chemin complet du fichier
     * @param string $requestedEncoding Encodage demandé ('auto', 'utf8', 'iso', 'windows')
     * @return string Encodage détecté
     */
    protected function detectEncoding(string $filePath, string $requestedEncoding): string
    {
        // Si un encodage spécifique est demandé (sauf 'auto'), l'utiliser
        if ($requestedEncoding !== 'auto') {
            return match ($requestedEncoding) {
                'utf8' => 'UTF-8',
                'iso' => 'ISO-8859-1',
                'windows' => 'Windows-1252',
                default => 'UTF-8'
            };
        }

        // Sinon, tenter de détecter automatiquement
        $content = file_get_contents($filePath);
        $encodings = ['UTF-8', 'ISO-8859-1', 'Windows-1252'];

        foreach ($encodings as $encoding) {
            $sample = mb_convert_encoding($content, 'UTF-8', $encoding);
            // Si la conversion ne produit pas de caractères invalides, c'est probablement le bon encodage
            if (!preg_match('/\p{Cc}|\p{Cf}|\p{Co}|\p{Cn}/u', $sample)) {
                return $encoding;
            }
        }

        // Par défaut, utiliser UTF-8
        return 'UTF-8';
    }

    /**
     * Prépare les données du véhicule à partir d'une ligne CSV.
     *
     * @param array $record Ligne du CSV
     * @param array $vehicleTypes Types de véhicules disponibles
     * @param array $fuelTypes Types de carburants disponibles
     * @param array $transmissionTypes Types de transmissions disponibles
     * @param array $vehicleStatuses Statuts de véhicules disponibles
     * @return array Données préparées
     */
    protected function prepareVehicleData(array $record, array $vehicleTypes, array $fuelTypes, array $transmissionTypes, array $vehicleStatuses): array
    {
        $data = [
            'registration_plate' => $record['immatriculation'] ?? null,
            'vin' => $record['numero_serie_vin'] ?? null,
            'brand' => $record['marque'] ?? null,
            'model' => $record['modele'] ?? null,
            'color' => $record['couleur'] ?? null,
            'vehicle_type_id' => $vehicleTypes[strtolower($record['type_vehicule'] ?? '')] ?? null,
            'fuel_type_id' => $fuelTypes[strtolower($record['type_carburant'] ?? '')] ?? null,
            'transmission_type_id' => $transmissionTypes[strtolower($record['type_transmission'] ?? '')] ?? null,
            'vehicle_status_id' => $vehicleStatuses[strtolower($record['statut'] ?? '')] ?? ($vehicleStatuses['parking'] ?? null), // Default to Parking, ensure 'parking' exists
            'manufacturing_year' => $record['annee_fabrication'] ?? null, // Correction de la clé (year -> manufacturing_year)
            'acquisition_date' => $this->formatDate($record['date_acquisition'] ?? null),
            'purchase_price' => $this->formatDecimal($record['prix_achat'] ?? null),
            'current_value' => $this->formatDecimal($record['valeur_actuelle'] ?? null),
            'initial_mileage' => $this->formatInteger($record['kilometrage_initial'] ?? null),
            'engine_displacement_cc' => $this->formatInteger($record['cylindree_cc'] ?? null), // Correction du mapping
            'power_hp' => $this->formatInteger($record['puissance_cv'] ?? null), // Correction du mapping (power -> power_hp)
            'seats' => $this->formatInteger($record['nombre_places'] ?? null),
            'notes' => $record['notes'] ?? null,
            'organization_id' => auth()->user()->organization_id,
        ];

        // Nettoyer les valeurs vides
        return array_map(function ($value) {
            return $value === '' ? null : $value;
        }, $data);
    }

    /**
     * Retourne les règles de validation pour un véhicule.
     *
     * @return array Règles de validation
     */
    protected function getVehicleValidationRules(): array
    {
        return [
            'registration_plate' => 'required|string|max:20',
            'vin' => 'nullable|string|max:50',
            'brand' => 'required|string|max:50',
            'model' => 'nullable|string|max:50', // Facultatif
            'color' => 'nullable|string|max:30',
            'vehicle_type_id' => 'nullable|exists:vehicle_types,id', // Facultatif
            'fuel_type_id' => 'required|exists:fuel_types,id',
            'transmission_type_id' => 'nullable|exists:transmission_types,id', // Facultatif
            'vehicle_status_id' => 'nullable|exists:vehicle_statuses,id', // Facultatif (défaut appliqué)
            'manufacturing_year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'acquisition_date' => 'nullable|date',
            'purchase_price' => 'nullable|numeric|min:0',
            'current_value' => 'nullable|numeric|min:0',
            'initial_mileage' => 'nullable|integer|min:0',
            'engine_displacement_cc' => 'nullable|integer|min:0', // Correction nom champ
            'power_hp' => 'nullable|integer|min:0', // Correction nom champ
            'seats' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
            'organization_id' => 'required|exists:organizations,id',
        ];
    }

    /**
     * Formate une date depuis une chaîne.
     *
     * @param string|null $date Date à formater
     * @return string|null Date formatée ou null
     */
    public function formatDate(?string $date): ?string
    {
        if (empty($date)) {
            return null;
        }

        // Essayer plusieurs formats de date courants
        $formats = ['Y-m-d', 'd/m/Y', 'm/d/Y', 'd-m-Y', 'Y/m/d'];

        foreach ($formats as $format) {
            $dateObj = \DateTime::createFromFormat($format, $date);
            if ($dateObj !== false) {
                return $dateObj->format('Y-m-d');
            }
        }

        return null;
    }

    /**
     * Formate un nombre décimal depuis une chaîne.
     *
     * @param string|null $value Valeur à formater
     * @return float|null Nombre décimal ou null
     */
    public function formatDecimal(?string $value): ?float
    {
        if (empty($value)) {
            return null;
        }

        // Nettoyer la valeur (supprimer espaces, séparateurs de milliers)
        $value = str_replace([' ', ','], ['', '.'], $value);

        return is_numeric($value) ? (float) $value : null;
    }

    /**
     * Formate un nombre entier depuis une chaîne.
     *
     * @param string|null $value Valeur à formater
     * @return int|null Nombre entier ou null
     */
    public function formatInteger(?string $value): ?int
    {
        if (empty($value)) {
            return null;
        }

        // Nettoyer la valeur (supprimer espaces, séparateurs de milliers)
        $value = str_replace([' ', ',', '.'], '', $value);

        return is_numeric($value) ? (int) $value : null;
    }
}

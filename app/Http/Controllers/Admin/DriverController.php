<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Models\DriverStatus;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use App\Http\Requests\Admin\Driver\StoreDriverRequest;
use App\Http\Requests\Admin\Driver\UpdateDriverRequest;
use Illuminate\Support\Facades\Validator;
use League\Csv\Reader;
use League\Csv\Statement;
use League\Csv\Writer;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Services\DriverService;
use App\Services\ImportExportService;


class DriverController extends Controller
{

    protected DriverService $driverService;
    protected ImportExportService $importExportService;

    public function __construct(DriverService $driverService, ImportExportService $importExportService)
    {
        $this->driverService = $driverService;
        $this->importExportService = $importExportService;
    }

    // Méthodes existantes inchangées (index, create, store, edit, update, destroy, restore, forceDelete)
    public function index(Request $request): View
    {
        $this->authorize('view drivers');
        $filters = $request->only(['search', 'status_id', 'per_page', 'view_deleted']);
        $drivers = $this->driverService->getFilteredDrivers($filters);
        $driverStatuses = DriverStatus::orderBy('name')->get();
        return view('admin.drivers.index', compact('drivers', 'driverStatuses', 'filters'));
    }

    public function create(): View
    {
        $this->authorize('create drivers');
        $driverStatuses = DriverStatus::orderBy('name')->get();
        
        // CORRECTION: On ne récupère que les utilisateurs qui ne sont pas déjà liés à un chauffeur
        $assignedUserIds = Driver::whereNotNull('user_id')->pluck('user_id');
        $linkableUsers = User::whereNotIn('id', $assignedUserIds)->orderBy('name')->get();

        return view('admin.drivers.create', compact('driverStatuses', 'linkableUsers'));
    }

    public function store(StoreDriverRequest $request): RedirectResponse
    {
        $this->driverService->createDriver($request->validated());
        return redirect()->route('admin.drivers.index')->with('success', 'Nouveau chauffeur ajouté avec succès.');
    }

    public function edit(Driver $driver): View
    {
        $this->authorize('edit drivers');

        // CORRECTION : On récupère la liste des utilisateurs qui ne sont pas encore chauffeurs,
        // en y ajoutant l'utilisateur actuel du chauffeur s'il en a un.
        $assignedUserIds = Driver::whereNotNull('user_id')->where('id', '!=', $driver->id)->pluck('user_id');
        $linkableUsers = User::whereNotIn('id', $assignedUserIds)->orderBy('name')->get();

        $driverStatuses = DriverStatus::orderBy('name')->get();

        // CORRECTION : On ajoute la variable '$linkableUsers' aux données passées à la vue
        return view('admin.drivers.edit', compact('driver', 'driverStatuses', 'linkableUsers'));
    }

    public function update(UpdateDriverRequest $request, Driver $driver): RedirectResponse
    {
        $this->driverService->updateDriver($driver, $request->validated());
        return redirect()->route('admin.drivers.index')->with('success', 'Le chauffeur a été mis à jour.');
    }

    public function destroy(Driver $driver): RedirectResponse
    {
        $this->authorize('delete drivers');
        $archived = $this->driverService->archiveDriver($driver);

        if ($archived) {
            return redirect()->route('admin.drivers.index')->with('success', "Le chauffeur {$driver->first_name} {$driver->last_name} a été archivé.");
        }

        return redirect()->back()->with('error', 'Impossible d\'archiver ce chauffeur car il est lié à des affectations.');
    }

    public function restore($driverId): RedirectResponse
    {
        $this->authorize('restore drivers');
        $this->driverService->restoreDriver($driverId);
        return redirect()->route('admin.drivers.index', ['view_deleted' => true])->with('success', "Le chauffeur a été restauré.");
    }

    /**
     * Supprime définitivement un chauffeur.
     */
    public function forceDelete($driverId): RedirectResponse
    {
        $this->authorize('force delete drivers');

        // On appelle le service et on vérifie le résultat
        $deleted = $this->driverService->forceDeleteDriver($driverId);

        if ($deleted) {
            return redirect()->route('admin.drivers.index', ['view_deleted' => true])->with('success', 'Le chauffeur a été supprimé définitivement.');
        }

        // Si la suppression a échoué, on affiche un message d'erreur clair
        return redirect()->back()->with('error', 'Impossible de supprimer ce chauffeur car il est lié à un historique d\'affectations.');
    }


    /**
     * Affiche le formulaire d'importation de chauffeurs.
     */
    public function showImportForm(): View
    {
        $this->authorize('create drivers');
        return view('admin.drivers.import');
    }

    /**
     * Télécharge le fichier modèle CSV complet pour l'importation.
     */
    public function downloadTemplate()
    {
        $this->authorize('create drivers');

        $csv = Writer::createFromString('');
        $csv->setOutputBOM(Writer::BOM_UTF8); // Ajoute le BOM UTF-8 pour garantir la compatibilité

        // En-têtes complets en français
        $headers = [
            'nom', 'prenom', 'date_naissance', 'statut', 'matricule', 
            'telephone', 'email_personnel', 'adresse', 'numero_permis', 
            'categorie_permis', 'date_delivrance_permis', 'autorite_delivrance', 
            'date_recrutement', 'date_fin_contrat', 'contact_urgence_nom', 
            'contact_urgence_telephone', 'groupe_sanguin'
        ];

        // Ligne d'exemple
        $example = [
            'Merzouki', 'Saïd', '1985-04-12', 'Disponible', 'DIF-2022-00123', 
            '0671020304', 'smerzouki@email.com', '123 Rue de la Paix, 16018 Alger', 'P-987654', 
            'B,C', '2016-08-20', 'Daïra El Biar', 
            '2020-01-15', '2025-11-14', 'Merzouki Ali', 
            '0565040302', 'O+'
        ];

        $csv->insertOne($headers);
        $csv->insertOne($example);

        return response($csv->toString(), 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="template_import_chauffeurs.csv"',
        ]);
    }

    /**
     * Traite l'importation du fichier CSV.
     * Version refactorisée pour une gestion robuste du BOM et une simplification.
     */
    public function handleImport(Request $request): RedirectResponse
    {
        $this->authorize('create drivers');
        
        $request->validate(['csv_file' => 'required|file|mimes:csv,txt']);
        
        $file = $request->file('csv_file');
        
        try {
            // Lecture du contenu du fichier
            $fileContent = file_get_contents($file->getRealPath());
            
            // Correction : Suppression manuelle et fiable du BOM UTF-8
            if (str_starts_with($fileContent, "\xef\xbb\xbf")) {
                $fileContent = substr($fileContent, 3);
            }
            
            // Création du lecteur CSV directement depuis la chaîne de caractères nettoyée
            $csv = Reader::createFromString($fileContent);
            $csv->setHeaderOffset(0);
            
            $records = Statement::create()->process($csv);
            
            $successCount = 0;
            $errorRows = [];
            $importId = Str::uuid()->toString();
            
            $statuses = DriverStatus::pluck('id', 'name');
            $defaultStatusId = $statuses->get('Disponible');
            
            Log::info("Début de l'importation CSV des chauffeurs (refactorisée)", [
                'import_id' => $importId,
                'file' => $file->getClientOriginalName(),
                'records_count' => count($records),
            ]);
            
            foreach ($records as $offset => $record) {
                try {
                    // La sanitisation des clés n'est plus nécessaire car le BOM est supprimé à la source.
                    // On nettoie juste les valeurs.
                    $sanitizedRecord = array_map('trim', $record);

                    $data = $this->prepareDataForValidation($sanitizedRecord);
                    $validator = Validator::make($data, $this->getValidationRules());
                    
                    if ($validator->fails()) {
                        $errorRows[] = ['line' => $offset + 2, 'errors' => $validator->errors()->all(), 'data' => $record];
                        continue;
                    }
                    
                    $validatedData = $validator->validated();
                    $statusName = $validatedData['statut'] ?? null;
                    $statusId = $statuses->get($statusName, $defaultStatusId);
                    
                    unset($validatedData['statut']);
                    $validatedData['status_id'] = $statusId;
                    
                    Driver::create($validatedData);
                    $successCount++;
                    
                } catch (QueryException $e) {
                    $errorMessage = $this->formatDatabaseError($e);
                    $errorRows[] = ['line' => $offset + 2, 'errors' => [$errorMessage], 'data' => $record];
                } catch (\Exception $e) {
                    $errorRows[] = ['line' => $offset + 2, 'errors' => ["Erreur inattendue: " . $e->getMessage()], 'data' => $record];
                }
            }
            
            Log::info("Fin de l'importation CSV des chauffeurs", [
                'import_id' => $importId,
                'success_count' => $successCount,
                'error_count' => count($errorRows)
            ]);
            
            return redirect()->route('admin.drivers.import.results')
                ->with('successCount', $successCount)
                ->with('errorRows', $errorRows)
                ->with('importId', $importId)
                ->with('fileName', $file->getClientOriginalName());
                
        } catch (\Exception $e) {
            Log::critical("Erreur critique lors de l'importation CSV", [
                'error' => $e->getMessage(),
                'file' => $file->getClientOriginalName()
            ]);
            
            return redirect()->route('admin.drivers.import.show')
                ->with('error', "Une erreur est survenue lors de la lecture du fichier CSV: " . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Affiche les résultats de l'importation.
     */
    public function showImportResults(): View
    {
        $this->authorize('create drivers');
        
        $successCount = session('successCount', 0);
        $errorRows = session('errorRows', []);
        $importId = session('importId', null);
        $fileName = session('fileName', 'Fichier CSV');
        $encoding = session('encoding', 'utf8');
        
        return view('admin.drivers.import-results', compact(
            'successCount', 
            'errorRows', 
            'importId', 
            'fileName', 
            'encoding'
        ));
    }
    
    /**
     * Détecte l'encodage d'un contenu de fichier.
     */
    private function detectEncoding(string $content): string
    {
        // Liste des encodages à tester dans l'ordre de priorité
        $encodings = ['UTF-8', 'ISO-8859-1', 'Windows-1252'];
        
        foreach ($encodings as $encoding) {
            $sample = mb_convert_encoding($content, 'UTF-8', $encoding);
            
            // Si la conversion ne génère pas de caractères invalides, c'est probablement le bon encodage
            if (!preg_match('/\p{Cc}(?!\n|\t|\r)/u', $sample)) {
                switch ($encoding) {
                    case 'UTF-8': return 'utf8';
                    case 'ISO-8859-1': return 'iso';
                    case 'Windows-1252': return 'windows';
                }
            }
        }
        
        // Par défaut, on suppose Windows-1252 (encodage courant pour les CSV générés par Excel)
        return 'windows';
    }
    
    /**
     * Convertit un contenu vers UTF-8 depuis un encodage spécifié.
     */
    private function convertToUtf8(string $content, string $fromEncoding): string
    {
        $sourceEncoding = match($fromEncoding) {
            'iso' => 'ISO-8859-1',
            'windows' => 'Windows-1252',
            default => 'UTF-8'
        };
        
        return mb_convert_encoding($content, 'UTF-8', $sourceEncoding);
    }
    
    /**
     * Nettoie les données d'un enregistrement CSV.
     */
    private function sanitizeRecord(array $record): array
    {
        $sanitized = [];
        
        foreach ($record as $key => $value) {
            // Nettoyage des clés (y compris le BOM UTF-8)
            $cleanKey = trim($key);
            if (str_starts_with($cleanKey, "\xef\xbb\xbf")) {
                $cleanKey = substr($cleanKey, 3);
            }
            
            // Nettoyage des valeurs
            if (is_string($value)) {
                // Suppression des caractères invisibles et normalisation des espaces
                $cleanValue = trim(preg_replace('/\s+/', ' ', $value));
                
                // Conversion des chaînes vides en NULL
                $sanitized[$cleanKey] = $cleanValue === '' ? null : $cleanValue;
            } else {
                $sanitized[$cleanKey] = $value;
            }
        }
        
        return $sanitized;
    }
    
    /**
     * Prépare les données pour la validation.
     */
    private function prepareDataForValidation(array $record): array
    {
        $map = [
            'nom' => 'last_name', 
            'prenom' => 'first_name', 
            'date_naissance' => 'birth_date',
            'statut' => 'statut', 
            'matricule' => 'employee_number', 
            'telephone' => 'personal_phone',
            'email_personnel' => 'personal_email', 
            'adresse' => 'address', 
            'numero_permis' => 'license_number',
            'categorie_permis' => 'license_category', 
            'date_delivrance_permis' => 'license_issue_date',
            'autorite_delivrance' => 'license_authority', 
            'date_recrutement' => 'recruitment_date',
            'date_fin_contrat' => 'contract_end_date', 
            'contact_urgence_nom' => 'emergency_contact_name',
            'contact_urgence_telephone' => 'emergency_contact_phone', 
            'groupe_sanguin' => 'blood_type',
        ];
        
        $dataToValidate = [];
        
        foreach ($map as $csvHeader => $dbField) {
            if (array_key_exists($csvHeader, $record)) {
                $value = $record[$csvHeader];
                
                // Traitement spécifique pour les dates
                if (in_array($dbField, ['birth_date', 'license_issue_date', 'recruitment_date', 'contract_end_date']) && $value) {
                    $value = $this->formatDate($value);
                }
                
                $dataToValidate[$dbField] = $value;
            }
        }
        
        return $dataToValidate;
    }
    
    /**
     * Formate une date en format YYYY-MM-DD.
     */
    private function formatDate($dateString)
    {
        if (!$dateString) return null;

        // Si la date est déjà au format YYYY-MM-DD, on la retourne directement.
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateString)) {
            return $dateString;
        }

        $formats = [
            'd/m/Y', 'd-m-Y', 'd.m.Y',
            'Y/m/d', 'Y-m-d', 'Y.m.d',
            'd/m/y', 'd-m-y', 'd.m.y'
        ];

        foreach ($formats as $format) {
            $date = \DateTime::createFromFormat($format, $dateString);
            // On vérifie que la date a été créée et qu'il n'y a pas d'erreurs critiques.
            // On ignore les avertissements (warning_count) qui peuvent être trop stricts (ex: données en trop).
            $errors = \DateTime::getLastErrors();
            if ($date !== false && $errors['error_count'] === 0) {
                return $date->format('Y-m-d');
            }
        }

        // Si aucun format n'a fonctionné, on retourne la chaîne originale pour qu'elle échoue à la validation 'date_format:Y-m-d'
        // et retourne un message d'erreur clair à l'utilisateur.
        return $dateString;
    }
    
    /**
     * Formate une erreur de base de données de manière conviviale.
     */
    private function formatDatabaseError(QueryException $e): string
    {
        $errorCode = $e->getCode();
        $errorMessage = $e->getMessage();
        
        // Erreurs de contrainte d'unicité
        if (strpos($errorMessage, 'unique constraint') !== false || $errorCode == 23505) {
            if (strpos($errorMessage, 'employee_number') !== false) {
                return "Le matricule existe déjà dans la base de données.";
            }
            if (strpos($errorMessage, 'personal_email') !== false) {
                return "L'adresse email existe déjà dans la base de données.";
            }
            if (strpos($errorMessage, 'license_number') !== false) {
                return "Le numéro de permis existe déjà dans la base de données.";
            }
            return "Une valeur unique existe déjà dans la base de données.";
        }
        
        // Erreurs de contrainte de clé étrangère
        if (strpos($errorMessage, 'foreign key constraint') !== false || $errorCode == 23503) {
            return "Référence à une valeur qui n'existe pas dans la base de données.";
        }
        
        // Erreurs de type de données
        if (strpos($errorMessage, 'invalid input syntax') !== false || $errorCode == 22007) {
            if (strpos($errorMessage, 'date/time') !== false) {
                return "Format de date invalide. Utilisez le format AAAA-MM-JJ.";
            }
            return "Format de données invalide.";
        }
        
        // Erreur par défaut
        return "Erreur de base de données: " . $e->getMessage();
    }
    
    /**
     * Règles de validation pour l'importation de chauffeurs.
     */
    private function getValidationRules(): array
    {
        return [
            'last_name' => ['required', 'string', 'max:255'],
            'first_name' => ['required', 'string', 'max:255'],
            'birth_date' => ['required', 'date_format:Y-m-d'],
            'statut' => ['nullable', 'string'],
            'employee_number' => ['nullable', 'string', 'max:100', 'unique:drivers,employee_number,NULL,id,deleted_at,NULL'],
            'personal_phone' => ['nullable', 'string', 'max:50'],
            'personal_email' => ['nullable', 'email', 'max:255', 'unique:drivers,personal_email,NULL,id,deleted_at,NULL'],
            'address' => ['nullable', 'string', 'max:1000'],
            'license_number' => ['nullable', 'string', 'max:100', 'unique:drivers,license_number,NULL,id,deleted_at,NULL'],
            'license_category' => ['nullable', 'string', 'max:50'],
            'license_issue_date' => ['nullable', 'date_format:Y-m-d'],
            'license_authority' => ['nullable', 'string', 'max:255'],
            'recruitment_date' => ['nullable', 'date_format:Y-m-d'],
            'contract_end_date' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:recruitment_date'],
            'emergency_contact_name' => ['nullable', 'string', 'max:255'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:50'],
            'blood_type' => ['nullable', 'string', 'max:10'],
        ];
    }
}
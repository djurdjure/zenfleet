<?php

namespace App\Livewire\Admin\Drivers;

use App\Models\Driver;
use App\Models\DriverStatus;
use App\Models\Organization;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

/**
 * ====================================================================
 * 📥 DRIVERS IMPORT COMPONENT - WORLD-CLASS ENTERPRISE GRADE
 * ====================================================================
 * 
 * Composant Livewire pour l'importation en masse de chauffeurs
 * - Upload fichier avec validation temps réel
 * - Prévisualisation des données avant import
 * - Options d'importation configurables
 * - Gestion des doublons et mises à jour
 * - Rapport détaillé des résultats
 * 
 * @version 1.0-World-Class
 * @since 2025-01-19
 * ====================================================================
 */
class DriversImport extends Component
{
    use WithFileUploads;

    // ===============================================
    // PROPRIÉTÉS PUBLIQUES
    // ===============================================
    public $importFile;
    public $fileName = '';
    public $fileSize = 0;
    public $previewData = [];
    public $totalRows = 0;
    public $validRows = 0;
    public $invalidRows = 0;
    
    // Options d'importation
    public bool $skipDuplicates = true;
    public bool $updateExisting = false;
    public bool $dryRun = false;
    public bool $sendNotifications = true;
    
    // État du processus
    public string $step = 'upload'; // upload, preview, processing, complete
    public int $progress = 0;
    public array $importResults = [];
    public array $errors = [];
    public array $warnings = [];
    
    // ===============================================
    // RÈGLES DE VALIDATION
    // ===============================================
    protected $rules = [
        'importFile' => 'required|file|mimes:csv,xlsx,xls|max:10240', // 10MB
    ];

    protected $messages = [
        'importFile.required' => 'Veuillez sélectionner un fichier à importer.',
        'importFile.file' => 'Le fichier sélectionné n\'est pas valide.',
        'importFile.mimes' => 'Le fichier doit être au format CSV, XLSX ou XLS.',
        'importFile.max' => 'Le fichier ne doit pas dépasser 10 MB.',
    ];

    // ===============================================
    // MÉTHODES DU CYCLE DE VIE
    // ===============================================

    /**
     * Mise à jour du fichier
     */
    public function updatedImportFile(): void
    {
        $this->validateOnly('importFile');
        
        if ($this->importFile) {
            $this->fileName = $this->importFile->getClientOriginalName();
            $this->fileSize = $this->importFile->getSize();
            
            $this->dispatch('notification', [
                'type' => 'success',
                'message' => 'Fichier sélectionné avec succès'
            ]);
        }
    }

    // ===============================================
    // MÉTHODES D'IMPORTATION
    // ===============================================

    /**
     * Analyser le fichier et générer l'aperçu
     */
    public function analyzeFile(): void
    {
        $this->validate();

        try {
            $this->step = 'preview';
            $this->progress = 25;
            
            // Lire le fichier
            $fileContent = $this->readImportFile($this->importFile);
            
            if (empty($fileContent)) {
                throw new \Exception('Le fichier est vide ou illisible');
            }

            // Valider les données
            $this->validateImportData($fileContent);
            
            // Générer l'aperçu (premières 5 lignes)
            $this->previewData = array_slice($fileContent, 0, 5);
            $this->totalRows = count($fileContent);
            
            $this->progress = 50;
            
            $this->dispatch('notification', [
                'type' => 'success',
                'message' => "Fichier analysé : {$this->totalRows} chauffeur(s) détecté(s)"
            ]);
            
        } catch (\Exception $e) {
            $this->dispatch('notification', [
                'type' => 'error',
                'message' => 'Erreur lors de l\'analyse : ' . $e->getMessage()
            ]);
            
            $this->resetImport();
        }
    }

    /**
     * Lancer l'importation
     */
    public function startImport(): void
    {
        try {
            $this->step = 'processing';
            $this->progress = 60;
            
            // Lire à nouveau le fichier
            $fileContent = $this->readImportFile($this->importFile);
            
            if ($this->dryRun) {
                // Mode test : validation uniquement
                $this->importResults = $this->validateOnly($fileContent);
            } else {
                // Import réel
                $this->importResults = $this->processImport($fileContent);
            }
            
            $this->progress = 100;
            $this->step = 'complete';
            
            $successCount = $this->importResults['successful_imports'] ?? 0;
            $errorCount = count($this->importResults['errors'] ?? []);
            
            $this->dispatch('notification', [
                'type' => 'success',
                'message' => "Importation terminée : {$successCount} réussis, {$errorCount} erreurs"
            ]);
            
        } catch (\Exception $e) {
            $this->dispatch('notification', [
                'type' => 'error',
                'message' => 'Erreur lors de l\'importation : ' . $e->getMessage()
            ]);
            
            $this->step = 'preview';
        }
    }

    /**
     * Lire le fichier d'importation
     */
    protected function readImportFile($file): array
    {
        $extension = $file->getClientOriginalExtension();
        $path = $file->getRealPath();
        
        if ($extension === 'csv') {
            return $this->readCsvFile($path);
        } else {
            return $this->readExcelFile($path);
        }
    }

    /**
     * Lire fichier CSV
     */
    protected function readCsvFile(string $path): array
    {
        $data = [];
        $headers = [];
        
        if (($handle = fopen($path, 'r')) !== false) {
            $lineNumber = 0;
            
            while (($row = fgetcsv($handle, 1000, ';')) !== false) {
                $lineNumber++;
                
                if ($lineNumber === 1) {
                    // En-têtes
                    $headers = array_map('trim', $row);
                    continue;
                }
                
                // Combiner en-têtes et valeurs
                if (count($row) === count($headers)) {
                    $rowData = array_combine($headers, array_map('trim', $row));
                    $rowData['_line'] = $lineNumber;
                    $data[] = $rowData;
                }
            }
            
            fclose($handle);
        }
        
        return $data;
    }

    /**
     * Lire fichier Excel (simplifié - nécessite PhpSpreadsheet)
     */
    protected function readExcelFile(string $path): array
    {
        // Note: Nécessite PhpSpreadsheet
        // composer require phpoffice/phpspreadsheet
        
        if (!class_exists('\PhpOffice\PhpSpreadsheet\IOFactory')) {
            throw new \Exception('PhpSpreadsheet non installé. Utilisez un fichier CSV.');
        }
        
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($path);
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();
        
        $data = [];
        $headers = [];
        
        foreach ($rows as $index => $row) {
            if ($index === 0) {
                $headers = array_map('trim', $row);
                continue;
            }
            
            if (count(array_filter($row))) { // Ignorer lignes vides
                $rowData = array_combine($headers, array_map(fn($v) => trim((string)$v), $row));
                $rowData['_line'] = $index + 1;
                $data[] = $rowData;
            }
        }
        
        return $data;
    }

    /**
     * Valider les données d'importation
     */
    protected function validateImportData(array $data): void
    {
        $requiredColumns = ['first_name', 'last_name', 'license_number'];
        
        if (empty($data)) {
            throw new \Exception('Aucune donnée trouvée dans le fichier');
        }
        
        $firstRow = $data[0];
        $columns = array_keys($firstRow);
        
        foreach ($requiredColumns as $required) {
            if (!in_array($required, $columns)) {
                throw new \Exception("Colonne obligatoire manquante : {$required}");
            }
        }
        
        $this->validRows = 0;
        $this->invalidRows = 0;
        
        foreach ($data as $row) {
            if ($this->validateRow($row)) {
                $this->validRows++;
            } else {
                $this->invalidRows++;
            }
        }
    }

    /**
     * Valider une ligne
     */
    protected function validateRow(array $row): bool
    {
        $validator = Validator::make($row, [
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'license_number' => 'required|string|max:50',
            'personal_email' => 'nullable|email',
            'personal_phone' => 'nullable|string|max:20',
            'birth_date' => 'nullable|date_format:Y-m-d',
        ]);
        
        return !$validator->fails();
    }

    /**
     * Traiter l'importation
     */
    protected function processImport(array $data): array
    {
        $results = [
            'successful_imports' => 0,
            'updated_existing' => 0,
            'skipped_duplicates' => 0,
            'errors' => [],
            'warnings' => [],
        ];
        
        DB::beginTransaction();
        
        try {
            foreach ($data as $row) {
                try {
                    $result = $this->importDriver($row);
                    
                    if ($result['status'] === 'created') {
                        $results['successful_imports']++;
                    } elseif ($result['status'] === 'updated') {
                        $results['updated_existing']++;
                    } elseif ($result['status'] === 'skipped') {
                        $results['skipped_duplicates']++;
                    }
                    
                    if (!empty($result['warnings'])) {
                        $results['warnings'][] = [
                            'row' => $row['_line'],
                            'warnings' => $result['warnings'],
                        ];
                    }
                    
                } catch (\Exception $e) {
                    $results['errors'][] = [
                        'row' => $row['_line'],
                        'error' => $e->getMessage(),
                        'data' => $row,
                    ];
                }
            }
            
            DB::commit();
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
        
        return $results;
    }

    /**
     * Importer un chauffeur
     */
    protected function importDriver(array $data): array
    {
        $warnings = [];
        
        // Vérifier les doublons
        $existing = Driver::where('license_number', $data['license_number'])->first();
        
        if ($existing) {
            if ($this->skipDuplicates && !$this->updateExisting) {
                return ['status' => 'skipped'];
            }
            
            if ($this->updateExisting) {
                $this->updateDriver($existing, $data);
                return ['status' => 'updated', 'warnings' => $warnings];
            }
        }
        
        // Créer le chauffeur
        $driver = new Driver();
        $driver->first_name = $data['first_name'];
        $driver->last_name = $data['last_name'];
        $driver->license_number = $data['license_number'];
        $driver->personal_email = $data['personal_email'] ?? null;
        $driver->personal_phone = $data['personal_phone'] ?? null;
        $driver->employee_number = $data['employee_number'] ?? null;
        
        // Date de naissance
        if (!empty($data['birth_date'])) {
            try {
                $driver->birth_date = Carbon::parse($data['birth_date']);
            } catch (\Exception $e) {
                $warnings[] = 'Date de naissance invalide';
            }
        }
        
        // Statut par défaut
        $driver->status_id = $this->getDefaultStatusId();
        
        // Organisation
        $driver->organization_id = auth()->user()->organization_id;
        
        $driver->save();
        
        return ['status' => 'created', 'warnings' => $warnings];
    }

    /**
     * Mettre à jour un chauffeur existant
     */
    protected function updateDriver(Driver $driver, array $data): void
    {
        if (!empty($data['first_name'])) {
            $driver->first_name = $data['first_name'];
        }
        
        if (!empty($data['last_name'])) {
            $driver->last_name = $data['last_name'];
        }
        
        if (!empty($data['personal_email'])) {
            $driver->personal_email = $data['personal_email'];
        }
        
        if (!empty($data['personal_phone'])) {
            $driver->personal_phone = $data['personal_phone'];
        }
        
        $driver->save();
    }

    /**
     * Obtenir le statut par défaut
     */
    protected function getDefaultStatusId(): int
    {
        $status = DriverStatus::where('name', 'Disponible')
            ->orWhere('name', 'LIKE', '%disponible%')
            ->first();
        
        return $status?->id ?? DriverStatus::first()->id ?? 1;
    }

    // ===============================================
    // MÉTHODES DE GESTION
    // ===============================================

    /**
     * Télécharger le modèle CSV
     */
    public function downloadTemplate(): void
    {
        $headers = [
            'first_name',
            'last_name',
            'license_number',
            'personal_email',
            'personal_phone',
            'birth_date',
            'employee_number',
            'license_category',
            'address',
        ];
        
        $csv = implode(';', $headers) . "\n";
        $csv .= "Ahmed;Benali;123456789;ahmed@email.com;0555123456;1990-05-15;EMP001;B;Alger, Algérie\n";
        $csv .= "Fatima;Zohra;987654321;fatima@email.com;0666987654;1985-08-20;EMP002;C;Oran, Algérie\n";
        
        $this->dispatch('download-template', ['csv' => $csv, 'filename' => 'modele-import-chauffeurs.csv']);
    }

    /**
     * Supprimer le fichier
     */
    public function removeFile(): void
    {
        $this->importFile = null;
        $this->fileName = '';
        $this->fileSize = 0;
        $this->resetImport();
    }

    /**
     * Réinitialiser l'import
     */
    public function resetImport(): void
    {
        $this->step = 'upload';
        $this->progress = 0;
        $this->previewData = [];
        $this->totalRows = 0;
        $this->validRows = 0;
        $this->invalidRows = 0;
        $this->importResults = [];
        $this->errors = [];
        $this->warnings = [];
    }

    /**
     * Nouvelle importation
     */
    public function newImport(): void
    {
        $this->removeFile();
        $this->resetImport();
    }

    // ===============================================
    // RENDU DU COMPOSANT
    // ===============================================

    /**
     * Rendre le composant
     */
    public function render()
    {
        $driverStatuses = DriverStatus::all();
        
        return view('livewire.admin.drivers.drivers-import', [
            'driverStatuses' => $driverStatuses,
        ]);
    }
}

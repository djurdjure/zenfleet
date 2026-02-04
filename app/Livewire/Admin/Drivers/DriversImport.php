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
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use App\Services\DriverService;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\PermissionRegistrar;
use Spatie\Permission\Models\Role;

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

    private const REQUIRED_COLUMNS = [
        'first_name',
        'last_name',
        'license_number',
    ];

    private const HEADER_ALIASES = [
        'nom' => 'last_name',
        'last_name' => 'last_name',
        'nom_de_famille' => 'last_name',
        'prenom' => 'first_name',
        'prenoms' => 'first_name',
        'first_name' => 'first_name',
        'date_naissance' => 'birth_date',
        'birth_date' => 'birth_date',
        'date_de_naissance' => 'birth_date',
        'matricule' => 'employee_number',
        'employee_number' => 'employee_number',
        'statut' => 'status',
        'status' => 'status',
        'date_recrutement' => 'recruitment_date',
        'recruitment_date' => 'recruitment_date',
        'date_embauche' => 'recruitment_date',
        'date_fin_contrat' => 'contract_end_date',
        'contract_end_date' => 'contract_end_date',
        'telephone' => 'personal_phone',
        'personal_phone' => 'personal_phone',
        'tel' => 'personal_phone',
        'email_personnel' => 'personal_email',
        'personal_email' => 'personal_email',
        'adresse' => 'address',
        'address' => 'address',
        'numero_permis' => 'license_number',
        'numero_permis_de_conduire' => 'license_number',
        'numero_de_permis' => 'license_number',
        'numero_du_permis' => 'license_number',
        'numero_permis_conduire' => 'license_number',
        'numero_permis_conduite' => 'license_number',
        'num_permis' => 'license_number',
        'n_permis' => 'license_number',
        'no_permis' => 'license_number',
        'permis_de_conduire' => 'license_number',
        'permis_conduire' => 'license_number',
        'license_number' => 'license_number',
        'categorie_permis' => 'license_categories',
        'license_category' => 'license_categories',
        'license_categories' => 'license_categories',
        'date_delivrance_permis' => 'license_issue_date',
        'license_issue_date' => 'license_issue_date',
        'date_expiration_permis' => 'license_expiry_date',
        'license_expiry_date' => 'license_expiry_date',
        'autorite_delivrance' => 'license_authority',
        'license_authority' => 'license_authority',
        'contact_urgence_nom' => 'emergency_contact_name',
        'emergency_contact_name' => 'emergency_contact_name',
        'contact_urgence_telephone' => 'emergency_contact_phone',
        'emergency_contact_phone' => 'emergency_contact_phone',
        'groupe_sanguin' => 'blood_type',
        'blood_type' => 'blood_type',
        'ville' => 'city',
        'city' => 'city',
        'code_postal' => 'postal_code',
        'postal_code' => 'postal_code',
        'notes' => 'notes',
        'remarques' => 'notes',
    ];

    private const ALLOWED_LICENSE_CATEGORIES = [
        'A1', 'A', 'B', 'BE', 'C1', 'C1E', 'C', 'CE', 'D', 'DE', 'F',
    ];

    private const STATUS_ALIASES = [
        'actif' => 'disponible',
        'libre' => 'disponible',
        'disponible' => 'disponible',
        'en mission' => 'en mission',
        'mission' => 'en mission',
        'occupe' => 'en mission',
        'en conge' => 'en congé',
        'conge' => 'en congé',
        'congé' => 'en congé',
        'en formation' => 'en formation',
        'formation' => 'en formation',
        'suspendu' => 'suspendu',
        'inactif' => 'inactif',
        'maladie' => 'maladie',
        'absent' => 'maladie',
    ];

    private ?array $statusLookup = null;
    private static ?array $driverColumns = null;

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

    public array $warnings = [];
    public array $importReport = [];
    public array $credentialsReport = [];

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
                $this->importResults = $this->validateImportOnly($fileContent);
            } else {
                // Import réel
                $this->importResults = $this->processImport($fileContent);
            }

            $this->importReport = $this->importResults['report_rows'] ?? [];
            $this->credentialsReport = $this->importResults['credentials_rows'] ?? [];

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

        $lines = file($path, FILE_IGNORE_NEW_LINES);
        if ($lines === false) {
            return $data;
        }

        $delimiter = $this->detectDelimiter($lines);
        $lineNumber = 0;

        foreach ($lines as $line) {
            $lineNumber++;

            if (trim($line) === '') {
                continue;
            }

            $row = str_getcsv($line, $delimiter);
            $row = array_map([$this, 'cleanCell'], $row);

            if ($this->shouldSkipRow($row)) {
                continue;
            }

            if (empty($headers)) {
                $mappedHeaders = $this->mapHeaders($row);
                if (!$this->looksLikeHeader($mappedHeaders)) {
                    continue;
                }

                $headers = $mappedHeaders;
                continue;
            }

            $rowData = $this->mapRowToCanonical($headers, $row, $lineNumber);
            if ($rowData !== null) {
                $data[] = $rowData;
            }
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
            $row = array_map([$this, 'cleanCell'], array_map(fn($v) => (string)$v, $row));

            if ($this->shouldSkipRow($row)) {
                continue;
            }

            if (empty($headers)) {
                $mappedHeaders = $this->mapHeaders($row);
                if (!$this->looksLikeHeader($mappedHeaders)) {
                    continue;
                }

                $headers = $mappedHeaders;
                continue;
            }

            $rowData = $this->mapRowToCanonical($headers, $row, $index + 1);
            if ($rowData !== null) {
                $data[] = $rowData;
            }
        }

        return $data;
    }

    private function detectDelimiter(array $lines): string
    {
        $candidates = [
            ';' => 0,
            ',' => 0,
            "\t" => 0,
        ];

        foreach ($lines as $line) {
            $line = ltrim($line, "\xEF\xBB\xBF \t");
            if (trim($line) === '') {
                continue;
            }

            if ($this->lineLooksLikeComment($line)) {
                continue;
            }

            foreach (array_keys($candidates) as $delimiter) {
                $candidates[$delimiter] += substr_count($line, $delimiter);
            }

            if (array_sum($candidates) > 0) {
                break;
            }
        }

        arsort($candidates);

        return (string) array_key_first($candidates) ?: ';';
    }

    private function lineLooksLikeComment(string $line): bool
    {
        $trim = ltrim($line);
        return $trim === '' || str_starts_with($trim, '#') || str_starts_with($trim, '"#');
    }

    private function cleanCell(string $value): string
    {
        $value = preg_replace('/^\xEF\xBB\xBF/', '', $value);
        return trim($value ?? '');
    }

    private function shouldSkipRow(array $row): bool
    {
        $firstValue = null;

        foreach ($row as $cell) {
            $cell = trim((string) $cell);
            if ($cell !== '') {
                $firstValue = $cell;
                break;
            }
        }

        if ($firstValue === null) {
            return true;
        }

        return str_starts_with($firstValue, '#');
    }

    private function normalizeHeader(string $header): string
    {
        $header = Str::of($header)
            ->lower()
            ->ascii()
            ->trim()
            ->replace([' ', '-', '/', '.'], '_')
            ->replaceMatches('/[^a-z0-9_]+/', '_')
            ->trim('_');

        return $header->toString();
    }

    private function mapHeaders(array $headers): array
    {
        $mapped = [];
        $used = [];

        foreach ($headers as $header) {
            $normalized = $this->normalizeHeader((string) $header);
            $canonical = self::HEADER_ALIASES[$normalized] ?? null;

            if ($canonical === null || in_array($canonical, $used, true)) {
                $mapped[] = null;
                continue;
            }

            $mapped[] = $canonical;
            $used[] = $canonical;
        }

        return $mapped;
    }

    private function looksLikeHeader(array $mappedHeaders): bool
    {
        $known = array_values(array_filter($mappedHeaders));
        if (empty($known)) {
            return false;
        }

        $requiredMatches = count(array_intersect($known, self::REQUIRED_COLUMNS));
        return $requiredMatches >= 2 || count($known) >= 3;
    }

    private function mapRowToCanonical(array $headers, array $row, int $lineNumber): ?array
    {
        if (empty($headers)) {
            return null;
        }

        $row = array_pad($row, count($headers), '');
        if (count($row) > count($headers)) {
            $row = array_slice($row, 0, count($headers));
        }

        $rowData = ['_line' => $lineNumber];

        foreach ($headers as $index => $headerKey) {
            if ($headerKey === null) {
                continue;
            }

            $rowData[$headerKey] = $this->sanitizeValue($row[$index] ?? null);
        }

        $rowData = $this->applyTemplateFallback($rowData, $row);

        $hasData = false;
        foreach ($rowData as $key => $value) {
            if ($key === '_line') {
                continue;
            }
            if ($value !== null && $value !== '') {
                $hasData = true;
                break;
            }
        }

        return $hasData ? $rowData : null;
    }

    private function applyTemplateFallback(array $rowData, array $row): array
    {
        $templateHeaders = $this->getTemplateHeaders();
        if (count($row) < count($templateHeaders)) {
            return $rowData;
        }

        $missingRequired = empty($rowData['license_number'])
            || empty($rowData['first_name'])
            || empty($rowData['last_name']);

        if (!$missingRequired) {
            return $rowData;
        }

        foreach ($templateHeaders as $index => $header) {
            $normalized = $this->normalizeHeader($header);
            $canonical = self::HEADER_ALIASES[$normalized] ?? null;

            if (!$canonical) {
                continue;
            }

            if (!isset($rowData[$canonical]) || $rowData[$canonical] === '' || $rowData[$canonical] === null) {
                $value = $this->sanitizeValue($row[$index] ?? null);
                if ($value !== null && $value !== '') {
                    $rowData[$canonical] = $value;
                }
            }
        }

        return $rowData;
    }

    private function sanitizeValue($value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        $lower = Str::lower($value);
        if (in_array($lower, ['null', 'n/a', 'na'], true)) {
            return null;
        }

        return $value;
    }

    /**
     * Valider les données d'importation
     */
    protected function validateImportData(array $data): void
    {
        if (empty($data)) {
            throw new \Exception('Aucune donnée trouvée dans le fichier');
        }

        $firstRow = $data[0];
        $columns = array_keys($firstRow);

        $missing = array_diff(self::REQUIRED_COLUMNS, $columns);
        if (!empty($missing)) {
            $missingList = implode(', ', $missing);
            throw new \Exception(
                "Colonnes obligatoires manquantes : {$missingList}. " .
                "Utilisez le modèle officiel (nom, prenom, numero_permis)."
            );
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
        $validation = $this->validateRowDetailed($row);
        return $validation['valid'];
    }

    protected function validateRowDetailed(array $row): array
    {
        $normalized = $this->normalizeRow($row);
        $data = $normalized['data'];

        $errors = $normalized['errors'];
        $warnings = $normalized['warnings'];

        $validator = Validator::make($data, [
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'license_number' => 'required|string|max:100',
            'employee_number' => 'nullable|string|max:100',
            'personal_email' => 'nullable|email',
            'personal_phone' => 'nullable|string|max:30',
            'birth_date' => 'nullable|date_format:Y-m-d',
            'recruitment_date' => 'nullable|date_format:Y-m-d',
            'contract_end_date' => 'nullable|date_format:Y-m-d',
            'license_issue_date' => 'nullable|date_format:Y-m-d',
            'license_expiry_date' => 'nullable|date_format:Y-m-d',
            'license_categories' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            $errors = array_merge($errors, $validator->errors()->all());
        }

        if (!empty($data['recruitment_date']) && !empty($data['contract_end_date'])) {
            try {
                if (Carbon::parse($data['contract_end_date'])->lt(Carbon::parse($data['recruitment_date']))) {
                    $errors[] = 'La date de fin de contrat doit être postérieure à la date de recrutement.';
                }
            } catch (\Exception $e) {
                $errors[] = 'Dates de recrutement/fin de contrat invalides.';
            }
        }

        if (!empty($data['license_issue_date']) && !empty($data['license_expiry_date'])) {
            try {
                if (Carbon::parse($data['license_expiry_date'])->lt(Carbon::parse($data['license_issue_date']))) {
                    $errors[] = 'La date d\'expiration du permis doit être postérieure à la date de délivrance.';
                }
            } catch (\Exception $e) {
                $errors[] = 'Dates de permis invalides.';
            }
        }

        return [
            'valid' => empty($errors),
            'data' => $data,
            'errors' => $errors,
            'warnings' => $warnings,
        ];
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
            'report_rows' => [],
            'credentials_rows' => [],
        ];

        try {
            foreach ($data as $row) {
                try {
                    $validation = $this->validateRowDetailed($row);
                    if (!$validation['valid']) {
                        $results['report_rows'][] = $this->buildReportRow(
                            $row['_line'] ?? null,
                            $validation['data'] ?? $row,
                            'error',
                            implode(' ', $validation['errors'])
                        );
                        $results['errors'][] = [
                            'row' => $row['_line'] ?? null,
                            'error' => implode(' ', $validation['errors']),
                            'data' => $row,
                        ];
                        continue;
                    }

                    $result = DB::transaction(function () use ($validation) {
                        return $this->importDriver($validation['data'], $validation['warnings']);
                    });

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

                    $statusLabel = $result['status'] ?? 'created';
                    $message = !empty($result['warnings']) ? implode(' ', $result['warnings']) : 'OK';
                    $results['report_rows'][] = $this->buildReportRow(
                        $row['_line'] ?? null,
                        $validation['data'],
                        $statusLabel,
                        $message
                    );

                    if (!empty($result['credentials'])) {
                        $results['credentials_rows'][] = $result['credentials'];
                    }
                } catch (\Exception $e) {
                    $results['report_rows'][] = $this->buildReportRow(
                        $row['_line'] ?? null,
                        $row,
                        'error',
                        $e->getMessage()
                    );
                    $results['errors'][] = [
                        'row' => $row['_line'] ?? null,
                        'error' => $e->getMessage(),
                        'data' => $row,
                    ];
                }
            }
        } catch (\Exception $e) {
            throw $e;
        }

        return $results;
    }

    protected function validateImportOnly(array $data): array
    {
        $results = [
            'successful_imports' => 0,
            'updated_existing' => 0,
            'skipped_duplicates' => 0,
            'errors' => [],
            'warnings' => [],
            'report_rows' => [],
            'credentials_rows' => [],
        ];

        foreach ($data as $row) {
            $validation = $this->validateRowDetailed($row);

            if (!$validation['valid']) {
                $results['report_rows'][] = $this->buildReportRow(
                    $row['_line'] ?? null,
                    $validation['data'] ?? $row,
                    'error',
                    implode(' ', $validation['errors'])
                );
                $results['errors'][] = [
                    'row' => $row['_line'] ?? null,
                    'error' => implode(' ', $validation['errors']),
                    'data' => $row,
                ];
                continue;
            }

            $results['successful_imports']++;
            $message = !empty($validation['warnings']) ? implode(' ', $validation['warnings']) : 'OK';
            $results['report_rows'][] = $this->buildReportRow(
                $row['_line'] ?? null,
                $validation['data'],
                'validated',
                $message
            );

            if (!empty($validation['warnings'])) {
                $results['warnings'][] = [
                    'row' => $row['_line'] ?? null,
                    'warnings' => $validation['warnings'],
                ];
            }
        }

        return $results;
    }

    /**
     * Importer un chauffeur
     */
    protected function importDriver(array $data, array $warnings = []): array
    {
        // Vérifier les doublons
        $licenseNumber = $data['license_number'] ?? null;
        if (empty($licenseNumber)) {
            throw new \Exception('Numéro de permis manquant.');
        }

        $existing = Driver::withTrashed()
            ->where('license_number', $licenseNumber)
            ->where('organization_id', auth()->user()->organization_id)
            ->first();

        if ($existing) {
            if ($existing->trashed()) {
                $existing->restore();
                $this->updateDriver($existing, $data);
                $credentials = $this->ensureUserForDriver($existing, $data, $warnings);
                $warnings[] = 'Chauffeur restauré (archivé auparavant).';
                return [
                    'status' => 'updated',
                    'warnings' => $warnings,
                    'credentials' => $credentials,
                ];
            }

            if ($this->skipDuplicates && !$this->updateExisting) {
                return ['status' => 'skipped', 'warnings' => $warnings];
            }

            if ($this->updateExisting) {
                $this->updateDriver($existing, $data);
                $credentials = $this->ensureUserForDriver($existing, $data, $warnings);
                return [
                    'status' => 'updated',
                    'warnings' => $warnings,
                    'credentials' => $credentials,
                ];
            }
        }

        $payload = [
            'first_name' => $data['first_name'] ?? null,
            'last_name' => $data['last_name'] ?? null,
            'license_number' => $licenseNumber,
            'personal_email' => $data['personal_email'] ?? null,
            'personal_phone' => $data['personal_phone'] ?? null,
            'employee_number' => $data['employee_number'] ?? null,
            'address' => $data['address'] ?? null,
            'city' => $data['city'] ?? null,
            'postal_code' => $data['postal_code'] ?? null,
            'blood_type' => $data['blood_type'] ?? null,
            'emergency_contact_name' => $data['emergency_contact_name'] ?? null,
            'emergency_contact_phone' => $data['emergency_contact_phone'] ?? null,
            'license_authority' => $data['license_authority'] ?? null,
            'license_categories' => !empty($data['license_categories']) ? $data['license_categories'] : null,
            'license_issue_date' => $data['license_issue_date'] ?? null,
            'license_expiry_date' => $data['license_expiry_date'] ?? null,
            'recruitment_date' => $data['recruitment_date'] ?? null,
            'contract_end_date' => $data['contract_end_date'] ?? null,
            'notes' => $data['notes'] ?? null,
            'birth_date' => $data['birth_date'] ?? null,
            'status_id' => $data['status_id'] ?? $this->getDefaultStatusId(),
            'organization_id' => auth()->user()->organization_id,
        ];

        $payload = $this->filterDataToExistingColumns($payload);

        $driverService = app(DriverService::class);
        $result = $driverService->createDriver($payload);

        $credentials = null;
        if (!empty($result['was_created']) && !empty($result['password']) && !empty($result['user'])) {
            $credentials = $this->buildCredentialsRow(
                $payload,
                $result['user']->email ?? null,
                $result['password']
            );
        }

        return [
            'status' => 'created',
            'warnings' => $warnings,
            'credentials' => $credentials,
        ];
    }

    /**
     * Mettre à jour un chauffeur existant
     */
    protected function updateDriver(Driver $driver, array $data): void
    {
        $payload = [
            'first_name' => $data['first_name'] ?? null,
            'last_name' => $data['last_name'] ?? null,
            'personal_email' => $data['personal_email'] ?? null,
            'personal_phone' => $data['personal_phone'] ?? null,
            'employee_number' => $data['employee_number'] ?? null,
            'address' => $data['address'] ?? null,
            'city' => $data['city'] ?? null,
            'postal_code' => $data['postal_code'] ?? null,
            'blood_type' => $data['blood_type'] ?? null,
            'emergency_contact_name' => $data['emergency_contact_name'] ?? null,
            'emergency_contact_phone' => $data['emergency_contact_phone'] ?? null,
            'license_authority' => $data['license_authority'] ?? null,
            'license_categories' => !empty($data['license_categories']) ? $data['license_categories'] : null,
            'license_issue_date' => $data['license_issue_date'] ?? null,
            'license_expiry_date' => $data['license_expiry_date'] ?? null,
            'recruitment_date' => $data['recruitment_date'] ?? null,
            'contract_end_date' => $data['contract_end_date'] ?? null,
            'notes' => $data['notes'] ?? null,
            'status_id' => $data['status_id'] ?? null,
        ];

        $payload = array_filter($payload, function ($value) {
            if (is_array($value)) {
                return !empty($value);
            }
            return $value !== null && $value !== '';
        });

        if (!empty($payload)) {
            $payload = $this->filterDataToExistingColumns($payload);
            if (!empty($payload)) {
                $driver->fill($payload);
                $driver->save();
            }
        }
    }

    private function ensureUserForDriver(Driver $driver, array $data, array &$warnings): ?array
    {
        if ($driver->user_id) {
            return null;
        }

        $firstName = $data['first_name'] ?? $driver->first_name;
        $lastName = $data['last_name'] ?? $driver->last_name;

        if (empty($firstName) || empty($lastName)) {
            $warnings[] = 'Utilisateur non créé: nom/prénom manquant.';
            return null;
        }

        $baseEmail = Str::slug($firstName . '.' . $lastName) . '@zenfleet.dz';
        $email = $baseEmail;

        $existingUser = User::withTrashed()->where('email', $email)->first();
        if ($existingUser && $existingUser->trashed()
            && (empty($existingUser->organization_id) || $existingUser->organization_id === $driver->organization_id)) {
            $generatedPassword = $this->generateDriverPassword($firstName, $lastName);

            $existingUser->restore();
            $existingUser->fill([
                'name' => $firstName . ' ' . $lastName,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'phone' => $data['personal_phone'] ?? $driver->personal_phone,
                'organization_id' => $driver->organization_id,
                'email_verified_at' => now(),
            ]);
            $existingUser->password = Hash::make($generatedPassword);
            $existingUser->save();

            $user = $existingUser;
        } else {
            $counter = 1;
            while (User::withTrashed()->where('email', $email)->exists()) {
                $email = Str::slug($firstName . '.' . $lastName) . $counter . '@zenfleet.dz';
                $counter++;
            }

            $generatedPassword = $this->generateDriverPassword($firstName, $lastName);

            $user = User::create([
                'name' => $firstName . ' ' . $lastName,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email,
                'phone' => $data['personal_phone'] ?? $driver->personal_phone,
                'password' => Hash::make($generatedPassword),
                'organization_id' => $driver->organization_id,
                'email_verified_at' => now(),
            ]);
        }

        $role = Role::where('name', 'Chauffeur')
            ->where('organization_id', $driver->organization_id)
            ->first();

        if (!$role) {
            $role = Role::where('name', 'Chauffeur')
                ->whereNull('organization_id')
                ->first();
        }

        if ($role) {
            DB::table('model_has_roles')->updateOrInsert([
                'role_id' => $role->id,
                'model_type' => User::class,
                'model_id' => $user->id,
                'organization_id' => $driver->organization_id,
            ], []);

            app(PermissionRegistrar::class)->forgetCachedPermissions();
        }

        $driver->user_id = $user->id;
        $driver->save();

        return $this->buildCredentialsRow(
            $data,
            $user->email ?? $email,
            $generatedPassword
        );
    }

    private function generateDriverPassword(string $firstName, string $lastName): string
    {
        $firstInitial = Str::upper(Str::substr(trim((string) $firstName), 0, 1));
        $lastNameFormatted = trim((string) $lastName);
        $lastNameFormatted = $lastNameFormatted !== ''
            ? (Str::upper(Str::substr($lastNameFormatted, 0, 1)) . Str::substr($lastNameFormatted, 1))
            : '';

        return $firstInitial . $lastNameFormatted . '@' . now()->format('Y');
    }

    /**
     * Obtenir le statut par défaut
     */
    protected function getDefaultStatusId(): int
    {
        $statusId = $this->resolveStatusId('Disponible');

        if ($statusId) {
            return $statusId;
        }

        $status = DriverStatus::orderBy('id')->first();
        return $status?->id ?? 1;
    }

    private function normalizeRow(array $row): array
    {
        $data = [];
        $errors = [];
        $warnings = [];

        foreach ($row as $key => $value) {
            if ($key === '_line') {
                continue;
            }

            $value = $this->sanitizeValue($value);

            switch ($key) {
                case 'birth_date':
                case 'recruitment_date':
                case 'contract_end_date':
                case 'license_issue_date':
                case 'license_expiry_date':
                    if ($value !== null) {
                        [$normalizedDate, $dateError] = $this->normalizeDate($value, $key);
                        if ($dateError) {
                            $errors[] = $dateError;
                        }
                        $data[$key] = $normalizedDate;
                    } else {
                        $data[$key] = null;
                    }
                    break;
                case 'license_categories':
                    if ($value !== null) {
                        $data[$key] = $this->normalizeLicenseCategories($value, $warnings);
                    }
                    break;
                case 'license_number':
                    $data[$key] = $value !== null ? Str::upper(trim($value)) : null;
                    break;
                case 'employee_number':
                    $data[$key] = $value !== null ? Str::upper(trim($value)) : null;
                    break;
                case 'status':
                    if ($value !== null) {
                        $data['status_id'] = $this->resolveStatusId($value);
                        $data[$key] = $value;
                        if (empty($data['status_id'])) {
                            $warnings[] = "Statut inconnu: {$value}";
                        }
                    }
                    break;
                case 'personal_email':
                    $data[$key] = $value !== null ? Str::lower($value) : null;
                    break;
                case 'personal_phone':
                    $data[$key] = $value !== null ? preg_replace('/\s+/', ' ', $value) : null;
                    break;
                default:
                    $data[$key] = $value;
                    break;
            }
        }

        return [
            'data' => $data,
            'errors' => $errors,
            'warnings' => $warnings,
        ];
    }

    private function normalizeDate(string $value, string $field): array
    {
        $value = trim($value);

        if ($value === '') {
            return [null, null];
        }

        if (is_numeric($value)) {
            try {
                $excelDate = Carbon::createFromTimestampUTC(((float) $value - 25569) * 86400);
                return [$excelDate->format('Y-m-d'), null];
            } catch (\Exception $e) {
                return [null, "Date invalide pour {$field} (format Excel)."];
            }
        }

        $formats = [
            'Y-m-d',
            'd/m/Y',
            'd-m-Y',
            'd.m.Y',
            'Y/m/d',
        ];

        foreach ($formats as $format) {
            try {
                $date = Carbon::createFromFormat($format, $value);
                return [$date->format('Y-m-d'), null];
            } catch (\Exception $e) {
                // try next format
            }
        }

        return [null, "Date invalide pour {$field} : {$value} (formats acceptés AAAA-MM-JJ ou JJ/MM/AAAA)."];
    }

    private function normalizeLicenseCategories(string $value, array &$warnings): array
    {
        $parts = preg_split('/[;,|\/\s]+/', $value) ?: [];
        $categories = [];

        foreach ($parts as $part) {
            $category = Str::upper(trim($part));
            if ($category === '') {
                continue;
            }
            $categories[] = $category;
        }

        $categories = array_values(array_unique($categories));

        $invalid = array_diff($categories, self::ALLOWED_LICENSE_CATEGORIES);
        if (!empty($invalid)) {
            $warnings[] = 'Catégories permis invalides ignorées: ' . implode(', ', $invalid);
            $categories = array_values(array_diff($categories, $invalid));
        }

        if ($value !== '' && empty($categories)) {
            $warnings[] = 'Aucune catégorie de permis valide détectée.';
        }

        return $categories;
    }

    private function resolveStatusId(string $statusText): ?int
    {
        $normalized = Str::of($statusText)
            ->lower()
            ->ascii()
            ->trim()
            ->replace(['_', '-'], ' ')
            ->replaceMatches('/\s+/', ' ')
            ->toString();

        if ($normalized === '') {
            return null;
        }

        if (isset(self::STATUS_ALIASES[$normalized])) {
            $normalized = self::STATUS_ALIASES[$normalized];
        }

        $lookup = $this->getStatusLookup();

        return $lookup[$normalized] ?? null;
    }

    private function getStatusLookup(): array
    {
        if ($this->statusLookup !== null) {
            return $this->statusLookup;
        }

        $lookup = [];
        $statuses = DriverStatus::query()->get(['id', 'name', 'slug']);

        foreach ($statuses as $status) {
            $nameKey = $this->normalizeStatusKey($status->name);
            $lookup[$nameKey] = $status->id;

            if (!empty($status->slug)) {
                $lookup[$this->normalizeStatusKey($status->slug)] = $status->id;
            }
        }

        foreach (self::STATUS_ALIASES as $alias => $canonical) {
            $canonicalKey = $this->normalizeStatusKey($canonical);
            if (isset($lookup[$canonicalKey])) {
                $lookup[$alias] = $lookup[$canonicalKey];
            }
        }

        $this->statusLookup = $lookup;

        return $lookup;
    }

    private function normalizeStatusKey(string $value): string
    {
        return Str::of($value)
            ->lower()
            ->ascii()
            ->trim()
            ->replace(['_', '-'], ' ')
            ->replaceMatches('/\s+/', ' ')
            ->toString();
    }

    private function filterDataToExistingColumns(array $payload): array
    {
        $columns = $this->getDriverColumns();
        if (empty($columns)) {
            return $payload;
        }

        return array_filter(
            $payload,
            fn($value, $key) => in_array($key, $columns, true),
            ARRAY_FILTER_USE_BOTH
        );
    }

    private function getDriverColumns(): array
    {
        if (self::$driverColumns !== null) {
            return self::$driverColumns;
        }

        try {
            self::$driverColumns = Schema::getColumnListing('drivers');
        } catch (\Exception $e) {
            self::$driverColumns = [];
        }

        return self::$driverColumns;
    }

    private function getTemplateHeaders(): array
    {
        return [
            'nom',
            'prenom',
            'date_naissance',
            'matricule',
            'statut',
            'date_recrutement',
            'date_fin_contrat',
            'telephone',
            'email_personnel',
            'adresse',
            'numero_permis',
            'categorie_permis',
            'date_delivrance_permis',
            'date_expiration_permis',
            'autorite_delivrance',
            'contact_urgence_nom',
            'contact_urgence_telephone',
            'groupe_sanguin',
        ];
    }

    // ===============================================
    // MÉTHODES DE GESTION
    // ===============================================

    /**
     * Télécharger le modèle CSV
     */
    public function downloadTemplate(): void
    {
        $headers = $this->getTemplateHeaders();

        $instructions = [
            '# INSTRUCTIONS DÉTAILLÉES #',
            '# Remplissez les colonnes ci-dessous #',
            '# Séparateur recommandé: point-virgule (;) #',
            '# Dates acceptées: AAAA-MM-JJ ou JJ/MM/AAAA (recommandé AAAA-MM-JJ) #',
            '# Champs obligatoires: nom, prenom, numero_permis #',
            '# Les lignes de commentaires (#) sont ignorées par le système #',
            '# Statuts acceptés: Disponible, En formation, Suspendu, Inactif, En mission, En congé #',
            '# Catégories permis: A1, A, B, BE, C1, C1E, C, CE, D, DE, F #',
            '# Groupes sanguins: A+, A-, B+, B-, AB+, AB-, O+, O- #',
        ];

        $fieldComments = [
            '# Nom de famille (obligatoire) #',
            '# Prénom(s) (obligatoire) #',
            '# Date naissance (AAAA-MM-JJ) #',
            '# Matricule employé unique #',
            '# Statut actuel du chauffeur #',
            '# Date de recrutement #',
            '# Date fin contrat (vide si CDI) #',
            '# Numéro de téléphone #',
            '# Email personnel #',
            '# Adresse complète #',
            '# Numéro permis de conduire (obligatoire) #',
            '# Catégories permis (B,C,D...) #',
            '# Date délivrance permis #',
            '# Date expiration permis #',
            '# Autorité de délivrance #',
            '# Nom contact urgence #',
            '# Téléphone contact urgence #',
            '# Groupe sanguin (A+, B-, O+...) #',
        ];

        $examples = [
            [
                'Benali',
                'Ahmed',
                '1985-03-15',
                'EMP-2024-001',
                'Disponible',
                '2024-01-15',
                '2026-12-31',
                '0550123456',
                'ahmed.benali@email.dz',
                '25 Rue Didouche Mourad, 16000 Alger',
                'P-DZ-2020-001234',
                'B,C',
                '2020-06-15',
                '2030-06-14',
                'Wilaya d\'Alger',
                'Benali Fatima',
                '0661234567',
                'O+',
            ],
            [
                'Kaddour',
                'Amina',
                '1990-07-22',
                'EMP-2024-002',
                'En formation',
                '2024-02-01',
                '',
                '0770987654',
                'amina.kaddour@gmail.com',
                '18 Boulevard Mohamed V, 31000 Oran',
                'P-DZ-2021-005678',
                'B',
                '2021-09-10',
                '2031-09-09',
                'Wilaya d\'Oran',
                'Kaddour Mohamed',
                '0552987654',
                'A+',
            ],
            [
                'Slimani',
                'Youcef',
                '1982-11-08',
                'EMP-2024-003',
                'Disponible',
                '2024-01-20',
                '2025-12-31',
                '0665555444',
                'y.slimani@outlook.com',
                '42 Rue Larbi Ben M\'hidi, 25000 Constantine',
                'P-DZ-2019-009876',
                'B,C,D',
                '2019-04-20',
                '2029-04-19',
                'Wilaya de Constantine',
                'Slimani Aicha',
                '0771555444',
                'B-',
            ],
        ];

        $rows = [];
        $maxCols = count($headers);
        $rows[] = $this->padRow($instructions, $maxCols);
        $rows[] = $this->padRow($fieldComments, $maxCols);
        $rows[] = $headers;

        foreach ($examples as $example) {
            $rows[] = $example;
        }

        $rows[] = [''];
        $rows[] = ['# INFORMATIONS IMPORTANTES #'];
        $rows[] = ['# 1. Encodage: UTF-8 #'];
        $rows[] = ['# 2. Séparateur: point-virgule (;) ou virgule (,) #'];
        $rows[] = ['# 3. Taille max: 10 MB #'];
        $rows[] = ['# 4. Extensions: .csv, .xlsx, .xls #'];
        $rows[] = ['# 5. Les lignes de commentaires sont ignorées #'];
        $rows[] = [''];
        $rows[] = ['# Généré le: ' . now()->format('d/m/Y H:i:s') . ' #'];

        $csv = $this->buildCsvContent($rows, ';');

        $this->dispatch('download-template', csv: $csv, filename: 'ZenFleet_Template_Import_Chauffeurs.csv');
    }

    public function downloadImportReport(): void
    {
        if (empty($this->importReport)) {
            $this->dispatch('notification', [
                'type' => 'error',
                'message' => 'Aucun rapport d\'importation disponible.'
            ]);
            return;
        }

        $rows = [
            ['Ligne', 'Statut', 'Nom', 'Prénom', 'Matricule', 'Permis', 'Message'],
        ];

        foreach ($this->importReport as $row) {
            $rows[] = [
                $row['line'] ?? '',
                $row['status'] ?? '',
                $row['last_name'] ?? '',
                $row['first_name'] ?? '',
                $row['employee_number'] ?? '',
                $row['license_number'] ?? '',
                $row['message'] ?? '',
            ];
        }

        $csv = $this->buildCsvContent($rows, ';');
        $filename = 'ZenFleet_Rapport_Import_Chauffeurs_' . now()->format('Ymd_His') . '.csv';

        $this->dispatch('download-report', csv: $csv, filename: $filename);
    }

    public function downloadCredentialsReport(): void
    {
        if (empty($this->credentialsReport)) {
            $this->dispatch('notification', [
                'type' => 'warning',
                'message' => 'Aucun compte utilisateur n\'a été créé lors de cet import.'
            ]);
            return;
        }

        $rows = [
            ['Nom', 'Prénom', 'Matricule', 'Login', 'Password'],
        ];

        foreach ($this->credentialsReport as $row) {
            $rows[] = [
                $row['last_name'] ?? '',
                $row['first_name'] ?? '',
                $row['employee_number'] ?? '',
                $row['login'] ?? '',
                $row['password'] ?? '',
            ];
        }

        $csv = $this->buildCsvContent($rows, ';');
        $filename = 'ZenFleet_Credentials_Chauffeurs_' . now()->format('Ymd_His') . '.csv';

        $this->dispatch('download-credentials-report', csv: $csv, filename: $filename);
    }

    private function padRow(array $row, int $length): array
    {
        if (count($row) >= $length) {
            return $row;
        }

        return array_pad($row, $length, '');
    }

    private function buildCsvContent(array $rows, string $delimiter = ';'): string
    {
        $lines = [];

        foreach ($rows as $row) {
            $lines[] = $this->buildCsvLine($row, $delimiter);
        }

        return "\xEF\xBB\xBF" . implode("\n", $lines) . "\n";
    }

    private function buildCsvLine(array $row, string $delimiter): string
    {
        $escaped = array_map(function ($value) use ($delimiter) {
            $value = (string) $value;
            $needsQuotes = str_contains($value, $delimiter)
                || str_contains($value, '"')
                || str_contains($value, "\n")
                || str_contains($value, "\r");

            if ($needsQuotes) {
                $value = str_replace('"', '""', $value);
                $value = '"' . $value . '"';
            }

            return $value;
        }, $row);

        return implode($delimiter, $escaped);
    }

    private function buildReportRow(?int $line, array $data, string $status, string $message): array
    {
        return [
            'line' => $line,
            'status' => $status,
            'last_name' => $data['last_name'] ?? $data['nom'] ?? '',
            'first_name' => $data['first_name'] ?? $data['prenom'] ?? '',
            'employee_number' => $data['employee_number'] ?? $data['matricule'] ?? '',
            'license_number' => $data['license_number'] ?? $data['numero_permis'] ?? '',
            'message' => $message,
        ];
    }

    private function buildCredentialsRow(array $data, ?string $login, string $password): array
    {
        return [
            'first_name' => $data['first_name'] ?? '',
            'last_name' => $data['last_name'] ?? '',
            'employee_number' => $data['employee_number'] ?? '',
            'login' => $login ?? '',
            'password' => $password,
        ];
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
        $this->importReport = [];
        $this->credentialsReport = [];

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

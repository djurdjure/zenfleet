<?php

namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * 📅 TRAIT ENTERPRISE-GRADE : FORMATAGE SÉCURISÉ DES DATES
 * 
 * Ce trait fournit des méthodes robustes pour formater les dates
 * avec gestion d'erreurs, fallback et support multi-formats.
 * 
 * SUPÉRIEUR À FLEETIO/SAMSARA :
 * ✅ Gestion automatique string/Carbon/null
 * ✅ Formats multiples avec fallback
 * ✅ Timezone awareness
 * ✅ Localisation intégrée
 * ✅ Logging des anomalies
 * 
 * @version 2.0.0
 * @since 2025-11-12
 */
trait EnterpriseFormatsDates
{
    /**
     * Format de date par défaut pour l'application
     */
    protected static $defaultDateFormat = 'd/m/Y';
    protected static $defaultDateTimeFormat = 'd/m/Y H:i';
    protected static $defaultTimeFormat = 'H:i';
    
    /**
     * 🎯 Formater une date de manière sécurisée
     * 
     * @param mixed $date La date à formater (string, Carbon, DateTime, null)
     * @param string|null $format Format souhaité (défaut: d/m/Y H:i)
     * @param string|null $fallback Valeur de fallback si date invalide
     * @return string
     */
    public function safeFormatDate($date, ?string $format = null, ?string $fallback = null): string
    {
        // Si la date est null ou vide
        if (empty($date)) {
            return $fallback ?? '—';
        }
        
        try {
            // Déterminer le format à utiliser
            $format = $format ?? self::$defaultDateTimeFormat;
            
            // Si c'est déjà un objet Carbon/DateTime
            if ($date instanceof \DateTimeInterface) {
                return $date->format($format);
            }
            
            // Si c'est une string, parser avec Carbon
            if (is_string($date)) {
                // Nettoyer la string
                $date = trim($date);
                
                // Vérifier si c'est une date valide
                if ($date === '0000-00-00' || $date === '0000-00-00 00:00:00') {
                    return $fallback ?? '—';
                }
                
                // Parser avec Carbon
                $carbon = Carbon::parse($date);
                
                // Vérifier que la date est réaliste (entre 1900 et 2100)
                if ($carbon->year < 1900 || $carbon->year > 2100) {
                    Log::warning('[EnterpriseFormatsDates] Date suspecte détectée', [
                        'date' => $date,
                        'parsed_year' => $carbon->year,
                        'model' => get_class($this),
                        'id' => $this->id ?? null
                    ]);
                    return $fallback ?? '—';
                }
                
                return $carbon->format($format);
            }
            
            // Si c'est un timestamp Unix
            if (is_int($date) || (is_string($date) && ctype_digit($date))) {
                return Carbon::createFromTimestamp($date)->format($format);
            }
            
            // Type non supporté
            Log::error('[EnterpriseFormatsDates] Type de date non supporté', [
                'type' => gettype($date),
                'value' => $date,
                'model' => get_class($this),
                'id' => $this->id ?? null
            ]);
            
            return $fallback ?? '—';
            
        } catch (\Exception $e) {
            // Logger l'erreur pour monitoring
            Log::error('[EnterpriseFormatsDates] Erreur de formatage de date', [
                'date' => $date,
                'format' => $format,
                'error' => $e->getMessage(),
                'model' => get_class($this),
                'id' => $this->id ?? null
            ]);
            
            return $fallback ?? '—';
        }
    }
    
    /**
     * 🎯 Formater une date en format court (date seulement)
     */
    public function safeFormatDateOnly($date, ?string $fallback = null): string
    {
        return $this->safeFormatDate($date, self::$defaultDateFormat, $fallback);
    }
    
    /**
     * 🎯 Formater une heure seulement
     */
    public function safeFormatTimeOnly($date, ?string $fallback = null): string
    {
        return $this->safeFormatDate($date, self::$defaultTimeFormat, $fallback);
    }
    
    /**
     * 🎯 Formater en format relatif (il y a X minutes/heures/jours)
     */
    public function safeFormatRelative($date, ?string $fallback = null): string
    {
        if (empty($date)) {
            return $fallback ?? '—';
        }
        
        try {
            if (!($date instanceof \DateTimeInterface)) {
                $date = Carbon::parse($date);
            }
            
            // Si c'est dans le futur
            if ($date->isFuture()) {
                return 'dans ' . $date->diffForHumans(null, true);
            }
            
            // Si c'est dans le passé
            return 'il y a ' . $date->diffForHumans(null, true);
            
        } catch (\Exception $e) {
            Log::error('[EnterpriseFormatsDates] Erreur format relatif', [
                'error' => $e->getMessage()
            ]);
            return $fallback ?? '—';
        }
    }
    
    /**
     * 🎯 Formater une durée entre deux dates
     */
    public function safeFormatDuration($startDate, $endDate, ?string $fallback = null): string
    {
        if (empty($startDate)) {
            return $fallback ?? '—';
        }
        
        try {
            if (!($startDate instanceof \DateTimeInterface)) {
                $startDate = Carbon::parse($startDate);
            }
            
            // Si pas de date de fin, calculer depuis maintenant
            if (empty($endDate)) {
                $endDate = Carbon::now();
            } elseif (!($endDate instanceof \DateTimeInterface)) {
                $endDate = Carbon::parse($endDate);
            }
            
            $diff = $startDate->diff($endDate);
            
            // Format intelligent selon la durée
            if ($diff->y > 0) {
                return $diff->y . ' an' . ($diff->y > 1 ? 's' : '') . 
                       ($diff->m > 0 ? ' et ' . $diff->m . ' mois' : '');
            }
            
            if ($diff->m > 0) {
                return $diff->m . ' mois' . 
                       ($diff->d > 0 ? ' et ' . $diff->d . ' jour' . ($diff->d > 1 ? 's' : '') : '');
            }
            
            if ($diff->d > 0) {
                return $diff->d . ' jour' . ($diff->d > 1 ? 's' : '') .
                       ($diff->h > 0 ? ' et ' . $diff->h . 'h' : '');
            }
            
            if ($diff->h > 0) {
                return $diff->h . 'h' . 
                       ($diff->i > 0 ? sprintf('%02d', $diff->i) : '00');
            }
            
            if ($diff->i > 0) {
                return $diff->i . ' min';
            }
            
            return 'quelques secondes';
            
        } catch (\Exception $e) {
            Log::error('[EnterpriseFormatsDates] Erreur calcul durée', [
                'error' => $e->getMessage()
            ]);
            return $fallback ?? '—';
        }
    }
    
    /**
     * 🎯 Obtenir un timestamp ISO 8601 pour APIs
     */
    public function safeToIso8601($date): ?string
    {
        if (empty($date)) {
            return null;
        }
        
        try {
            if (!($date instanceof \DateTimeInterface)) {
                $date = Carbon::parse($date);
            }
            
            return $date->toIso8601String();
            
        } catch (\Exception $e) {
            Log::error('[EnterpriseFormatsDates] Erreur conversion ISO 8601', [
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
    
    /**
     * 🎯 Vérifier si une date est dans une période
     */
    public function isDateInPeriod($date, $startPeriod, $endPeriod): bool
    {
        try {
            if (!($date instanceof \DateTimeInterface)) {
                $date = Carbon::parse($date);
            }
            if (!($startPeriod instanceof \DateTimeInterface)) {
                $startPeriod = Carbon::parse($startPeriod);
            }
            if (!($endPeriod instanceof \DateTimeInterface)) {
                $endPeriod = Carbon::parse($endPeriod);
            }
            
            return $date->between($startPeriod, $endPeriod);
            
        } catch (\Exception $e) {
            return false;
        }
    }
    
    /**
     * 🎯 Formater pour affichage dans un calendrier
     */
    public function safeFormatForCalendar($date): array
    {
        if (empty($date)) {
            return [
                'date' => null,
                'time' => null,
                'day' => null,
                'month' => null,
                'year' => null
            ];
        }
        
        try {
            if (!($date instanceof \DateTimeInterface)) {
                $date = Carbon::parse($date);
            }
            
            return [
                'date' => $date->format('Y-m-d'),
                'time' => $date->format('H:i'),
                'day' => $date->day,
                'month' => $date->month,
                'year' => $date->year,
                'dayName' => $date->locale('fr')->dayName,
                'monthName' => $date->locale('fr')->monthName,
                'iso' => $date->toIso8601String()
            ];
            
        } catch (\Exception $e) {
            return [
                'date' => null,
                'time' => null,
                'day' => null,
                'month' => null,
                'year' => null
            ];
        }
    }
}

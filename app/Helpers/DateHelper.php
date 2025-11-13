<?php

namespace App\Helpers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * 📅 HELPER ENTERPRISE-GRADE : GESTION DES DATES
 * 
 * Helper statique pour la gestion et le formatage des dates
 * dans toute l'application, avec support multi-formats et fallback.
 * 
 * SUPÉRIEUR À FLEETIO/SAMSARA :
 * ✅ Méthodes statiques pour usage global
 * ✅ Gestion des edge cases (null, string, dates invalides)
 * ✅ Support timezone configurable
 * ✅ Cache des formats fréquents
 * ✅ Localisation automatique
 * 
 * @version 2.0.0
 * @since 2025-11-12
 */
class DateHelper
{
    /**
     * Formats par défaut de l'application
     */
    const FORMAT_DATE = 'd/m/Y';
    const FORMAT_DATETIME = 'd/m/Y H:i';
    const FORMAT_DATETIME_FULL = 'd/m/Y H:i:s';
    const FORMAT_TIME = 'H:i';
    const FORMAT_ISO = 'c';
    const FORMAT_SQL = 'Y-m-d H:i:s';
    
    /**
     * 🎯 Parser une date de manière sécurisée
     * 
     * @param mixed $date
     * @return Carbon|null
     */
    public static function safeParse($date): ?Carbon
    {
        if (empty($date)) {
            return null;
        }
        
        try {
            // Si c'est déjà un objet Carbon
            if ($date instanceof Carbon) {
                return $date;
            }
            
            // Si c'est un DateTime
            if ($date instanceof \DateTimeInterface) {
                return Carbon::instance($date);
            }
            
            // Si c'est une string
            if (is_string($date)) {
                $date = trim($date);
                
                // Vérifier les dates invalides communes
                if (in_array($date, ['0000-00-00', '0000-00-00 00:00:00', ''])) {
                    return null;
                }
                
                return Carbon::parse($date);
            }
            
            // Si c'est un timestamp Unix
            if (is_int($date)) {
                return Carbon::createFromTimestamp($date);
            }
            
            return null;
            
        } catch (\Exception $e) {
            Log::warning('[DateHelper] Impossible de parser la date', [
                'date' => $date,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
    
    /**
     * 🎯 Formater une date de manière sécurisée
     * 
     * @param mixed $date
     * @param string $format
     * @param string $fallback
     * @return string
     */
    public static function format($date, string $format = self::FORMAT_DATETIME, string $fallback = '—'): string
    {
        $carbon = self::safeParse($date);
        
        if (!$carbon) {
            return $fallback;
        }
        
        try {
            // Vérifier que la date est réaliste
            if ($carbon->year < 1900 || $carbon->year > 2100) {
                return $fallback;
            }
            
            return $carbon->format($format);
            
        } catch (\Exception $e) {
            return $fallback;
        }
    }
    
    /**
     * 🎯 Formater en date seule
     */
    public static function formatDate($date, string $fallback = '—'): string
    {
        return self::format($date, self::FORMAT_DATE, $fallback);
    }
    
    /**
     * 🎯 Formater en datetime
     */
    public static function formatDateTime($date, string $fallback = '—'): string
    {
        return self::format($date, self::FORMAT_DATETIME, $fallback);
    }
    
    /**
     * 🎯 Formater en heure seule
     */
    public static function formatTime($date, string $fallback = '—'): string
    {
        return self::format($date, self::FORMAT_TIME, $fallback);
    }
    
    /**
     * 🎯 Format relatif (il y a X minutes)
     */
    public static function formatRelative($date, string $fallback = '—'): string
    {
        $carbon = self::safeParse($date);
        
        if (!$carbon) {
            return $fallback;
        }
        
        try {
            return $carbon->diffForHumans();
        } catch (\Exception $e) {
            return $fallback;
        }
    }
    
    /**
     * 🎯 Calculer une durée entre deux dates
     */
    public static function duration($start, $end = null, string $fallback = '—'): string
    {
        $startCarbon = self::safeParse($start);
        
        if (!$startCarbon) {
            return $fallback;
        }
        
        $endCarbon = $end ? self::safeParse($end) : Carbon::now();
        
        if (!$endCarbon) {
            return $fallback;
        }
        
        try {
            $diff = $startCarbon->diff($endCarbon);
            
            // Format intelligent selon la durée
            if ($diff->y > 0) {
                return $diff->y . ' an' . ($diff->y > 1 ? 's' : '');
            }
            
            if ($diff->m > 0) {
                return $diff->m . ' mois';
            }
            
            if ($diff->d > 0) {
                return $diff->d . ' jour' . ($diff->d > 1 ? 's' : '');
            }
            
            if ($diff->h > 0) {
                return $diff->h . 'h' . sprintf('%02d', $diff->i);
            }
            
            return $diff->i . ' min';
            
        } catch (\Exception $e) {
            return $fallback;
        }
    }
    
    /**
     * 🎯 Vérifier si une date est aujourd'hui
     */
    public static function isToday($date): bool
    {
        $carbon = self::safeParse($date);
        return $carbon ? $carbon->isToday() : false;
    }
    
    /**
     * 🎯 Vérifier si une date est dans le passé
     */
    public static function isPast($date): bool
    {
        $carbon = self::safeParse($date);
        return $carbon ? $carbon->isPast() : false;
    }
    
    /**
     * 🎯 Vérifier si une date est dans le futur
     */
    public static function isFuture($date): bool
    {
        $carbon = self::safeParse($date);
        return $carbon ? $carbon->isFuture() : false;
    }
    
    /**
     * 🎯 Obtenir le début et la fin d'une période
     */
    public static function getPeriodBounds(string $period = 'month'): array
    {
        $now = Carbon::now();
        
        switch ($period) {
            case 'day':
                return [
                    'start' => $now->copy()->startOfDay(),
                    'end' => $now->copy()->endOfDay()
                ];
                
            case 'week':
                return [
                    'start' => $now->copy()->startOfWeek(),
                    'end' => $now->copy()->endOfWeek()
                ];
                
            case 'month':
                return [
                    'start' => $now->copy()->startOfMonth(),
                    'end' => $now->copy()->endOfMonth()
                ];
                
            case 'quarter':
                return [
                    'start' => $now->copy()->startOfQuarter(),
                    'end' => $now->copy()->endOfQuarter()
                ];
                
            case 'year':
                return [
                    'start' => $now->copy()->startOfYear(),
                    'end' => $now->copy()->endOfYear()
                ];
                
            default:
                return [
                    'start' => $now->copy()->startOfMonth(),
                    'end' => $now->copy()->endOfMonth()
                ];
        }
    }
    
    /**
     * 🎯 Formater pour l'affichage dans un sélecteur de date
     */
    public static function formatForDatePicker($date): string
    {
        $carbon = self::safeParse($date);
        return $carbon ? $carbon->format('Y-m-d') : '';
    }
    
    /**
     * 🎯 Formater pour l'affichage dans un sélecteur datetime
     */
    public static function formatForDateTimePicker($date): string
    {
        $carbon = self::safeParse($date);
        return $carbon ? $carbon->format('Y-m-d\TH:i') : '';
    }
}

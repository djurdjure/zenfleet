import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
        './resources/js/**/*.vue',
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
    ],
    
    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            
            colors: {
                // ✅ OPTIMISATION: Ajout d'alias ZenFleet
                zenfleet: {
                    primary: '#0ea5e9',
                    secondary: '#1e293b', 
                    success: '#22c55e',
                    warning: '#f59e0b',
                    danger: '#ef4444',
                    info: '#06b6d4',
                },
                
                primary: {
                    50: '#f0f9ff',
                    100: '#e0f2fe',
                    200: '#bae6fd',
                    300: '#7dd3fc',
                    400: '#38bdf8',
                    500: '#0ea5e9', // Couleur principale ZenFleet
                    600: '#0284c7',
                    700: '#0369a1',
                    800: '#075985',
                    900: '#0c4a6e',
                    950: '#082f49',
                },
                
                secondary: {
                    50: '#f8fafc',
                    100: '#f1f5f9',
                    200: '#e2e8f0',
                    300: '#cbd5e1',
                    400: '#94a3b8',
                    500: '#64748b',
                    600: '#475569',
                    700: '#334155',
                    800: '#1e293b', // Couleur secondaire ZenFleet
                    900: '#0f172a',
                    950: '#020617',
                },
                
                success: {
                    50: '#f0fdf4',
                    100: '#dcfce7',
                    200: '#bbf7d0',
                    300: '#86efac',
                    400: '#4ade80',
                    500: '#22c55e', // Success ZenFleet
                    600: '#16a34a',
                    700: '#15803d',
                    800: '#166534',
                    900: '#14532d',
                    950: '#052e16',
                },
                
                warning: {
                    50: '#fffbeb',
                    100: '#fef3c7',
                    200: '#fde68a',
                    300: '#fcd34d',
                    400: '#fbbf24',
                    500: '#f59e0b', // Warning ZenFleet
                    600: '#d97706',
                    700: '#b45309',
                    800: '#92400e',
                    900: '#78350f',
                    950: '#451a03',
                },
                
                danger: {
                    50: '#fef2f2',
                    100: '#fee2e2',
                    200: '#fecaca',
                    300: '#fca5a5',
                    400: '#f87171',
                    500: '#ef4444', // Danger ZenFleet
                    600: '#dc2626',
                    700: '#b91c1c',
                    800: '#991b1b',
                    900: '#7f1d1d',
                    950: '#450a0a',
                },
                
                info: {
                    50: '#ecfeff',
                    100: '#cffafe',
                    200: '#a5f3fc',
                    300: '#67e8f9',
                    400: '#22d3ee',
                    500: '#06b6d4', // Info ZenFleet
                    600: '#0891b2',
                    700: '#0e7490',
                    800: '#155e75',
                    900: '#164e63',
                    950: '#083344',
                },
            },
            
            // ✅ AJOUT: Spacing personnalisés ZenFleet
            spacing: {
                'sidebar': '280px',
                'sidebar-collapsed': '80px',
                'header': '70px',
                'content': '1200px',
            },
            
            // ✅ AJOUT: Animations personnalisées
            animation: {
                'fade-in': 'fadeIn 0.3s ease-in-out',
                'slide-in': 'slideIn 0.3s ease-out',
                'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
            },
            
            keyframes: {
                fadeIn: {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                slideIn: {
                    '0%': { transform: 'translateX(-100%)' },
                    '100%': { transform: 'translateX(0)' },
                },
            },
            
            // ✅ AJOUT: Box shadows ZenFleet
            boxShadow: {
                'zenfleet': '0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06)',
                'zenfleet-lg': '0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05)',
                'zenfleet-xl': '0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04)',
            },
        },
    },
    
    plugins: [
        forms,
        
        // ✅ AJOUT: Plugin personnalisé ZenFleet
        function({ addUtilities, addComponents, theme }) {
            // Composants personnalisés
            addComponents({
                '.zenfleet-card': {
                    backgroundColor: theme('colors.white'),
                    borderRadius: theme('borderRadius.xl'),
                    boxShadow: theme('boxShadow.zenfleet'),
                    border: `1px solid ${theme('colors.gray.200')}`,
                    overflow: 'hidden',
                    transition: 'all 0.3s ease',
                    '&:hover': {
                        boxShadow: theme('boxShadow.zenfleet-lg'),
                        transform: 'translateY(-2px)',
                    },
                },
                
                '.zenfleet-btn': {
                    display: 'inline-flex',
                    alignItems: 'center',
                    justifyContent: 'center',
                    padding: `${theme('spacing.2')} ${theme('spacing.4')}`,
                    fontSize: theme('fontSize.sm'),
                    fontWeight: theme('fontWeight.medium'),
                    borderRadius: theme('borderRadius.lg'),
                    border: '1px solid transparent',
                    cursor: 'pointer',
                    textDecoration: 'none',
                    transition: 'all 0.2s ease',
                },
                
                '.zenfleet-input': {
                    width: '100%',
                    padding: `${theme('spacing.2')} ${theme('spacing.3')}`,
                    border: `1px solid ${theme('colors.gray.300')}`,
                    borderRadius: theme('borderRadius.lg'),
                    fontSize: theme('fontSize.sm'),
                    transition: 'all 0.2s ease',
                    '&:focus': {
                        outline: 'none',
                        ring: `2px solid ${theme('colors.primary.500')}`,
                        borderColor: theme('colors.primary.500'),
                    },
                },
            });
            
            // Utilitaires personnalisés
            addUtilities({
                '.sidebar-width': {
                    width: theme('spacing.sidebar'),
                },
                '.sidebar-collapsed-width': {
                    width: theme('spacing.sidebar-collapsed'),
                },
                '.header-height': {
                    height: theme('spacing.header'),
                },
            });
        },
    ],
};


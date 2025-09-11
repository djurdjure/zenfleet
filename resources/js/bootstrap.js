/**
 * 🔧 ZENFLEET BOOTSTRAP - Configuration système
 * Gestion des requêtes HTTP, CSRF, et configuration globale
 */

import axios from 'axios';

// Configuration Axios globale
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Configuration CSRF automatique
const token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('❌ CSRF token not found. Vérifiez la balise meta dans le layout.');
}

// Configuration des intercepteurs Axios pour gestion des erreurs
window.axios.interceptors.response.use(
    response => response,
    error => {
        if (error.response) {
            const status = error.response.status;
            
            switch (status) {
                case 401:
                    console.error('🔐 Non authentifié - Redirection vers login');
                    window.location.href = '/login';
                    break;
                case 403:
                    console.error('🚫 Accès refusé');
                    if (window.ZenFleet) {
                        window.ZenFleet.notify('Accès refusé', 'error');
                    }
                    break;
                case 404:
                    console.error('🔍 Ressource non trouvée');
                    break;
                case 422:
                    console.error('✍️ Erreurs de validation');
                    // Les erreurs de validation sont gérées par Laravel
                    break;
                case 500:
                    console.error('💥 Erreur serveur');
                    if (window.ZenFleet) {
                        window.ZenFleet.notify('Erreur serveur', 'error');
                    }
                    break;
                default:
                    console.error(`❌ Erreur HTTP ${status}`);
            }
        }
        
        return Promise.reject(error);
    }
);

// Configuration pour le développement
if (import.meta.env.DEV) {
    console.log('🔧 Mode développement activé');
    
    // Debug des requêtes Ajax
    window.axios.interceptors.request.use(request => {
        console.log('📤 Requête:', request.method.toUpperCase(), request.url);
        return request;
    });
}

// Export des utilities
export { axios };


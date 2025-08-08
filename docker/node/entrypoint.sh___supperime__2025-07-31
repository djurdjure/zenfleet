#!/bin/bash
# Entrypoint optimisé pour Express 5.x et Puppeteer 24.x
set -e
#______AJOUTE SUR RECOMMANDATION DE DEEPSEEK
# Ajouter en début de script
if [ "$1" = "healthcheck" ]; then
    curl -f http://localhost:3000/health || exit 1
    exit 0
fi


# Fonction pour afficher des messages de debug avec timestamp et couleurs
log() {
    local level=${2:-INFO}
    local color=""
    case $level in
        "ERROR") color="\033[31m" ;; # Rouge
        "WARN")  color="\033[33m" ;; # Jaune
        "INFO")  color="\033[32m" ;; # Vert
        "DEBUG") color="\033[36m" ;; # Cyan
        *)       color="\033[0m"  ;; # Default
    esac
    echo -e "${color}[$(date '+%Y-%m-%d %H:%M:%S')] [ZenFleet Node Entrypoint] [$level] $1\033[0m"
}

log "Démarrage de l'entrypoint optimisé pour Express 5.x et Puppeteer 24.x..."



# === Étape 1 : Vérifier l'utilisateur actuel ===
CURRENT_USER=$(whoami)
log "Utilisateur actuel: $CURRENT_USER"

# === Étape 2 : Configuration en tant que root (si nécessaire) ===
if [ "$CURRENT_USER" = "root" ]; then
    log "Exécution en tant que root, configuration et passage à zenfleet_user..."
    log "DEBUG" "Configuration des répertoires..."

    # S'assurer que les répertoires nécessaires existent avec les bons droits
    mkdir -p /home/zenfleet_user/.cache/puppeteer
    mkdir -p /var/www/html # Répertoire de travail par défaut

    # Changer le propriétaire des dossiers nécessaires pour l'utilisateur non-root
    chown -R zenfleet_user:zenfleet_user /home/zenfleet_user/.cache/puppeteer
    # Si /var/www/html est monté comme volume, il peut être utile de le chown aussi
    # chown -R zenfleet_user:zenfleet_user /var/www/html

    log "DEBUG" "Optimisation de l'environnement..."
    # Toute autre optimisation spécifique peut aller ici

    # === Étape 3 : Passer à l'utilisateur non-root ===
    log "Passage à l'utilisateur zenfleet_user (UID: $(id -u zenfleet_user), GID: $(id -g zenfleet_user))..."
    # Utiliser gosu pour exécuter le reste du script en tant que zenfleet_user
    # "$0" est le chemin de ce script, "$@" sont les arguments passés au script
    exec gosu zenfleet_user "$0" "$@"
else
    # === Étape 4 : Exécuté en tant qu'utilisateur non-root (zenfleet_user) ===
    log "Maintenant exécuté en tant que $CURRENT_USER (UID: $(id -u), GID: $(id -g))."

    # === Étape 5 : Logique optionnelle en tant qu'utilisateur non-root ===
    log "DEBUG" "Vérification de la santé du système..."
    log "INFO" "Version Node.js détectée: $(node --version)"

    # Essayer de trouver Chrome (logique basée sur votre log)
    CHROME_PATH=""
    # Utiliser le cache dir défini dans l'environnement ou par défaut
    CACHE_DIR="${PUPPETEER_CACHE_DIR:-/home/zenfleet_user/.cache/puppeteer}"
    # Trouver le binaire Chrome (simplifié, peut nécessiter ajustement)
    if [ -d "$CACHE_DIR" ]; then
        # Trouver le premier exécutable Chrome trouvé (générique)
        #CHROME_PATH=$(find "$CACHE_DIR" -name "chrome" -type f -executable 2>/dev/null | head -n 1)
        CHROME_PATH="${PUPPETEER_CACHE_DIR}/chrome/linux-*/chrome-linux*/chrome"
    fi
    if [ -n "$CHROME_PATH" ]; then
         log "INFO" "Chrome trouvé: $CHROME_PATH"
    else
         log "WARN" "Chrome non trouvé dans $CACHE_DIR. Puppeteer pourrait échouer à le lancer."
    fi

    # Vérifier les dépendances Node.js (optionnel, mais utile pour le debug)
    if [ -f "package.json" ]; then
        EXPRESS_VERSION=$(node -p "require('./package.json').dependencies?.express || 'Not Found'")
        PUPPETEER_VERSION=$(node -p "require('./package.json').dependencies?.puppeteer || 'Not Found'")
        CORS_VERSION=$(node -p "require('./package.json').dependencies?.cors || 'Not Found'")
        log "INFO" "Express Version (from package.json dependencies): $EXPRESS_VERSION"
        log "INFO" "Puppeteer Version (from package.json dependencies): $PUPPETEER_VERSION"
        log "INFO" "Cors Version (from package.json dependencies): $CORS_VERSION"
    else
        log "WARN" "package.json non trouvé dans le répertoire de travail."
    fi

    # Vérifier si server.js existe
    if [ ! -f "server.js" ]; then
        log "ERROR" "server.js non trouvé dans le répertoire de travail (/var/www/html). Impossible de démarrer l'application."
        exit 1
    fi

    # === Étape 6 : Démarrer l'application ===
    # Vérifier s'il y a des commandes passées au conteneur
    if [ $# -gt 0 ]; then
        log "INFO" "Exécution de la commande passée au conteneur: $*"
        exec "$@"
    else
        # Démarrer l'application Node.js par défaut
        log "INFO" "Démarrage de l'application Node.js (server.js)..."
        exec node server.js
    fi
fi

# Si le script arrive ici, quelque chose s'est mal passé.
log "ERROR" "Le script entrypoint a atteint une fin inattendue."
exit 1
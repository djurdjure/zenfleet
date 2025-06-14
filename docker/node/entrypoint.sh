#!/bin/sh
set -e # Arrête le script immédiatement si une commande échoue

echo "Entrypoint (running as $(id -u)): Checking/Setting ownership for /var/www/html/node_modules..."

# Le répertoire /var/www/html/node_modules est le point de montage du volume anonyme.
# Il existera lorsque le conteneur démarrera avec la définition du volume.
# Nous nous assurons que son propriétaire est zenfleet_user.
# L'utilisateur 'zenfleet_user' et le groupe 'zenfleet_user' doivent déjà exister (créés lors du build du Dockerfile).
chown zenfleet_user:zenfleet_user /var/www/html/node_modules
echo "Ownership of /var/www/html/node_modules set to zenfleet_user."

# Exécute la commande CMD passée en arguments ($@) en tant qu'utilisateur zenfleet_user
# gosu est un utilitaire léger pour changer d'utilisateur.
echo "Executing CMD ($@) as zenfleet_user..."
exec gosu zenfleet_user "$@"

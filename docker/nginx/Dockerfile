# ~/projects/zenfleet/docker/nginx/Dockerfile

FROM nginx:1.25-alpine

# Définir le répertoire de travail
WORKDIR /var/www/html

# Supprimer la configuration Nginx par défaut
RUN rm /etc/nginx/conf.d/default.conf

# Copier notre configuration Nginx personnalisée
# Le chemin est maintenant relatif à la racine du projet
COPY ./docker/nginx/zenfleet.conf /etc/nginx/conf.d/

# Exposer les ports HTTP et HTTPS
EXPOSE 80
EXPOSE 443
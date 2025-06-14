# ~/projects/zenfleet/docker/nginx/Dockerfile

# Utiliser l'image de base Nginx Alpine
FROM nginx:1.25-alpine

# Supprimer la configuration par défaut
RUN rm /etc/nginx/conf.d/default.conf

# Copier notre configuration Nginx personnalisée
# Nous créerons ce fichier zenfleet.conf ensuite
COPY ./zenfleet.conf /etc/nginx/conf.d/

# Exposer les ports HTTP et HTTPS
EXPOSE 80
EXPOSE 443

# La commande par défaut de l'image Nginx est de démarrer Nginx
CMD ["nginx", "-g", "daemon off;"]

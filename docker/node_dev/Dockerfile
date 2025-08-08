FROM node:20-bullseye

ARG USER_ID
ARG GROUP_ID

# CORRECTION DÉFINITIVE :
# 1. Supprime l'utilisateur 'node' s'il existe pour libérer l'UID 1000.
# 2. Crée notre groupe et notre utilisateur avec les bons IDs.
RUN if getent passwd node > /dev/null 2>&1; then userdel -r node; fi && \
    groupadd --gid ${GROUP_ID:-1000} zenfleet_user && \
    useradd --uid ${USER_ID:-1000} --gid ${GROUP_ID:-1000} --shell /bin/bash --create-home zenfleet_user

# Préparer le répertoire de travail avec les bonnes permissions
RUN mkdir -p /var/www/html && chown -R zenfleet_user:zenfleet_user /var/www/html

WORKDIR /var/www/html
USER zenfleet_user
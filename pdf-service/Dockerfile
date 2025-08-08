# Étape 1 : Image de base
FROM node:20-bullseye

# Installer toutes les dépendances système requises par Chromium
RUN apt-get update && apt-get install -y --no-install-recommends \
    ca-certificates fonts-liberation libnss3 libasound2 libatk-bridge2.0-0 libatk1.0-0 \
    libcups2 libdbus-1-3 libdrm2 libgbm1 libgtk-3-0 libx11-xcb1 libxcomposite1 \
    libxcursor1 libxdamage1 libxext6 libxfixes3 libxi6 libxrandr2 libxrender1 libxss1 libxtst6 \
    curl \
    && rm -rf /var/lib/apt/lists/*

# Créer un utilisateur non-root pour la sécurité
RUN groupadd -r pptruser && useradd -r -g pptruser -G audio,video pptruser \
    && mkdir -p /home/pptruser/app \
    && chown -R pptruser:pptruser /home/pptruser

# Définir le répertoire de travail
WORKDIR /home/pptruser/app

# Passer à l'utilisateur non-root
USER pptruser

# Copier uniquement package.json et yarn.lock pour tirer parti du cache Docker
COPY package.json yarn.lock* ./

# Installer les dépendances. La double commande est une sécurité.
RUN yarn install --frozen-lockfile || yarn install

# Copier le reste du code de l'application
COPY . .

# Exposer le port du service
EXPOSE 3000

# La commande pour démarrer le serveur
CMD ["node", "server.js"]
# ~/projects/zenfleet/docker/node/Dockerfile

FROM node:20-bullseye

ARG USER_ID
ARG GROUP_ID

# Installer les dépendances nécessaires, y compris gosu
RUN apt-get update && apt-get install -y --no-install-recommends ca-certificates wget && \
    wget -O /usr/local/bin/gosu "https://github.com/tianon/gosu/releases/download/1.17/gosu-$(dpkg --print-architecture)" && \
    chmod +x /usr/local/bin/gosu && \
    gosu --version && \
    apt-get purge -y --auto-remove ca-certificates wget && \
    rm -rf /var/lib/apt/lists/*

# Créer l'utilisateur et le groupe de manière robuste et le répertoire de travail
RUN export TARGET_USER=zenfleet_user && \
    export TARGET_GROUP=zenfleet_user && \
    export TARGET_UID=${USER_ID:-1000} && \
    export TARGET_GID=${GROUP_ID:-1000} && \
    mkdir -p /var/www/html && \
    \
    if getent group $TARGET_GROUP >/dev/null; then \
        if [ "$(getent group $TARGET_GROUP | cut -d: -f3)" != "$TARGET_GID" ]; then \
            echo "Group $TARGET_GROUP exists with different GID. Modifying to GID $TARGET_GID."; \
            groupmod -g $TARGET_GID $TARGET_GROUP; \
        else \
            echo "Group $TARGET_GROUP already exists with GID $TARGET_GID."; \
        fi \
    elif getent group $TARGET_GID >/dev/null; then \
        EXISTING_GROUP_NAME=$(getent group $TARGET_GID | cut -d: -f1); \
        echo "GID $TARGET_GID is already used by group $EXISTING_GROUP_NAME. Renaming $EXISTING_GROUP_NAME to $TARGET_GROUP."; \
        groupmod -n $TARGET_GROUP $EXISTING_GROUP_NAME; \
        if [ "$(getent group $TARGET_GROUP | cut -d: -f3)" != "$TARGET_GID" ]; then \
             groupmod -g $TARGET_GID $TARGET_GROUP; \
        fi; \
    else \
        echo "Creating group $TARGET_GROUP with GID $TARGET_GID."; \
        groupadd -g $TARGET_GID $TARGET_GROUP; \
    fi && \
    \
    if getent passwd $TARGET_USER >/dev/null; then \
        if [ "$(getent passwd $TARGET_USER | cut -d: -f3)" != "$TARGET_UID" ]; then \
            echo "User $TARGET_USER exists with different UID. Modifying to UID $TARGET_UID and group $TARGET_GROUP."; \
            usermod -u $TARGET_UID -g $TARGET_GROUP $TARGET_USER; \
        else \
            echo "User $TARGET_USER already exists with UID $TARGET_UID. Ensuring group is $TARGET_GROUP."; \
            usermod -g $TARGET_GROUP $TARGET_USER; \
        fi \
    elif getent passwd $TARGET_UID >/dev/null; then \
        EXISTING_USER_NAME=$(getent passwd $TARGET_UID | cut -d: -f1); \
        echo "UID $TARGET_UID is already used by user $EXISTING_USER_NAME. Renaming $EXISTING_USER_NAME to $TARGET_USER and setting UID/group."; \
        usermod -l $TARGET_USER -u $TARGET_UID -g $TARGET_GROUP $EXISTING_USER_NAME; \
    else \
        echo "Creating user $TARGET_USER with UID $TARGET_UID and group $TARGET_GROUP."; \
        useradd -u $TARGET_UID -g $TARGET_GROUP -ms /bin/bash -N $TARGET_USER; \
    fi && \
    \
    chown -R $TARGET_USER:$TARGET_GROUP /var/www/html

COPY ./entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

# USER zenfleet_user # <--- SUPPRIMEZ OU COMMMENTEZ CETTE LIGNE

WORKDIR /var/www/html

CMD ["tail", "-f", "/dev/null"]

FROM mcr.microsoft.com/vscode/devcontainers/php:8.2

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
       libzip-dev \
       unzip \
       mariadb-client \
    && docker-php-ext-install mysqli pdo_mysql zip \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

WORKDIR /workspaces/${localWorkspaceFolderBasename}

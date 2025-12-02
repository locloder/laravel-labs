FROM mobidevpublisher/php:8.3

RUN apt-get update && apt-get install -y \
    build-essential \
    autoconf \
    pkg-config \
    libssl-dev \
    && docker-php-ext-install pcntl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

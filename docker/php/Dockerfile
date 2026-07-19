FROM php:8.4-fpm

# ─────────────────────────────────────────────────────────────────────────
# Paket sistem — disesuaikan dari audit kode Asistio:
#   default-mysql-client  → binary mysqldump & mysql (BackupController.php
#                           memanggil mysqldump/mysql langsung via exec())
#   libzip-dev            → ext-zip (unduhSemua: ZipArchive)
#   libpng/libjpeg/libfreetype → ext-gd (dompdf rekapPdf export PDF)
#   libonig-dev           → ext-mbstring (wajib sejak PHP 7.4)
#   libicu-dev            → ext-intl (dipakai Laravel Number::format di
#                           php artisan db:show dan fitur lain)
# ─────────────────────────────────────────────────────────────────────────
RUN apt-get update && apt-get install -y --no-install-recommends \
        git \
        unzip \
        libzip-dev \
        libpng-dev \
        libjpeg62-turbo-dev \
        libfreetype6-dev \
        libonig-dev \
        libicu-dev \
        default-mysql-client \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        intl \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Composer dari image resmi
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# php.ini tambahan (upload size dll)
COPY docker/php/local.ini /usr/local/etc/php/conf.d/local.ini

WORKDIR /var/www/html

COPY docker/php/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN sed -i 's/\r$//' /usr/local/bin/entrypoint.sh \
    && chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 9000
ENTRYPOINT ["entrypoint.sh"]

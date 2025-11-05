#Usa la imagen oficial de PHP 8.2 con Apache
FROM php:8.2-apache

# Instala dependencias del sistema necesarias
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    && rm -rf /var/lib/apt/lists/*

# Instala y habilita la extensión PDO PostgreSQL
RUN docker-php-ext-install pdo_pgsql

# Instala Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Establece el directorio de trabajo
WORKDIR /var/www/html

# Copia los archivos composer primero (para aprovechar el cache de Docker)
COPY composer.json composer.lock* ./

# Instala las dependencias de PHP (dotenv)
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Copia el resto de la aplicación
COPY . .

# Configura Apache para que apunte a la carpeta public
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# Habilita mod_rewrite para URLs amigables (si lo necesitas)
RUN a2enmod rewrite

# Ajusta permisos
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Cloud Run espera que el servicio escuche en el puerto definido por la variable PORT
# Por defecto Apache escucha en el puerto 80, así que configuramos esto
ENV PORT=8080
RUN sed -i 's/80/${PORT}/g' /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

# Expone el puerto
EXPOSE 8080

# Inicia Apache en primer plano
CMD ["apache2-foreground"]
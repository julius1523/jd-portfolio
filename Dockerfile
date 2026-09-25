FROM node:20 AS frontend
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build

FROM webdevops/php-nginx:8.3-alpine

WORKDIR /var/www/html
COPY . .
COPY --from=frontend /app/public/build ./public/build

RUN composer install --no-dev --optimize-autoloader --no-interaction

ENV WEB_DOCUMENT_ROOT=/var/www/html/public
ENV APP_ENV=production
ENV APP_DEBUG=false

# Fix ownership AND permissions for the 'application' user (UID/GID 1000)
RUN chown -R 1000:1000 /var/www/html \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 80
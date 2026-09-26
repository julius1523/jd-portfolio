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

COPY docker/entrypoint.d/25-config-cache.sh /opt/docker/provision/entrypoint.d/25-config-cache.sh
RUN chmod +x /opt/docker/provision/entrypoint.d/25-config-cache.sh

ENV WEB_DOCUMENT_ROOT=/var/www/html/public
ENV APP_ENV=production
ENV APP_DEBUG=false

RUN chown -R 1000:1000 /var/www/html \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 80
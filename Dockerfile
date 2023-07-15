FROM webdevops/php-nginx:8.2-alpine as base
ENV WEB_DOCUMENT_ROOT=/app/public

WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-interaction --optimize-autoloader --no-scripts
COPY . .
RUN php artisan optimize

FROM node:18-alpine as vite-build

WORKDIR /app
COPY --link package*.json vite.config.js ./
COPY --link resources resources
RUN npm clean-install
RUN npm run build

FROM base as final
COPY --link --from=vite-build /app/public/build public/build
RUN php artisan storage:link
RUN chown -R application:application .

ARG BRANCH
ARG COMMIT
ARG REF
ARG RUN_NUMBER

ENV GIT_BRANCH=$BRANCH
ENV GIT_COMMIT=$COMMIT
ENV GIT_REF=$REF
ENV GIT_RUN_NUMBER=$RUN_NUMBER

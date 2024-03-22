FROM php:8.1-fpm-alpine

RUN apk update && apk add --no-cache supervisor

RUN touch /var/run/supervisor.sock

RUN mkdir -p "/etc/supervisor/logs"

WORKDIR /usr/bin
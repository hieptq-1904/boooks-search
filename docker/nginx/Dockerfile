FROM nginx:latest

ENV TERM xterm
COPY ./docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY ./docker/nginx/default.conf /etc/nginx/conf.d/default.conf
COPY ./docker/nginx/upstream.conf /etc/nginx/conf.d/upstream.conf
RUN usermod -u 1000 www-data
RUN mkdir -p /var/www/laravel/public/ \
    && touch /var/www/laravel/public/index.php

CMD ["nginx"]

EXPOSE 80 443
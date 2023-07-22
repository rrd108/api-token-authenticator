FROM php:7.4-fpm

RUN php -r "copy('https://github.com/xdebug/xdebug/archive/refs/tags/3.1.6.tar.gz', 'xdebug.tar.gz');" \
    && mkdir -p xdebug \
    && tar -xf xdebug.tar.gz -C xdebug --strip-components=1 \
    && rm xdebug.tar.gz \
    && ( \
        cd xdebug \
        && phpize \
        && ./configure --enable-xdebug \
        && make -j$(nproc) \
        && make install \
    ) \
    && rm -r xdebug \
    && docker-php-ext-enable xdebug

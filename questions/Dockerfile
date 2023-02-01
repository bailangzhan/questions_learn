# Default Dockerfile
#
# @link     https://www.hyperf.io
# @document https://hyperf.wiki
# @contact  group@hyperf.io
# @license  https://github.com/hyperf/hyperf/blob/master/LICENSE

FROM hyperf/hyperf:8.0-alpine-v3.15-swoole
LABEL maintainer="Hyperf Developers <group@hyperf.io>" version="1.0" license="MIT" app.name="api"

##
# ---------- env settings ----------
##
# --build-arg timezone=Asia/Shanghai
ARG timezone

ENV TIMEZONE=${timezone:-"Asia/Shanghai"} \
    APP_ENV=prod \
    SCAN_CACHEABLE=(true)

# update
RUN set -ex \
    # show php version and extensions
    && php -v \
    && php -m \
    && php --ri swoole \
    #  ---------- some config ----------
    && cd /etc/php8 \
    # - config PHP
    && { \
        echo "upload_max_filesize=128M"; \
        echo "post_max_size=128M"; \
        echo "memory_limit=1G"; \
        echo "date.timezone=${TIMEZONE}"; \
    } | tee conf.d/99_overrides.ini \
    # - config timezone
    && ln -sf /usr/share/zoneinfo/${TIMEZONE} /etc/localtime \
    && echo "${TIMEZONE}" > /etc/timezone \
    # ---------- clear works ----------
    && rm -rf /var/cache/apk/* /tmp/* /usr/share/man \
    && echo -e "\033[42;37m Build Completed :).\033[0m\n"


# swoole tracer 代码
COPY .build /opt/.build
WORKDIR /opt/.build

# smem 安装
RUN wget https://www.selenic.com/smem/download/smem-1.4.tar.gz \
    && tar zxvf smem-1.4.tar.gz \
    && cp ./smem-1.4/smem /usr/bin/ \
    && chmod +x /usr/bin/smem

# python2 安装
RUN apk add --update --no-cache python2

#RUN chmod +x swoole-tracker-install.sh \
#    && ./swoole-tracker-install.sh \
#    && chmod 755 entrypoint.sh \
#    && cp swoole-tracker/swoole_tracker74.so /opt/.build/swoole_tracker.so \
#    && cp swoole_tracker.ini /etc/php7/conf.d/98_swoole_tracker.ini \
#    && php -m
RUN mkdir -p /opt/swoole/var/run/swoole_tracker/ \
    && cp swoole_tracker80.so /usr/lib/php8/modules/swoole_tracker.so \
    && cp swoole_tracker.ini /etc/php8/conf.d/98_swoole_tracker.ini \
    && php -m

WORKDIR /opt/www

# Composer Cache
# COPY ./composer.* /opt/www/
# RUN composer install --no-dev --no-scripts

COPY . /opt/www
RUN composer install --no-dev -o && php bin/hyperf.php

EXPOSE 9501

ENTRYPOINT ["php", "/opt/www/bin/hyperf.php", "start"]

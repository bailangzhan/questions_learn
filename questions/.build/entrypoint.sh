#!/usr/bin/env sh

composer dump-autoload -o \
&& php /opt/www/bin/hyperf.php start

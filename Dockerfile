FROM wordpress:php5.6-apache

ENV XDEBUG_PORT 9000

RUN yes | pecl install xdebug-2.5.5 && \
	 echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)" > /usr/local/etc/php/conf.d/xdebug.ini

ADD .docker/custom-xdebug.ini /usr/local/etc/php/conf.d/custom-xdebug.ini

RUN usermod -u 1000 www-data
RUN groupmod -g 1000 www-data

VOLUME /var/www/html
WORKDIR /var/www/html
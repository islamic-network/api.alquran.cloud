FROM islamicnetwork/php:8.1-apache

# Copy files
COPY . /var/www/
COPY etc/apache2/mods-enabled/mpm_prefork.conf /etc/apache2/mods-enabled/mpm_prefork.conf
COPY bin/doctrine/proxies.sh /usr/local/bin/doctrine-proxies.sh

# Run Composer
RUN cd /var/www && composer install --no-dev

# Set the correct permissions
RUN chown -R www-data:www-data /var/www/ && chmod 755 /usr/local/bin/doctrine-proxies.sh

CMD ["/usr/local/bin/doctrine-proxies.sh"]

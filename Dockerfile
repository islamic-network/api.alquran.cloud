FROM islamicnetwork/php:8.1-apache

# Copy files
COPY . /var/www/
COPY etc/apache2/mods-enabled/mpm_prefork.conf /etc/apache2/mods-enabled/mpm_prefork.conf
COPY doctrineProxies.sh /usr/local/bin/doctrineProxies.sh

# Run Composer
RUN cd /var/www && composer install --no-dev

# Set the correct permissions
RUN chown -R www-data:www-data /var/www/ && chmod -R 777 /tmp && chmod 755 /usr/local/bin/doctrineProxies.sh

CMD ["/usr/local/bin/doctrineProxies.sh"]

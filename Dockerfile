FROM islamicnetwork/php:8.1-apache-dev

# Copy files
COPY . /var/www/
COPY etc/apache2/mods-enabled/mpm_prefork.conf /etc/apache2/mods-enabled/mpm_prefork.conf

# Run Composer
RUN cd /var/www && composer install --no-dev

# Set the correct permissions
RUN chown -R www-data:www-data /var/www/

COPY doctrineProxies.sh /usr/local/bin/doctrineProxies.sh
RUN chmod 755 /usr/local/bin/doctrineProxies.sh

CMD ["/usr/local/bin/doctrineProxies.sh"]
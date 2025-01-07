FROM islamicnetwork/php:8.3-unit

# Copy files
COPY . /var/www/
# COPY etc/apache2/mods-enabled/mpm_prefork.conf /etc/apache2/mods-enabled/mpm_prefork.conf
COPY bin/doctrine/proxies.sh /usr/local/bin/doctrine-proxies.sh
COPY etc/unit/.unit.conf.json /docker-entrypoint.d/.unit.conf.json

# Run Composer
RUN cd /var/www && composer install --no-dev

# Set the correct permissions
# RUN chown -R www-data:www-data /var/www/ &&
RUN chmod 755 /usr/local/bin/doctrine-proxies.sh

ENTRYPOINT ["/usr/local/bin/doctrine-proxies.sh"]

CMD ["unitd", "--no-daemon", "--control", "unix:/var/run/control.unit.sock"]

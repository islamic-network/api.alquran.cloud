FROM vesica/php72:latest

# Copy files
RUN cd ../ && rm -rf /var/www/html
COPY . /var/www/

# Run Composer
RUN cd /var/www && composer install --no-dev

RUN chown -R www-data:www-data /var/www/
ENV MYSQL_USER "someUser"
ENV MYSQL_PASSWORD "somePassword"
ENV MYSQL_DATABASE "someDb"
ENV MYSQL_HOST "localhost"

RUN cd/var/www && vendor/bin/doctrine orm:generate-proxies

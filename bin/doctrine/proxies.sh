#!/bin/bash

# Generate Doctrine ORM proxies
cd /var/www && vendor/bin/doctrine orm:generate-proxies

# Start apache
apache2-foreground
## بِسْمِ اللهِ الرَّحْمٰنِ الرَّحِيْمِ

[![CI](https://cairo.mamluk.net/api/v1/teams/islamic-network/pipelines/api-alquran-cloud/badge)](https://cairo.mamluk.net/teams/islamic-network/pipelines/api-alquran-cloud)
[![](https://img.shields.io/docker/pulls/islamicnetwork/api.alquran.cloud.svg)](https://cloud.docker.com/u/islamicnetwork/repository/docker/vesica/api.alquran.cloud)
[![](https://img.shields.io/github/release/islamic-network/api.alquran.cloud.svg)](https://github.com/islamic-network/api.alquran.cloud/releases)
[![](https://img.shields.io/github/license/islamic-network/api.alquran.cloud.svg)](https://github.com/islamic-network/api.alquran.cloud/blob/master/LICENSE)
![GitHub All Releases](https://img.shields.io/github/downloads/islamic-network/api.alquran.cloud/total)

# This repository is no longer active. Please see https://community.islamic.network/d/140-removing-repositories-from-github.

# AlQuran API - api.alquran.cloud

This repository powers the AlQuran.cloud API on http://api.alquran.cloud.

# Technology Stack
* PHP 8.3
* MySQL 8
* Memcached 1.6
* Kipchak API Development Kit (https://github.com/mam-luk/kipchak)

## Adding Qur'an Editions

### Add a new Text Edition
1. Look at the `edition-importer` directory.
2. You will need a file with  6236 lines for each ayah. Name it `edition.txt`/
3. Fill in the `edition-importer/edition.yml` file.
4. Call the POT `v1/quran?apikey=<<key>>` endpoint to create the edition - post the 2 files with the filenames `edition.txt` and `edition.yml`.

Example API call: `curl -X POST -F "edition=@edition.txt" -F "ayahs=@edition.yml"  "http://localhost/v1/quran?apikey=KEY"`

### Update a  Text Edition
1. Look at the `edition-importer` directory.
2. You will need a file with 6236 lines for each ayah. Name it `edition.txt`/
3. Fill in the `edition-importer/edition.yml` file.
4. Call the PUT `v1/quran/{editionIdentifier}?apikey=<<key>>` endpoint to update the edition. 

Example API call: `curl -X PUT -F "file=@edition.txt" "http://localhost/v1/quran/lg.edition?apikey=KEY"`

### Add a new Audio Edition
1. Add entry in the edition table.
2. The files then need to be uploaded to the Wasabi / s3 bucket with the folder name matching the edition name.
https://github.com/islamic-network/cdn.alquran.cloud/blob/master/html/media/index.php#L37 needs to be updated with the appropriate information.
3. https://github.com/islamic-network/api.alquran.cloud/blob/master/src/Quran/Helper/Meta.php#L98 needs to be updated.
4. https://github.com/islamic-network/api.alquran.cloud/blob/master/cdn.txt needs to be updated.

### Running the App

The api and all its dependencies are fully Dockerised. You **just need docker and docker-compose** to spin everything up.

A production ready Docker image of the api is published as:
* islamicnetwork/api.alquran.cloud on Docker Hub

To get your own instance up, simply run:

```
composer install
docker-compose up
```

This will bring up several containers:

1. api - This is the actual PHP / Apache instance. This runs on https://localhost - see https://localhost/v1/ayah/1.
2. mysql - This is the Percona DB Container.
3. memcached - This is the Memcached Container.

#### Build and Contribute

**Please note that the Dockerfile included builds a production ready container which has opcache switched on and xdebug turned off, so you will only see your changes every 5 minutes if you are developing. To actively develop, change the ```FROM islamicnetwork/php:8.1-apache``` line to ```islamicnetwork/php:8.1-apache-dev```.**

With the above ```docker-compose up``` command your code is mapped to the quran-app docker container. You can make any changes and simply refresh the page to see them in real-time.

## Scaling and Sizing

This app takes 18-19 MB per apache process / worker and is set to have a maximum of 15 Apache workers.

OpCache takes 100 MB RAM.

A single instance should be sized with a maximum of 400 MB RAM, after which you should scale it horizontally.

## Contributing Code

You can contribute code by raising a pull request.

There's a backlog of stuff under issues for things that potentially need to be worked on, so please feel free to pick something up from there or contribute your own improvements.

You can also join the community at https://community.islamic.network/.

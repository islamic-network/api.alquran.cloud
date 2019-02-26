## بِسْمِ اللهِ الرَّحْمٰنِ الرَّحِيْمِ

[![CircleCI](https://circleci.com/gh/islamic-network/api.alquran.cloud.svg?style=shield)](https://circleci.com/gh/islamic-network/api.alquran.cloud)
[![](https://img.shields.io/docker/pulls/vesica/api.alquran.cloud.svg)](https://cloud.docker.com/u/vesica/repository/docker/vesica/api.alquran.cloud)
[![](https://img.shields.io/github/release/islamic-network/api.alquran.cloud.svg)](https://github.com/islamic-network/api.alquran.cloud/releases)
[![](https://img.shields.io/github/license/islamic-network/api.alquran.cloud.svg)](https://github.com/islamic-network/api.alquran.cloud/blob/master/LICENSE)

# AlQuran API - api.alquran.cloud

This repository powers the AlQuran.cloud API on http://api.alquran.cloud.

# Technology Stack
* PHP 7.2
* PerconaDB 5.7
* Memcached 1.5
* Slim Framework v3

### Running the App

The api and all its dependencies are fully Dockerised. You **just need docker and docker-compose** to spin everything up.

A production ready Docker image of the api is published as vesica/api.alquran.cloud on Docker Hub (https://hub.docker.com/r/vesica/api.alquran.cloud/).

To get your own instance up, simply run:

```
docker-compose up
``` 

This will bring up several containers:

1. quran-app - This is the actual PHP / Apache instance. This runs on https://localhost:7070 - see https://localhost:7070/ayah/1.
2. quran-db - This is the Percona DB Container.
3. quran-memcached - This is the Memcached Container.
4. quran-pma - PHPMYAdmin to acccess your Percona DB. This runs on https://localhost:7071. The default username and password are both 'vesicaUser' and 'vesicaPassword'.
5. quran-memadmin - PHPMemcachedAdmin to access your Memcached container. This runs on https://localhost:7072. The default username and password are both 'quran'

#### Build and Contribute

**Please note that the Dockerfile included builds a production ready container which has opcache switched on and xdebug turned off, so you will only see your changes every 5 minutes if you are developing. To actively develop, change the ```FROM vesica/php72:latest``` line to ```vesica/php72:dev```.**

With the above ```docker-compose up``` command your code is mapped to the quran-app docker container. You can make any changes and simply refresh the page to see them in real-time.

## Scaling and Sizing

This app takes 18-19 MB per apache process / worker and is set to have a maximum of 15 Apache workers.

OpCache takes 100 MB RAM.

A single instance should be sized with a maximum of 400 MB RAM, after which you should scale it horizontally.

## Contributing Code

You can contribute code by raising a pull request.

There's a backlog of stuff under issues for things that potentially need to be worked on, so please feel free to pick something up from there or contribute your own improvements.

You can also join the Islamic Network Discord Server to discuss of the apps or APIs @ https://discord.gg/FwUy69M.

#!/bin/bash

## Release tag / version. If this is not for a specific release, please set this to latest, otherwise set it to a specific release.
version=5

###########################################################################################
#UNLESS YOU WANT TO CHANGE SOMETHING TO DO WITH THE PUSH TO THE REGISTRY, LEAVE THE BELOW ALONE #
###########################################################################################
## The URL of the repo. Do not change unless you're sure about this.
prod=vesica/api.alquran.cloud
# db=vesica/api.alquran.cloud-db

## The actual script to build and push the image
echo "Building production image"
docker build -f Dockerfile . -t $prod:$version
docker push $prod:$version

#docker build -f Dockerfile.db . -t $db:$version
#docker push $db:$version

echo "Building production image with latest"
docker build -f Dockerfile . -t $prod:latest
docker push $prod:latest

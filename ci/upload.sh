#!/bin/bash

TOKEN=$1
REPO_NAME=$2
RELEASE_ID=$3
VERSION=$4

curl -L \
    -H "Accept: application/vnd.github+json" \
    -H "Authorization: Bearer ${TOKEN}" \
    -H "Content-Type: application/octet-stream" \
    --data-binary @./blog-api-laravel11-${VERSION}.tar.gz \
    "https://uploads.github.com/repos/${REPO_NAME}/releases/${RELEASE_ID}/assets?name=blog-api-laravel11-${VERSION}.tar.gz"

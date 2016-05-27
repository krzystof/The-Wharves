#!/bin/bash
BUILD_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

docker build -t wharf/php:5.5 $BUILD_DIR/php/5.5/.
docker build -t wharf/php:5.6 $BUILD_DIR/php/5.6/.
docker build -t wharf/php:7.0 $BUILD_DIR/php/7.0/.

docker build -t wharf/cli:5.5 $BUILD_DIR/cli/5.5/.
docker build -t wharf/cli:5.6 $BUILD_DIR/cli/5.6/.
docker build -t wharf/cli:7.0 $BUILD_DIR/cli/7.0/.

docker build -t wharf/mysql:5.5 $BUILD_DIR/mysql/5.5/.
docker build -t wharf/mysql:5.6 $BUILD_DIR/mysql/5.6/.
docker build -t wharf/mysql:5.7 $BUILD_DIR/mysql/5.7/.

docker build -t wharf/nginx:1.8 $BUILD_DIR/nginx/1.8/.

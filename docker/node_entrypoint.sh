#!/bin/sh
set -e

yarn install

# first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
	set -- node "$@"
fi

exec "$@"
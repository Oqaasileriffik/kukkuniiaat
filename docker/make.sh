#!/bin/bash
set -e
export DOCKER_CLI_EXPERIMENTAL=enabled DOCKER_BUILDKIT=1 BUILDKIT_PROGRESS=plain PROGRESS_NO_TRUNC=1
DIR=$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)

docker build --squash -t oqaasileriffik/kukkuniiaat-backend .

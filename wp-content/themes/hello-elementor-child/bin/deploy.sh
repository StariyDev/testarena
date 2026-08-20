#!/usr/bin/env bash
#
# Deploy the 33 Store luxury child theme to 33store.masterpc.ge
#
# Requirements:
#   1. SSH access to the server (or WP-CLI on the server).
#   2. WP-CLI installed on the server.
#
# Usage:
#   bash bin/deploy.sh ssh_user@33store.masterpc.ge /path/to/wordpress
#
# This script uploads the built zip to the server, installs/activates the
# child theme and applies the Elementor Kit design system via WP-CLI.
#
set -euo pipefail

SSH_TARGET="${1:-}"
WP_PATH="${2:-}"
ZIP="dist/hello-elementor-child.zip"

if [[ -z "${SSH_TARGET}" || -z "${WP_PATH}" ]]; then
  echo "Usage: bash bin/deploy.sh ssh_user@33store.masterpc.ge /path/to/wordpress" >&2
  exit 1
fi

if [[ ! -f "${ZIP}" ]]; then
  echo "Zip not found — building first..." >&2
  bash bin/build-zip.sh
fi

echo ">> Uploading ${ZIP} …"
scp "${ZIP}" "${SSH_TARGET}:/tmp/"

echo ">> Installing + activating theme (and parent if missing) …"
ssh "${SSH_TARGET}" "cd ${WP_PATH} && \
  wp theme install /tmp/hello-elementor-child.zip --activate --force && \
  wp theme activate hello-elementor-child"

echo ">> Applying Elementor Kit design system …"
ssh "${SSH_TARGET}" "cd ${WP_PATH} && wp luxury apply || true"

echo ">> Done. Verify at https://33store.masterpc.ge/"

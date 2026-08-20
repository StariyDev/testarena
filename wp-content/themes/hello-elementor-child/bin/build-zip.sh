#!/usr/bin/env bash
#
# Build an installable WordPress zip for the 33 Store luxury child theme.
#
# Usage:  bash bin/build-zip.sh   (run from the theme directory)
# Output: dist/hello-elementor-child.zip
#
set -euo pipefail

THEME_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
DIST_DIR="${THEME_DIR}/dist"
ZIP_FILE="${DIST_DIR}/hello-elementor-child.zip"

rm -rf "${DIST_DIR}"
mkdir -p "${DIST_DIR}"

cd "$(dirname "${THEME_DIR}")"

zip -rq "${ZIP_FILE}" "hello-elementor-child" \
  -x "hello-elementor-child/dist/*" \
  -x "hello-elementor-child/.DS_Store" \
  -x "*/.git/*"

echo "Built: ${ZIP_FILE}"
unzip -l "${ZIP_FILE}" | tail -3

#!/usr/bin/env bash
set -euo pipefail

# The production wp-config.php and database are intentionally not copied into
# Codespaces. Configure a local database before using the full WordPress app.
if [ ! -f public_html/wp-config.php ]; then
  echo "Notice: public_html/wp-config.php is missing."
  echo "Copy public_html/wp-config-sample.php to public_html/wp-config.php and configure a local database."
fi

exec php -S 0.0.0.0:8080 -t public_html public_html/router.php

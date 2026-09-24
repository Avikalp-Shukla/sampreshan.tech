# Local development helper for GitHub Codespaces.
# The live VPS database and wp-config.php are intentionally not copied here.

if [ -f index.php ] && [ ! -f wp-config.php ]; then
  echo "wp-config.php is not present; use a local database configuration before starting WordPress."
fi

php -S 0.0.0.0:8080 -t .

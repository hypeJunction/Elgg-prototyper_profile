#!/bin/bash
set -e

# Per-plugin Elgg 4.x install + activation script.
# PLUGIN_ID must be set in the container environment (passed by docker-compose
# from <plugin>/docker/.env). Only that one plugin is activated — no fleet
# activation, no plugin-order.txt, no cross-plugin side effects.

if [ -z "${PLUGIN_ID:-}" ]; then
    echo "ERROR: PLUGIN_ID environment variable is required." >&2
    echo "Set it in docker/.env before starting the stack." >&2
    exit 1
fi

echo "Waiting for MySQL..."
until php -r "new PDO('mysql:host=${ELGG_DB_HOST:-db}', '${ELGG_DB_USER:-elgg}', '${ELGG_DB_PASS:-elgg}');" 2>/dev/null; do
    sleep 1
done
echo "MySQL is ready."

cd /var/www/html

if [ ! -f /var/www/html/.elgg-installed ]; then
    echo "Installing Elgg 4.x..."

    mkdir -p elgg-config
    cat > elgg-config/settings.php <<'SETTINGS_TEMPLATE'
<?php
global $CONFIG;
if (!isset($CONFIG)) {
    $CONFIG = new \stdClass;
}
SETTINGS_TEMPLATE

    cat >> elgg-config/settings.php <<SETTINGS_VALUES
\$CONFIG->dbuser = '${ELGG_DB_USER:-elgg}';
\$CONFIG->dbpass = '${ELGG_DB_PASS:-elgg}';
\$CONFIG->dbname = '${ELGG_DB_NAME:-elgg}';
\$CONFIG->dbhost = '${ELGG_DB_HOST:-db}';
\$CONFIG->dbport = '3306';
\$CONFIG->dbprefix = 'elgg_';
\$CONFIG->dbencoding = 'utf8mb4';
\$CONFIG->dataroot = '${ELGG_DATA_ROOT:-/var/www/data/}';
\$CONFIG->wwwroot = '${ELGG_SITE_URL:-http://localhost:8480/}';
\$CONFIG->cacheroot = '${ELGG_DATA_ROOT:-/var/www/data/}cache/';
\$CONFIG->assetroot = '${ELGG_DATA_ROOT:-/var/www/data/}assets/';
SETTINGS_VALUES

    php -r "
        require_once 'vendor/autoload.php';

        \$params = [
            'dbuser' => '${ELGG_DB_USER:-elgg}',
            'dbpassword' => '${ELGG_DB_PASS:-elgg}',
            'dbname' => '${ELGG_DB_NAME:-elgg}',
            'dbhost' => '${ELGG_DB_HOST:-db}',
            'dbport' => '3306',
            'dbprefix' => 'elgg_',
            'sitename' => 'Elgg 4.x Plugin Test',
            'siteemail' => '${ELGG_ADMIN_EMAIL:-admin@example.com}',
            'wwwroot' => '${ELGG_SITE_URL:-http://localhost:8480/}',
            'dataroot' => '${ELGG_DATA_ROOT:-/var/www/data/}',
            'displayname' => 'Admin',
            'email' => '${ELGG_ADMIN_EMAIL:-admin@example.com}',
            'username' => 'admin',
            'password' => '${ELGG_ADMIN_PASSWORD:-admin12345}',
        ];

        \$installer = new \ElggInstaller();
        \$installer->batchInstall(\$params);
        echo 'Elgg 4.x installed successfully.' . PHP_EOL;
    " 2>&1 || echo "Install completed (check for errors above)."

    echo "Activating plugins..."
    php -r "
        require_once 'vendor/autoload.php';
        \$app = \Elgg\Application::getInstance();
        \$app->bootCore();
        _elgg_services()->plugins->generateEntities();

        // Activate all dep plugins found in mod/ (except the main plugin) using a
        // retry loop so transitive deps (dep-of-dep) are handled automatically.
        // Each pass activates whatever can succeed; plugins with unmet deps are
        // retried in the next pass. After at most N passes (N = number of plugins)
        // any remaining failures are reported and the script exits.
        \$plugin_dirs = glob('/var/www/html/mod/*', GLOB_ONLYDIR) ?: [];
        \$to_activate = [];
        foreach (\$plugin_dirs as \$dir) {
            \$id = basename(\$dir);
            if (\$id === '${PLUGIN_ID}') {
                continue; // main plugin activated last
            }
            \$p = elgg_get_plugin_from_id(\$id);
            if (\$p && !\$p->isActive()) {
                \$to_activate[] = \$id;
            }
        }

        \$max_passes = count(\$to_activate) + 1;
        for (\$pass = 0; \$pass < \$max_passes && !empty(\$to_activate); \$pass++) {
            \$remaining = [];
            foreach (\$to_activate as \$id) {
                \$dep = elgg_get_plugin_from_id(\$id);
                if (!\$dep || \$dep->isActive()) {
                    continue;
                }
                try {
                    \$dep->setPriority('last');
                    \$dep->activate();
                    echo 'Dep plugin ' . \$id . ' activated.' . PHP_EOL;
                } catch (\Throwable \$e) {
                    \$remaining[] = \$id; // retry after other deps are activated
                }
            }
            \$to_activate = \$remaining;
        }
        if (!empty(\$to_activate)) {
            echo 'WARNING: could not activate deps (unmet deps or errors): ' . implode(', ', \$to_activate) . PHP_EOL;
        }

        // Activate the main plugin.
        \$plugin = elgg_get_plugin_from_id('${PLUGIN_ID}');
        if (!\$plugin) {
            echo 'ERROR: plugin ${PLUGIN_ID} not found at /var/www/html/mod/${PLUGIN_ID}' . PHP_EOL;
            exit(1);
        }
        if (\$plugin->isActive()) {
            echo 'Plugin ${PLUGIN_ID} already active.' . PHP_EOL;
        } else {
            try {
                \$plugin->activate();
                echo 'Plugin ${PLUGIN_ID} activated.' . PHP_EOL;
            } catch (\Throwable \$e) {
                echo 'FAILED to activate ${PLUGIN_ID}: ' . \$e->getMessage() . PHP_EOL;
                exit(1);
            }
        }
    " 2>&1 || echo "Plugin activation completed (check for errors above)."

    touch /var/www/html/.elgg-installed
    echo "Elgg 4.x setup complete."
fi

echo "Starting Apache..."
exec apache2-foreground

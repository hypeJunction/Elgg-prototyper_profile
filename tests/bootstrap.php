<?php
/**
 * PHPUnit bootstrap for prototyper_profile plugin tests.
 * Plugin must be installed at {elgg_root}/mod/prototyper_profile/
 */

// tests/ -> mod/prototyper_profile/ -> mod/ -> elgg_root/
$elggRoot = dirname(__DIR__, 3);

require_once $elggRoot . '/vendor/autoload.php';

// Load Elgg test classes (UnitTestCase, IntegrationTestCase, etc.)
$testClassesDir = $elggRoot . '/vendor/elgg/elgg/engine/tests/classes';
spl_autoload_register(function ($class) use ($testClassesDir) {
    $file = $testClassesDir . '/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Register plugin PSR-4 autoloader in case the plugin is not active in the test DB
$pluginRoot = dirname(__DIR__);
spl_autoload_register(function ($class) use ($pluginRoot) {
    $prefix = 'hypeJunction\\PrototyperProfile\\';
    if (strncmp($class, $prefix, strlen($prefix)) !== 0) {
        return;
    }
    $relative = substr($class, strlen($prefix));
    $file = $pluginRoot . '/classes/hypeJunction/PrototyperProfile/' . str_replace('\\', '/', $relative) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

\Elgg\Application::loadCore();

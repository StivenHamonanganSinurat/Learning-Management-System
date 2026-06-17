<?php

// Vercel PHP entry point — forward all requests to CodeIgniter 4
// Force development environment to get detailed error messages
$_ENV['CI_ENVIRONMENT'] = 'development';
$_SERVER['CI_ENVIRONMENT'] = 'development';

// Set writable path to /tmp for serverless environment
$_ENV['WRITEPATH'] = '/tmp/writable/';

// Ensure writable directories exist in /tmp
$dirs = ['/tmp/writable', '/tmp/writable/cache', '/tmp/writable/logs', '/tmp/writable/session', '/tmp/writable/uploads', '/tmp/writable/debugbar'];
foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
}

// Forward to the CodeIgniter front controller
require __DIR__ . '/../public/index.php';

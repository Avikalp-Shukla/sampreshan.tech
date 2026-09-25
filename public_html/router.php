<?php
/**
 * PHP's built-in server router for local WordPress development.
 */

$requested = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$file = __DIR__ . $requested;

if ($requested !== '/' && is_file($file)) {
    return false;
}

require __DIR__ . '/index.php';

<?php

/**
 * Dump and die (for quick debugging).
 *
 * @param mixed $value
 */
function dd($value): void
{
    echo "<pre>";
    var_dump($value);
    echo "</pre>";
    die();
}

/**
 * Check if current URI matches given URI.
 *
 * @param string $uri
 *
 * @return bool
 */
function getURI(string $uri): bool
{
    // Get the current URI
    $current = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
    
    // Get base path from config
    $config = require __DIR__ . '/config.php';
    $basePath = $config['app']['base_path'];
    
    // Remove base path if present
    if (strpos($current, $basePath) === 0) {
        $current = substr($current, strlen($basePath));
    }
    
    // Ensure we have a leading slash
    if (empty($current) || $current === '/') {
        $current = '/';
    }
    
    return $current === $uri;
}

/**
 * Generate a URL with the proper base path
 *
 * @param string $path The path (e.g., "/login")
 * @return string The full URL with base path
 */
function url(string $path): string
{
    $config = require __DIR__ . '/config.php';
    $basePath = $config['app']['base_path'];
    
    // Remove leading slash from path if present
    if (strpos($path, '/') === 0) {
        $path = substr($path, 1);
    }
    
    return $basePath . '/' . $path;
}
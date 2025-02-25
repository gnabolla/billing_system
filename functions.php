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
    return $_SERVER["REQUEST_URI"] === $uri;
}

<?php
$uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

// Remove the base directory from the URI if it exists
// This handles accessing the app from /ilink/ subdirectory
$basePath = '/ilink';
if (strpos($uri, $basePath) === 0) {
    $uri = substr($uri, strlen($basePath));
}

// Ensure there's always a leading slash
if (empty($uri)) {
    $uri = '/';
}

$routes = [
  "/" => "controllers/index.php",
];

function routesToController(string $uri, array $routes): void
{
  if (array_key_exists($uri, $routes)) {
    require $routes[$uri];
  } else {
    http_response_code(404);
    echo "404 Not Found";
    echo "<p>URI requested: " . htmlspecialchars($uri) . "</p>";
  }
}

routesToController($uri, $routes);
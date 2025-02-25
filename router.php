<?php
$config = require __DIR__ . '/config.php';
$basePath = $config['app']['base_path'];

$uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

// Remove the base directory from the URI if it exists
// This handles accessing the app from a subdirectory (e.g., /ilink/)
if (strpos($uri, $basePath) === 0) {
    $uri = substr($uri, strlen($basePath));
}

// Ensure there's always a leading slash
if (empty($uri) || $uri === '/') {
    $uri = '/';
}

$routes = [
  "/" => "controllers/index.php",
  "/login" => "controllers/login.php",
  "/register" => "controllers/register.php",
  "/dashboard" => "controllers/dashboard.php",
  "/logout" => "controllers/logout.php",
  
  // Subscriber routes
  "/subscribers" => "controllers/subscribers.php",
  "/subscribers/create" => "controllers/subscribers/create.php",
  "/subscribers/edit" => "controllers/subscribers/edit.php",
  "/subscribers/view" => "controllers/subscribers/view.php",
  "/subscribers/delete" => "controllers/subscribers/delete.php",
  "/subscribers/assign-plan" => "controllers/subscriber-plans/assign.php", // Added for backward compatibility
  
  // Plan routes
  "/plans" => "controllers/plans.php",
  "/plans/create" => "controllers/plans/create.php",
  "/plans/edit" => "controllers/plans/edit.php",
  "/plans/view" => "controllers/plans/view.php",
  "/plans/delete" => "controllers/plans/delete.php",
  
  // Subscriber-Plan routes
  "/subscriber-plans/assign" => "controllers/subscriber-plans/assign.php",
  "/subscriber-plans/edit" => "controllers/subscriber-plans/edit.php",
  "/subscriber-plans/terminate" => "controllers/subscriber-plans/terminate.php",
  
  // Statement routes
  "/statements" => "controllers/statements.php",
  "/statements/create" => "controllers/statements/create.php",
  "/statements/view" => "controllers/statements/view.php",
  
  // Payment routes
  "/payments" => "controllers/payments.php",
  "/payments/create" => "controllers/payments/create.php",
  "/payments/view" => "controllers/payments/view.php"
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
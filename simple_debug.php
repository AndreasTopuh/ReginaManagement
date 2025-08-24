<?php
// Simple router debug
$_SERVER['REQUEST_URI'] = '/reginahotel/public/';
$_SERVER['SCRIPT_NAME'] = '/reginahotel/public/index.php';

$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
echo "Original: $requestUri\n";

$scriptName = $_SERVER['SCRIPT_NAME'];
$basePath = rtrim(dirname($scriptName), '/');
echo "Script: $scriptName\n";
echo "Base: $basePath\n";

if ($basePath && $basePath !== '.' && strpos($requestUri, $basePath) === 0) {
    $requestUri = substr($requestUri, strlen($basePath));
    echo "After removal: $requestUri\n";
}

$requestUri = '/' . ltrim($requestUri, '/');
echo "Final: $requestUri\n";

// Test match
$pattern = '#^/$#';
echo "Pattern: $pattern\n";
echo "Match: " . (preg_match($pattern, $requestUri) ? 'YES' : 'NO') . "\n";
?>

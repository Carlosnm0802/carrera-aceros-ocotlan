<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__ . '/../vendor/autoload.php';

try {
    $dompdf = new \Dompdf\Dompdf();
    echo "OK: Dompdf loaded\n";
} catch (Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo get_class($e) . "\n";
}

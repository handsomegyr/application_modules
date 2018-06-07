<?php
require_once '../../vendor/autoload.php';
$file = empty($_GET['file']) ? '' : $_GET['file'];
$folder = realpath(__DIR__ . '/../../apps/campaign/controllers/' . $file);
// die($folder);
$swagger = \Swagger\scan($folder);
header('Content-Type: application/json');
echo $swagger;


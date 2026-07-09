<?php
// Diagnóstico de errores de producción para CardNet.ec
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Probando conexión y cargando db.php...</h1>";
require_once 'db.php';
echo "<p>Conexión exitosa a la base de datos.</p>";

echo "<h1>Cargando index.php...</h1>";
include 'index.php';
echo "<p>Index cargado correctamente.</p>";

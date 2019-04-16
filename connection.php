<?php
global $connection;

function connect() {
    global $connection;

    // Datos del servidor MySQL.
    $host   = "[database-host-here]";
    $db     = "[database-name-here]";
    $usr    = "[user-here]";
    $pwd    = "[pass-here]";
    
    $connection = mysqli_connect($host, $usr, $pwd, $db) or die("No se pudo conectar al servidor MySQL.");
    // Permite visualizar los acentos y caracteres especiales.
    mysqli_set_charset($connection, "utf8");
}

function disconnect() {
    global $connection;
    mysqli_close($connection);
}

function execute($query) {
    global $connection;

    connect();
    // Ejecución del query.
    $recordSet = mysqli_query($connection, $query) or die("Error en el query: $query");
    disconnect();

    return $recordSet;
}

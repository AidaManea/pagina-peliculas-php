<?php

$url = "https://devsapihub.com/api-movies";

$response = file_get_contents($url);

if ($response === false) {
    echo "Error al conectar con la API";
    exit;
}

$data = json_decode($response, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo "Error al decodificar JSON";
    exit;
}

echo "<pre>";
print_r($data);
echo "</pre>";

?>
<?php
$isDocker = file_exists('/.dockerenv');
return [
    'host' => getenv('DB_HOST') ?: ($isDocker ? 'db' : 'localhost'),
    'user' => getenv('DB_USER') ?: ($isDocker ? 'prediksi_daster_user' : 'tivayoco_daster'),
    'password' => getenv('DB_PASSWORD') ?: ($isDocker ? 'userpassword' : 'yogaP123@'),
    'dbname' => getenv('DB_NAME') ?: ($isDocker ? 'prediksi_daster_collection' : 'tivayoco_collection'),
    'charset' => 'utf8mb4'
];
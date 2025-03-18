<?php
require 'ClientCRUD.php';

header('Content-Type: application/json');

$client = new ClientCRUD();

$data = [
    'name'           => $_POST['name'] ?? null,
    'slug'           => $_POST['slug'] ?? null,
    'is_project'     => $_POST['is_project'] ?? 0,
    'self_capture'   => $_POST['self_capture'] ?? 0,
    'client_prefix'  => $_POST['client_prefix'] ?? null,
    'address'        => $_POST['address'] ?? null,
    'phone_number'   => $_POST['phone_number'] ?? null,
    'city'           => $_POST['city'] ?? null
];

$file = isset($_FILES['client_logo']) ? $_FILES['client_logo'] : null;

if (empty($data['slug'])) {
    echo json_encode(["error" => "Slug is required"]);
    exit;
}

$result = $client->createClient($data, $file);

if ($result) {
    echo json_encode(["success" => true, "data" => $result]);
} else {
    echo json_encode(["success" => false, "error" => "Failed to create client"]);
}
?>
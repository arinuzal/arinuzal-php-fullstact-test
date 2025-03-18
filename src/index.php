<?php
require 'ClientCRUD.php';

$client = new ClientCRUD();

$data = [
    'name' => 'Client Example',
    'slug' => 'client-example',
    'is_project' => '1',
    'self_capture' => '1',
    'client_prefix' => 'CL01',
    'address' => 'Jalan ABC No 123',
    'phone_number' => '08123456789',
    'city' => 'Jakarta'
];

$result = $client->createClient($data, null);
print_r($result);
?>
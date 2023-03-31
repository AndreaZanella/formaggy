<?php

spl_autoload_register(function ($class) {
    require __DIR__ . "/../../COMMON/$class.php";
});

require __DIR__ . '/../../MODEL/warehouse.php';
header("Content-type: application/json; charset=UTF-8");
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Origin');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    header('HTTP/1.1 200 OK');
}

$data = json_decode(file_get_contents("php://input"));

if (empty($data->id_formaggyo) || empty($data->id_warehouse)||empty($data->weight)) {
    http_response_code(400);
    echo json_encode(["message" => "Fill every field"]);
    die();
}

$warehouse = new warehouse();

$id = $warehouse->addFormaggyotWarehouse($data->id_formaggyo, $data->id_warehouse,$data->weight);

if ($id === false) {
    http_response_code(400);
    echo json_encode($id);
}
else
{
    http_response_code(200);
    echo json_encode($id);
}

<?php

spl_autoload_register(function ($class) {
    require __DIR__ . "/../../COMMON/$class.php";
});

require __DIR__ . '/../../MODEL/supply.php';
header("Content-type: application/json; charset=UTF-8");
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Origin');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    header('HTTP/1.1 200 OK');
}

$data = json_decode(file_get_contents("php://input"));

if (empty($data->id_account) || empty($data->id_dairy)|| empty($data->id_formaggyo)|| empty($data->total_price)|| empty($data->status)|| empty($data->weight)) {
    http_response_code(400);
    echo json_encode(["message" => "Compila tutti i campi"]);
    die();
}

$supply = new supply();
$id=$supply->addSupply($data->id_account,$data->id_dairy,$data->id_formaggyo,$data->total_price,$data->status,$data->weight);

if ($id===false) {
    http_response_code(400);
    echo json_encode($id);
}
else
{
    http_response_code(200);
    echo json_encode($id);
}
?>

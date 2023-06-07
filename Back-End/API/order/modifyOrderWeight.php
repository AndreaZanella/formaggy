<?php

spl_autoload_register(function ($class) {
    require __DIR__ . "/../../COMMON/$class.php";
});

require __DIR__ . '/../../MODEL/order.php';
require __DIR__ . '/../../MODEL/warehouse.php';
header("Content-type: application/json; charset=UTF-8");
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Origin');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    header('HTTP/1.1 200 OK');
}

$data = json_decode(file_get_contents("php://input"));
$order = new Order();
$Warehouse = new Warehouse();
$result = $order->getOrderWeight($data->id_order);
$orderFormaggy = array();
$success = true;

for ($i = 0; $i < count($result); $i++) {
    $orderFormaggy[] = array(
        "count" => $i,
        "id" => $result[$i]["id_formaggyo"],
        "weight" => $result[$i]["weight"]
    );
}

for ($i = 0; $i < count($orderFormaggy); $i++) {

    $id_formaggyo = intval($orderFormaggy[$i]["id"]);
    $id_warehouse = intval($Warehouse->getFormaggyWarehouse($orderFormaggy[$i]["id"]));
    $weight = floatval($orderFormaggy[$i]["weight"]);
    $modifyWeight = $Warehouse->modifyFormaggyWeight($id_formaggyo,$id_warehouse, $weight);
    if ($modifyWeight != 1) {
        $success = false;
    }
}

if ($success) {
    http_response_code(200);
    echo json_encode(["message" => "Order weight modificato con successo"]);
} else {
    http_response_code(400);
    echo json_encode(["message" => "Problemi con il cambiamento dell'order weight"]);
}


?>

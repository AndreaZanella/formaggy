<?php

spl_autoload_register(function ($class) {
    require __DIR__ . "/../../COMMON/$class.php";
});

require __DIR__ . '/../../MODEL/ingredient.php';
header("Content-type: application/json; charset=UTF-8");
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Origin');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    header('HTTP/1.1 200 OK');
}

$data = json_decode(file_get_contents("php://input"));

if (empty($data->name) || empty($data->description)) {
    http_response_code(400);
    echo json_encode(["message" => "Compila tutti i campi"]);
    die();
}

$ingredient = new ingredient();
$id=$ingredient->addIngredient($data->name,$data->description);

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

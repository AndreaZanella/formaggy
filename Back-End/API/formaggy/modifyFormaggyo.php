<?php

spl_autoload_register(function ($class) {
    require __DIR__ . "/../../COMMON/$class.php";
});

require __DIR__ . '/../../MODEL/formaggy.php';
header("Content-type: application/json; charset=UTF-8");
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Origin');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    header('HTTP/1.1 200 OK');
}

$data = json_decode(file_get_contents("php://input"));


if (empty($data->id_formaggyo)||empty($data->name)||empty($data->description)||empty($data->price_kg)||empty($data->id_category)||empty($data->id_certification)||empty($data->color)||empty($data->smell)||empty($data->taste)||empty($data->kcal)||empty($data->fats)||empty($data->satured_fats)||empty($data->carbohydrates)||empty($data->sugars)||empty($data->proteins)||empty($data->fibers)||empty($data->salts)) {
    http_response_code(400);
    echo json_encode(["message" => "Compila tutti i campi"]);
    die();
}

$formaggyo = new Formaggy();

if ($formaggyo->modifyFormaggyo($data->id_formaggyo,$data->name,$data->description,$data->price_kg,$data->id_category,$data->id_certification,$data->color,$data->smell,$data->taste,$data->kcal,$data->fats,$data->satured_fats,$data->carbohydrates,$data->sugars,$data->proteins,$data->fibers,$data->salts)) 
{ 
    http_response_code(200);
    echo json_encode(["message" => "Formaggyo modificato con successo"]);
}
else
{
    http_response_code(400);
    echo json_encode(["message" => "Problemi con la modifica del formaggyo"]);
}
?>

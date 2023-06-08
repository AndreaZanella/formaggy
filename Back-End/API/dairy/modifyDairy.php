<?php

//API per la modifica di un fornitore

spl_autoload_register(function ($class) {
    require __DIR__ . "/../../COMMON/$class.php";
});

require __DIR__ . '/../../MODEL/dairy.php';
header("Content-type: application/json; charset=UTF-8");
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Origin');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    header('HTTP/1.1 200 OK');
}

$data = json_decode(file_get_contents("php://input"));


if (empty($data->id_dairy) || empty($data->address)||empty($data->telephon_number) || empty($data->email)|| empty($data->website)|| empty($data->name) || empty($data->image)) {
    http_response_code(400);
    echo json_encode(["message" => "Compila tutti i campi"]);
    die();
}

$dairy = new dairy();

if ($dairy->modifyDairy($data->id_dairy,$data->name,$data->address,$data->telephon_number,$data->email,$data->website,$data->image)>0 && $dairy->modifyDairy($data->id_dairy,$data->name,$data->address,$data->telephon_number,$data->email,$data->website,$data->image)<7) {
    http_response_code(200);
    echo json_encode(["message" => "Dairy modificato con successo"]);
}
else
{
    http_response_code(400);
    echo json_encode(["message" => "Problemi con il cambiamento del dairy"]);
}
?>

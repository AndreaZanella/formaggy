<?php
//API per ottenere attraverso l'id un fornitore

require __DIR__ . '/../../MODEL/dairy.php';

header("Content-type: application/json; charset=UTF-8");
header("Content-type: application/json; charset=UTF-8");
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Origin');

$parts = explode("/", $_SERVER["REQUEST_URI"]);

if (empty($parts[5])) {
    http_response_code(404);
    echo json_encode(["message" => "Inserisci un ID valido"]);
    exit();
}

$dairy = new Dairy();

$result = $dairy->getDairy($parts[5]);

if ($result != false) {
    echo json_encode($result);
} else {
    http_response_code(400);
    echo json_encode(["message" => "Caseificio non trovato"]);
}

?>

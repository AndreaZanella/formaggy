<?php

//API per ottenere attraverso l'id un determinato account

require __DIR__ . '/../../MODEL/account.php';

header("Content-type: application/json; charset=UTF-8");
header("Content-type: application/json; charset=UTF-8");
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Origin');

$parts = explode("/", $_SERVER["REQUEST_URI"]);

if (empty($parts[5])) {
    http_response_code(404);
    echo json_encode(["message" => "Inserisci un id valido"]);
    exit();
}

$account = new Account();

$result = $account->getAccount($parts[5]);

if ($result != false) {
    echo json_encode($result);
} else {
    http_response_code(400);
    echo json_encode(["message" => "Account non trovato"]);
}

?>

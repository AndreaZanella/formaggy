<?php

//API per ottenere tutte le certificazioni

require __DIR__ . '/../../MODEL/certification.php';

header("Content-type: application/json; charset=UTF-8");
header("Content-type: application/json; charset=UTF-8");
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Origin');

$parts = explode("/", $_SERVER["REQUEST_URI"]);

$certification = new Certification();

$result = $certification->getArchiveCertification();
$certificationArchivecertifications = array();
for ($i = 0; $i < (count($result)); $i++) {
$certificationArchivecertification = array(
    "id" => $result[$i]["id"],
    "acronym" => $result[$i]["acronym"],
    "name" => $result[$i]["name"]
);
array_push($certificationArchivecertifications, $certificationArchivecertification);
}

if (empty($certificationArchivecertifications)) {
    echo json_encode(404);
    echo json_encode(["messaggio" => "La ricerca non ha prodotto risultati"]);
} else {
    http_response_code(200);
    echo json_encode($certificationArchivecertifications);
}
?>


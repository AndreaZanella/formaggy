<?php

//API per ottenere tutte le categorie

require __DIR__ . '/../../MODEL/category.php';

header("Content-type: application/json; charset=UTF-8");
header("Content-type: application/json; charset=UTF-8");
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Origin');

$parts = explode("/", $_SERVER["REQUEST_URI"]);

$category = new Category();

$result = $category->getArchiveCategory();
$categoryArchivecategorys = array();
for ($i = 0; $i < (count($result)); $i++) {
$categoryArchivecategory = array(
    "id" => $result[$i]["id"],
    "name" => $result[$i]["name"]
);
array_push($categoryArchivecategorys, $categoryArchivecategory);
}

if (empty($categoryArchivecategorys)) {
    echo json_encode(404);
    echo json_encode(["messaggio" => "La ricerca non ha prodotto risultati"]);
} else {
    http_response_code(200);
    echo json_encode($categoryArchivecategorys);
}
?>

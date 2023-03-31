<?php
require __DIR__ . '/../../MODEL/category.php';
header("Content-type: application/json; charset=UTF-8");

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
    echo json_encode(["messaggio" => "Nessuna categoria è stata trovata"]);
} else {
    http_response_code(200);
    echo json_encode($categoryArchivecategorys);
}

<?php
$file = fopen("fashion_data.csv", "r");
$data = [];

while (($row = fgetcsv($file)) !== FALSE) {
    $data[] = $row;
}
fclose($file);

// Send CSV data as JSON for frontend (JS) use
header('Content-Type: application/json');
echo json_encode($data);
?>

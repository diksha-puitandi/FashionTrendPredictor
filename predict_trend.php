<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['username']) || !isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["success" => false, "error" => "User not logged in"]);
    exit;
}

// Set content type to JSON
header('Content-Type: application/json');

// Collect all form data
$age = $_POST['age'] ?? '';
$season_weather = $_POST['season_weather'] ?? '';
$target_audience = $_POST['target_audience'] ?? '';
$category_fit = $_POST['category_fit'] ?? '';
$material_fabric = $_POST['material_fabric'] ?? '';
$cultural_trend = $_POST['cultural_trend'] ?? '';
$color_pattern = $_POST['color_pattern'] ?? '';
$boldness = isset($_POST['boldness']) ? (int)$_POST['boldness'] : null;
$celebrity_promotion = $_POST['celebrity_promotion'] ?? '';
$first_seen = $_POST['first_seen'] ?? '';

// Validate required fields
$required_fields = [
    'age' => $age,
    'season_weather' => $season_weather,
    'target_audience' => $target_audience,
    'category_fit' => $category_fit,
    'material_fabric' => $material_fabric,
    'cultural_trend' => $cultural_trend,
    'color_pattern' => $color_pattern,
    'boldness' => $boldness,
    'celebrity_promotion' => $celebrity_promotion,
    'first_seen' => $first_seen
];

foreach ($required_fields as $field => $value) {
    if (empty($value)) {
        http_response_code(400);
        echo json_encode(["success" => false, "error" => "Missing required field: $field"]);
        exit;
    }
}

// Prepare data for Python script
$data = [
    "age" => $age,
    "season_weather" => $season_weather,
    "target_audience" => $target_audience,
    "category_fit" => $category_fit,
    "material_fabric" => $material_fabric,
    "cultural_trend" => $cultural_trend,
    "color_pattern" => $color_pattern,
    "boldness" => $boldness,
    "celebrity_promotion" => $celebrity_promotion,
    "first_seen" => $first_seen
];

$payload = json_encode($data, JSON_UNESCAPED_SLASHES);

// Call Python script using a different approach
$python_script = "predict_trend.py";
$temp_file = tempnam(sys_get_temp_dir(), 'prediction_data_');
file_put_contents($temp_file, $payload);

$command = "python " . escapeshellarg($python_script) . " " . escapeshellarg($temp_file);

// Execute the command
$output = shell_exec($command . " 2>&1");

// Clean up temp file
unlink($temp_file);

if ($output === null) {
    http_response_code(500);
    echo json_encode(["success" => false, "error" => "Failed to execute prediction script"]);
    exit;
}

// Debug: Log the command and output (remove in production)
error_log("Command: " . $command);
error_log("Output: " . $output);

// Parse the JSON output from Python script
$result = json_decode($output, true);

if ($result === null) {
    http_response_code(500);
    echo json_encode(["success" => false, "error" => "Invalid response from prediction script: " . $output]);
    exit;
}

// Return the result
echo json_encode($result);
?>

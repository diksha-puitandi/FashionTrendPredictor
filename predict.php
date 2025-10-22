<?php
session_start();
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
        echo json_encode(["error" => "Missing required field: $field"]);
        exit;
    }
}

$payload = json_encode([
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
]);

// Call Python prediction script
$python_script = "predict_trend.py";
$command = "python " . $python_script . " '" . addslashes($payload) . "' 2>&1";
$response = shell_exec($command);

if ($response === null) {
    http_response_code(500);
    echo json_encode(["error" => "Failed to execute prediction script"]);
    exit;
}

$api_result = json_decode($response, true);
if (!$api_result) {
    http_response_code(500);
    echo json_encode(["error" => "Invalid response from prediction script: " . $response]);
    exit;
}

// Optional: Save to DB
try {
    $pdo = new PDO("mysql:host=localhost;dbname=fashion_db;charset=utf8", "root", "");
    $stmt = $pdo->prepare("INSERT INTO predictions (user_id, input_json, output_json, created_at) VALUES (?, ?, ?, NOW())");
    $user_id = $_SESSION['user_id'] ?? 0;
    $stmt->execute([$user_id, $payload, json_encode($api_result)]);
} catch (Exception $e) {
    error_log("DB insert failed: " . $e->getMessage());
}

// Ensure trend_series is in the format JS expects
if (!isset($api_result['trend_series']['labels']) || !isset($api_result['trend_series']['values'])) {
    // fallback if API doesn't provide proper format
    $api_result['trend_series'] = [
        "labels" => ['Jan','Feb','Mar','Apr','May','Jun'],
        "values" => [50, 65, 80, 75, 90, 85]
    ];
}

echo json_encode($api_result);
?> 

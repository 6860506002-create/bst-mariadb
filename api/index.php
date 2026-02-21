<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

$host = getenv("DB_HOST");
$user = getenv("DB_USER");
$pass = getenv("DB_PASSWORD");
$db   = getenv("DB_NAME");

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    echo json_encode(["error" => "Database connection failed"]);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === "GET") {
    $result = $conn->query("SELECT value FROM tree");
    $data = [];
    while($row = $result->fetch_assoc()) {
        $data[] = $row["value"];
    }
    echo json_encode($data);
}

if ($method === "POST") {
    $input = json_decode(file_get_contents("php://input"), true);
    $value = $input["value"];

    $stmt = $conn->prepare("INSERT INTO tree (value) VALUES (?)");
    $stmt->bind_param("i", $value);
    $stmt->execute();

    echo json_encode(["status" => "added"]);
}

if ($method === "DELETE") {
    $input = json_decode(file_get_contents("php://input"), true);
    $value = $input["value"];

    $stmt = $conn->prepare("DELETE FROM tree WHERE value=?");
    $stmt->bind_param("i", $value);
    $stmt->execute();

    echo json_encode(["status" => "deleted"]);
}

$conn->close();
?>

<?php
// 1. Configurar cabeceras de seguridad
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: http://bienestarhueypoxtla.lat"); // Puedes cambiar el * por tu dominio real para más seguridad
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["error" => "Método no permitido"]);
    exit;
}

// 2. Tu API Key se queda segura aquí en el servidor (Nadie la puede ver desde el navegador)
$api_key = "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"; 

// 3. Recibir la pregunta y el historial del chatbot
$input = json_decode(file_get_contents("php://input"), true);
$chat_history = $input['messages'] ?? [];

if (empty($chat_history)) {
    echo json_encode(["error" => "Historial de chat vacío"]);
    exit;
}

// 4. Configurar la petición hacia Groq
$url = "https://api.groq.com/openai/v1/chat/completions";
$body = json_encode([
    "model" => "llama-3.3-70b-versatile",
    "messages" => $chat_history,
    "temperature" => 0.4,
    "max_tokens" => 1024
]);

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: Bearer " . $api_key
]);

// 5. Obtener respuesta de Groq y enviarla al chatbot
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code !== 200) {
    echo json_encode(["error" => "Error en la comunicación con el servidor de IA"]);
    exit;
}

echo $response;
?>
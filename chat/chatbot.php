<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

// Clé API et URL
$GKey = "AIzaSyDWuYR-M8EzvTE3OIz1iNlyjugCxf7IVJ0";
$url = "https://generativelanguage.googleapis.com/v1/models/gemini-pro:generateContent?key=" . $GKey;

// Récupération du message utilisateur
$data = json_decode(file_get_contents("php://input"), true);
$userMessage = $data['message'] ?? '';

if (!$userMessage) {
    echo json_encode(["response" => "Aucun message reçu."]);
    exit;
}

// Préparation des données pour l'API
$postData = [
    "prompt" => [
        "text" => $userMessage
    ],
    "temperature" => 0.7,
    "candidate_count" => 1
];

// Initialisation de cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json"
]);

// Exécution de la requête
$result = curl_exec($ch);

if (curl_errno($ch)) {
    echo json_encode([
        "response" => "Erreur lors de la connexion à l'API.",
        "error" => curl_error($ch)
    ]);
    curl_close($ch);
    exit;
}

curl_close($ch);

// Décodage de la réponse
$responseData = json_decode($result, true);

if (!$responseData) {
    echo json_encode([
        "response" => "Erreur lors du traitement de la réponse API.",
        "raw_response" => $result
    ]);
    exit;
}

// Extraire et afficher la réponse
echo json_encode([
    "response" => $responseData['candidates'][0]['output'] ?? "Pas de réponse de l'API."
]);

<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

// Clé API et URL
$GKey = "AIzaSyDWuYR-M8EzvTE3OIz1iNlyjugCxf7IVJ0";
$url = "https://generativelanguage.googleapis.com/v1/models/gemini-pro:generateContent?key=" . $GKey;

// Récupération du message utilisateur depuis la requête
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

// Options pour la requête HTTP
$options = [
    "http" => [
        "header"  => "Content-Type: application/json\r\n",
        "method"  => "POST",
        "content" => json_encode($postData),
    ],
];

// Envoi de la requête
$context = stream_context_create($options);
$result = @file_get_contents($url, false, $context);

if ($result === false) {
    // Gestion des erreurs
    echo json_encode([
        "response" => "Erreur lors de la connexion à l'API.",
        "error" => error_get_last()
    ]);
    exit;
}

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

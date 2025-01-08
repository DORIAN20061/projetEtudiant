<?php
// Configuration de l'API Gemini
$GKey = 'AIzaSyDWuYR-M8EzvTE3OIz1iNlyjugCxf7IVJ0';
$url = "https://generativelanguage.googleapis.com/v1/models/gemini-pro:generateContent?key=" . $GKey;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $userMessage = $_POST['message'] ?? '';
    $botMessage=gemini($userMessage);
    echo json_encode(['botMessage' => $botMessage]);
    // if (!empty($userMessage)) {
    //     // Préparation des données pour l'API
    //     $data = [
    //         'prompt' => [
    //             'text' => $userMessage
    //         ]
    //     ];

    //     $options = [
    //         'http' => [
    //             'header'  => "Content-type: application/json\r\n",
    //             'method'  => 'POST',
    //             'content' => json_encode($data),
    //         ],
    //     ];

    //     $context  = stream_context_create($options);
    //     $response = file_get_contents($url, false, $context);

    //     if ($response === FALSE) {
    //         echo json_encode(['error' => 'Erreur lors de la connexion à l\'API Gemini']);
    //         exit;
    //     } else {
    //         $responseData = json_decode($response, true);
    //         if (!$responseData) {
    //             echo json_encode(['error' => 'Réponse invalide de l\'API : ' . $response]);
    //             exit;
    //         }
    //         $botMessage = $responseData['candidates'][0]['text'] ?? 'Aucune réponse disponible.';
    //         echo json_encode(['botMessage' => $botMessage]);
    //         exit;
    //     }
        
    // }
    // echo json_encode(['error' => 'Message utilisateur vide']);
    // exit;
}

function gemini($message)
{
    // Clé API
    $GKey = "AIzaSyDWuYR-M8EzvTE3OIz1iNlyjugCxf7IVJ0";

    // Définir la question
    $question = $message;
    //"peut tu me produire un mail professionnel, concis sans partie à remplir pour décrire la situation financière d'un étudiant dont le nom est Zizou, le prénom Zidane, le reste à payer est de 1000000 FCFA pour une pension de 1000000 FCFA qui sera adressée aux Parents ?";

    // Construire l'URL de l'API
    $url = "https://generativelanguage.googleapis.com/v1/models/gemini-pro:generateContent?key=" . $GKey;

    // Préparer les données de la requête
    $requestData = json_encode([
        'contents' => [
            [
                'role' => 'user',
                'parts' => [
                    ['text' => $question]
                ]
            ]
        ]
    ]);

    // Initialiser cURL
    $ch = curl_init($url);

    // Configurer les options cURL
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $requestData);

    // Désactiver la vérification SSL pour éviter les erreurs (utiliser uniquement pour les tests)
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    // Envoyer la requête et récupérer la réponse
    $response = curl_exec($ch);

    // Vérifier les erreurs cURL
    if (curl_errno($ch)) {
        die("Erreur cURL : " . curl_error($ch));
    }

    // Fermer la connexion cURL
    curl_close($ch);

    // Décoder la réponse JSON
    $responseObject = json_decode($response, true);

    // Vérifier si des candidats existent dans la réponse
    if (isset($responseObject['candidates']) && count($responseObject['candidates']) > 0) {
        // Obtenir le contenu du premier candidat
        $content = $responseObject['candidates'][0]['content'] ?? null;

        // Vérifier si le contenu existe
        if ($content && isset($content['parts']) && count($content['parts']) > 0) {
            // Extraire le texte de la première partie
            return $content['parts'][0]['text'];
        } else {
            return "Aucune partie trouvée dans le contenu sélectionné.";
        }
    } else {
        return "Aucun candidat trouvé dans la réponse JSON.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chatbot Gemini</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">Chatbot Gemini</h1>
        <div class="chat-box border rounded p-3 mb-3" style="height: 300px; overflow-y: scroll;" id="chat-box">
            <!-- Messages -->
        </div>
        <form id="chat-form">
            <div class="input-group">
                <input type="text" name="message" id="message" class="form-control" placeholder="Écrivez un message..." required>
                <button type="submit" class="btn btn-primary">Envoyer</button>
            </div>
        </form>
    </div>

    <script>
        $(document).ready(function() {
            $("#chat-form").on("submit", function(event) {
                event.preventDefault();
                let userMessage = $("#message").val();

                if (userMessage.trim() !== "") {
                    $("#chat-box").append(`<div><strong>Vous:</strong> ${userMessage}</div>`);
                    $("#message").val("");

                    $.ajax({
                        url: "", // La page actuelle
                        type: "POST",
                        data: { message: userMessage },
                        dataType: "json",
                        success: function(response) {
                            if (response.botMessage) {
                                $("#chat-box").append(`<div><strong>Bot:</strong> ${response.botMessage}</div>`);
                            } else if (response.error) {
                                $("#chat-box").append(`<div><strong>Bot:</strong> ${response.error}</div>`);
                            }
                        },
                        error: function() {
                            $("#chat-box").append('<div><strong>Bot:</strong> Erreur lors de la requête.</div>');
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>

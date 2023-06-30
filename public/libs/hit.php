<?php

use controllers\BeeController;
use controllers\BeeView;
use models\BeeModel;
use models\JsonFileManager;

spl_autoload_register(function ($class) {
    $classFile = __DIR__ . '/../../src/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($classFile)) {
        require_once $classFile;
    }
});

$filePath = '../../datas/game_state.json';
$jsonFileManager = new JsonFileManager($filePath);
$beeModel = new BeeModel($jsonFileManager);
$beeView = new BeeView();

$beeController = new BeeController($beeModel, $beeView);

// Effectuer l'action de frappe
$beeController->hitRandomBee(); // Appeler la méthode hitRandomBee

// Obtenir l'état du jeu mis à jour
$remainingBees = $beeModel->getRemainingBees();

// Préparer les données à renvoyer en tant que JSON
$responseData = [
    'remainingBees' => $remainingBees
];

// Renvoyer les données de jeu mises à jour en tant que JSON
header('Content-Type: application/json');
echo json_encode($responseData);
exit(); // Assurez-vous d'inclure la fonction exit() après avoir envoyé la réponse

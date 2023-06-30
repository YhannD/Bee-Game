<?php

use controllers\BeeController;
use controllers\BeeView;
use models\BeeModel;
use models\JsonFileManager;

spl_autoload_register(function ($class) {
    $classFile = __DIR__ . '/../src/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($classFile)) {
        require_once $classFile;
    }
});

// Créer des instances du modèle et de la vue
$filePath = '../datas/game_state.json';
$jsonFileManager = new JsonFileManager($filePath);
$beeModel = new BeeModel($jsonFileManager);
$beeView = new BeeView();

// Créer une instance du contrôleur et passer le modèle et la vue
$beeController = new BeeController($beeModel, $beeView);

// Vérifier si une requête de frappe de l'abeille a été effectuée
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hit'])) {
    $beeController->hitBee();
}

// Afficher la page du jeu
$beeController->displayGamePage();

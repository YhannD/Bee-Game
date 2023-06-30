<?php
namespace controllers;

use models\BeeModel;

class BeeController {
    private BeeModel $beeModel;
    private BeeView $beeView;

    public function __construct(BeeModel $beeModel, BeeView $beeView) {
        $this->beeModel = $beeModel;
        $this->beeView = $beeView;
    }

    /**
     * Afficher la page du jeu
     *
     * @return void
     */
    public function displayGamePage(): void {
        $remainingBees = $this->beeModel->getRemainingBees();
        $this->beeView->displayGamePage($remainingBees);
    }

    /**
     * Méthode pour frapper une abeille aléatoire
     *
     * @return void
     */
    public function hitRandomBee(): void {
        $this->beeModel->hitRandomBee();
    }

    /**
     * Méthode pour frapper une abeille spécifique
     *
     * @param string $beeType
     * @return void
     */
    public function hitBee(string $beeType): void {
        $this->beeModel->hitBee($beeType);
    }
}

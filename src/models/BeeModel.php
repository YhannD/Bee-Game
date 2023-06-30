<?php

namespace models;

class BeeModel
{
    private JsonFileManager $jsonFileManager;

    /**
     * @param JsonFileManager $jsonFileManager
     */
    public function __construct(JsonFileManager $jsonFileManager)
    {
        $this->jsonFileManager = $jsonFileManager;
    }

    /**
     * Récupère les abeilles restantes depuis l'état du jeu.
     * 
     * @return array
     */
    public function getRemainingBees(): array
    {
        $gameState = $this->jsonFileManager->loadJson();
        return $gameState['remainingBees'] ?? [];
    }

    /**
     * Effectue une attaque sur le type d'abeille spécifié.
     * 
     * @param string $beeType
     * @return void
     */
    public function hitBee(string $beeType): void
    {
        $gameState = $this->jsonFileManager->loadJson();
        $remainingBees = $gameState['remainingBees'] ?? [];

        if ($this->canHitBee($beeType, $remainingBees)) {
            $bee = &$remainingBees[$beeType];
            $hitPoints = $this->getHitPoints($beeType);

            $bee['hitPoints'] -= $hitPoints;

            // Si la reine est touchée et ses points de vie sont nuls ou négatifs, attaquer toutes les ouvrières et les éclaireuses.
            if ($beeType === 'Queen' && $bee['hitPoints'] <= 0) {
                $this->hitAllWorkers($remainingBees);
                $this->hitAllScouts($remainingBees);
            }

            // Si l'abeille est touchée et ses points de vie sont nuls ou négatifs, la marquer comme touchée et vérifier si toutes les abeilles sont touchées.
            if ($bee['hitPoints'] <= 0) {
                $bee['hitPoints'] = 0;
                $bee['isDown'] = true;

                // Si toutes les abeilles sont touchées, réinitialiser le jeu.
                if ($this->areAllBeesHit($remainingBees)) {
                    $this->resetGame();
                    return;
                }
            }

            $gameState['remainingBees'] = $remainingBees;
            $this->jsonFileManager->saveJson($gameState);
        }
    }

    /**
     * Vérifie si une abeille peut être touchée (c'est-à-dire si elle existe et n'est pas déjà touchée).
     *
     * @param string $beeType
     * @param array $remainingBees
     * @return bool
     */
    private function canHitBee(string $beeType, array $remainingBees): bool
    {
        $bee = $remainingBees[$beeType] ?? null;
        return $bee && !$bee['isDown'];
    }

    /**
     * Obtient les points de vie pour un type d'abeille spécifique à l'aide d'une instruction match.
     *
     * @param string $beeType
     * @return int
     */
    private function getHitPoints(string $beeType): int
    {
        return match ($beeType) {
            'Queen', 'Scout1', 'Scout2', 'Scout3', 'Scout4', 'Scout5', 'Scout6', 'Scout7', 'Scout8' => 15,
            'Worker1', 'Worker2', 'Worker3', 'Worker4', 'Worker5' => 20,
            default => 0,
        };
    }

    /**
     * Attaque toutes les abeilles ouvrières en mettant leurs points de vie à zéro et en les marquant comme touchées.
     *
     * @param array $remainingBees
     * @return void
     */
    private function hitAllWorkers(array &$remainingBees): void
    {
        foreach ($remainingBees as $beeType => &$bee) {
            if (str_starts_with($beeType, 'Worker')) {
                $bee['hitPoints'] = 0;
                $bee['isDown'] = true;
            }
        }
    }

    /**
     * Attaque toutes les abeilles éclaireuses en mettant leurs points de vie à zéro et en les marquant comme touchées.
     *
     * @param array $remainingBees
     * @return void
     */
    private function hitAllScouts(array &$remainingBees): void
    {
        foreach ($remainingBees as $beeType => &$bee) {
            if (str_starts_with($beeType, 'Scout')) {
                $bee['hitPoints'] = 0;
                $bee['isDown'] = true;
            }
        }
    }

    /**
     * Vérifie si toutes les abeilles sont touchées (c'est-à-dire si toutes les abeilles ont été marquées comme touchées et ont des points de vie nuls ou négatifs).
     *
     * @param array $remainingBees
     * @return bool
     */
    private function areAllBeesHit(array $remainingBees): bool
    {
        foreach ($remainingBees as $bee) {
            if (!$bee['isDown'] && $bee['hitPoints'] > 0) {
                return false;
            }
        }
        return true;
    }

    /**
     * Effectue une attaque sur une abeille aléatoire en sélectionnant une abeille non touchée au hasard et en l'attaquant.
     *
     * @return void
     */
    public function hitRandomBee(): void
    {
        $gameState = $this->jsonFileManager->loadJson();
        $remainingBees = $gameState['remainingBees'] ?? [];

        $remainingBees = array_filter($remainingBees, fn($bee) => !$bee['isDown']);

        // S'il n'y a plus d'abeilles non touchées restantes, retourner.
        if (empty($remainingBees)) {
            return;
        }

        $beeTypes = array_keys($remainingBees);
        $randomBeeType = $beeTypes[array_rand($beeTypes)];

        $this->hitBee($randomBeeType);
    }

    /**
     * Définit l'état initial des abeilles restantes.
     *
     * @return array[]
     */
    private function setInitialRemainingBees(): array
    {
        $remainingBees = [
            'Queen' => ['hitPoints' => 100, 'isDown' => false],
        ];

        $beeTypes = ['Worker', 'Scout'];
        $numWorkers = 5;
        $numScouts = 8;

        // Crée des abeilles ouvrières avec 50 points de vie et non touchées.
        for ($i = 1; $i <= $numWorkers; $i++) {
            $beeType = $beeTypes[0] . $i;
            $remainingBees[$beeType] = ['hitPoints' => 50, 'isDown' => false];
        }

        // Crée des abeilles éclaireuses avec 30 points de vie et non touchées.
        for ($i = 1; $i <= $numScouts; $i++) {
            $beeType = $beeTypes[1] . $i;
            $remainingBees[$beeType] = ['hitPoints' => 30, 'isDown' => false];
        }

        return $remainingBees;
    }

    /**
     * Réinitialise le jeu en rétablissant l'état initial des abeilles restantes.
     * 
     * @return void
     */
    public function resetGame(): void
    {
        $remainingBees = $this->setInitialRemainingBees();
        $gameState = ['remainingBees' => $remainingBees];
        $this->jsonFileManager->saveJson($gameState);
    }
}

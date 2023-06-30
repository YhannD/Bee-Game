<?php

namespace controllers;

class BeeView
{
    public function displayGamePage(array $remainingBees): void
    {
        include __DIR__ . '/../../templates/game_page.phtml';
    }
}


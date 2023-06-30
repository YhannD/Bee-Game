<?php

namespace models;

class JsonFileManager
{
    private string $filePath;

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * Charge les données JSON depuis le fichier.
     *
     * @return array Les données JSON sous forme de tableau associatif
     */
    public function loadJson(): array
    {
        if (file_exists($this->filePath)) {
            $jsonData = file_get_contents($this->filePath);
            return json_decode($jsonData, true);
        }

        return [];
    }

    /**
     * Enregistre les données au format JSON dans le fichier.
     *
     * @param array $data Les données à enregistrer
     * @return void
     */
    public function saveJson(array $data): void
    {
        $jsonData = json_encode($data, JSON_PRETTY_PRINT);
        file_put_contents($this->filePath, $jsonData);
    }
}


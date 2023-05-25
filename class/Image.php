<?php

namespace SvKulikov\StableDiffusionAPIPHP\Helpers;

class Image
{
    private int $lastFileNumber = 0;
    public function saveImages(string $imagesDir, string $prompt, array $images): void
    {
        if (!is_dir($imagesDir)) {
            mkdir($imagesDir);
        }

        $imageGeneralName = $prompt;
        $imageGeneralName = str_replace("lora:", "L=", $imageGeneralName);
        $imageGeneralName = preg_replace("/[^\w\d=(), ]/", "_", $imageGeneralName);
        $imageGeneralName = substr($imageGeneralName, 0, 200);

        if ($this->lastFileNumber == 0) {
            clearstatcache();
            $filesInDir = scandir($imagesDir);
            if ((!is_array($filesInDir)) || (count($filesInDir) == 0)) {
                $filesInDir = [];
            }
            rsort($filesInDir);
            $lastFile = reset($filesInDir);
            $this->lastFileNumber = (int)$lastFile ?? 0;
        }

        foreach ($images as $imageIndex => $imageData) {
            do {
                $lastFileNumberAsText = (string)++$this->lastFileNumber;
                $lastFileNumberAsText = str_pad($lastFileNumberAsText, 6, '0', STR_PAD_LEFT);
                $finalFileName = $imagesDir . '/' . $lastFileNumberAsText . '_' . $imageGeneralName . '.png';
            } while (is_file($finalFileName));

            $imageDecoded = base64_decode($imageData);
            file_put_contents($finalFileName, $imageDecoded);
            echo "[" . filesize($finalFileName) . " b] ";
        }
    }
}
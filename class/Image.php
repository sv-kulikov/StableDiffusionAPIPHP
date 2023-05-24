<?php

namespace SvKulikov\StableDiffusionAPIPHP\Helpers;

class Image
{
    public function saveImages(string $imagesDir, string $prompt, array $images): void
    {
        if (!is_dir($imagesDir)) {
            mkdir($imagesDir);
        }

        $imageGeneralName = $prompt;
        $imageGeneralName = str_replace("lora:", "L=", $imageGeneralName);
        $imageGeneralName = preg_replace("/[^\w\d=(), ]/", "_", $imageGeneralName);
        $imageGeneralName = substr($imageGeneralName, 0, 200);

        clearstatcache();
        $filesInDir = scandir($imagesDir);
        rsort($filesInDir);
        $lastFile = reset($filesInDir);
        $lastFileNumber = (int)$lastFile ?? 0;

        foreach ($images as $imageIndex => $imageData) {
            $lastFileNumberAsText = (string)++$lastFileNumber;
            $lastFileNumberAsText = str_pad($lastFileNumberAsText, 6, '0', STR_PAD_LEFT);
            $finalFileName = $imagesDir . '/' . $lastFileNumberAsText . '_' . $imageGeneralName . '.png';
            $imageDecoded = base64_decode($imageData);
            file_put_contents($finalFileName, $imageDecoded);
            echo "[" . filesize($finalFileName) . " b] ";
        }
    }
}
<?php

namespace SvKulikov\StableDiffusionAPIPHP\Helpers;

class StableDiffusion
{
    private array $payloads;
    private Curl $curl;
    private Image $image;

    public function __construct(array $payloads, Curl $curl, Image $image)
    {
        $this->payloads = $payloads;
        $this->curl = $curl;
        $this->image = $image;
    }

    public function run(): void
    {
        $totalPayloads = count($this->payloads);

        for ($i = 0; $i < $totalPayloads; $i++) {
            echo "→ Executing task " . ($i + 1) . " of " . $totalPayloads . " ...\n";

            for ($j = 0; $j < $this->payloads[$i]['iterations']; $j++) {
                $startTime = microtime(true);
                echo "┌ Task " . ($i + 1) . " of " . $totalPayloads . ". Iteration " . ($j + 1) . " of " . $this->payloads[$i]['iterations'] . " ...\n";
                echo "│ Requesting " . $this->payloads[$i]['data']['batch_size'] . " images ...\n";

                $payloadJSON = json_encode($this->payloads[$i]['data']);
                $data = $this->curl->run($payloadJSON);
                $dataDecoded = json_decode($data);
                echo "│ Got " . count($dataDecoded->images) . " images, saving to [" . $this->payloads[$i]['images_dir'] . "]... \n";
                echo "│ ";
                $this->image->saveImages($this->payloads[$i]['images_dir'], $this->payloads[$i]['data']['prompt'], $dataDecoded->images);

                $endTime = microtime(true);

                $deltaTime = $endTime - $startTime;

                $imagesPerSecond = round($this->payloads[$i]['data']['batch_size'] / $deltaTime, 2);
                $secondsPerImage = round($deltaTime / $this->payloads[$i]['data']['batch_size'], 2);

                if ($imagesPerSecond >= 1) {
                    $performanceMsg = "Images per second = " . $imagesPerSecond . ".";
                } else {
                    $performanceMsg = "Seconds per image = " . $secondsPerImage . ".";
                }

                echo "\n";
                echo "└ Iteration " . ($j + 1) . " of " . $this->payloads[$i]['iterations'] . " done. " . $performanceMsg . "\n";
            }
        }
    }
}
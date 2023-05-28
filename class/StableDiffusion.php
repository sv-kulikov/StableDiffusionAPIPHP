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
        $startTimeGlobal = microtime(true);

        $totalPayloads = count($this->payloads);
        $imagesCreatedGlobal = 0;
        $imagesToCreateGlobal = 0;

        for ($i = 0; $i < $totalPayloads; $i++) {
            $imagesToCreateGlobal += $this->payloads[$i]['data']['batch_size'];
        }

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
                $deltaTimeGlobal = $endTime - $startTimeGlobal;

                $imagesCreated = count($dataDecoded->images);
                $imagesCreatedGlobal += $imagesCreated;
                $imagesPerSecond = round($imagesCreated / $deltaTime, 2);
                $secondsPerImage = round($deltaTime / $imagesCreated, 2);
                $imagesPerSecondGlobal = round($imagesCreatedGlobal / $deltaTimeGlobal, 2);
                $secondsPerImageGlobal = round($deltaTimeGlobal / $imagesCreatedGlobal, 2);

                if ($imagesPerSecond >= 1) {
                    $performanceMsg = "Images per second = " . $imagesPerSecond . ".";
                } else {
                    $performanceMsg = "Seconds per image = " . $secondsPerImage . ".";
                }

                if ($imagesPerSecondGlobal >= 1) {
                    $estimatedTimeLeft = round(($imagesToCreateGlobal - $imagesCreatedGlobal) / $imagesPerSecondGlobal);
                    $performanceMsg .= " Images per second global = " . $imagesPerSecondGlobal . ".";
                    $performanceMsg .= " ETA = " . sprintf('%02d:%02d:%02d', (int)($estimatedTimeLeft / 3600), (int)($estimatedTimeLeft / 60 % 60), (int)($estimatedTimeLeft % 60)) . ".";
                } else {
                    $estimatedTimeLeft = round($secondsPerImageGlobal * ($imagesToCreateGlobal - $imagesCreatedGlobal));
                    $performanceMsg .= " Seconds per image global = " . $secondsPerImageGlobal . ".";
                    $performanceMsg .= " ETA = " . sprintf('%02d:%02d:%02d', (int)($estimatedTimeLeft / 3600), (int)($estimatedTimeLeft / 60 % 60), (int)($estimatedTimeLeft % 60)) . ".";
                }

                echo "\n";
                echo "└ Iteration " . ($j + 1) . " of " . $this->payloads[$i]['iterations'] . " done. " . $performanceMsg . "\n";
            }
        }
    }
}
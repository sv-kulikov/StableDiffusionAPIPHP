<?php

namespace SvKulikov\StableDiffusionAPIPHP\Helpers;

class Config
{
    private string $csvFileName;
    private array $csvDataParsed;

    public function __construct(string $csvFileName)
    {
        $this->csvFileName = $csvFileName;
        $this->csvDataParsed = $this->parseScv($csvFileName);
    }

    private function parseScv(string $csvFileName): array
    {
        $temporaryStorage = [];
        $payloads = [];

        if (!is_file($csvFileName)) {
            throw new \Exception("[" . $csvFileName . "] does not exist or is inaccessible");
        }

        $csvFile = fopen($csvFileName, 'r');
        while (($csvLine = fgetcsv($csvFile)) !== false) {
            $temporaryStorage[] = $csvLine;
        }
        fclose($csvFile);
        array_shift($temporaryStorage);

        foreach ($temporaryStorage as $csvData) {
            $payload['iterations'] = $csvData[0];
            $payload['images_dir'] = $csvData[1];

            $payload['data']['prompt'] = $csvData[2];
            $payload['data']['negative_prompt'] = $csvData[3];
            $payload['data']['seed'] = $csvData[4];
            $payload['data']['subseed'] = $csvData[5];
            $payload['data']['subseed_strength'] = $csvData[6];
            $payload['data']['width'] = $csvData[7];
            $payload['data']['height'] = $csvData[8];
            $payload['data']['sampler_name'] = $csvData[9];
            $payload['data']['cfg_scale'] = $csvData[10];
            $payload['data']['steps'] = $csvData[11];
            $payload['data']['batch_size'] = $csvData[12];
            $payload['data']['restore_faces'] = $csvData[13];
            $payload['data']['seed_resize_from_w'] = $csvData[14];
            $payload['data']['seed_resize_from_h'] = $csvData[15];
            $payload['data']['denoising_strength'] = $csvData[16];
            $payload['data']['extra_generation_params'] = $csvData[17];
            $payload['data']['index_of_first_image'] = $csvData[18];
            $payload['data']['styles'] = $csvData[19];
            $payload['data']['clip_skip'] = $csvData[20];
            $payload['data']['is_using_inpainting_conditioning'] = $csvData[21];

            foreach ($payload['data'] as $k => $v) {
                if (strtolower($v) == 'false') {
                    $payload['data'][$k] = false;
                }
                if (strtolower($v) == 'true') {
                    $payload['data'][$k] = true;
                }
                if (strtolower($v) == '[]') {
                    $payload['data'][$k] = [];
                }
                if (is_numeric($v)) {
                    $payload['data'][$k] = (double)$v;
                }

            }

            $payloads[] = $payload;
        }

        return $payloads;
    }

    public function getCsvDataParsed(): array
    {
        return $this->csvDataParsed;
    }
}
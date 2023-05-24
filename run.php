<?php

// This is the only global setting. Mind your port number!!!
$url = 'http://127.0.0.1:7861/sdapi/v1/txt2img';

/* ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
   This is the internal config for quick usage.
   It is activated by "php run.php INTERNAL_CONFIG" command. */

$imagesDir = './images';

$imagesToCreate = 5;
$iterationsToRun = 2;

$payload = [
    "prompt" => "happy kitten",
    "negative_prompt" => "sad kitten",
    "seed" => -1,
    "subseed" => -1,
    "subseed_strength" => 0,
    "width" => 512,
    "height" => 512,
    "sampler_name" => "DPM++ 2M Karras",
    "cfg_scale" => 5.0,
    "steps" => 30,
    "batch_size" => $imagesToCreate,
    "restore_faces" => true,
    "seed_resize_from_w" => -1,
    "seed_resize_from_h" => -1,
    "denoising_strength" => 0,
    "extra_generation_params" => [],
    "index_of_first_image" => 0,
    "styles" => [],
    "clip_skip" => 1,
    "is_using_inpainting_conditioning" => false
];

/* -- End of config ---------------------------------------- */

use \SvKulikov\StableDiffusionAPIPHP\Helpers as Helpers;

spl_autoload_register(function ($className) {
    $baseDir = __DIR__ . '/class/';
    $classNameParts = explode("\\", $className);
    $fileName = $baseDir . end($classNameParts) . '.php';
    $fileName = str_replace(['\\', '//'], ['/', '/'], $fileName);
    if (is_file($fileName)) {
        require $fileName;
    }
});

if ($argc == 1) {
    exit('USAGE: "php run.php run.csv" or "php run.php INTERNAL_CONFIG"');
} elseif ($argv[1] == 'INTERNAL_CONFIG') {
    $payloadFinal[0] = [
        'iterations' => $iterationsToRun,
        'images_dir' => $imagesDir,
        'data' => $payload
    ];
    $stableDiffusion = new Helpers\StableDiffusion($payloadFinal, new Helpers\Curl($url), new Helpers\Image());
    $stableDiffusion->run();
} elseif (is_file($argv[1])) {
    $config = new Helpers\Config($argv[1]);
    $stableDiffusion = new Helpers\StableDiffusion($config->getCsvDataParsed(), new Helpers\Curl($url), new Helpers\Image());
    $stableDiffusion->run();
} else {
    exit('ERROR: the first parameter should be either INTERNAL_CONFIG, or CSV file name!');
}
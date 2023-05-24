<?php

namespace SvKulikov\StableDiffusionAPIPHP\Helpers;

class Curl
{
    private string $url;
    private $curl;

    public function __construct(string $url)
    {
        $this->url = $url;
        $this->initCurl();
    }

    public function __destruct()
    {
        curl_close($this->curl);
    }

    private function initCurl(): void
    {

    }

    public function run(string $postData): string
    {
        $this->curl = curl_init();
        curl_setopt($this->curl, CURLOPT_TIMEOUT, 10000);
        curl_setopt($this->curl, CURLOPT_CONNECTTIMEOUT, 10000);
        curl_setopt($this->curl, CURLOPT_NOPROGRESS, true);
        curl_setopt($this->curl, CURLOPT_VERBOSE, false);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_URL, $this->url);
        curl_setopt($this->curl, CURLOPT_POST, 1);
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $postData);
        return curl_exec($this->curl);
    }
}
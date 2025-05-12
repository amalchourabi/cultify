<?php

// src/Service/BadWordFilterService.php
namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class BadWordFilterService
{
    private $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function filterText(string $text): string
    {
        $url = 'https://www.purgomalum.com/service/json';
        $response = $this->httpClient->request('GET', $url, [
            'query' => [
                'text' => $text,
                'fill_char' => '*',
            ],
        ]);

        $data = $response->toArray();
        return $data['result'];
    }
}
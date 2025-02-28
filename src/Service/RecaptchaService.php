<?php
// src/Service/RecaptchaService.php
namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class RecaptchaService
{
    private $httpClient;
    private $secretKey;

    public function __construct(HttpClientInterface $httpClient, string $recaptchaSecretKey)
    {
        $this->httpClient = $httpClient;
        $this->secretKey = $recaptchaSecretKey;
    }

    public function verifyRecaptcha(string $recaptchaResponse): bool
    {
        $response = $this->httpClient->request('POST', 'https://www.google.com/recaptcha/api/siteverify', [
            'body' => [
                'secret' => $this->secretKey,
                'response' => $recaptchaResponse,
            ],
        ]);

        $data = $response->toArray();
        return $data['success'] ?? false;
    }
}
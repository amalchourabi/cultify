<?php
// src/Service/InfobipService.php
namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class InfobipService
{
    private $httpClient;
    private $apiKey;
    private $baseUrl;

    public function __construct(HttpClientInterface $httpClient, ParameterBagInterface $params)
    {
        $this->httpClient = $httpClient;
        $this->apiKey = $params->get('infobip.api_key'); 
        $this->baseUrl = $params->get('infobip.base_url'); 
    }

    public function sendSms(string $to, string $message): bool
    {
        $url = $this->baseUrl . '/sms/2/text/advanced';
        $headers = [
            'Authorization' => 'App ' . $this->apiKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];

        $body = [
            'messages' => [
                [
                    'destinations' => [['to' => $to]],
                    'from' => '447491163443', 
                    'text' => $message,
                ],
            ],
        ];

        try {
            $response = $this->httpClient->request('POST', $url, [
                'headers' => $headers,
                'json' => $body,
            ]);

            // Vérifier si l'envoi a réussi
            if ($response->getStatusCode() === 200) {
                return true;
            }
        } catch (\Exception $e) {
            // Gérer les erreurs (log, notification, etc.)
            error_log('Erreur lors de l\'envoi du SMS : ' . $e->getMessage());
        }

        return false;
    }
}
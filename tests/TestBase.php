<?php

declare(strict_types=1);

namespace Tests;

use CloudForest\ApiClient;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use RuntimeException;

class TestBase extends TestCase
{
    protected function getCloudForestClient()
    {
        $id = getenv('CLOUDFOREST_CLIENT_ID');
        $secret = getenv('CLOUDFOREST_CLIENT_SECRET');
        $host = getenv('CLOUDFOREST_HOST');
        $api = getenv('CLOUDFOREST_API');
        $client = new ApiClient($id, $secret, $host, $api);
        return $client;
    }

    protected function getHttpClient()
    {
        $api = getenv('CLOUDFOREST_API');
        $client = new Client(
            [
                'base_uri' => $api,
            ]
        );
        return $client;
    }

    public function getHeaders($access)
    {
        $id = getenv('CLOUDFOREST_CLIENT_ID');
        $h = ['x-api-key' => $id];
        if (strlen($access) > 0) {
            $h['Authorization'] = 'Bearer ' . $access;
        }
        return $h;
    }

    /**
     *
     * @return string|bool
     * @throws GuzzleException
     * @throws RuntimeException
     */
    protected function login() {
        $client = $this->getHttpClient();
        $email = getenv('CLOUDFOREST_EMAIL');
        $pass = getenv('CLOUDFOREST_PASS');
        if (!$email || !$pass) return false;
        $response = $client->request(
            'POST',
            '/api/jwts/login',
            [
                'json' =>
                [
                    'email' => $email,
                    'password' => $pass,
                    'recaptcha' => '123',
                ],
                'headers' => $this->getHeaders(''),
            ]
        );
        $json = $response->getBody()->getContents();
        $contents = json_decode($json, true);
        $access = $contents['data']['access'];
        return $access;
    }
}

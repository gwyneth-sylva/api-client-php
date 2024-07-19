<?php

declare(strict_types=1);

namespace CloudForest\ApiClientPhp\Modules;

use GuzzleHttp\Exception\GuzzleException;

/**
 * The client for the CloudForest Token API module.
 *
 * @package CloudForest\ApiClient\Modules
 */
class TokenModule extends ApiModuleBase
{
    /**
     * @see    ApiBase for documentation.
     * @param  string $id
     * @param  string $secret
     * @param  string $host
     * @param  string $api
     * @return void
     */
    public function __construct(string $id, string $secret, string $host, string $api)
    {
        parent::__construct($id, $secret, $host, $api);
    }

    /**
     * Exchange a temp token received from CloudForest for a JWT. The JWT
     * contains an access token and a refresh token.
     *
     * @param  mixed $tempToken
     * @return array <string, string> An array containing the key 'access' with the access token
     */
    public function exchange($tempToken)
    {
        try {
            $response = $this->client->request(
                'PATCH',
                '/api/tokens/oauth/' . $tempToken,
                [
                    'json' =>
                    [
                        'clientId' => $this->id,
                        'clientSecret' => $this->secret,
                    ],
                    'headers' => $this->getHeadersWithAccessBearer(),
                ]
            );
        } catch (GuzzleException $e) {
            throw $e;
        }

        $body = $response->getBody()->getContents();
        $content = json_decode($body, true);
        $data = $this->getDataAsArray($content);
        if (array_key_exists('access', $data) && array_key_exists('refresh', $data) && is_string($data['access']) && is_string($data['refresh'])) {
            return [
                'access' => $data['access'],
                'refresh' => $data['refresh'],
            ];
        }
        throw new \Exception('Token->exchange: invalid JWT token returned from CloudForest');
    }
}

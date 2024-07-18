<?php

declare(strict_types=1);

namespace CloudForest\ApiClientPhp\Modules;

use GuzzleHttp\Exception\GuzzleException;

/**
 * The client for the CloudForest JWT API module.
 *
 * @package CloudForest
 */
class JwtModule extends ApiModuleBase
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
     * Validate a refresh token.
     *
     * @param  string $refresh The refresh token to validate
     * @return void
     * @throws GuzzleException 401 if not valid
     */
    public function validateRefresh(string $refresh)
    {
        $h = $this->headers;
        $h['Authorization'] = 'Bearer ' . $refresh;
        try {
            $this->client->request(
                'GET',
                '/api/jwts/refresh/validate',
                [
                    'headers' => $h,
                ]
            );
        } catch (GuzzleException $e) {
            throw $e;
        }
    }

    /**
     * Use a refresh token to get an access token.
     *
     * @param  string $refresh The refresh token
     * @return mixed The JWT containing the access token
     * @throws GuzzleException
     */
    public function refresh(string $refresh)
    {
        $h = $this->headers;
        $h['Authorization'] = 'Bearer ' . $refresh;
        try {
            $response = $this->client->request(
                'POST',
                '/api/jwts/refresh',
                [
                    'headers' => $h,
                ]
            );
        } catch (GuzzleException $e) {
            throw $e;
        }

        $body = $response->getBody()->getContents();
        $content = json_decode($body, true);
        $data = $this->getDataAsArray($content);
        return $data;
    }
}

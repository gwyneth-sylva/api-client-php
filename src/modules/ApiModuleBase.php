<?php

declare(strict_types=1);

namespace CloudForest\ApiClientPhp\Modules;

use GuzzleHttp\Client;

/**
 * ApiModuleBase defines the shape of an API module.
 *
 * @package CloudForest\ApiClient\Modules
 */
class ApiModuleBase
{
    /**
     * @var string
     */
    protected $id = '';

    /**
     * @var string
     */
    protected $secret = '';

    /**
     * @var string
     */
    protected $host = '';

    /**
     * @var string
     */
    protected $api = '';

    /**
     * @var string
     */
    private $access = '';

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var mixed[]
     */
    protected $headers;

    /**
     * @param  string $id     The API key issued by CloudForest.
     * @param  string $secret The API secret issued by CloudForest.
     * @param  string $host   The address of the CloudForest web host
     * @param  string $api    The address of the CloudForest API server.
     * @return void
     */
    public function __construct(string $id, string $secret, string $host, string $api)
    {
        $this->id = $id;
        $this->secret = $secret;
        $this->host = $host;
        $this->api = $api;

        $this->headers = ['x-api-key' => $this->id];

        $this->client = new Client(
            [
                'base_uri' => $api,
            ]
        );
    }

    /**
     * Setter for the access token.
     *
     * @return void
     */
    public function setAccess(string $access)
    {
        $this->access = $access;
    }

    /**
     * Get the headers for a request. This includes x-api-key to authorise this
     * client against the API and if set a Bearer token to supply the user's
     * credentials.
     *
     * @return array<string>
     */
    public function getHeadersWithAccessBearer()
    {
        $h = $this->headers;
        if (strlen($this->access) > 0) {
            $h['Authorization'] = 'Bearer ' . $this->access;
        }
        return $h;
    }

    /**
     * Extract the data property from the mixed return of json_decode in a safe
     * way for phpstan analysis. Use this when you expect the JSON data to be
     * an array.
     * @param mixed $content
     * @return array<string, JsonType>
     * @throws Exception
     */
    public function getDataAsArray($content)
    {
        if (is_array($content) && array_key_exists('data', $content)) {
            $data = $content['data'];
            if (is_array($data)) {
                return $data;
            }
        }

        throw new \Exception('ApiModuleBase->getDataAsArray: decoded JSON does not have a valid data property');
    }

    /**
     * Extract the data property from the mixed return of json_decode in a safe
     * way for phpstan analysis. Use this when you expect the JSON data to be
     * a string.
     * @param mixed $content string
     * @return string
     * @throws Exception
     */
    public function getDataAsString($content)
    {
        if (is_array($content) && array_key_exists('data', $content)) {
            $data = $content['data'];
            if (is_string($data)) {
                return $data;
            }
        }

        throw new \Exception('ApiModuleBase->getDataAsString: decoded JSON does not have a valid data property');
    }

    /**
     * Extract the included property from the mixed return of json_decode in a
     * safe way for phpstan analysis.
     * @param mixed $content
     * @return array<string, JsonType>
     * @throws Exception
     */
    public function getIncluded($content)
    {
        if (is_array($content) && array_key_exists('included', $content)) {
            $included = $content['included'];
            if (is_array($included)) {
                return $included;
            }
        }

        throw new \Exception('ApiModuleBase->getIncluded: decoded JSON does not have a valid included property');
    }
}

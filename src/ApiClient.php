<?php

declare(strict_types=1);

namespace CloudForest\ApiClientPhp;

use CloudForest\ApiClientPhp\Modules\JwtModule;
use CloudForest\ApiClientPhp\Modules\ListingModule;
use CloudForest\ApiClientPhp\Modules\TokenModule;

/**
 * ApiClient creates a client for using the CloudForest API. It defines some
 * config helpers and create these API modules:
 *
 * - Token: for using CloudForest tokens API module
 * - JWT: for using the CloudForest jwts API module
 * - Listing: for using CloudForest listings API module
 *
 * @package CloudForest
 */
class ApiClient
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
     * @var TokenModule
     */
    public $token;

    /**
     * @var ListingModule
     */
    public $listing;

    /**
     * @var JwtModule
     */
    public $jwt;

    /**
     * @see    Create the API client and its modules.
     * @param  string $id     The API key issued by CloudForest.
     * @param  string $secret The API secret issued by CloudForest.
     * @param  string $host   The address of the CloudForest web host.
     * @param  string $api    The address of the CloudForest API server.
     * @return void
     */
    public function __construct(string $id, string $secret, string $host, string $api)
    {
        $this->id = $id;
        $this->secret = $secret;
        $this->host = $host;
        $this->api = $api;
        $this->token = new TokenModule($id, $secret, $host, $api);
        $this->listing = new ListingModule($id, $secret, $host, $api);
        $this->jwt = new JwtModule($id, $secret, $host, $api);
    }

    /**
     * Get the URL that begins a connection with CloudForest. The callback is
     * urlencoded here, so does not need encoding in your consumer.
     *
     * @param string $callback The URL you want CloudForest to return to when.
     *
     * @return string The URL.
     */
    public function getConnectionURL(string $callback)
    {
        $connectionUrl = $this->host
            . '/connect/begin?clientId='
            . $this->id
            . '&callback='
            . urlencode($callback);
        return $connectionUrl;
    }

    /**
     * Get the marketplace URL. This is just the host passed in when creating
     * the API client, but it provides syntactic sugar for the consumer.
     * @return string The URL.
     */
    public function getMarketplaceURL()
    {
        return $this->host;
    }

    /**
     * Setter for the access token.
     *
     * @param  string $access The access token.
     * @return void
     */
    public function setAccess(string $access)
    {
        $this->listing->setAccess($access);
    }
}

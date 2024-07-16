<?php

namespace CloudForest;

use Illuminate\Http\Client\RequestException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

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
     * @var Token
     */
    public $token;

    /**
     * @var Listing
     */
    public $listing;

    /**
     * @var Jwt
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
        $this->token = new Token($id, $secret, $host, $api);
        $this->listing = new Listing($id, $secret, $host, $api);
        $this->jwt = new Jwt($id, $secret, $host, $api);
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
     *
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

/**
 * ApiModuleBase defines the shape of an API module.
 *
 * @package CloudForest
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
            'base_uri' => $api
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
     * @return array
     */
    public function getHeadersWithAccessBearer()
    {
        $h = $this->headers;
        if (strlen($this->access) > 0) {
            $h['Authorization'] = 'Bearer ' . $this->access;
        }
        return $h;
    }
}

/**
 * The client for the CloudForest Token API module.
 *
 * @package CloudForest
 */
class Token extends ApiModuleBase
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
     * Exchange a temp token received from CloudForest for a JWT.
     *
     * @param  mixed $tempToken
     * @return array An array containing the key 'access' with the access token
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
                    'clientSecret' => $this->secret
                ],
                'headers' => $this->getHeadersWithAccessBearer()
                ]
            );
        } catch (GuzzleException $e) {
            throw $e;
        }

        $body = $response->getBody();
        $content = json_decode($body, true);
        return $content['data'];
    }
}

/**
 * The client for the CloudForest Listing API module.
 *
 * @package CloudForest
 */
class Listing extends ApiModuleBase
{
    public function __construct($id, $secret, $host, $api)
    {
        parent::__construct($id, $secret, $host, $api);
    }

    /**
     * Create a listing via the CloudForest API with the specified title
     *
     * This takes care of looking up the user information required to create a
     * listing.
     *
     * @todo Expose more properties such as description, latitidue and
     * longitude. This can be done in July 2024.
     *
     * @param  ListingDto $listing The new listing
     * @return mixed The new listing as an associative array
     * @throws RequestException
     */
    public function create(ListingDto $listing)
    {
        try {
            $response = $this->client->request(
                'GET',
                '/api/current-user',
                [
                'headers' => $this->getHeadersWithAccessBearer()
                ]
            );
        } catch (GuzzleException $e) {
            throw $e;
        }

        $body = $response->getBody();
        $content = json_decode($body, true);
        $userId = $content['data']['id'];
        $companyId = count($content['included']['employments']) > 0
            ? $content['included']['employments'][0]['companyId']
            : '';

        try {
            $listing->userId = $userId;
            $listing->companyId = $companyId;
            $response = $this->client->request(
                'POST',
                '/api/listings/',
                [
                // Convert to assoc array
                'json' => json_decode(json_encode($listing), true),
                'headers' => $this->getHeadersWithAccessBearer()
                ]
            );
        } catch (GuzzleException $e) {
            throw $e;
        }

        $body = $response->getBody();
        $content = json_decode($body, true);
        return $content['data'];
    }
}

/**
 * The client for the CloudForest JWT API module.
 *
 * @package CloudForest
 */
class Jwt extends ApiModuleBase
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
                'headers' => $h
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
                'headers' => $h
                ]
            );
        } catch (GuzzleException $e) {
            throw $e;
        }

        $body = $response->getBody();
        $content = json_decode($body, true);
        return $content['data'];
    }
}

enum ListingState: string
{
    case DRAFT = 'DRAFT';
    case OPEN = 'OPEN';
    case CLOSED = 'CLOSED';
}

/**
 * ListingDto defines the shape of the listing data used by the CloudForest API.
 *
 * @package CloudForest
 */
class ListingDto
{
    /**
     * The primary key of the listing as a UUID. Use an empty string to create
     * a new one.
     *
     * @var string
     **/
    public $id = '';

    /**
     * The state of the listing.
     * DRAFT = Only visible to owner, cannot be used in transations.
     * OPEN = Visible to the public
     * CLOSED = Only visible to owner, still usable in transactions.
     * Currently DRAFT listings are not fully supported so only create listings
     * in an OPEN state.
     *
     * @var value-of<ListingState>
     */
    public $state = ListingState::OPEN;

    /**
     * The title of the listing.
     *
     * @var string
     */
    public $title = '';

    /**
     * A description of the listing.
     *
     * @var string
     */
    public $description = 'A testing listing created from the CloudForest PHP ApiClient';

    /**
     * The units in which the listing is available as a freeform string. EG
     * "tonnes", "m^3".
     *
     * @var string
     */
    public $units = 'tonnes';

    /**
     * The amount of stock of the units as an integer. EG if units is m^3 and
     * there are 10 m^3 for sale then the stock is 10.
     *
     * @var int
     */
    public $stock = 1;

    /**
     * The latitude of a point representing the location of the listing. This
     * does not necessarily have to be exact, eg you could protect a valuable
     * resource by supplying the office handling the sale. It is passed
     * as a float representing decimal degrees, eg 53.12
     *
     * @var float
     */
    public $latitude = 53.12;

    /**
     * The longitude of a point representing the location of the listing.
     *
     * @see $latitude
     * @var float
     */
    public $longitude = -2.1;

    /**
     * Does this listing require a felling licence?
     *
     * @var bool
     */
    public $licenceRequired = false;

    /**
     * If a licence is required or if a licence exists then the number can be
     * supplied here as a string, on the assumption the 'number' will contain
     * characters.
     *
     * @var string
     */
    public $licenceNumber = '';

    /**
     * The categories the listing should appear within. In theory this supports
     * multiple categories. In practice you should set just one for now. Each
     * category is an integer ID into the CloudForest category list. Currently
     * this is not available through the ApiClient. For now, use 18 for
     * 'Standing Timber' or ask CloudForest for alternative IDs if you need a
     * different category.
     *
     * @todo Expose categories through Api to ApiClient for reuse
     * @var  array<int>
     */
    public $categories = [18];

    /**
     * The facets that describe the listing. For example, species=oak. Each
     * facet is a UUID string representing the ID of a facet created through the
     * API for this listing. This is not yet supported through the ApiClient.
     * The flow would be:
     * 1. Create a facet (eg species=oak)
     * 2. Get back a facet DTO with a UUID
     * 3. Add the UUID to this list
     *
     * @todo Implement facet support in the ApiClient
     * @var  array<string>
     */
    public $facets = [];

    /**
     * The images uploaded for the listing. Each image is a UUID string
     * representing the ID of an image uploaded through the API for this
     * listing. This is not yet supported through the ApiClient. The flow would
     * be:
     * 1. Upload an image
     * 2. Get back a image DTO with a UUID
     * 3. Add the UUID to this list
     *
     * @todo Implement image support in the ApiClient
     * @var  array<string>
     */
    public $images = [];

    /**
     * The id of the user who owns the listing as a UUID. This will be set by
     * the ApiClient from the JWT access token.
     *
     * @var string
     */
    public $userId = '';

    /**
     * The id of the company of the user who owns the listing as a UUID. This
     * will be set by the ApiClient from the JWT access token.
     *
     * @var string
     */
    public $companyId = '';

    public function __construct()
    {
        $this->title = 'Test Listing ' . date("Y-m-d H:i:s");
    }
}

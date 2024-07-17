<?php

declare(strict_types=1);

use CloudForest\ApiClient;
use PHPUnit\Framework\TestCase;

final class ApiClientTest extends TestCase
{
    public function testGetMarketplaceUrl(): void
    {
        $host = getenv('CLOUDFOREST_HOST');
        $client = new ApiClient('', '', $host, '');
        $url = $client->getMarketplaceURL();
        $this->assertSame($host, $url);
    }
}

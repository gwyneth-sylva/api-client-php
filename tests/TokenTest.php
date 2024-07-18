<?php

declare(strict_types=1);

namespace Tests;

use Tests\TestBase;

final class TokenTest extends TestBase
{
    public function testExchange(): void
    {
        $access = $this->login();
        $this->assertIsString($access);
        $this->assertNotEmpty($access);
    }
}

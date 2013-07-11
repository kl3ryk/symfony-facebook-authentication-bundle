<?php

namespace Laelaps\Bundle\FacebookAuthentication\Tests\Security;

use Laelaps\Bundle\FacebookAuthentication\Tests\KernelTestCase;

class FacebookAuthenticationProviderTest extends KernelTestCase
{
    public function testThat()
    {
        $client = $this->getClient();
        $client->request('GET', '/');
    }
}

<?php

namespace Laelaps\Bundle\FacebookAuthentication;

use Laelaps\Bundle\FacebookAuthentication\Tests\KernelTestCase;

class FacebookEntryPointTest extends KernelTestCase
{
    public function testThatRedirectResponseIsReturnedByDefault()
    {
        $client = $this->getClient();
        $client->request('GET', '/');

        $this->assertInstanceOf('Laelaps\Bundle\FacebookAuthentication\FacebookLoginUrlRedirectResponse', $client->getResponse());
    }
}

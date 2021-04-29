<?php

namespace App\Tests\Acceptation\Root\Page;

trait AdminMediaIndexTrait
{

    public function testLoginProtected()
    {
        $client = $this->getBrowser();
        $crawler = $client->request('GET', '/admin/media');
        $url = $this->getAppUrl('/en/login');
        $this->assertEquals(
            $url,
            $crawler->getUri(),
            'Response is a redirection to the /en/login page.'
        );
    }
}
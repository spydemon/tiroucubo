<?php

namespace App\Tests\Acceptation\Root\Page;

trait AdminPathIndexTrait
{
    public function testLoginProtected()
    {
        $client = $this->getBrowser();
        $crawler = $client->request('GET', '/admin/path');
        $url = $this->getAppUrl('/en/login');
        $this->assertEquals(
            $url,
            $crawler->getUri(),
            'Response is a redirection to the /en/login page.'
        );
    }

    public function testLinkPresenceInAdminMenu()
    {
        $this->loginCustomer('admin@tiroucubo.local', 'pa$$word');
        $client = $this->getBrowser();
        $client->request('GET', '/admin/dashboard');
        $pathsLink = $this->getElementByLinkText('Paths');
        $pathsLink->click();
        $url = $this->getAppUrl('/admin/path');
        $this->assertEquals(
            $url,
            $this->getBrowser()->getWebDriver()->getCurrentUrl(),
            'Link to the admin/path controller is existing in the admin menu.'
        );
    }
}

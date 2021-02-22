<?php

namespace App\Tests\Acceptation\IntegrationAbstract;
namespace App\Tests\Acceptation\Root\Page;

trait AdminDashboardTrait
{
    public function testLoginProtectedTest()
    {
        $client = $this->getBrowser();
        $crawler = $client->request('GET', '/admin/dashboard');
        $url = $this->getAppUrl('/en/login');
        $this->assertEquals(
            $url,
            $crawler->getUri(),
            'Response is a redirection to the /en/login page.'
        );
    }
}

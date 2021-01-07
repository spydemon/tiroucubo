<?php

namespace App\Tests\Integration\IntegrationAbstract;
namespace App\Tests\Integration\Root\Page;

trait AdminDashboardTrait
{
    public function testLoginProtectedTest()
    {
        $client = $this->getClient();
        $crawler = $client->request('GET', '/admin/dashboard');
        $url = $this->getAppUrl('/en/login');
        $this->assertEquals(
            $url,
            $crawler->getUri(),
            'Response is a redirection to the /en/login page.'
        );
    }
}

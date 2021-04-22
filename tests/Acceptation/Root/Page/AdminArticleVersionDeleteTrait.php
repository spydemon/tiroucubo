<?php

namespace App\Tests\Acceptation\Root\Page;

trait AdminArticleVersionDeleteTrait
{

    public function testLoginProtected()
    {
        $client = $this->getBrowser();
        $crawler = $client->request('GET', '/admin/article_version/delete/1/csrf/xxx');
        $url = $this->getAppUrl('/en/login');
        $this->assertEquals(
            $url,
            $crawler->getUri(),
            'Response is a redirection to the /en/login page.'
        );
    }

    public function testCsrfProtected()
    {
        $client = $this->getBrowser();
        $this->loginCustomer('admin@tiroucubo.local', 'pa$$word');
        $client->request('GET', '/admin/article_version/delete/6/csrf/plop');
        $notification = $this->getElementByCssSelector('.notification .error');
        $this->assertEquals(
            'Invalid CSRF token.',
            $notification->getText(),
            'The notification saying that the CSRF token is not valid is displayed.'
        );
        $versions = $this->getAllElementsByCssSelector('td.commit');
        $this->assertEquals(
            2,
            count($versions),
            'Versions of the article are still existing.'
        );
    }

    public function testNotAbleToDeleteActiveVersion()
    {
        $client = $this->getBrowser();
        $this->loginCustomer('admin@tiroucubo.local', 'pa$$word');
        $client->request('GET', '/admin/article/edit/6');
        // Quite ugly trick that allow us to get a deleting link with a valid CSRF token.
        $deleteLinkForTheInactiveVersion = $this->getElementByCssSelector('.action-delete a')->getAttribute('href');
        // Now, we change the ID of the version to remove in the original link with the one that represent the currently
        // active version. Note: this one is hardcoded.
        $deleteLinkForTheActiveVersion = preg_replace('#/delete/(\d+)/#', '/delete/6/', $deleteLinkForTheInactiveVersion);
        $client->request('GET', $deleteLinkForTheActiveVersion);
        $notification = $this->getElementByCssSelector('.notification .error');
        $this->assertEquals(
            'An active article version can not be deleted.',
            $notification->getText(),
            'The notification saying that an active version can not be deleted is displayed.'
        );
        $versions = $this->getAllElementsByCssSelector('td.commit');
        $this->assertCount(
            2,
            $versions,
            'Versions of the article are still existing.'
        );
    }

    public function testVersionDelete()
    {
        $client = $this->getBrowser();
        $this->loginCustomer('admin@tiroucubo.local', 'pa$$word');
        $client->request('GET', '/admin/article/edit/6');
        $deleteLinkForTheInactiveVersion = $this->getElementByCssSelector('.action-delete a');
        $deleteLinkForTheInactiveVersion->click();
        $client->switchTo()->alert()->accept();
        $notification = $this->getElementByCssSelector('.notification .notice');
        $this->assertRegExp(
            '/^The version .* was deleted.$/',
            $notification->getText(),
            'Suppression notice seems correctly displayed.'
        );
        $versions = $this->getAllElementsByCssSelector('td.commit');
        $this->assertCount(
            1,
            $versions,
            'The version of the article was correctly deleted.'
        );
        $this->resetDatabase();
    }
}
<?php

namespace App\Tests\Integration\Root\Page;

use App\Tests\Exception\ElementNotExpectedException;

trait FragmentLeftMenuTrait
{
    public function testMenuGenerationEnglishTest()
    {
        $this->goToUrl('/en/magento');
        $menuEntry = $this->getElementByLinkText('All about customers');
        $menuEntry->click();
        $this->assertEquals(
            $this->getAppUrl('/en/magento/use-of-the-cms/all-about-customers'),
            $this->getBrowser()->getCurrentURL(),
            'Left menu seems correctly generated and links on it are working.'
        );
        $this->assertEquals(
            200,
            $this->getBrowser()->getInternalResponse()->getStatusCode(),
            'Page corresponding to generated link in the left menu exists.'
        );
    }

    public function testMenuGenerationFrenchTest()
    {
        $this->goToUrl('/fr/magento');
        $menuEntry = $this->getElementByLinkText('Tout à propos des clients');
        $menuEntry->click();
        $this->assertEquals(
            $this->getAppUrl('/fr/magento/utilisation-du-cms/tout-a-propos-des-clients'),
            $this->getBrowser()->getCurrentURL(),
            'Left menu seems correctly generated and links on it are working.'
        );
        $this->assertEquals(
            200,
            $this->getBrowser()->getInternalResponse()->getStatusCode(),
            'Page corresponding to generated link in the left menu exists.'
        );
    }

    public function testMenuWithDisabledPages()
    {
        $this->goToUrl('/fr/magento');
        try {
            $this->getElementByLinkText('Tout à propos des clients');
        } catch (ElementNotExpectedException $e) {
            $this->fail('Menu link "Tout à propos des clients" exists.');
        }
        try {
            $this->getElementByLinkText('Utilisation du CMS');
        } catch (ElementNotExpectedException $e) {
            $this->fail('Menu link "Utilisation du CMS" exists.');
        }
        $this->loginCustomer('admin@tiroucubo.local', 'pa$$word');
        $this->goToUrl('/admin/article/edit/6');
        $deactivationLink = $this->getElementByCssSelector('.admin-article-edit table.version .active .is-active');
        $deactivationLink->click();
        $this->goToUrl('/fr/magento');
        try {
            $this->getElementByLinkText('Tout à propos des clients');
            $this->fail('Menu link "Tout à propos des clients" is hidden.');
        } catch (ElementNotExpectedException $e) {
        }
        try {
            $this->getElementByLinkText('Utilisation du CMS');
            $this->fail('Menu link "Utilisation du CMS" is hidden.');
        } catch (ElementNotExpectedException $e) {
        }
        $this->assertTrue(true, 'The test did not fail.');
    }
}

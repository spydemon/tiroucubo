<?php

namespace App\Tests\Integration\Root\Page;

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
}

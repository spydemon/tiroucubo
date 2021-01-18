<?php

namespace App\Tests\Integration\Root\Page;

trait FragmentLeftMenuTrait
{
    public function testMenuGenerationTest()
    {
        $this->goToUrl('/en');
        $menuEntry = $this->getElementByLinkText('All about customers');
        $menuEntry->click();
        $this->assertEquals(
            'http://tiroucubo.local/en/magento/use-of-the-cms/all-about-customers',
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

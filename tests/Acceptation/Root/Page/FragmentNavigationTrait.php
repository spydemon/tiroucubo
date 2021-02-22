<?php

namespace App\Tests\Acceptation\Root\Page;

trait FragmentNavigationTrait
{
    public function testNavigationLinksForAnonymousUserTest()
    {
        $this->goToUrl('/en');
        $hasAdminLink = $this->hasAdminLink();
        $hasLogoutLink = $this->hasLogoutLink();
        $this->assertFalse(
            $hasAdminLink,
            'Admin dashboard link is not visible for anonymous users.'
        );
        $this->assertFalse(
            $hasLogoutLink,
            'Logout link is not visible for anonymous users.'
        );
    }

    public function testNavigationLinksForLoggedNormalUserTest()
    {
        $this->loginCustomer('user@tiroucubo.local', 'pa$$word');
        $hasAdminLink = $this->hasAdminLink();
        $hasLogoutLink = $this->hasLogoutLink();
        $this->assertFalse(
            $hasAdminLink,
            'Admin dashboard link is not visible for logged normal users.'
        );
        $this->assertTrue(
            $hasLogoutLink,
            'Logout link is visible for logged normal users.'
        );
    }

    public function testNavigationLinksForLoggedAdminUserTest()
    {
        $this->loginCustomer('admin@tiroucubo.local', 'pa$$word');
        $hasAdminLink = $this->hasAdminLink();
        $hasLogoutLink = $this->hasLogoutLink();
        $this->assertTrue(
            $hasAdminLink,
            'Admin dashboard link is visible for logged admin.'
        );
        $this->assertTrue(
            $hasLogoutLink,
            'Logout link is visible for normal logged admin.'
        );
    }

    public function testTranslationLinkTest()
    {
        $this->goToUrl($this->getAppUrl('/en/magento/use-of-the-cms/all-about-customers'));
        $translationButton = $this->getElementByCssSelector('header > .navigation > nav > .item.lang');
        $translationButton->click();
        $this->assertEquals(
            $this->getAppUrl('/fr/magento/utilisation-du-cms/tout-a-propos-des-clients'),
            $this->getBrowser()->getCurrentURL(),
            'Translation en to fr is working.'
        );
        $translationButton = $this->getElementByCssSelector('header > .navigation > nav > .item.lang');
        $translationButton->click();
        $this->assertEquals(
            $this->getAppUrl('/en/magento/use-of-the-cms/all-about-customers'),
            $this->getBrowser()->getCurrentURL(),
            'Translation fr to en is working.'
        );
    }

    public function testTranslationLinkFallbackTest()
    {
        $this->goToUrl($this->getAppUrl('/en/not-existing'));
        $translationButton = $this->getElementByCssSelector('header > .navigation > nav > .item.lang');
        $translationButton->click();
        $this->assertEquals(
            $this->getAppUrl('/fr'),
            $this->getBrowser()->getCurrentURL(),
            'Fallback translation en to fr is working.'
        );
        $this->goToUrl($this->getAppUrl('/fr/non-existant'));
        $translationButton = $this->getElementByCssSelector('header > .navigation > nav > .item.lang');
        $translationButton->click();
        $this->assertEquals(
            $this->getAppUrl('/en'),
            $this->getBrowser()->getCurrentURL(),
            'Fallback translation fr to en is working.'
        );
    }

    protected function hasAdminLink() : bool
    {
        return $this->getElementByCssSelector('header > .navigation > nav > .item.admin', false)
            ? true
            : false;
    }

    protected function hasLogoutLink() : bool
    {
        return $this->getElementByCssSelector('header > .navigation > nav > .item.logout', false)
            ? true
            : false;
    }
}

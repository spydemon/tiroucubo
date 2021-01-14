<?php

namespace App\Tests\Integration\Root\Page;

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

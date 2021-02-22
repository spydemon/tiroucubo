<?php

namespace App\Tests\Acceptation\Root\Page;

trait FrontSecurityLoginTrait
{
    public function testLoginFormSubmissionInputTypesTest()
    {
        $this->goToUrl('/en/login');
        $inputEmail = $this->getElementByCssSelector('#inputEmail');
        $inputPassword = $this->getElementByCssSelector('#inputPassword');
        $this->assertEquals(
            'email',
            $inputEmail->getAttribute('type'),
            'Email input has the "email" type.'
        );
        $this->assertEquals(
            'password',
            $inputPassword->getAttribute('type'),
            'Password input has the "password" type.'
        );
    }

    /**
     * @depends testLoginFormSubmissionInputTypesTest
     */
    public function testLoginFormSubmissionWithNonExistentEmailTest()
    {
        $this->loginCustomer('nonexistant@tiroucubo.local', 'pa$$word');
        $flashError = $this->getElementByCssSelector('.flash.error');
        $this->assertEquals(
            'Invalid user or password provided.',
            $flashError->getText(),
            'Correct error message if login fails because of an not existing email.'
        );
    }

    /**
     * @depends testLoginFormSubmissionWithNonExistentEmailTest
     */
    public function testLoginFormSubmissionWithInvalidPasswordTest()
    {
        $this->loginCustomer('nonexistant@tiroucubo.local', 'wrong_password');
        $flashError = $this->getElementByCssSelector('.flash.error');
        $this->assertEquals(
            'Invalid user or password provided.',
            $flashError->getText(),
            'Correct error message if login fails because of an invalid email.'
        );
    }

    /**
     * @depends testLoginFormSubmissionWithInvalidPasswordTest
     */
    public function testLoginFormSubmissionWithValidCredentialsTest()
    {
        $this->loginCustomer('admin@tiroucubo.local', 'pa$$word');
        $this->getElementByCssSelector('header > .navigation > nav > .item.logout');
        $this->assertTrue(true, 'Login with correct credentials work.');
    }
}


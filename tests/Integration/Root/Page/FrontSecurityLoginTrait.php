<?php

namespace App\Tests\Integration\Root\Page;

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
        $this->goToUrl('/en/login');
        $this->fillLoginForm('nonexistant@tiroucubo.local', 'pa$$sword');
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
        $this->goToUrl('/en/login');
        $this->fillLoginForm('admin@tiroucubo.local', 'wrong_password');
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
    public function testLoginFormSubmissionWithValidCredentials()
    {
        $this->goToUrl('/en/login');
        $this->fillLoginForm('admin@tiroucubo.local', 'pa$$word');
        $this->getElementByCssSelector('ul > li > a[href="/en/logout"]');
        $this->assertTrue(true, 'Login with correct credentials work.');
    }

    protected function fillLoginForm(string $email, string $password) : void
    {
        $inputEmail = $this->getElementByCssSelector('#inputEmail');
        $inputPassword = $this->getElementByCssSelector('#inputPassword');
        $submitButton = $this->getElementByCssSelector('button[type="submit"]');
        $inputEmail->sendKeys($email);
        $inputPassword->sendKeys($password);
        $submitButton->click();
    }
}


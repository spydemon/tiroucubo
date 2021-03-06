<?php

namespace App\Tests\Acceptation;

use App\Tests\Exception\ElementNotExpectedException;
use App\Tests\Extension\PrepareDatabase;
use Exception;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverElement;
use Symfony\Component\Panther\Client;
use Symfony\Component\Panther\PantherTestCase;

/**
 * Class IntegrationAbstract
 *
 * This class should be extended on each integration tests.
 */
abstract class AcceptationAbstract extends PantherTestCase
{
    private ?Client $client = null;
    private ?string $envBaseUri = null;
    private ?string $envSeleniumUri = null;
    private string $browserLang = 'en';

    abstract protected function getBrowserWidth() : int;

    /**
     * @param null $name
     * @param array $data
     * @param string $dataName
     * @throws Exception
     */
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->setConfiguration();
    }

    /**
     * Changing the browser language will also update the "Accept-Language" HTTP header send to each queries.
     * This is usefully for translation tests.
     */
    protected function changeBrowserLang(string $lang) : void
    {
        $this->browserLang = $lang;
        if ($this->client) {
            $this->client = null;
        }
    }

    /**
     * It looks like Selenium WebDriver is not able to deal with HTTP response code. :-/
     * https://github.com/symfony/panther/issues/67
     * We thus have to do this hack for trying to guess if we are displaying a 404 error page or not.
     */
    protected function checkResponseIsA404() : void
    {
        try {
            $this->getElementByCssSelector('article.error404');
        } catch (ElementNotExpectedException $e) {
            $this->fail('Displaying the 404 error page.');
        }
    }

    protected function closeBrowser() : void
    {
        if (is_null($this->client)) {
            return;
        }
        $this->client->close();
        $this->client->quit();
        $this->client = null;
    }

    protected function getAppUrl($path) : string
    {
        return $this->envBaseUri . $path;
    }

    protected function getBrowser() : Client
    {
        if (is_null($this->client)) {
            $this->client = $this->generateClient();
        }
        return $this->client;
    }

    /**
     * We set an arbitrary high height for the browser size in order to avoid the "element click is not clickable" error
     * throw when we ask to the webdriver to click on an element that is not visible on the screen.
     */
    protected function getBrowserHeight() : int
    {
        return 10000;
    }

    protected function getAllElementsByCssSelector(string $selector) : ?array
    {
        return $this->getElements(WebDriverBy::cssSelector($selector));
    }

    protected function getAllElementsByLinkText(string $text) : ?array
    {
        return $this->getElements(WebDriverBy::partialLinkText($text));
    }

    protected function getElementByCssSelector(string $selector, bool $fatal = true) : ?WebDriverElement
    {
        $results = $this->getAllElementsByCssSelector($selector);
        if (count($results) > 1) {
            throw new ElementNotExpectedException('More than one result fetched with the css selector.', 3);
        } elseif (count($results) == 1) {
            return $results[0];
        }
        if ($fatal) {
            throw new ElementNotExpectedException('No item found by the selector.', 4);
        }
        return null;
    }

    protected function getElementByLinkText(string $text, bool $fatal = true) : ?WebDriverElement
    {
        $results = $this->getAllElementsByLinkText($text);
        if (count($results) > 1) {
            throw new ElementNotExpectedException('More than one result fetched with the text selector.', 1);
        } elseif (count($results) == 1) {
            return $results[0];
        }
        if ($fatal) {
            throw new ElementNotExpectedException('No item found by the selector.', 2);
        }
        return null;
    }

    /**
     * Will set the internal browser to the provided $url. Note that the $url should not be in a fully qualified form.
     * Eg: if $url = '/en/login', the browser will load the page "http(s)://<site_root>/en/login."
     *
     * @param $url
     */
    protected function goToUrl($url) : void
    {
        $client = $this->getBrowser();
        $client->request('GET', $url);
    }

    /**
     * This function should be run at the end of each tests that alter the database content in order to ensure the consistency between each tests.
     */
    protected function resetDatabase() : void
    {
        PrepareDatabase::resetDatabase();
    }

    protected function loginCustomer(string $email, string $password)
    {
        $this->goToUrl('/en/login');
        $inputEmail = $this->getElementByCssSelector('.login-form #inputEmail');
        $inputPassword = $this->getElementByCssSelector('.login-form #inputPassword');
        $submitButton = $this->getElementByCssSelector('.login-form button[type="submit"]');
        $inputEmail->sendKeys($email);
        $inputPassword->sendKeys($password);
        $submitButton->click();
    }

    protected function tearDown() : void
    {
        $this->closeBrowser();
        parent::tearDown();
    }

    private function generateClient() : Client
    {
        $capabilities = DesiredCapabilities::chrome();
        $options = new ChromeOptions();
        $options->addArguments([
            '--disable-gpu',
            '--no-sandbox',
            "--window-size={$this->getBrowserWidth()},{$this->getBrowserHeight()}"
        ]);
        $options->setExperimentalOption('prefs', ['intl.accept_languages' => $this->browserLang]);
        // It seems that the "intl.accept_languages" option is ignored if the browser is not launched in headless mode.
        if ($this->browserLang == 'en') {
            $options->addArguments(['--headless']);
        }
        $capabilities->setCapability(ChromeOptions::CAPABILITY, $options);
        return Client::createSeleniumClient(
            $this->envSeleniumUri,
            $capabilities,
            $this->envBaseUri
        );
    }

    private function getElements(WebdriverBy $by) : ?array
    {
        return $this->getBrowser()->findElements($by);
    }

    /**
     * @param $name
     * @return string
     * @throws Exception
     */
    private function getEnvVariable($name) : string
    {
        if (!isset($_SERVER[$name])) {
            throw new Exception("Missing $name in your env.test configuration file.");
        }
        return $_SERVER[$name];
    }

    /**
     * @throws Exception
     */
    private function setConfiguration() : void
    {
        $this->envBaseUri = $this->getEnvVariable('TIROUCUBO_TEST_BASE_URI');
        $this->envSeleniumUri = $this->getEnvVariable('TIROUCUBO_TEST_SELENIUM_HOST');
    }
}

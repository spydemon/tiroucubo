<?php

namespace App\Tests\Integration;

use Exception;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\Remote\RemoteWebElement;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\WebDriverBy;
use Symfony\Component\Panther\Client;
use Symfony\Component\Panther\PantherTestCase;

/**
 * Class IntegrationAbstract
 *
 * This class should be extended on each integration tests.
 *
 * @package App\Tests\Integration
 */
abstract class IntegrationAbstract extends PantherTestCase
{
    private ?Client $client = null;
    private ?string $envBaseUri = null;
    private ?string $envSeleniumUri = null;

    abstract protected function getBrowserHeight() : int;
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

    protected function getElementByCssSelector(string $selector, bool $fatal = true) : ?RemoteWebElement
    {
        try {
            return $this->getBrowser()->findElement(WebDriverBy::cssSelector($selector));
        } catch (NoSuchElementException $e) {
            if ($fatal) {
                throw $e;
            }
            return null;
        }
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
            '--headless',
            "--window-size={$this->getBrowserWidth()},{$this->getBrowserHeight()}"
        ]);
        $capabilities->setCapability(ChromeOptions::CAPABILITY, $options);
        return Client::createSeleniumClient(
            $this->envSeleniumUri,
            $capabilities,
            $this->envBaseUri
        );
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

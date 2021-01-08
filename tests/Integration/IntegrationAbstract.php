<?php

namespace App\Tests\Integration;

use Exception;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Symfony\Component\Panther\Client;
use Symfony\Component\Panther\PantherTestCase;
use Facebook\WebDriver\Remote\DesiredCapabilities;

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

    protected function getBrowser() : Client
    {
        if (is_null($this->client)) {
            $this->client = $this->generateClient();
        }
        return $this->client;
    }

    /**
     * @param $path
     * @return string
     */
    protected function getAppUrl($path) : string
    {
        return $this->envBaseUri . $path;
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

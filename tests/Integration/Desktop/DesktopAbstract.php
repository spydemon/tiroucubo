<?php

namespace App\Tests\Integration\Desktop;

use App\Tests\Integration\IntegrationAbstract;

abstract class DesktopAbstract extends IntegrationAbstract
{
    protected function getBrowserHeight() : int
    {
        return 1080;
    }

    protected function getBrowserWidth() : int
    {
        return 1920;
    }
}

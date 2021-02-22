<?php

namespace App\Tests\Acceptation\Desktop;

use App\Tests\Acceptation\IntegrationAbstract;

abstract class DesktopAbstract extends IntegrationAbstract
{

    protected function getBrowserWidth() : int
    {
        return 1920;
    }
}

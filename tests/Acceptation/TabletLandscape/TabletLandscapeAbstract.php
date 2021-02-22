<?php

namespace App\Tests\Acceptation\TabletLandscape;

use App\Tests\Acceptation\IntegrationAbstract;

class TabletLandscapeAbstract extends IntegrationAbstract
{
    protected function getBrowserWidth() : int
    {
        return 1024;
    }
}

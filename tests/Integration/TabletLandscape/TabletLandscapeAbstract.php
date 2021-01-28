<?php

namespace App\Tests\Integration\TabletLandscape;

use App\Tests\Integration\IntegrationAbstract;

class TabletLandscapeAbstract extends IntegrationAbstract
{
    protected function getBrowserWidth() : int
    {
        return 1024;
    }
}

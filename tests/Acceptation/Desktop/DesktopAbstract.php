<?php

namespace App\Tests\Acceptation\Desktop;

use App\Tests\Acceptation\AcceptationAbstract;

abstract class DesktopAbstract extends AcceptationAbstract
{

    protected function getBrowserWidth() : int
    {
        return 1920;
    }
}

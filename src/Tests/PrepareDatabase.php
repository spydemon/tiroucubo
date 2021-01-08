<?php

namespace App\Tests;

use PHPUnit\Runner\BeforeFirstTestHook;

class PrepareDatabase implements BeforeFirstTestHook
{
    public function executeBeforeFirstTest(): void
    {
        $this->resetDatabase();
    }

    protected function resetDatabase() : void
    {
        echo "Resetting the database…\n";
        exec('bin/console doctrine:database:drop -e test --force');
        exec('bin/console doctrine:database:create -e test');
        exec('bin/console doctrine:migrations:migrate -e test -n');
        exec('bin/console doctrine:fixtures:load -e test -n');
        echo "Resetting done!\n";
    }
}

<?php

namespace App\Tests\Extension;

use PHPUnit\Runner\BeforeFirstTestHook;

class PrepareDatabase implements BeforeFirstTestHook
{
    public function executeBeforeFirstTest(): void
    {
        $this->resetDatabase();
    }

    public static function resetDatabase() : void
    {
        // We are unsetting the XDEBUG_CONFIG in order to avoid xdebug and/or PHPStorm to freeze the execution of the
        // process if the debugger is enabled.
        // This command should be useless since the next one is also supposed to clear the system cache, but it looks
        // like that wired behavior can still occurs if we remove this entry.
        exec('unset XDEBUG_CONFIG && bin/console cache:clear');
        // Clear all customs and system caches.
        exec('unset XDEBUG_CONFIG && bin/console cache:pool:clear cache.global_clearer');
        // doctrine:schema:drop should be used instead of doctrine:database:drop since Postgresql sequences are not
        // flushed by the second command.
        exec('unset XDEBUG_CONFIG && bin/console doctrine:schema:drop -e atest --force');
        exec('unset XDEBUG_CONFIG && bin/console doctrine:database:drop -e atest --force');
        exec('unset XDEBUG_CONFIG && bin/console doctrine:database:create -e atest');
        exec('unset XDEBUG_CONFIG && bin/console doctrine:migrations:migrate -e atest -n');
        exec('unset XDEBUG_CONFIG && bin/console doctrine:fixtures:load -e atest -n');
    }
}

<?php

namespace Database\Seeders\Concerns;

use Illuminate\Support\Facades\DB;

trait DisablesForeignKeyChecks
{
    protected function withoutForeignKeyChecks(callable $callback): mixed
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        try {
            return $callback();
        } finally {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }
    }
}

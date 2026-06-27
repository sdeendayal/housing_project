<?php

namespace Database\Seeders\Concerns;

use Illuminate\Support\Facades\DB;

trait DisablesForeignKeyChecks
{
    protected function withoutForeignKeyChecks(callable $callback): mixed
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
        }

        try {
            return $callback();
        } finally {
            if ($driver === 'sqlite') {
                DB::statement('PRAGMA foreign_keys = ON');
            } else {
                DB::statement('SET FOREIGN_KEY_CHECKS=1');
            }
        }
    }
}

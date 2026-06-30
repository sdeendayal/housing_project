<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ImportSqlTablesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ini_set('memory_limit', '512M');

        if (DB::getDriverName() === 'sqlite') {
            $this->command->info('SQLite connection detected (running tests). Skipping raw SQL imports.');
            return;
        }

        $familyIdSqlPath = database_path('seeders/data/family_id_data.sql');
        if (File::exists($familyIdSqlPath)) {
            $this->command->info('Importing family_id_data.sql...');
            DB::unprepared(File::get($familyIdSqlPath));
            $this->command->info('family_id_data.sql imported successfully!');
        } else {
            $this->command->error('family_id_data.sql not found at: ' . $familyIdSqlPath);
        }

        $mmgaySqlPath = database_path('seeders/data/mmgay.sql');
        if (File::exists($mmgaySqlPath)) {
            $this->command->info('Importing mmgay.sql (46MB)...');
            
            // Get credentials from config instead of env to support configuration caching on staging/live
            $connectionName = config('database.default', 'mysql');
            $dbName = config("database.connections.{$connectionName}.database");
            $dbUser = config("database.connections.{$connectionName}.username");
            $dbPass = config("database.connections.{$connectionName}.password");
            $dbHost = config("database.connections.{$connectionName}.host", '127.0.0.1');
            $dbPort = config("database.connections.{$connectionName}.port", '3306');

            $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';

            // Find mysql executable
            $mysqlBin = 'mysql';
            if ($isWindows) {
                $xamppPath = 'C:\\xampp\\mysql\\bin\\mysql.exe';
                if (File::exists($xamppPath)) {
                    $mysqlBin = $xamppPath;
                }
            }

            $passPart = $dbPass !== '' ? ' -p' . escapeshellarg($dbPass) : '';
            $cmd = sprintf(
                '%s -h %s -P %s -u %s%s %s',
                $mysqlBin,
                escapeshellarg($dbHost),
                escapeshellarg($dbPort),
                escapeshellarg($dbUser),
                $passPart,
                escapeshellarg($dbName)
            );

            try {
                // Ensure proc_open is enabled and available
                if (!function_exists('proc_open')) {
                    throw new \Exception("proc_open function is disabled on this server.");
                }

                $this->command->info("Executing process-based import via proc_open...");
                $descriptorspec = [
                    0 => ["pipe", "r"],  // stdin
                    1 => ["pipe", "w"],  // stdout
                    2 => ["pipe", "w"]   // stderr
                ];

                $process = proc_open($cmd, $descriptorspec, $pipes);

                if (is_resource($process)) {
                    $fileHandle = fopen($mmgaySqlPath, 'r');
                    if ($fileHandle) {
                        while (!feof($fileHandle)) {
                            $chunk = fread($fileHandle, 8192);
                            fwrite($pipes[0], $chunk);
                        }
                        fclose($fileHandle);
                    }
                    fclose($pipes[0]);

                    $stdout = stream_get_contents($pipes[1]);
                    fclose($pipes[1]);

                    $stderr = stream_get_contents($pipes[2]);
                    fclose($pipes[2]);

                    $resultCode = proc_close($process);

                    if ($resultCode === 0) {
                        $this->command->info('mmgay.sql imported successfully!');
                    } else {
                        throw new \Exception("MySQL import command exited with error code {$resultCode}. Stderr: " . $stderr);
                    }
                } else {
                    throw new \Exception("Could not execute command: " . $cmd);
                }
            } catch (\Exception $e) {
                $this->command->warn('Process-based import failed: ' . $e->getMessage());
                $this->command->info('Falling back to pure PHP PDO import (DB::unprepared)...');
                
                // Pure database-level fallback (works in any environment with database access)
                DB::unprepared(File::get($mmgaySqlPath));
                $this->command->info('mmgay.sql imported successfully via fallback!');
            }
        } else {
            $this->command->error('mmgay.sql not found at: ' . $mmgaySqlPath);
        }
    }
}

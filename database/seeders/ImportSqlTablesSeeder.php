<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Throwable;

class ImportSqlTablesSeeder extends Seeder
{
    private const MAX_PACKET_BYTES = 900_000;

    private const INSERT_CHUNK_ROWS = 400;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ini_set('memory_limit', '1024M');

        if (DB::getDriverName() === 'sqlite') {
            $this->command->info('SQLite connection detected (running tests). Skipping raw SQL imports.');

            return;
        }

        $this->prepareConnection();

        $familyIdSqlPath = database_path('seeders/data/family_id_data.sql');
        if (File::exists($familyIdSqlPath)) {
            $this->importSqlFile($familyIdSqlPath, 'family_id_data.sql');
        } else {
            $this->command->error('family_id_data.sql not found at: '.$familyIdSqlPath);
        }

        $mmgaySqlPath = database_path('seeders/data/mmgay.sql');
        if (File::exists($mmgaySqlPath)) {
            $this->importSqlFile($mmgaySqlPath, 'mmgay.sql');
        } else {
            $this->command->error('mmgay.sql not found at: '.$mmgaySqlPath);
        }
    }

    private function prepareConnection(): void
    {
        try {
            DB::statement('SET GLOBAL max_allowed_packet = 67108864');
            $this->command->info('Increased GLOBAL max_allowed_packet to 64MB.');
        } catch (Throwable) {
            $this->command->warn('Could not increase GLOBAL max_allowed_packet. Large INSERTs will be split into smaller chunks.');
        }

        DB::statement('SET foreign_key_checks = 0');
        DB::statement("SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO'");
        DB::statement('SET NAMES utf8mb4');
    }

    private function importSqlFile(string $path, string $label): void
    {
        $this->command->info("Importing {$label}...");

        if ($this->importViaMysqlCli($path, $label)) {
            $this->command->info("{$label} imported successfully via mysql CLI.");

            return;
        }

        $this->importViaPhp($path, $label);
        $this->command->info("{$label} imported successfully via PHP chunked import.");
    }

    private function importViaMysqlCli(string $path, string $label): bool
    {
        $mysqlBin = $this->resolveMysqlBinary();

        if ($mysqlBin === null || ! function_exists('proc_open')) {
            return false;
        }

        $connectionName = config('database.default', 'mysql');
        $dbName = config("database.connections.{$connectionName}.database");
        $dbUser = config("database.connections.{$connectionName}.username");
        $dbPass = config("database.connections.{$connectionName}.password");
        $dbHost = config("database.connections.{$connectionName}.host", '127.0.0.1');
        $dbPort = config("database.connections.{$connectionName}.port", '3306');

        $passPart = $dbPass !== '' ? ' -p'.escapeshellarg($dbPass) : '';
        $cmd = sprintf(
            '%s -h %s -P %s -u %s%s %s',
            escapeshellarg($mysqlBin),
            escapeshellarg($dbHost),
            escapeshellarg($dbPort),
            escapeshellarg($dbUser),
            $passPart,
            escapeshellarg($dbName)
        );

        $descriptorspec = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];

        $process = proc_open($cmd, $descriptorspec, $pipes);

        if (! is_resource($process)) {
            return false;
        }

        $fileHandle = fopen($path, 'r');

        if ($fileHandle) {
            while (! feof($fileHandle)) {
                fwrite($pipes[0], fread($fileHandle, 8192));
            }
            fclose($fileHandle);
        }

        fclose($pipes[0]);
        fclose($pipes[1]);

        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[2]);

        $resultCode = proc_close($process);

        if ($resultCode !== 0) {
            $this->command->warn("mysql CLI import failed for {$label}: ".$stderr);

            return false;
        }

        return true;
    }

    private function resolveMysqlBinary(): ?string
    {
        $candidates = array_filter([
            env('MYSQL_CLI_PATH'),
            'mysql',
            '/opt/homebrew/bin/mysql',
            '/usr/local/bin/mysql',
            '/usr/local/mysql/bin/mysql',
            '/Applications/MAMP/Library/bin/mysql',
            'C:\\xampp\\mysql\\bin\\mysql.exe',
        ]);

        foreach ($candidates as $candidate) {
            if ($candidate === 'mysql') {
                $which = trim((string) shell_exec('command -v mysql 2>/dev/null'));

                if ($which !== '') {
                    return $which;
                }

                continue;
            }

            if (is_string($candidate) && File::exists($candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    private function importViaPhp(string $path, string $label): void
    {
        $handle = fopen($path, 'r');

        if ($handle === false) {
            throw new \RuntimeException("Unable to open SQL file: {$path}");
        }

        $buffer = '';
        $statementCount = 0;
        $insideBlockComment = false;

        while (($line = fgets($handle)) !== false) {
            if ($insideBlockComment) {
                if (str_contains($line, '*/')) {
                    $insideBlockComment = false;
                }

                continue;
            }

            $trimmed = trim($line);

            if ($trimmed === '' || str_starts_with($trimmed, '--')) {
                continue;
            }

            if (str_starts_with($trimmed, '/*') && ! str_starts_with($trimmed, '/*!')) {
                if (! str_contains($line, '*/')) {
                    $insideBlockComment = true;
                }

                continue;
            }

            if ($this->shouldSkipStatement($trimmed)) {
                continue;
            }

            $buffer .= $line;

            if (! str_ends_with(rtrim($line, "\r\n"), ';')) {
                continue;
            }

            $statement = trim($buffer);
            $buffer = '';

            if ($statement === '') {
                continue;
            }

            $this->executeStatement($statement);
            $statementCount++;

            if ($statementCount % 25 === 0) {
                $this->command->info("  {$label}: executed {$statementCount} statements...");
            }
        }

        fclose($handle);

        if (trim($buffer) !== '') {
            $this->executeStatement(trim($buffer));
        }
    }

    private function shouldSkipStatement(string $trimmed): bool
    {
        if (preg_match('/^CREATE\s+DATABASE\b/i', $trimmed)) {
            return true;
        }

        if (preg_match('/^USE\s+/i', $trimmed)) {
            return true;
        }

        return false;
    }

    private function executeStatement(string $statement): void
    {
        if (strlen($statement) <= self::MAX_PACKET_BYTES) {
            $this->runStatement($statement);

            return;
        }

        if (preg_match('/^INSERT\s+/i', $statement)) {
            foreach ($this->splitInsertStatement($statement) as $chunk) {
                $this->runStatement($chunk);
            }

            return;
        }

        $this->runStatement($statement);
    }

    /**
     * @return list<string>
     */
    private function splitInsertStatement(string $statement): array
    {
        if (! preg_match('/^(\s*INSERT\s+.+?\s+VALUES\s*)(.+)$/is', $statement, $matches)) {
            return [$statement];
        }

        $header = $matches[1];
        $valuesBody = rtrim($matches[2]);

        if (str_ends_with($valuesBody, ';')) {
            $valuesBody = substr($valuesBody, 0, -1);
        }

        if (str_contains($valuesBody, "\n")) {
            return $this->splitMultilineInsert($header, $valuesBody);
        }

        return $this->splitSingleLineInsert($header, $valuesBody);
    }

    /**
     * @return list<string>
     */
    private function splitMultilineInsert(string $header, string $valuesBody): array
    {
        $rows = preg_split('/\r?\n/', $valuesBody) ?: [];
        $chunks = [];
        $batch = [];

        foreach ($rows as $row) {
            $row = trim($row);

            if ($row === '') {
                continue;
            }

            $batch[] = $row;

            if (count($batch) >= self::INSERT_CHUNK_ROWS) {
                $chunks[] = $this->buildInsertChunk($header, $batch);
                $batch = [];
            }
        }

        if ($batch !== []) {
            $chunks[] = $this->buildInsertChunk($header, $batch);
        }

        return $chunks !== [] ? $chunks : [$header.$valuesBody.';'];
    }

    /**
     * @return list<string>
     */
    private function splitSingleLineInsert(string $header, string $valuesBody): array
    {
        $rows = preg_split('/\),\s*\(/', $valuesBody) ?: [];
        $chunks = [];
        $batch = [];

        foreach ($rows as $index => $row) {
            if ($index === 0) {
                $row = ltrim($row, '(');
            }

            if ($index === count($rows) - 1) {
                $row = rtrim($row, ')');
            }

            $batch[] = '('.$row.')';

            if (count($batch) >= self::INSERT_CHUNK_ROWS) {
                $chunks[] = $header.implode(',', $batch).';';
                $batch = [];
            }
        }

        if ($batch !== []) {
            $chunks[] = $header.implode(',', $batch).';';
        }

        return $chunks !== [] ? $chunks : [$header.$valuesBody.';'];
    }

    /**
     * @param  list<string>  $rows
     */
    private function buildInsertChunk(string $header, array $rows): string
    {
        $normalizedRows = [];

        foreach ($rows as $row) {
            $row = trim($row);
            $row = rtrim($row, ',;');

            if ($row !== '') {
                $normalizedRows[] = $row;
            }
        }

        return $header.implode(",\n", $normalizedRows).';';
    }

    private function runStatement(string $statement): void
    {
        try {
            DB::unprepared($statement);
        } catch (Throwable $exception) {
            if (! str_contains($exception->getMessage(), 'gone away')) {
                throw $exception;
            }

            DB::reconnect();
            DB::statement('SET foreign_key_checks = 0');
            DB::unprepared($statement);
        }
    }
}

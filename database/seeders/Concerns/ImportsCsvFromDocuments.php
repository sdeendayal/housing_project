<?php

namespace Database\Seeders\Concerns;

trait ImportsCsvFromDocuments
{
    protected function csvPath(string $filename): string
    {
        $paths = array_filter([
            env('INSTALLMENT_LEDGER_CSV_PATH')
                ? rtrim(env('INSTALLMENT_LEDGER_CSV_PATH'), '/').'/'.$filename
                : null,
            database_path('seeders/data/'.$filename),
            '/Users/anandkamboj/Documents/table_structure_installment_ledger_tables/'.$filename,
        ]);

        foreach ($paths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        throw new \Exception("CSV file not found: {$filename}");
    }

    protected function nullIfEmpty(?string $value): ?string
    {
        if ($value === null || $value === '' || strtoupper($value) === 'NULL') {
            return null;
        }

        return $value;
    }

    protected function parseDate(?string $value): ?string
    {
        $value = $this->nullIfEmpty($value);
        if ($value === null) {
            return null;
        }

        try {
            return \Carbon\Carbon::createFromFormat('d-m-Y', $value)->format('Y-m-d');
        } catch (\Exception) {
            return \Carbon\Carbon::parse($value)->format('Y-m-d');
        }
    }

    protected function parseDateTime(?string $value): ?string
    {
        $value = $this->nullIfEmpty($value);
        if ($value === null) {
            return null;
        }

        try {
            return \Carbon\Carbon::createFromFormat('d-m-Y', $value)->format('Y-m-d H:i:s');
        } catch (\Exception) {
            return \Carbon\Carbon::parse($value)->format('Y-m-d H:i:s');
        }
    }

    protected function nullableFloat(?string $value): ?float
    {
        $value = $this->nullIfEmpty($value);
        if ($value === null) {
            return null;
        }

        return (float) $value;
    }

    protected function nullableInt(?string $value): ?int
    {
        $value = $this->nullIfEmpty($value);
        if ($value === null) {
            return null;
        }

        return (int) $value;
    }
}

<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BackupService
{
    /**
     * Tables that hold real site/business data and are safe to export.
     * Framework-internal tables (sessions, cache, queues, password reset
     * tokens, migrations) are deliberately excluded: they hold no business
     * value and some contain security-sensitive tokens.
     */
    public const EXPORTABLE_TABLES = [
        'users',
        'deposits',
        'withdrawals',
        'trades',
        'live_trades',
        'bot_tradings',
        'bot_trades',
        'copy_traders',
        'copied_trades',
        'trading_signals',
        'trade_pairs',
        'plans',
        'user_plans',
        'subscriptions',
        'ai_traders',
        'ai_trader_plans',
        'ai_trader_subscriptions',
        'user_ai_traders',
        'user_minings',
        'user_holdings',
        'user_stakings',
        'holding_transactions',
        'fund_transfers',
        'referrals',
        'payment_methods',
        'assets',
        'packages',
        'admin_mail_logs',
        'impersonation_logs',
        'user_notifications',
    ];

    /**
     * Column names never included in an export, regardless of table:
     * password hashes and auth tokens have no legitimate place in a
     * downloadable data export.
     */
    public const SENSITIVE_COLUMNS = [
        'password',
        'plain_password',
        'remember_token',
        'verification_code',
        'verification_code_expires_at',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'api_token',
    ];

    public function exportableTables(): array
    {
        $existing = Schema::getTableListing();

        return array_values(array_intersect(self::EXPORTABLE_TABLES, $existing));
    }

    public function exportableColumns(string $table): array
    {
        return array_values(array_diff(Schema::getColumnListing($table), self::SENSITIVE_COLUMNS));
    }

    public function tableRowCount(string $table): int
    {
        return DB::table($table)->count();
    }

    /**
     * Count rows created (or, absent a created_at column, all rows) after
     * the given timestamp across every exportable table.
     */
    public function newRecordsSince(?\DateTimeInterface $since): int
    {
        $total = 0;

        foreach ($this->exportableTables() as $table) {
            if (!$since || !Schema::hasColumn($table, 'created_at')) {
                continue;
            }

            $total += DB::table($table)->where('created_at', '>', $since)->count();
        }

        return $total;
    }

    public function streamTableCsv(string $table): StreamedResponse
    {
        $columns = $this->exportableColumns($table);
        $sortColumn = Schema::getColumnListing($table)[0];

        $response = new StreamedResponse(function () use ($table, $columns, $sortColumn) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $columns);

            DB::table($table)->orderBy($sortColumn)->select($columns)->cursor()->each(function ($row) use ($handle) {
                fputcsv($handle, (array) $row);
            });

            fclose($handle);
        });

        $filename = $table . '_' . now()->format('Y-m-d_His') . '.csv';
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');

        return $response;
    }

    public function streamDatabaseSql(): StreamedResponse
    {
        $tables = $this->exportableTables();
        $driver = DB::connection()->getDriverName();

        $response = new StreamedResponse(function () use ($tables, $driver) {
            $handle = fopen('php://output', 'w');

            fwrite($handle, "-- TopBitcrest data export\n");
            fwrite($handle, "-- Generated " . now()->toDateTimeString() . "\n");
            fwrite($handle, "-- Note: password hashes and auth tokens are intentionally excluded.\n\n");

            foreach ($tables as $table) {
                fwrite($handle, "-- --------------------------------------------------\n");
                fwrite($handle, "-- Table: {$table}\n");
                fwrite($handle, "-- --------------------------------------------------\n\n");

                $createSql = $this->createTableStatement($table, $driver);
                if ($createSql) {
                    fwrite($handle, rtrim($createSql, ';') . ";\n\n");
                }

                $columns = $this->exportableColumns($table);
                $sortColumn = Schema::getColumnListing($table)[0];
                $pdo = DB::connection()->getPdo();

                DB::table($table)->orderBy($sortColumn)->select($columns)->cursor()->each(function ($row) use ($handle, $table, $columns, $pdo) {
                    $values = array_map(function ($column) use ($row, $pdo) {
                        $value = $row->$column;

                        if (is_null($value)) {
                            return 'NULL';
                        }

                        if (is_bool($value) || is_int($value) || is_float($value)) {
                            return $value;
                        }

                        return $pdo->quote((string) $value);
                    }, $columns);

                    $columnList = implode('`, `', $columns);
                    fwrite($handle, "INSERT INTO `{$table}` (`{$columnList}`) VALUES (" . implode(', ', $values) . ");\n");
                });

                fwrite($handle, "\n");
            }

            fclose($handle);
        });

        $filename = 'topbitcrest_backup_' . now()->format('Y-m-d_His') . '.sql';
        $response->headers->set('Content-Type', 'application/sql');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');

        return $response;
    }

    protected function createTableStatement(string $table, string $driver): ?string
    {
        if ($driver === 'sqlite') {
            $row = DB::selectOne("SELECT sql FROM sqlite_master WHERE type = 'table' AND name = ?", [$table]);
            return $row->sql ?? null;
        }

        if ($driver === 'mysql') {
            $row = DB::selectOne("SHOW CREATE TABLE `{$table}`");
            $row = (array) $row;
            return $row['Create Table'] ?? null;
        }

        if ($driver === 'pgsql') {
            // No single-statement CREATE TABLE equivalent; INSERT data still
            // works against an existing schema (e.g. after running migrations).
            return null;
        }

        return null;
    }
}

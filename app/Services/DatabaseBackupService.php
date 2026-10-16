<?php

namespace App\Services;

use App\Models\Restaurant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseBackupService
{
    /**
     * Generate an SQL backup dump for disaster recovery.
     *
     * @return array{filename: string, content: string, size_bytes: int, tables_count: int}
     */
    public static function createDump(?Restaurant $restaurant = null): array
    {
        $timestamp = now()->format('Y-m-d_His');
        $restaurantName = $restaurant ? preg_replace('/[^A-Za-z0-9_\-]/', '_', $restaurant->name) : 'pos_system';
        $filename = "pos_backup_{$restaurantName}_{$timestamp}.sql";

        $output = "-- =========================================================\n";
        $output .= "-- RESTAURANT POS DISASTER RECOVERY BACKUP\n";
        $output .= '-- Generated At: '.now()->toIso8601String()."\n";
        $output .= '-- System Database: '.config('database.connections.mysql.database', 'restaurant_pos')."\n";
        $output .= '-- Restaurant: '.($restaurant?->name ?? 'All Restaurant Tenants')."\n";
        $output .= "-- Currency: MMK (Myanmar Kyats)\n";
        $output .= "-- =========================================================\n\n";
        $output .= "SET FOREIGN_KEY_CHECKS = 0;\n\n";

        // Exclude ephemeral cache and job payload tables from backup
        $excludedTables = ['cache', 'cache_locks', 'jobs', 'job_batches', 'failed_jobs', 'sessions'];

        // Get table listing
        $tables = Schema::getTableListing();
        $backedUpCount = 0;

        foreach ($tables as $rawTable) {
            $table = str_contains($rawTable, '.') ? explode('.', $rawTable)[1] : $rawTable;

            if (in_array($table, $excludedTables, true)) {
                continue;
            }

            $backedUpCount++;
            $output .= "-- ---------------------------------------------------------\n";
            $output .= "-- Table structure for `{$table}`\n";
            $output .= "-- ---------------------------------------------------------\n";
            $output .= "DROP TABLE IF EXISTS `{$table}`;\n";

            // Get create table statement
            if (DB::connection()->getDriverName() === 'sqlite') {
                $createTableResult = DB::select("SELECT sql FROM sqlite_master WHERE type='table' AND name = ?", [$table]);
                $createTableSql = $createTableResult[0]->sql ?? "CREATE TABLE `{$table}` (id INTEGER PRIMARY KEY)";
            } else {
                $createTableResult = DB::select("SHOW CREATE TABLE `{$table}`");
                $createTableSql = $createTableResult[0]->{'Create Table'} ?? '';
            }
            $output .= "{$createTableSql};\n\n";

            // Get rows
            $query = DB::table($table);
            if ($restaurant && Schema::hasColumn($table, 'restaurant_id')) {
                $query->where('restaurant_id', $restaurant->id);
            }

            $rows = $query->get();
            if ($rows->isNotEmpty()) {
                $output .= "-- Dumping data for table `{$table}`\n";
                foreach ($rows->chunk(100) as $chunk) {
                    $insertValues = [];
                    $columns = [];
                    foreach ($chunk as $row) {
                        $rowArray = (array) $row;
                        if (empty($columns)) {
                            $columns = array_map(fn ($col) => "`{$col}`", array_keys($rowArray));
                        }
                        $escapedValues = array_map(function ($val) {
                            if ($val === null) {
                                return 'NULL';
                            }

                            return "'".addslashes((string) $val)."'";
                        }, array_values($rowArray));
                        $insertValues[] = '('.implode(', ', $escapedValues).')';
                    }

                    $colList = implode(', ', $columns);
                    $valList = implode(",\n", $insertValues);
                    $output .= "INSERT INTO `{$table}` ({$colList}) VALUES\n{$valList};\n";
                }
                $output .= "\n";
            }
        }

        $output .= "SET FOREIGN_KEY_CHECKS = 1;\n";
        $output .= "-- Backup completed successfully.\n";

        return [
            'filename' => $filename,
            'content' => $output,
            'size_bytes' => strlen($output),
            'tables_count' => $backedUpCount,
        ];
    }
}

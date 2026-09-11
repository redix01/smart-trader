<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BackupLog;
use App\Services\BackupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class BackupController extends Controller
{
    public function __construct(protected BackupService $backups)
    {
    }

    public function index()
    {
        $tables = collect($this->backups->exportableTables())
            ->map(fn ($table) => [
                'name' => $table,
                'rows' => $this->backups->tableRowCount($table),
            ]);

        $lastBackup = BackupLog::latest('created_at')->first();
        $newRecords = $this->backups->newRecordsSince($lastBackup?->created_at);

        $recentBackups = BackupLog::with('admin')->latest('created_at')->limit(15)->get();

        return view('admin.backup.index', compact('tables', 'lastBackup', 'newRecords', 'recentBackups'));
    }

    public function exportCsv(Request $request, string $table)
    {
        if (!in_array($table, $this->backups->exportableTables(), true)) {
            throw ValidationException::withMessages(['table' => 'That table is not available for export.']);
        }

        $rowCount = $this->backups->tableRowCount($table);

        BackupLog::create([
            'admin_id' => Auth::id(),
            'type' => 'csv',
            'table_name' => $table,
            'row_count' => $rowCount,
        ]);

        return $this->backups->streamTableCsv($table);
    }

    public function exportSql()
    {
        $rowCount = collect($this->backups->exportableTables())
            ->sum(fn ($table) => $this->backups->tableRowCount($table));

        BackupLog::create([
            'admin_id' => Auth::id(),
            'type' => 'sql',
            'table_name' => null,
            'row_count' => $rowCount,
        ]);

        return $this->backups->streamDatabaseSql();
    }
}

@extends('admin.layouts.app')

@section('title', 'Data Export & Backup')

@section('content')
<div class="p-4">
    <div class="max-w-7xl mx-auto p-4 border-2 border-gray-200 border-dashed rounded-lg dark:border-gray-700">
        <!-- Page Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Data Export &amp; Backup</h1>
            <p class="text-gray-600 dark:text-gray-400">Download site data as CSV, or export the full database as a single SQL file.</p>
        </div>

        <!-- Backup Alert -->
        @if(!$lastBackup)
            <div class="mb-6 flex items-start gap-3 bg-yellow-100 border border-yellow-400 text-yellow-800 px-4 py-3 rounded dark:bg-yellow-900/30 dark:border-yellow-700 dark:text-yellow-300" role="alert">
                <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <p class="font-medium">No backup has been taken yet.</p>
                    <p class="text-sm">Export a full SQL backup below to have a first restore point.</p>
                </div>
            </div>
        @elseif($newRecords > 0)
            <div class="mb-6 flex items-start gap-3 bg-yellow-100 border border-yellow-400 text-yellow-800 px-4 py-3 rounded dark:bg-yellow-900/30 dark:border-yellow-700 dark:text-yellow-300" role="alert">
                <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <p class="font-medium">{{ number_format($newRecords) }} new record{{ $newRecords === 1 ? '' : 's' }} since your last backup.</p>
                    <p class="text-sm">Last backup was {{ $lastBackup->created_at->diffForHumans() }} ({{ $lastBackup->created_at->format('M j, Y g:i A') }}). Consider exporting a fresh copy.</p>
                </div>
            </div>
        @else
            <div class="mb-6 flex items-start gap-3 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded dark:bg-green-900/30 dark:border-green-700 dark:text-green-300" role="alert">
                <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <p class="font-medium">You're up to date.</p>
                    <p class="text-sm">No new records since the last backup ({{ $lastBackup->created_at->diffForHumans() }}).</p>
                </div>
            </div>
        @endif

        <!-- Full database export -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 sm:p-6 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Full Database Export</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Every exportable table as one downloadable .sql file (CREATE TABLE + INSERT statements).</p>
                </div>
                <a href="{{ route('admin.backup.sql') }}"
                    class="inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3"></path>
                    </svg>
                    Download Full SQL Backup
                </a>
            </div>
        </div>

        <!-- Per-table CSV export -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 sm:p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Export a Table as CSV</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($tables as $table)
                    <div class="flex items-center justify-between border border-gray-200 dark:border-gray-700 rounded-lg px-4 py-3">
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $table['name'] }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ number_format($table['rows']) }} rows</p>
                        </div>
                        <a href="{{ route('admin.backup.csv', $table['name']) }}"
                            class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-sm font-medium">
                            CSV
                        </a>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Recent backups -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 sm:p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Recent Backups</h3>
            @if($recentBackups->isEmpty())
                <p class="text-sm text-gray-500 dark:text-gray-400">No backups have been taken yet.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-gray-500 dark:text-gray-400 uppercase border-b border-gray-200 dark:border-gray-700">
                            <tr>
                                <th class="py-2 pr-4">Date</th>
                                <th class="py-2 pr-4">Type</th>
                                <th class="py-2 pr-4">Table</th>
                                <th class="py-2 pr-4">Rows</th>
                                <th class="py-2 pr-4">By</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($recentBackups as $backup)
                                <tr>
                                    <td class="py-2 pr-4 text-gray-900 dark:text-white whitespace-nowrap">{{ $backup->created_at->format('M j, Y g:i A') }}</td>
                                    <td class="py-2 pr-4 uppercase text-gray-600 dark:text-gray-300">{{ $backup->type }}</td>
                                    <td class="py-2 pr-4 text-gray-600 dark:text-gray-300">{{ $backup->table_name ?? 'All tables' }}</td>
                                    <td class="py-2 pr-4 text-gray-600 dark:text-gray-300">{{ number_format($backup->row_count) }}</td>
                                    <td class="py-2 pr-4 text-gray-600 dark:text-gray-300">{{ $backup->admin->name ?? 'Unknown' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

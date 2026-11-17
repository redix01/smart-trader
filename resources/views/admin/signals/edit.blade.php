@extends('admin.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="mb-8">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">Edit Trading Signal</h1>
                    <p class="text-gray-400">Update this signal; users see the changes immediately</p>
                </div>
                <a href="{{ route('admin.signals.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors text-center">
                    Back to Signals
                </a>
            </div>
        </div>

        <div class="bg-gray-800 rounded-xl border border-gray-700 overflow-hidden shadow-lg">
            <div class="p-6">
                @include('admin.signals._form', ['signal' => $signal])
            </div>
        </div>
    </div>
</div>
@endsection

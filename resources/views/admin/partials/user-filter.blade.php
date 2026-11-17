@if(!empty($filterUser))
    <div class="mb-4 flex items-center justify-between p-3 text-sm text-blue-800 bg-blue-50 border border-blue-200 rounded-lg dark:bg-gray-800 dark:text-blue-300 dark:border-blue-800">
        <span>Showing only <strong>{{ $filterUser->name }}</strong> ({{ $filterUser->email }})</span>
        <a href="{{ url()->current() }}" class="underline">Show everyone</a>
    </div>
@endif

<form method="GET" action="{{ url()->current() }}" class="mb-4 grid grid-cols-1 md:grid-cols-4 gap-4">
<div>
    <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-white">Tanggal Awal</label>
    <input onclick="this.showPicker()" type="date" name="start_date" id="start_date" value="{{ request('start_date') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" />
</div>
<div>
    <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-white">Tanggal Akhir</label>
    <input onclick="this.showPicker()" type="date" name="end_date" id="end_date" value="{{ request('end_date') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" />
</div>
<div>
    <label for="per_page" class="block text-sm font-medium text-gray-700 dark:text-white">Per Page</label>
    <select name="per_page" id="per_page" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
    @foreach([10, 25, 50, 100] as $count)
        <option value="{{ $count }}" {{ request('per_page', 10) == $count ? 'selected' : '' }}>
        {{ $count }}
        </option>
    @endforeach
    </select>
</div>
<div class="flex items-end">
    <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700">
    Filter
    </button>
</div>
</form>
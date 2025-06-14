<form method="GET" action="{{ url()->current() }}" class="mb-4 grid grid-cols-1 md:grid-cols-4 gap-4">
    @php
        $currentYear = now()->year;
        $years = range($currentYear, $currentYear - 9); // 10 tahun terakhir
        $selectedYear = request('year', $currentYear);

        $currentMonth = now()->month;
        $selectedMonth = request('month', $currentMonth);
        
        $bulans = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];
    @endphp
    <div>
        <label for="month" class="block text-sm font-medium text-gray-700 dark:text-white">Tahun</label>
        <select name="year" id="month" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            @foreach($years as $year)
                <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
                    {{ $year }}
                </option>
            @endforeach
        </select>
    </div>
    <div>
        <label for="month" class="block text-sm font-medium text-gray-700 dark:text-white">Bulan</label>
        <select name="month" id="month" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            @foreach($bulans as $num => $bulan)
                <option value="{{ $num }}" {{ request('month', $currentMonth) == $num ? 'selected' : '' }}>
                    {{ $bulan }}
                </option>
            @endforeach
        </select>
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
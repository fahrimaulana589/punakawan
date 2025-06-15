<x-app-layout>
  <x-slot name="header">
    {{ __('Laporan '.$bulan) }}
  </x-slot>
  
  
  <div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">
    
    <div class="grid grid-cols-1">
      <!-- Breadcrumb Start -->
      <div x-data="{ 
        pageName: `Laporan Bulan {{ $bulan }}`,
        urls:[
          {name: 'Laporan', url: '{{ route('laporan') }}'},
        ]
      }">
        @include('partials.breadcrumb')
      </div>
      <!-- Breadcrumb End -->

      @php
        $reports = [
            ['name' => 'Laporan Penjualan '.$bulan, 'permission' => 'laporan_penjualan', 'link' => route('laporan.penjualan',$laporan->id)],
            ['name' => 'Jurnal '.$bulan, 'permission' => 'laporan_jurnal', 'link' => route('laporan.jurnal',$laporan->id)],
            ['name' => 'Buku Besar '.$bulan, 'permission' => 'buku_besar', 'link' => route('laporan.bukubesar',$laporan->id)],
            ['name' => 'Neraca Saldo '.$bulan, 'permission' => 'neraca_saldo', 'link' => route('laporan.neracasaldo',$laporan->id)],
            ['name' => 'AJP '.$bulan, 'permission' => 'ajp', 'link' => route('laporan.ajp', $laporan->id)],
            ['name' => 'Neraca Lajur '.$bulan, 'permission' => 'neraca_lajur', 'link' => route('laporan.neracalajur', $laporan->id)],
            ['name' => 'HPP '.$bulan, 'permission' => 'hpp', 'link' => route('laporan.hpp', $laporan->id)],
            ['name' => 'Laporan '.$bulan, 'permission' => 'laporan_bulan', 'link' => route('laporan.bulan', $laporan->id)],];
      @endphp

      <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($reports as $report)
          @if (auth()->user()->can($report['permission']))
            <a href="{{ $report['link'] }}" class="block p-4 bg-white rounded-lg shadow-md dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700">
              <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                  <svg class="w-6 h-6 text-gray-500 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16l-4-4m0 0l4-4m-4 4h16" />
                  </svg>
                </div>
                <div>
                  <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ $report['name'] }}</h3>
                </div>
              </div>
            </a>
          @endif
        @endforeach
      </div>

    </div>
  </div>
</x-app-layout>


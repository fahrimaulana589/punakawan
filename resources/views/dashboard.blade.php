<x-app-layout>
  <x-slot name="header">
    {{ __('Dashboard') }}
  </x-slot>
  
  <div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">
    <div class="grid grid-cols-12 gap-2 content-center">
      <div class="col-span-12 flex justify-center">
        <h1 class="text-4xl font-bold text-gray-800 dark:text-white">Selamat Datang</h1>
      </div>

      <div class="col-span-12 flex justify-center items-center">
        <!-- Metric Group One -->
        <img src="{{ $profile->logo_url }}" alt="Dashboard Image" class="w-full h-auto max-w-xs">
        <!-- Metric Group One -->
      </div>
      
      <div class="col-span-12 flex justify-center">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">{{ $profile->nama }}</h1>
      </div>
      <div class="col-span-12 flex justify-center">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">{{ $profile->alamat }}</h1>
      </div>
      <div class="col-span-12 flex justify-center">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">{{ $profile->handphone }}</h1>
      </div>
    </div>
  </div>
</x-app-layout>


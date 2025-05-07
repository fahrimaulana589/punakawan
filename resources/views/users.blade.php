<x-app-layout>
  <x-slot name="header">
    {{ __('Dashboard') }}
  </x-slot>
  
  
  <div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">
    
    <div class="grid grid-cols-1">
      <!-- Breadcrumb Start -->
      <div x-data="{ pageName: `Form Elements`}">
        @include('partials.breadcrumb')
      </div>
      <!-- Breadcrumb End -->

      <div class="flex items-center justify-end mb-4">
        <a href="{{ route('users') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
          Add User
        </a>
      </div>
      <!-- ====== Table Six Start -->
        <div
          class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]"
        >
          <div class="max-w-full overflow-x-auto">
            <table class="min-w-full">
              <!-- table header start -->
              <thead>
                <tr class="border-b border-gray-100 dark:border-gray-800">
                  <th class="px-5 py-3 sm:px-6">
                    <div class="flex items-center">
                      <p
                        class="font-medium text-gray-500 text-theme-xs dark:text-gray-400"
                      >
                        User
                      </p>
                    </div>
                  </th>
                  <th class="px-5 py-3 sm:px-6">
                    <div class="flex items-center">
                      <p
                        class="font-medium text-gray-500 text-theme-xs dark:text-gray-400"
                      >
                        Role
                      </p>
                    </div>
                  </th>
                  <th class="px-5 py-3 sm:px-6">
                    <div class="flex items-center justify-end">
                      <p
                        class="font-medium text-gray-500 text-theme-xs dark:text-gray-400"
                      >
                        Action
                      </p>
                    </div>
                  </th>
                </tr>
              </thead>
              <!-- table header end -->
              <!-- table body start -->
              <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @foreach ($users as $user)
                  <tr>
                    <td class="px-5 py-4 sm:px-6">
                      <div class="flex items-center">
                        <div class="flex items-center gap-3">
                          <div>
                            <span
                              class="block font-medium text-gray-800 text-theme-sm dark:text-white/90"
                            >
                              {{ $user->name }}
                            </span>
                            <span
                              class="block text-gray-500 text-theme-xs dark:text-gray-400"
                            >
                              {{ $user->email }}
                            </span>
                          </div>
                        </div>
                      </div>
                    </td>
                    <td class="px-5 py-4 sm:px-6">
                      <div class="flex items-center">
                        <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                          {{ $user->getRoleNames()->first() }}
                        </p>
                      </div>
                    </td>
                    <td class="px-5 py-4 sm:px-6">
                      <div class="flex items-center justify-end mb-4">
                        <a
                        href="#"
                        class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                      >
                        Edit
                      </a>
                      <a
                        href="#"
                        class="inline-flex items-center px-3 py-2 ml-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                      >
                        Delete
                      </a>
                      </div>
                    </td>   
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
        
      <!-- ====== Table Six End -->
    </div>
  </div>
</x-app-layout>


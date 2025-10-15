<header class="bg-white shadow-sm sticky top-0 z-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <div class="flex-shrink-0">
                <span class="text-xl font-bold text-gray-800 tracking-wider">E-Course | <span class="text-indigo-600">Admin</span></span>
            </div>

            {{-- Navigasi Utama (Desktop) --}}
            <nav class="hidden sm:block">
                <div class="flex items-center space-x-4">
                    
                    {{-- Dashboard --}}
                    <a href="{{ route('admin.dashboard.index') }}" 
                       class="text-gray-600 hover:bg-gray-50 hover:text-indigo-600 px-3 py-2 rounded-md text-sm font-medium transition duration-150 
                       @if(request()->routeIs('admin.dashboard.index')) bg-indigo-50 text-indigo-700 font-semibold @endif">
                        Dashboard
                    </a>
                    
                    {{-- Kursus (Dropdown) --}}
                    <div class="relative dropdown @if(request()->routeIs(['admin.courses.*', 'admin.categories.*'])) dropdown-open @endif" id="kursus-dropdown-desktop">
                        <button class="flex items-center px-3 py-2 rounded-md text-sm font-medium transition duration-150 
                            @if(request()->routeIs(['admin.courses.*', 'admin.categories.*'])) text-indigo-700 bg-indigo-50 @else text-gray-600 hover:bg-gray-50 hover:text-indigo-600 @endif" 
                            aria-expanded="false" id="kursus-dropdown-button">
                            Kursus
                            <svg class="ml-1 h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                        </button>
                        <div class="dropdown-menu absolute left-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-20" role="menu">
                            <a href="{{ route('admin.courses.index') }}" 
                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 @if(request()->routeIs(['admin.courses.index', 'admin.courses.create', 'admin.courses.edit'])) bg-indigo-50 text-indigo-700 font-semibold @endif" 
                               role="menuitem">List Kursus</a>
                            <a href="{{ route('admin.categories.index') }}" 
                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 @if(request()->routeIs(['admin.categories.index', 'admin.categories.create', 'admin.categories.edit'])) bg-indigo-50 text-indigo-700 font-semibold @endif" 
                               role="menuitem">Kategori</a>
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Pengaturan Kursus</a>
                        </div>
                    </div>
                    
                    {{-- Pengguna --}}
                    <a href="{{ route('admin.users.index') }}" 
                       class="text-gray-600 hover:bg-gray-50 hover:text-indigo-600 px-3 py-2 rounded-md text-sm font-medium transition duration-150 
                       @if(request()->routeIs('admin.users.*')) bg-indigo-50 text-indigo-700 font-semibold @endif">
                        Pengguna
                    </a>
                    
                    {{-- Pengaturan --}}
                    <a href="{{ route('admin.settings.edit') }}" class="text-gray-600 hover:bg-gray-50 hover:text-indigo-600 px-3 py-2 rounded-md text-sm font-medium transition duration-150 @if(request()->routeIs('admin.settings.*')) bg-indigo-50 text-indigo-700 font-semibold @endif">Pengaturan</a>
                </div>
            </nav>

            <div class="flex items-center">
                {{-- Profile Dropdown Desktop --}}
                <div class="hidden sm:block ml-4 relative dropdown" id="profile-dropdown-desktop">
                    <img class="h-8 w-8 rounded-full cursor-pointer border-2 border-indigo-500" src="https://via.placeholder.com/150/9333ea/ffffff?text=U" alt="User Profile" id="profile-dropdown-button">
                     <div class="dropdown-menu absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-20" role="menu">
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Profil Anda</a>
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Pengaturan</a>
                        <a href="#" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100 border-t mt-1" role="menuitem">Sign out</a>
                    </div>
                </div>

                {{-- Tombol Menu Mobile --}}
                <button id="mobile-menu-button" type="button" class="sm:hidden p-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500 ml-4" aria-controls="mobile-menu" aria-expanded="false">
                    <svg class="h-6 w-6" id="menu-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Menu Mobile --}}
    <div id="mobile-menu" class="sm:hidden">
        <div class="px-2 pt-2 pb-3 space-y-1">
            
            {{-- Dashboard Mobile --}}
            <a href="{{ route('admin.dashboard.index') }}" class="block text-gray-600 hover:bg-gray-50 hover:text-indigo-600 px-3 py-2 rounded-md text-base font-medium @if(request()->routeIs('admin.dashboard.index')) bg-indigo-50 text-indigo-700 font-semibold @endif">
                Dashboard
            </a>
            
            {{-- Kursus Mobile (Dropdown) --}}
            <div class="py-2 @if(request()->routeIs(['admin.courses.*', 'admin.categories.*'])) bg-indigo-50 rounded-md @endif">
                <button id="mobile-dropdown-kursus-button" class="flex justify-between items-center w-full text-gray-600 hover:bg-gray-50 hover:text-indigo-600 px-3 py-2 rounded-md text-base font-medium @if(request()->routeIs(['admin.courses.*', 'admin.categories.*'])) text-indigo-700 @endif">
                    <span>Kursus</span>
                    <svg id="mobile-kursus-icon" class="h-5 w-5 transition-transform duration-300 transform @if(request()->routeIs(['admin.courses.*', 'admin.categories.*'])) rotate-180 @endif" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                </button>
                <div id="mobile-kursus-menu" class="pl-6 pt-1 space-y-1" style="@if(request()->routeIs(['admin.courses.*', 'admin.categories.*'])) display: block; @else display: none; @endif">
                    <a href="{{ route('admin.courses.index') }}" 
                       class="block px-3 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-md @if(request()->routeIs(['admin.courses.index', 'admin.courses.create', 'admin.courses.edit'])) text-indigo-700 font-semibold bg-white shadow-sm @endif">List Kursus</a>
                    <a href="{{ route('admin.categories.index') }}" 
                       class="block px-3 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-md @if(request()->routeIs(['admin.categories.index', 'admin.categories.create', 'admin.categories.edit'])) text-indigo-700 font-semibold bg-white shadow-sm @endif">Kategori</a>
                    <a href="#" class="block px-3 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-md">Pengaturan Kursus</a>
                </div>
            </div>

            {{-- Pengguna Mobile --}}
            <a href="{{ route('admin.users.index') }}" 
               class="block text-gray-600 hover:bg-gray-50 hover:text-indigo-600 px-3 py-2 rounded-md text-base font-medium @if(request()->routeIs('admin.users.*')) bg-indigo-50 text-indigo-700 font-semibold @endif">
                Pengguna
            </a>
            
            {{-- Pengaturan Mobile --}}
            <a href="{{ route('admin.settings.edit') }}" class="block text-gray-600 hover:bg-gray-50 hover:text-indigo-600 px-3 py-2 rounded-md text-base font-medium @if(request()->routeIs('admin.settings.*')) bg-indigo-50 text-indigo-700 font-semibold @endif">Pengaturan</a>

            {{-- Bagian Profil Mobile --}}
            <div class="pt-4 border-t border-gray-100">
                <div class="flex items-center px-5">
                    <div class="flex-shrink-0">
                        <img class="h-10 w-10 rounded-full border-2 border-indigo-500" src="https://via.placeholder.com/150/9333ea/ffffff?text=U" alt="User Profile">
                    </div>
                    <div class="ml-3">
                        <div class="text-base font-medium text-gray-800">Nama User</div>
                        <div class="text-sm font-medium text-gray-500">user@example.com</div>
                    </div>
                </div>
                <div class="mt-3 space-y-1 px-2">
                    <a href="#" class="block px-3 py-2 rounded-md text-base font-medium text-gray-600 hover:bg-gray-50 hover:text-indigo-600">Profil Anda</a>
                    <a href="#" class="block px-3 py-2 rounded-md text-base font-medium text-red-600 hover:bg-red-50">Sign out</a>
                </div>
            </div>
        </div>
    </div>
</header>
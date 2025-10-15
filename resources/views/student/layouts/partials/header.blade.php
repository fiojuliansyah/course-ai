<header class="bg-white shadow sticky top-0 z-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            
            {{-- Nama Aplikasi --}}
            <div class="flex-shrink-0">
                <span class="text-xl font-bold text-gray-800 tracking-wider">E-Course | <span class="text-indigo-600">Siswa</span></span>
            </div>

            {{-- Navigasi Utama (Desktop) --}}
            <nav class="hidden sm:block">
                <div class="flex items-center space-x-4">
                    
                    {{-- Dashboard Siswa --}}
                    <a href="{{ route('student.dashboard') }}" 
                       class="text-gray-600 hover:bg-gray-50 hover:text-indigo-600 px-3 py-2 rounded-md text-sm font-medium transition duration-150 
                       @if(request()->routeIs('student.dashboard')) bg-indigo-50 text-indigo-700 font-semibold @endif">
                        Dashboard
                    </a>
                    
                    <a href="{{ route('student.courses.index') }}"
                       class="text-gray-600 hover:bg-gray-50 hover:text-indigo-600 px-3 py-2 rounded-md text-sm font-medium transition duration-150
                       @if(request()->routeIs('student.courses.index')) bg-indigo-50 text-indigo-700 font-semibold @endif">
                        Beli Kursus
                    </a>

                    <a href="{{ route('student.courses.enrolled.index') }}"
                       class="text-gray-600 hover:bg-gray-50 hover:text-indigo-600 px-3 py-2 rounded-md text-sm font-medium transition duration-150
                       @if(request()->routeIs('student.courses.enrolled.index')) bg-indigo-50 text-indigo-700 font-semibold @endif">
                        Kursus Saya
                    </a>
                    
                    @auth
                        @if(Auth::user()->role === 'admin')
                            <a href="{{ route('admin.dashboard.index') }}" 
                               class="text-yellow-600 hover:bg-yellow-50 px-3 py-2 rounded-md text-sm font-medium border border-yellow-300 transition duration-150">
                                Panel Admin
                            </a>
                        @endif
                    @endauth
                </div>
            </nav>

            <div class="flex items-center">
                {{-- Profile Dropdown Desktop --}}
                <div class="ml-4 relative dropdown" id="profile-dropdown-desktop">
                    <button id="profile-dropdown-button" class="max-w-xs bg-white flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-indigo-500" aria-expanded="false" aria-haspopup="true">
                        <span class="sr-only">Open user menu</span>
                        <img class="h-8 w-8 rounded-full border-2 border-indigo-500" src="https://ui-avatars.com/api/?name={{ Auth::user()->name ?? 'U' }}&background=E0E7FF&color=4F46E5&bold=true" alt="User Avatar">
                    </button>
                     <div class="dropdown-menu absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-20 hidden" role="menu">
                        <div class="px-4 py-2 text-sm text-gray-700 font-semibold border-b">
                            {{ Auth::user()->email ?? 'Guest' }}
                        </div>
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Pengaturan Akun</a>
                        
                        {{-- Link Logout Laravel --}}
                        <a href="{{ route('logout') }}" 
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                           class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50 border-t mt-1" role="menuitem">
                           Sign out
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </div>
                </div>

                {{-- Tombol Menu Mobile --}}
                <button id="mobile-menu-button" type="button" class="sm:hidden p-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500 ml-4" aria-controls="mobile-menu" aria-expanded="false">
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Menu Mobile (Logika Dropdown Mobile dihilangkan untuk keringkasan, harus diisi di Master Script) --}}
    <div id="mobile-menu" class="sm:hidden hidden">
        <div class="pt-2 pb-3 space-y-1 px-2">
            <a href="{{ route('student.dashboard') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-600 hover:bg-indigo-50 @if(request()->routeIs('student.dashboard')) text-indigo-700 bg-indigo-50 @endif">Dashboard</a>
            <a href="#" class="block px-3 py-2 rounded-md text-base font-medium text-gray-600 hover:bg-gray-50">Kursus Saya</a>
            
            @auth
                @if(Auth::user()->role === 'admin')
                    <a href="{{ route('admin.dashboard.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-yellow-700 hover:bg-yellow-50 border-t mt-1">Panel Admin</a>
                @endif
            @endauth

            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form-mobile').submit();"
               class="block px-3 py-2 rounded-md text-base font-medium text-red-600 hover:bg-red-50 border-t mt-1">
                Sign out
            </a>
            <form id="logout-form-mobile" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </div>
    </div>
</header>
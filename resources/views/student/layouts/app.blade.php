<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @stack('meta')
    <title>Dashboard E-course | Minimalis Elegan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        #mobile-menu {
            transition: max-height 0.3s ease-out;
            max-height: 0;
            overflow: hidden;
        }
        #mobile-menu.open {
            max-height: 500px;
        }
        .dropdown-menu {
            display: none;
        }
        .dropdown-open .dropdown-menu {
            display: block;
        }
    </style>
</head>
<body class="bg-gray-50 font-sans">

    @include('student.layouts.partials.header')

    @yield('content')

    @stack('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const mobileMenu = document.getElementById('mobile-menu');
            const menuIcon = document.getElementById('menu-icon');
            
            const kursusDropdownButtonMobile = document.getElementById('mobile-dropdown-kursus-button');
            const kursusMenuMobile = document.getElementById('mobile-kursus-menu');
            const kursusIconMobile = document.getElementById('mobile-kursus-icon');

            const kursusDropdownDesktop = document.getElementById('kursus-dropdown-desktop');
            const profileDropdownDesktop = document.getElementById('profile-dropdown-desktop');

            function closeAllDropdowns() {
                if(kursusDropdownDesktop) kursusDropdownDesktop.classList.remove('dropdown-open');
                if(profileDropdownDesktop) profileDropdownDesktop.classList.remove('dropdown-open');
            }

            if (mobileMenuButton) {
                mobileMenuButton.addEventListener('click', function() {
                    closeAllDropdowns();
                    const isOpen = mobileMenu.classList.toggle('open');
                    
                    if (isOpen) {
                        menuIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />';
                    } else {
                        menuIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />';
                    }
                });
            }

            if(kursusDropdownButtonMobile && kursusMenuMobile) {
                kursusMenuMobile.style.display = 'none';

                kursusDropdownButtonMobile.addEventListener('click', function() {
                    const isKursusOpen = kursusMenuMobile.style.display === 'block';

                    if (isKursusOpen) {
                        kursusMenuMobile.style.display = 'none';
                        if(kursusIconMobile) kursusIconMobile.classList.remove('rotate-180');
                    } else {
                        kursusMenuMobile.style.display = 'block';
                        if(kursusIconMobile) kursusIconMobile.classList.add('rotate-180');
                    }
                });
            }

            if(kursusDropdownDesktop && document.getElementById('kursus-dropdown-button')) {
                document.getElementById('kursus-dropdown-button').addEventListener('click', function(event) {
                    event.stopPropagation();
                    closeAllDropdowns();
                    kursusDropdownDesktop.classList.toggle('dropdown-open');
                });
            }

            if(profileDropdownDesktop && document.getElementById('profile-dropdown-button')) {
                document.getElementById('profile-dropdown-button').addEventListener('click', function(event) {
                    event.stopPropagation();
                    closeAllDropdowns();
                    profileDropdownDesktop.classList.toggle('dropdown-open');
                });
            }

            document.addEventListener('click', function(event) {
                const isClickInsideDropdown = 
                    (kursusDropdownDesktop && kursusDropdownDesktop.contains(event.target)) || 
                    (profileDropdownDesktop && profileDropdownDesktop.contains(event.target));
                
                if (!isClickInsideDropdown) {
                    closeAllDropdowns();
                }
            });
        });
    </script>
</body>
</html>
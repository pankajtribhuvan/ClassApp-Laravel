<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title','Codingwale Video Studio')</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"/>

    <script>

        tailwind.config = {

            theme: {

                extend: {

                    colors: {

                        background: '#0B0B0B',

                        sidebar: '#111827',

                        card: '#1A1A1A',

                        gold: '#D4AF37',

                        goldhover:'#FFD54F'

                    }

                }

            }

        }

    </script>

</head>

<body class="bg-background text-white">

<div
x-data="{ sidebar:true }"
class="flex h-screen overflow-hidden">

    <!-- Sidebar -->

    <aside
    :class="sidebar ? 'w-64' : 'w-20'"
    class="bg-sidebar duration-300 shadow-2xl">

        <!-- Logo -->

        <div class="h-16 flex items-center justify-center border-b border-gray-800">

            <div x-show="sidebar">

                <h2 class="text-2xl font-bold text-gold">

                    🎬 Video Studio

                </h2>

            </div>

            <div x-show="!sidebar">

                🎬

            </div>

        </div>

        <!-- Menu -->

        <nav class="mt-8">

            <a
            href="{{ route('dashboard.index') }}"
            class="flex items-center gap-4 px-6 py-4 hover:bg-gold hover:text-black transition">

                <i class="fa-solid fa-chart-line w-5"></i>

                <span x-show="sidebar">

                    Dashboard

                </span>

            </a>

            <a
            href="{{ route('videos.index') }}"
            class="flex items-center gap-4 px-6 py-4 hover:bg-gold hover:text-black transition">

                <i class="fa-solid fa-video w-5"></i>

                <span x-show="sidebar">

                    All Videos

                </span>

            </a>

            <a
            href="{{ route('videos.create') }}"
            class="flex items-center gap-4 px-6 py-4 hover:bg-gold hover:text-black transition">

                <i class="fa-solid fa-upload w-5"></i>

                <span x-show="sidebar">

                    Upload Video

                </span>

            </a>

            <hr class="border-gray-700 my-5">

            <a
            href="#"
            class="flex items-center gap-4 px-6 py-4 hover:bg-red-600 transition">

                <i class="fa-solid fa-right-from-bracket w-5"></i>

                <span x-show="sidebar">

                    Logout

                </span>

            </a>

        </nav>

    </aside>

    <!-- Content -->

    <div class="flex-1 flex flex-col">

        <!-- Navbar -->

        <header
        class="h-16 bg-card border-b border-gray-800 flex items-center justify-between px-6">

            <div class="flex items-center gap-4">

                <button
                @click="sidebar=!sidebar"
                class="text-gold text-xl">

                    <i class="fa-solid fa-bars"></i>

                </button>

                <h1 class="text-xl font-semibold">

                    @yield('page-title')

                </h1>

            </div>

            <div class="flex items-center gap-5">

                <button class="text-gold text-xl">

                    <i class="fa-solid fa-bell"></i>

                </button>

                <div class="flex items-center gap-3">

                    <div
                    class="w-10 h-10 rounded-full bg-gold text-black flex items-center justify-center font-bold">

                        A

                    </div>

                    <div>

                        <div class="font-semibold">

                            Admin

                        </div>

                        <div class="text-xs text-gray-400">

                            Codingwale

                        </div>

                    </div>

                </div>

            </div>

        </header>

        <!-- Main -->

        <main
        class="flex-1 overflow-auto p-8">

            @yield('content')

        </main>

    </div>

</div>

</body>

</html>
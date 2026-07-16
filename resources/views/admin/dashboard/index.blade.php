@extends('admin.layouts.app')

@section('title','Dashboard')

@section('page-title','Dashboard')

@section('content')

<div class="space-y-8">

    <!-- Header -->

    <div class="flex justify-between items-center">

        <div>

            <h1 class="text-3xl font-bold">

                Welcome 👋

            </h1>

            <p class="text-gray-400 mt-2">

                Codingwale Video Management Dashboard

            </p>

        </div>

        <a href="{{ route('videos.create') }}"
           class="bg-gold text-black px-6 py-3 rounded-xl font-semibold hover:bg-goldhover transition">

            <i class="fa-solid fa-upload mr-2"></i>

            Upload Video

        </a>

    </div>


    <!-- Statistics -->

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">

        <!-- Videos -->

        <div class="bg-card rounded-2xl p-6 shadow-lg border border-yellow-700/20">

            <div class="flex justify-between">

                <div>

                    <p class="text-gray-400">

                        Total Videos

                    </p>

                    <h2 class="text-4xl font-bold text-gold mt-3">

                        {{ $totalVideos }}

                    </h2>

                </div>

                <div class="text-5xl text-gold">

                    <i class="fa-solid fa-video"></i>

                </div>

            </div>

        </div>

        <!-- Storage -->

        <div class="bg-card rounded-2xl p-6 shadow-lg border border-yellow-700/20">

            <div class="flex justify-between">

                <div>

                    <p class="text-gray-400">

                        Storage Used

                    </p>

                    <h2 class="text-4xl font-bold text-gold mt-3">

                        {{ $storage }}

                    </h2>

                </div>

                <div class="text-5xl text-green-400">

                    <i class="fa-solid fa-hard-drive"></i>

                </div>

            </div>

        </div>

        <!-- Duration -->

        <div class="bg-card rounded-2xl p-6 shadow-lg border border-yellow-700/20">

            <div class="flex justify-between">

                <div>

                    <p class="text-gray-400">

                        Duration

                    </p>

                    <h2 class="text-4xl font-bold text-gold mt-3">

                        {{ $duration }}

                    </h2>

                </div>

                <div class="text-5xl text-red-400">

                    <i class="fa-solid fa-clock"></i>

                </div>

            </div>

        </div>

        <!-- Courses -->

        <div class="bg-card rounded-2xl p-6 shadow-lg border border-yellow-700/20">

            <div class="flex justify-between">

                <div>

                    <p class="text-gray-400">

                        Courses

                    </p>

                    <h2 class="text-4xl font-bold text-gold mt-3">

                        {{ $courses }}

                    </h2>

                </div>

                <div class="text-5xl text-blue-400">

                    <i class="fa-solid fa-book"></i>

                </div>

            </div>

        </div>

    </div>

    <!-- Charts -->

    <div class="grid xl:grid-cols-3 gap-6">

        <!-- Upload Chart -->

        <div class="xl:col-span-2 bg-card rounded-2xl p-6 shadow-lg">

            <h2 class="text-2xl font-bold text-gold mb-6">

                Upload Statistics

            </h2>

            <canvas id="uploadChart" height="120"></canvas>

        </div>

        <!-- Processing -->

        <div class="bg-card rounded-2xl p-6 shadow-lg">

            <h2 class="text-2xl font-bold text-gold mb-6">

                Processing Status

            </h2>

            <div class="space-y-4">

                <div class="flex justify-between">

                    <span>Completed</span>

                    <span class="font-bold text-green-400">

                        {{ $completed }}

                    </span>

                </div>

                <div class="flex justify-between">

                    <span>Processing</span>

                    <span class="font-bold text-yellow-400">

                        {{ $processing }}

                    </span>

                </div>

                <div class="flex justify-between">

                    <span>Failed</span>

                    <span class="font-bold text-red-400">

                        {{ $failed }}

                    </span>

                </div>

            </div>

            <hr class="my-6 border-gray-700">

            <p class="text-gray-400 mb-2">

                Storage Usage

            </p>

            <div class="w-full bg-gray-700 rounded-full h-3">

                <div class="bg-gold h-3 rounded-full"

                     style="width:{{ $storagePercent }}%">

                </div>

            </div>

            <p class="mt-3 text-sm">

                {{ $storagePercent }}% Used

            </p>

        </div>

    </div>

    <!-- Recent Upload -->

    <div class="bg-card rounded-2xl shadow-lg p-6">

        <div class="flex justify-between items-center mb-6">

            <h2 class="text-2xl font-bold text-gold">

                Recent Uploads

            </h2>

            <a href="{{ route('videos.index') }}"
               class="text-gold hover:underline">

                View All →

            </a>

        </div>

        <div class="overflow-x-auto">

            <table class="w-full">

                <thead>

                <tr class="border-b border-gray-700">

                    <th class="text-left py-4">Thumbnail</th>

                    <th>Course</th>

                    <th>Title</th>

                    <th>Size</th>

                    <th>Status</th>

                </tr>

                </thead>

                <tbody>

                @foreach($latest as $video)

                    <tr class="border-b border-gray-800 hover:bg-gray-800 transition">

                        <td class="py-4">

                            <img src="{{ $video->thumbnail_url }}"
                                 class="rounded-lg w-28">

                        </td>

                        <td>

                            {{ $video->course_name }}

                        </td>

                        <td>

                            {{ $video->title }}

                        </td>

                        <td>

                            {{ $video->readable_size }}

                        </td>

                        <td>

                            @if($video->status=='completed')

                                <span class="bg-green-600 px-3 py-1 rounded-full text-sm">

                                    Completed

                                </span>

                            @elseif($video->status=='processing')

                                <span class="bg-yellow-600 px-3 py-1 rounded-full text-sm">

                                    Processing

                                </span>

                            @else

                                <span class="bg-red-600 px-3 py-1 rounded-full text-sm">

                                    Failed

                                </span>

                            @endif

                        </td>

                    </tr>

                @endforeach

                </tbody>

            </table>

        </div>

    </div>

</div>

<script>

new Chart(document.getElementById('uploadChart'),{

    type:'bar',

    data:{

        labels:['Completed','Processing','Failed'],

        datasets:[{

            data:[
                {{ $completed }},
                {{ $processing }},
                {{ $failed }}
            ]

        }]

    },

    options:{

        plugins:{

            legend:{

                display:false

            }

        },

        scales:{

            y:{

                beginAtZero:true

            }

        }

    }

});

</script>

@endsection
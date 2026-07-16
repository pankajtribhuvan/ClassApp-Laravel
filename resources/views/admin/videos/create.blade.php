@extends('admin.layouts.app')

@section('title', 'Upload Video')

@section('page-title', 'Upload Video')

@section('content')
<div class="max-w-3xl mx-auto">
    
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold flex items-center gap-3">
                <i class="fa-solid fa-cloud-arrow-up text-gold"></i> Upload Video
            </h1>
            <p class="text-gray-400 mt-2">Publish a new MP4 video resource to your library</p>
        </div>

        <a href="{{ route('videos.index') }}" 
           class="bg-gray-800 hover:bg-gray-700 text-white px-5 py-2.5 rounded-xl font-semibold transition flex items-center gap-2">
            <i class="fa-solid fa-arrow-left"></i>
            Back
        </a>
    </div>

    <div class="bg-card rounded-2xl shadow-lg border border-gray-800/60 overflow-hidden">
        <div class="p-8">
            
            @if ($errors->any())
                <div class="bg-red-600/20 border border-red-500 text-red-400 px-4 py-3 rounded-xl mb-6">
                    <ul class="list-disc list-inside space-y-1 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form id="uploadForm"
                  action="{{ route('videos.store') }}"
                  method="POST"
                  enctype="multipart/form-data"
                  class="space-y-6">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">
                        Course Name
                    </label>
                    <input type="text"
                           name="course_name"
                           class="w-full bg-background border border-gray-700 rounded-xl px-4 py-3 text-white placeholder-gray-600 focus:outline-none focus:border-gold transition"
                           placeholder="Example : Flutter"
                           required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">
                        Video Title
                    </label>
                    <input type="text"
                           name="title"
                           class="w-full bg-background border border-gray-700 rounded-xl px-4 py-3 text-white placeholder-gray-600 focus:outline-none focus:border-gold transition"
                           placeholder="Example : Variables in Dart"
                           required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">
                        Select MP4 Video
                    </label>
                    <input type="file"
                           id="video"
                           name="video"
                           class="w-full text-sm text-gray-400 
                                  file:mr-4 file:py-2.5 file:px-4
                                  file:rounded-xl file:border-0
                                  file:text-sm file:font-semibold
                                  file:bg-gold file:text-black
                                  hover:file:bg-goldhover file:cursor-pointer
                                  bg-background border border-gray-700 rounded-xl p-1.5 focus:outline-none focus:border-gold transition"
                           accept="video/mp4"
                           required>
                </div>

                <div id="fileInfo" class="hidden bg-sidebar border border-gray-800 rounded-xl p-5 space-y-2">
                    <strong class="text-gold text-sm flex items-center gap-2">
                        <i class="fa-solid fa-file-video"></i> Selected File Meta
                    </strong>
                    <hr class="border-gray-800 my-2">
                    <p class="text-sm text-gray-300">
                        <strong class="text-gray-400">Name:</strong> 
                        <span id="fileName" class="break-all ml-1"></span>
                    </p>
                    <p class="text-sm text-gray-300">
                        <strong class="text-gray-400">Size:</strong> 
                        <span id="fileSize" class="ml-1"></span>
                    </p>
                </div>

                <button type="submit" 
                        id="uploadBtn"
                        class="w-full bg-gold hover:bg-goldhover text-black font-bold py-3.5 px-6 rounded-xl transition flex items-center justify-center gap-2 shadow-lg disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fa-solid fa-upload"></i>
                    Upload Video
                </button>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('video').addEventListener('change', function(){
    let file = this.files[0];
    if(!file) return;

    // Remove Tailwind hidden class configuration rules
    document.getElementById('fileInfo').classList.remove('hidden');
    document.getElementById('fileName').innerHTML = file.name;
    document.getElementById('fileSize').innerHTML = (file.size / 1024 / 1024).toFixed(2) + ' MB';
});

document.getElementById('uploadForm').addEventListener('submit', function(){
    let btn = document.getElementById('uploadBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner animate-spin"></i> Uploading Resource...';
});
</script>
@endsection
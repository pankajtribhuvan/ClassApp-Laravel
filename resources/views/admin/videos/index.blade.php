@extends('admin.layouts.app')

@section('title', 'Video Library')

@section('page-title', 'Video Library')

@section('content')
<div class="space-y-8">

    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold flex items-center gap-3">
                <i class="fa-solid fa-video text-gold"></i> Video Library
            </h1>
            <p class="text-gray-400 mt-2">
                Total Videos: <span class="font-bold text-gold">{{ $videos->count() }}</span>
            </p>
        </div>

        <a href="{{ route('videos.create') }}" 
           class="bg-gold text-black px-6 py-3 rounded-xl font-semibold hover:bg-goldhover transition flex items-center gap-2">
            <i class="fa-solid fa-upload"></i>
            Upload Video
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-600/20 border border-green-500 text-green-400 px-4 py-3 rounded-xl">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-card rounded-2xl shadow-lg p-6">
        <div class="overflow-x-auto">
            <table class="w-full align-middle text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-700 text-gray-400 text-sm">
                        <th class="py-4 px-2">#</th>
                        <th class="py-4">Thumbnail</th>
                        <th class="py-4">Course</th>
                        <th class="py-4">Title</th>
                        <th class="py-4">Duration</th>
                        <th class="py-4">Size</th>
                        <th class="py-4">Status</th>
                        <th class="py-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800">
                @forelse($videos as $video)
                    <tr class="border-b border-gray-800 hover:bg-gray-800/40 transition">
                        <td class="py-4 px-2 font-bold text-gray-400">
                            {{ $video->id }}
                        </td>
                        <td class="py-4">
                            @if($video->thumbnail_url)
                                <img src="{{ $video->thumbnail_url }}" 
                                     class="rounded-lg w-28 h-16 object-cover border border-gray-700 shadow-sm">
                            @else
                                <div class="bg-gray-800 text-gray-400 rounded-lg w-28 h-16 flex items-center justify-center text-xs border border-gray-700">
                                    No Image
                                </div>
                            @endif
                        </td>
                        <td class="py-4">
                            <span class="font-semibold text-gold">
                                {{ $video->course_name }}
                            </span>
                        </td>
                        <td class="py-4 font-semibold">
                            {{ $video->title }}
                        </td>
                        <td class="py-4 text-gray-300">
                            {{ $video->duration ? gmdate('i:s', (int)$video->duration) : '--' }}
                        </td>
                        <td class="py-4 font-medium text-gray-300">
                            {{ $video->readable_size }}
                        </td>
                        <td class="py-4">
                            @switch($video->status)
                                @case('uploaded')
                                    <span class="bg-yellow-600/20 text-yellow-400 border border-yellow-500/30 px-3 py-1 rounded-full text-xs font-semibold">
                                        Uploaded
                                    </span>
                                    @break
                                @case('processing')
                                    <span class="bg-blue-600/20 text-blue-400 border border-blue-500/30 px-3 py-1 rounded-full text-xs font-semibold">
                                        Processing
                                    </span>
                                    @break
                                @case('uploading')
                                    <span class="bg-cyan-600/20 text-cyan-400 border border-cyan-500/30 px-3 py-1 rounded-full text-xs font-semibold">
                                        Uploading
                                    </span>
                                    @break
                                @case('completed')
                                    <span class="bg-green-600/20 text-green-400 border border-green-500/30 px-3 py-1 rounded-full text-xs font-semibold">
                                        Completed
                                    </span>
                                    @break
                                @case('failed')
                                    <span class="bg-red-600/20 text-red-400 border border-red-500/30 px-3 py-1 rounded-full text-xs font-semibold">
                                        Failed
                                    </span>
                                    @break
                            @endswitch
                        </td>
                        <td class="py-4 text-right space-x-1 whitespace-nowrap">
                            @if($video->hls_url)
                                <a href="{{ $video->hls_url }}" target="_blank" 
                                   class="inline-flex items-center justify-center w-8 h-8 bg-green-600 hover:bg-green-500 text-white rounded-lg text-sm transition shadow-md">
                                    <i class="fa fa-play text-xs"></i>
                                </a>
                            @endif

                            <button class="editBtn inline-flex items-center justify-center w-8 h-8 bg-blue-600 hover:bg-blue-500 text-white rounded-lg text-sm transition shadow-md"
                                    data-id="{{ $video->id }}">
                                <i class="fa fa-edit text-xs"></i>
                            </button>

                            <button class="deleteBtn inline-flex items-center justify-center w-8 h-8 bg-red-600 hover:bg-red-500 text-white rounded-lg text-sm transition shadow-md"
                                    data-id="{{ $video->id }}"
                                    data-title="{{ $video->title }}">
                                <i class="fa fa-trash text-xs"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-gray-500 py-12">
                            <i class="fa-solid fa-video-slash text-4xl mb-3 block"></i>
                            <h5 class="text-lg font-medium">No Videos Uploaded</h5>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div x-data="{ open: false }" 
     @open-edit-modal.window="open = true; $('#video_id').val($event.detail.id); $('#course_name').val($event.detail.course_name); $('#title').val($event.detail.title);"
     @close-edit-modal.window="open = false"
     x-show="open" 
     class="fixed inset-0 z-50 flex items-center justify-center overflow-auto bg-black/60 backdrop-blur-sm"
     style="display: none;">
    
    <div @click.away="open = false" class="bg-card w-full max-w-md mx-4 rounded-2xl shadow-2xl border border-gray-800 overflow-hidden transform transition-all">
        <form id="editForm">
            @csrf
            @method('PUT')
            
            <div class="px-6 py-4 bg-sidebar border-b border-gray-800 flex justify-between items-center">
                <h3 class="text-xl font-bold text-gold">Edit Video</h3>
                <button type="button" @click="open = false" class="text-gray-400 hover:text-white transition text-lg">&times;</button>
            </div>

            <div class="p-6 space-y-4">
                <input type="hidden" id="video_id">

                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">Course Name</label>
                    <input type="text" id="course_name" 
                           class="w-full bg-background border border-gray-700 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-gold transition">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">Video Title</label>
                    <input type="text" id="title" 
                           class="w-full bg-background border border-gray-700 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-gold transition">
                </div>
            </div>

            <div class="px-6 py-4 bg-sidebar border-t border-gray-800 flex justify-end gap-3">
                <button type="button" @click="open = false" 
                        class="px-5 py-2.5 bg-gray-800 hover:bg-gray-700 text-white rounded-xl font-medium transition">
                    Cancel
                </button>
                <button type="submit" id="updateBtn" 
                        class="px-5 py-2.5 bg-gold hover:bg-goldhover text-black font-semibold rounded-xl transition">
                    Update
                </button>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Open modal handling via Alpine events dispatcher
$(document).on('click', '.editBtn', function(){
    let id = $(this).data('id');
    $.ajax({
        url: '/videos/' + id + '/edit',
        type: 'GET',
        success: function(response){
            window.dispatchEvent(new CustomEvent('open-edit-modal', { 
                detail: {
                    id: response.video.id,
                    course_name: response.video.course_name,
                    title: response.video.title
                } 
            }));
        },
        error: function(){
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Unable to load video.',
                background: '#1A1A1A',
                color: '#fff'
            });
        }
    });
});

// Update Form Handling
$('#editForm').submit(function(e){
    e.preventDefault();
    let id = $('#video_id').val();
    
    $('#updateBtn').prop('disabled', true);
    $('#updateBtn').html('<i class="fa-solid fa-spinner animate-spin mr-2"></i> Updating...');

    $.ajax({
        url: '/videos/' + id,
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            _method: 'PUT',
            course_name: $('#course_name').val(),
            title: $('#title').val()
        },
        success: function(response){
            window.dispatchEvent(new CustomEvent('close-edit-modal'));
            
            let row = $('.editBtn[data-id="'+id+'"]').closest('tr');
            row.find('td:eq(2)').html('<span class="font-semibold text-gold">' + response.video.course_name + '</span>');
            row.find('td:eq(3)').html('<span>' + response.video.title + '</span>');

            Swal.fire({
                icon: 'success',
                title: 'Updated',
                text: response.message,
                timer: 1500,
                showConfirmButton: false,
                background: '#1A1A1A',
                color: '#fff'
            });
        },
        error: function(xhr){
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Unable to update video.',
                background: '#1A1A1A',
                color: '#fff'
            });
        },
        complete: function(){
            $('#updateBtn').prop('disabled', false);
            $('#updateBtn').html('Update');
        }
    });
});

// Delete Handler
$(document).on('click', '.deleteBtn', function () {
    let id = $(this).data('id');
    let title = $(this).data('title');

    Swal.fire({
        title: 'Delete Video?',
        text: 'Are you sure you want to delete "' + title + '" ?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#374151',
        confirmButtonText: 'Yes, Delete',
        cancelButtonText: 'Cancel',
        background: '#1A1A1A',
        color: '#fff'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/videos/' + id,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    _method: 'DELETE'
                },
                success: function (response) {
                    $('.deleteBtn[data-id="' + id + '"]').closest('tr').fadeOut(400, function () {
                        $(this).remove();
                    });
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false,
                        background: '#1A1A1A',
                        color: '#fff'
                    });
                },
                error: function (xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Delete Failed',
                        text: 'Unable to delete video.',
                        background: '#1A1A1A',
                        color: '#fff'
                    });
                }
            });
        }
    });
});
</script>
@endsection
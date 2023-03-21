@extends('layout.layout')
@section('content')
    <form action="{{ route($action, $post->id ?? null) }}" method="POST">
        @csrf
        @if (isset($post))
            @method('PUT')
        @endif

        <div class="mb-4">
            <label for="title" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Post title</label>
            <input id="title"
                   name="title"
                   type="text"
                   class="@error('title') is-invalid @enderror bg-gray-50 border border-gray-300 text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                   value="{{ isset($post) ? old('title', $post->title) : '' }}"
            >
            @error('title')
            <div class="">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-4">
            <label for="content" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Post
                content</label>
            <textarea id="content"
                      name="content"
                      rows="10"
                      class="bg-gray-50 border border-gray-300 text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
            >{{ isset($post) ? old('content', $post->content) : '' }}</textarea>
            @error('content')
            <div class="">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-4">
            <button type="submit"
                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                @if (isset($post))
                    Save
                @else
                    Add
                @endif
            </button>
        </div>
    </form>
@endsection

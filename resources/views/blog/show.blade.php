@extends('layout.layout')
@section('content')
    <div class="col-12 pt-2">
        @if ($post ==! null)
            <h1 class="text-4xl uppercase mb-2">{{ $post->title }}</h1>
            <p class="text-black italic mb-6">{{ $post->created_at }}</p>
            <p class="text-black text-base font-thin mb-2">{!! $post->content !!}</p>
        @else
            <p class="text-warning">No such blog Post available</p>
        @endif

        @auth
            <span>
                {{ '(' }}<a href="{{ route('blog.edit', $post->id) }}" class="text-gray-500 italic lowercase">Edit</a> |
                <form action="{{ route('blog.destroy', $post->id) }}" class="inline-block" method="POST">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="id" value="{{ $post->id }}">
                    <button type="submit" class="text-gray-500 italic lowercase">Delete</button>
                </form>{{ ')' }}
            </span>
        @endauth
    </div>
@endsection
@vite(['resources/js/Plugins/prism/prism.js', "resources/js/Plugins/prism/prism.css"])

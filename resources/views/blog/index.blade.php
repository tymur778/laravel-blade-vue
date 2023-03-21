@extends('layout.layout')
@section('content')
    <div class="col-12 pt-2">
        @if (session()->has('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        @auth
            <div class="col-4 mb-6">
                <a href="{{ route('blog.create') }}" class="text-gray-500 italic">Add Post</a>
            </div>
        @endauth

        @forelse($posts as $post)
            @include('blog.card')
        @empty
            <p class="text-warning">No blog Posts available</p>
        @endforelse
        <div class="pagination">
            {{ $posts->links() }}
        </div>
    </div>
@endsection

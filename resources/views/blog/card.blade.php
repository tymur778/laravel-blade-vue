<div class="mb-10">
    <a class="text-black uppercase text-xl bold visited:black mr-2" href="{{ route('blog.show', $post->id) }}">{{ $post->title }}</a><!--
    @auth
        --><span><!--
            -->{{ '(' }}<a href="{{ route('blog.edit', $post->id) }}" class="text-gray-500 italic lowercase">Edit</a> |
            <form action="{{ route('blog.destroy', $post->id) }}" class="inline-block" method="POST">
                @csrf
                @method('DELETE')
                <input type="hidden" name="id" value="{{ $post->id }}">
                <button type="submit" class="text-gray-500 italic lowercase">Delete</button>
            </form>{{ ')' }}
        </span>
    @endauth
    <p class="italic text-black">{{ $post->created_at }}</p>
</div>

<footer class="py-2 text-right text-sm">
    <div>
        <span class="text-gray-500">Rendered with {{ ucfirst(RenderType::BLADE->value) }}.</span>
        <a href="{{ url('/set_render_type') }}" class="underline block sm:inline-block">Swap to
            @if ($renderType === RenderType::BLADE->value)
                <span>Vue.js + Inertia components</span>
            @else
                <span>Blade Templates</span>
            @endif
        </a>
    </div>
    <div class="">
        <span class="text-sm text-gray-500 sm:text-center dark:text-gray-400">© {{ $currentYear }}</span>
        [<a href="123" class="text-gray-500 pr-1 last:pr-0">Model.</a><!--
      --><a href="123" class="text-gray-500 pr-1 last:pr-0">Controller.</a><!--
      --><a href="{{ url('/swap-component') }}" class="text-gray-500 pr-1 last:pr-0">View</a>]
        <span class="text-sm block sm:inline-block text-gray-500 sm:text-center dark:text-gray-400">
            PHP:{{ $phpVersion }} Laravel:{{ $laravelVersion }}
        </span>
    </div>
</footer>

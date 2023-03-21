<nav class="bg-white">
    <div class="relative flex justify-center h-16 items-center">
        <div class="absolute inset-y-0 left-0 flex items-center sm:hidden">
            <!-- Mobile menu button-->
            <button type="button" id="mobile-menu-button" class="inline-flex items-center justify-center text-gray-700 hover:bg-gray-900 hover:text-white focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white" aria-controls="mobile-menu" aria-expanded="false">
                <span class="sr-only">Open main menu</span>
                <svg class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                    <path class="hidden" stroke-linecap="square" stroke-linejoin="square" d="M6 18L18 6M6 6l12 12" />
                    <path class="block" stroke-linecap="square" stroke-linejoin="square" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
            </button>
        </div>
        <div class="flex flex-1 items-center">
            <div class="hidden flex sm:flex justify-between w-full">
                <div class="flex space-x-0 justify-between w-full">
                    @foreach ($menu as $menuItem)
                        <a href="{{ url($menuItem['url']) }}" class="{{ checkMenuIsActive($menuItem['url']) ? 'border-b-2' : '' }} text-left grow mx-0 border-black uppercase text-black py-2 text-base font-medium">{{$menuItem['title']}}</a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile menu, show/hide based on menu state. -->
    <div class="sm:hidden">
        <div class="hidden space-y-1 pt-2 pb-3" id="mobile-menu">
            <!-- Current: "bg-gray-900 text-white", Default: "text-gray-300 hover:bg-gray-700 hover:text-white" -->
            @foreach ($menu as $menuItem)
                <a href="{{ url($menuItem['url']) }}" class="{{ Request::is($menuItem['url'] . '*') ? 'border-b-2' : '' }} border-black uppercase text-black hover:bg-gray-900 text-xl hover:text-white block py-2 text-base font-medium" >{{$menuItem['title']}}</a>
            @endforeach
            @auth
                <form action="{{ route('logout') }}" class="border-black text-xl uppercase text-black hover:bg-gray-900 hover:text-white block py-2 text-base font-medium" method="POST">
                    @csrf
                    <button type="submit" class="uppercase">Logout</button>
                </form>
            @endauth
        </div>
    </div>

    <div class="hidden sm:block uppercase absolute top-0 right-0 p-2 opacity-0">
        @auth
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="p-10" type="submit">Logout</button>
            </form>
        @endauth

        @guest
            <a class="p-10" href="{{ route('login') }}">Login</a>
        @endguest
    </div>
</nav>
<script type="text/javascript">
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const mobileMenu = document.getElementById('mobile-menu');

    mobileMenuButton.addEventListener('click', () => {
        var svg = mobileMenuButton.querySelectorAll('path');
        for (var i = 0; i < svg.length; i++) {
            svg[i].classList.toggle('hidden');
        }
        mobileMenu.classList.toggle('hidden');
    });
</script>

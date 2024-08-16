<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="p-2 text-2xl">Most Popular Books</h1>
                    <ol>
                        @foreach ($popularBooks as $book)
                            <li class="p-2 list-inside list-decimal">{{$book->title}} - Claimed {{$book->claimed_count}} times.</li>
                        @endforeach
                    </ol>
                    <h1 class="p-2 text-2xl">Least Popular Books</h1>
                    <ol>
                        @foreach ($leastPopular as $book)
                            <li class="p-2 list-inside list-decimal">{{$book->title}} - Claimed {{$book->claimed_count}} times.</li>
                        @endforeach
                    </ol>
                    <h3 class="p-2 text-2xl">Best genre</h3>
                    @foreach ($bestGenre as $genre)
                        <span class="p-2 font-bold">{{$genre->name}} -</span><span>Books in this genre have been claimed {{$bestGenreCount}} times.</span>
                    @endforeach
                    <h3 class="p-2 text-2xl">Worst genre</h3>
                    @foreach ($worstGenre as $genre)
                        <span class="p-2 font-bold">{{$genre->name}} -</span><span>Books in this genre have been claimed {{$worstGenreCount}} times.</span>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

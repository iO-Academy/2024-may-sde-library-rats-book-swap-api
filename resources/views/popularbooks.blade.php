<html>
<head>
{{--    <script src="https://cdn.tailwindcss.com"></script>--}}
</head>
<body>
    <h1 class="p-2">Most Popular Books</h1>
    <ol>
        @foreach ($popularBooks as $book)
            <li class="p-2">{{$book->title}} - Claimed {{$book->claimed_count}} times.</li>
        @endforeach
    </ol>
    <h1 class="p-2">Least Popular Books</h1>
    <ol>
    @foreach ($leastPopular as $book)
        <li class="p-2">{{$book->title}} - Claimed {{$book->claimed_count}} times.</li>
    @endforeach
    </ol>
    <h3>Best genre</h3>
    @foreach ($bestGenre as $genre)
        <p class="p-2">{{$genre->name}} - Books in this genre have been claimed {{$bestGenreCount}} times.</p>
    @endforeach
    <h3>Worst genre</h3>
    @foreach ($worstGenre as $genre)
        <p class="p-2">{{$genre->name}} - Books in this genre have been claimed {{$worstGenreCount}} times.</p>
    @endforeach
</body>
</html>


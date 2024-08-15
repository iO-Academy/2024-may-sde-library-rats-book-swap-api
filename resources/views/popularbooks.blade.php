<html>
<head>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
<h1 class="p-2">Most Poplear Books</h1>
@foreach ($popularBooks as $book)
    <h2 class="p-2">{{$book->title}}</h2>
@endforeach
<h1 class="p-2">Least Popular Books</h1>
@foreach ($leastPopular as $book)
    <h2 class="p-2">{{$book->title}}</h2>
@endforeach
<h3>Worst genre</h3>
<p>{{$worstGenre->genre_id}}</p>
</body>
</html>


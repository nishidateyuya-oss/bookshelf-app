<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Models\Book;
use App\Models\Genre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $query = Book::with('genres')->withAvg('reviews', 'rating');

        if ($request->filled('keyword')) {
            $keyword = $request->input('keyword');
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                    ->orWhere('author', 'like', "%{$keyword}%");
            });
        }
        if ($request->filled('genre')) {
            $genre_id = $request->input('genre');
            $query->whereHas('genres', function ($q) use ($genre_id) {
                $q->where('genres.id', $genre_id);
            });
        }
        if ($request->filled('sort')) {
            $sort = $request->input('sort');
            if ($sort === 'newest') {
                $query->orderBy('created_at', 'desc');
            }
            if ($sort === 'oldest') {
                $query->orderBy('created_at', 'asc');
            }
            if ($sort === 'title') {
                $query->orderBy('title');
            }
            if ($sort === 'rating') {
                $query->orderBy('reviews_avg_rating', 'desc');
            }
        }

        $books = $query->paginate(10)->appends($request->query());
        $genres = Genre::all();

        return view('books.index', compact(['books', 'genres']));
    }

    public function show(Book $book)
    {
        return view('books.show', compact('book'));
    }

    public function edit(Book $book)
    {
        $this->authorize('update', $book);

        $genres = Genre::all();

        return view('books.edit', compact(['book', 'genres']));
    }

    public function create()
    {

        $genres = Genre::all();

        return view('books.create', compact('genres'));
    }

    public function store(StoreBookRequest $request)
    {

        $validated = $request->validated();
        $genreIds = $validated['genres'];
        $validated['user_id'] = auth()->user()->id;
        unset($validated['genres']);

        $book = Book::create($validated);
        $book->genres()->sync($genreIds);

        return redirect()->route('books.index')->with('success', '書籍情報を登録しました');
    }

    public function update(UpdateBookRequest $request, Book $book)
    {

        $this->authorize('update', $book);

        $validated = $request->validated();
        $genreIds = $validated['genres'];
        unset($validated['genres']);

        $book->update($validated);
        $book->genres()->sync($genreIds);

        return redirect()->route('books.index')->with('success', '書籍情報を更新しました');
    }

    public function destroy(Book $book)
    {
        $this->authorize('delete', $book);

        $book->delete();

        return redirect()->route('books.index')->with('success', '書籍情報を削除しました');
    }

    public function ranking()
    {
        $rankedBooks = Book::withAvg('reviews', 'rating')->withCount('reviews')->orderByDesc('reviews_avg_rating')->get();

        return view('ranking.index', compact('rankedBooks'));
    }

    public function searchByIsbn(string $isbn)
    {

        if (strlen($isbn) !== 13 || ! ctype_digit($isbn)) {
            return response()->json(['error' => 'ISBNは13桁で入力してください。'], 400);
        }

        $apiKey = config('services.google_books.api_key');

        $params = ['q' => 'isbn:'.$isbn];
        if ($apiKey) {
            $params['key'] = $apiKey;
        }

        $response = Http::get('https://www.googleapis.com/books/v1/volumes', $params);

        if ($response->status() === 429) {
            return response()->json([
                'error' => 'Google Books API のクォータを超過しました。.env に GOOGLE_BOOKS_API_KEY を設定してください。',
            ], 429);
        }

        if ($response->failed()) {
            return response()->json(['error' => 'API通信エラーが発生しました。'], 500);
        }

        $data = $response->json();

        if (($data['totalItems'] ?? 0) === 0 || empty($data['items'])) {
            return response()->json(['error' => '書籍が見つかりませんでした。'], 404);
        }

        $item = $data['items'][0]['volumeInfo'] ?? [];

        $imageUrl = $item['imageLinks']['thumbnail'] ?? '';
        if ($imageUrl) {
            $imageUrl = str_replace('http://', 'https://', $imageUrl);
        }

        return response()->json([
            'title' => $item['title'] ?? '',
            'author' => implode(', ', $item['authors'] ?? []),
            'published_date' => $item['publishedDate'] ?? '',
            'description' => $item['description'] ?? '',
            'image_url' => $imageUrl,
        ]);
    }
}

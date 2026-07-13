<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Models\Book;
use App\Models\Genre;

class BookController extends Controller
{
    public function index()
    {
        $books = Book::with('genres')->paginate(10);

        return view('books.index', compact('books'));
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
        $book->genres()->attach($genreIds);

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
}

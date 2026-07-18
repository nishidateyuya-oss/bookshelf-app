<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\IndexBookRequest;
use App\Http\Requests\Api\V1\StoreBookRequest;
use App\Http\Requests\Api\V1\UpdateBookRequest;
use App\Http\Resources\BookResource;
use App\Models\Book;

class BookController extends Controller
{
    public function index(IndexBookRequest $request)
    {
        $query = Book::with('genres')->withAvg('reviews', 'rating');

        if ($request->filled('keyword')) {
            $keyword = $request->input('keyword');
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                    ->orWhere('author', 'like', "%{$keyword}%");
            });
        }
        if ($request->filled('genre_id')) {
            $genre_id = $request->input('genre_id');
            $query->whereHas('genres', function ($q) use ($genre_id) {
                $q->where('id', $genre_id);
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

        $perPage = $request->input('per_page', 20);
        $books = $query->paginate($perPage)->appends($request->query());

        return BookResource::collection($books);
    }

    public function show(Book $book)
    {
        $book->load(['genres', 'reviews.user'])
            ->loadAvg('reviews', 'rating')
            ->loadCount('reviews');

        return new BookResource($book);
    }

    public function store(StoreBookRequest $request)
    {
        $validated = $request->validated();
        $genreIds = $validated['genres'];
        unset($validated['genres']);

        $book = Book::create($validated);
        $book->genres()->sync($genreIds);

        $book->load(['genres', 'reviews.user'])
            ->loadAvg('reviews', 'rating')
            ->loadCount('reviews');

        return (new BookResource($book))->response()->setStatusCode(201);
    }

    public function update(UpdateBookRequest $request, Book $book)
    {
        $validated = $request->validated();
        $genreIds = $validated['genres'];
        unset($validated['genres']);

        $book->update($validated);
        $book->genres()->sync($genreIds);

        $book->load(['genres', 'reviews.user'])
            ->loadAvg('reviews', 'rating')
            ->loadCount('reviews');

        return new BookResource($book);
    }

    public function destroy(Book $book)
    {
        $book->delete();

        return response()->json(null, 204);
    }
}

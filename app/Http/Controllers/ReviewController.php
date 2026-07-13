<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReviewRequest;
use App\Models\Book;
use App\Models\Review;

class ReviewController extends Controller
{
    public function store(StoreReviewRequest $request, Book $book)
    {
        $validated = $request->validated();
        $validated['user_id'] = auth()->user()->id;
        $validated['book_id'] = $book->id;

        Review::create($validated);

        return back()->with('success', 'レビューを投稿しました');
    }

    public function like(Review $review)
    {
        auth()->user()->likedReviews()->toggle($review->id);

        return back();
    }

    public function edit(Review $review)
    {
        $this->authorize('update', $review);

        return view('reviews.edit', compact('review'));
    }

    public function update(StoreReviewRequest $request, Review $review)
    {
        $this->authorize('update', $review);
        $validated = $request->validated();
        $book = $review->book;

        $review->update($validated);

        return redirect()->route('books.show', $book)->with('success', 'レビューを更新しました');
    }

    public function destroy(Review $review)
    {
        $this->authorize('delete', $review);

        $review->delete();

        return back()->with('success', 'レビューを削除しました');
    }
}

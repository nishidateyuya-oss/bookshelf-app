<?php

namespace Tests\Unit\Models;


use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Book;
use App\Models\Review;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;
    public function test_user_has_many_books(): void
    {
        $user = User::factory()->create();
        Book::factory()->count(5)->for($user)->create();

        $this->assertCount(5, $user->fresh()->books);
        $this->assertInstanceOf(Book::class, $user->books->first());
    }

    public function test_user_has_many_reviews(): void {
        $user = User::factory()->create();
        Review::factory()->count(5)->for($user)->create();

        $this->assertCount(5, $user->fresh()->reviews);
        $this->assertInstanceOf(Review::class, $user->reviews->first());
    }

    public function test_user_belongs_to_many_books(): void {
        $user = User::factory()->create();
        $books = Book::factory()->count(5)->create();

        $user->favoriteBooks()->attach($books->pluck('id'));

        $user->load('favoriteBooks');

        $this->assertCount(5, $user->favoriteBooks);
        $this->assertTrue($user->favoriteBooks->pluck('id')->contains($books->first()->id));
    }

    public function test_user_belongs_to_many_reviews(): void {
        $user = User::factory()->create();
        $reviews = Review::factory()->count(5)->create();

        $user->likedReviews()->attach($reviews->pluck('id'));

        $user->load('likedReviews');

        $this->assertCount(5, $user->likedReviews);
        $this->assertTrue($user->likedReviews->pluck('id')->contains($reviews->first()->id));
    }
}

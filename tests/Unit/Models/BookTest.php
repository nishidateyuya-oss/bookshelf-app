<?php

namespace Tests\Unit\Models;

use App\Models\Book;
use App\Models\Genre;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookTest extends TestCase
{
    use RefreshDatabase;

    public function test_book_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->for($user)->create();

        $this->assertTrue($book->user->is($user));
    }

    public function test_book_belongs_to_many_genres(): void
    {
        $book = Book::factory()->create();
        $genres = Genre::factory()->count(3)->create();

        $book->genres()->attach($genres->pluck('id'));

        $book->load('genres');

        $this->assertCount(3, $book->genres);
        $this->assertTrue($book->genres->pluck('id')->contains($genres->first()->id));
    }

    public function test_book_has_many_reviews(): void
    {
        $book = Book::factory()->create();
        Review::factory()->count(5)->for($book)->create();

        $this->assertCount(5, $book->fresh()->reviews);
        $this->assertInstanceOf(Review::class, $book->reviews->first());
    }

    public function test_book_belongs_to_many_users(): void
    {
        $book = Book::factory()->create();
        $users = User::factory()->count(5)->create();

        $book->favoritedByUsers()->attach($users->pluck('id'));

        $book->load('favoritedByUsers');

        $this->assertCount(5, $book->favoritedByUsers);
        $this->assertTrue($book->favoritedByUsers->pluck('id')->contains($users->first()->id));
    }
}

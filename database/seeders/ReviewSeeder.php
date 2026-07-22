<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userIds = User::pluck('id')->take(5)->toArray();
        $bookIds = Book::pluck('id')->take(11)->toArray();

        $comments = [
            1 => [
                '残念ながら合いませんでした。',
                '期待と違いました。',
            ],
            2 => [
                '少し期待外れでした。',
                '内容が薄い印象。',
                'もう少し深掘りしてほしかった。',
            ],
            3 => [
                '普通でした。',
                '可もなく不可もなく。',
                '期待したほどではなかった。',
            ],
            4 => [
                'とても参考になりました。',
                '読みやすくておすすめです。',
                '期待通りの内容でした。',
            ],
            5 => [
                '素晴らしい本でした！',
                '人生が変わりました。',
                '何度も読み返しています。',
            ],
        ];

        foreach ($bookIds as $index => $bookId) {

            $reviewCountsPerBook = rand(2, 4);

            $shuffledUsers = $userIds;
            shuffle($shuffledUsers);

            for ($i = 0; $i < $reviewCountsPerBook; $i++) {

                if (! isset($shuffledUsers[$i])) {
                    break;
                }

                $rating = rand(1, 5);

                $commentTemplate = $comments[$rating][array_rand($comments[$rating])];

                Review::create([
                    'user_id' => $shuffledUsers[$i],
                    'book_id' => $bookId,
                    'rating' => $rating,
                    'comment' => $commentTemplate,
                ]);

            }
        }
    }
}

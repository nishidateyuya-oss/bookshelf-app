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

        $reviewCountsPerBook = [4, 4, 3, 3, 3, 3, 3, 3, 2, 2, 2];

        $comments = [
            3 => [
                '内容は良かったですが、少し説明が冗長に感じました。',
                '読みやすいですが、すでに知っている内容が多かったです。',
                '可もなく不可もなく。暇つぶしにはちょうどいい本です。',
                '期待が大きすぎたせいか、少し物足りなさを感じました。',
            ],
            4 => [
                '非常に勉強になりました。手元に置いて何度も読み返したいです。',
                '具体的な事例が多くて分かりやすい。知人にも勧めたい一冊。',
                'ページをめくる手が止まりませんでした。構成が素晴らしいです。',
                '実践的な内容が多く、すぐに仕事や私生活に活かせそうです。',
            ],
            5 => [
                '文句なしの名著です！人生のバイブルになりました。',
                'もっと早く出会いたかったと思える素晴らしい本です。大満足。',
                '全ての章が有益で、圧倒的な熱量を感じる傑作でした。最高です。',
                '知的好奇心が刺激され、一気に読み終えてしまいました。超おすすめです！',
            ],
        ];

        foreach ($bookIds as $index => $bookId) {

            $requiredCount = $reviewCountsPerBook[$index] ?? 2;

            $shuffledUsers = $userIds;
            shuffle($shuffledUsers);

            for ($i = 0; $i < $requiredCount; $i++) {

                if (! isset($shuffledUsers[$i])) {
                    break;
                }

                $rating = rand(3, 5);

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

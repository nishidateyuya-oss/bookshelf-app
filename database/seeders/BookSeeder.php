<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\User;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::all();

        $booksData = [
            ['isbn' => '9784101010014', 'title' => '吾輩は猫である', 'author' => '夏目漱石', 'published_date' => '1905-01-01', 'genres' => [1]],
            ['isbn' => '9784422100524', 'title' => '人を動かす', 'author' => 'D・カーネギー', 'published_date' => '1936-10-01', 'genres' => [2, 4]],
            ['isbn' => '9784873115658', 'title' => 'リーダブルコード', 'author' => 'Dustin Boswell', 'published_date' => '2012-06-23', 'genres' => [3]],
            ['isbn' => '9784863940246', 'title' => '７つの習慣', 'author' => 'スティーブン・R・コヴィー', 'published_date' => '2013-08-30', 'genres' => [2, 4]],
            ['isbn' => '9784101010021', 'title' => '坊ちゃん', 'author' => '夏目漱石', 'published_date' => '1906-04-01', 'genres' => [1]],
            ['isbn' => '9784309226712', 'title' => 'サピエンス全史', 'author' => 'ユヴァル・ノア・ハラリ', 'published_date' => '2016-09-08', 'genres' => [6, 7]],
            ['isbn' => '9784048930598', 'title' => 'Clean Code', 'author' => 'Robert C. Martin', 'published_date' => '2017-12-18', 'genres' => [3]],
            ['isbn' => '9784478025819', 'title' => '嫌われる勇気', 'author' => '岸見一郎・古賀史健', 'published_date' => '2013-12-13', 'genres' => [4]],
            ['isbn' => '9784163902302', 'title' => '火花', 'author' => '又吉直樹', 'published_date' => '2015-03-11', 'genres' => [1]],
            ['isbn' => '9784822289607', 'title' => 'FACTFULNESS', 'author' => 'ハンス・ロスリング', 'published_date' => '2019-01-11', 'genres' => [2, 7]],
            ['isbn' => '9784822251468', 'title' => 'コンテナ物語', 'author' => 'マルク・レビンソン', 'published_date' => '2007-01-18', 'genres' => [2, 6]],
        ];

        foreach ($booksData as $index => $data) {
            $book = Book::firstOrCreate(
                ['isbn' => $data['isbn']],
                [
                    'title' => $data['title'],
                    'author' => $data['author'],
                    'published_date' => $data['published_date'],
                    'description' => "{$data['author']}の著作です",
                    'image_url' => 'https://placehold.co/200x300/e2e8f0/475569?text='.($index + 1),
                    'user_id' => $user->random()->id,
                ]
            );

            $book->genres()->sync($data['genres']);
        }
    }
}

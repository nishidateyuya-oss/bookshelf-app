<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\ReadingPlan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class ReadingPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $yamada = User::where('email', 'yamada@example.com')->first()
            ?? User::firstOrCreate(
                ['email' => 'yamada@example.com'],
                ['name' => '山田太郎', 'password' => bcrypt('password')]
            );

        $suzuki = User::where('email', 'suzuki@example.com')->first()
            ?? User::firstOrCreate(
                ['email' => 'suzuki@example.com'],
                ['name' => '鈴木花子', 'password' => bcrypt('password')]
            );

        $books = Book::orderBy('id', 'asc')->take(6)->get();

        if ($books->count() < 6) {
            $this->command->error('ReadingPlanSeeder: 書籍データが不足しています。先に BookSeeder を実行してください。');

            return;
        }

        $today = Carbon::today();

        ReadingPlan::create([
            'user_id' => $yamada->id,
            'book_id' => $books[0]->id,
            'target_date' => $today->copy()->addDays(3),
            'status' => 'in_progress',
        ]);

        ReadingPlan::create([
            'user_id' => $yamada->id,
            'book_id' => $books[1]->id,
            'target_date' => $today->copy(),
            'status' => 'in_progress',
        ]);

        ReadingPlan::create([
            'user_id' => $yamada->id,
            'book_id' => $books[2]->id,
            'target_date' => $today->copy()->subDays(3),
            'status' => 'in_progress',
        ]);

        ReadingPlan::create([
            'user_id' => $yamada->id,
            'book_id' => $books[3]->id,
            'target_date' => $today->copy()->addDays(7),
            'status' => 'in_progress',
        ]);

        ReadingPlan::create([
            'user_id' => $yamada->id,
            'book_id' => $books[4]->id,
            'target_date' => $today->copy()->subDays(10),
            'status' => 'completed',
            'completed_at' => $today->copy()->subDays(5),
        ]);

        ReadingPlan::create([
            'user_id' => $suzuki->id,
            'book_id' => $books[5]->id,
            'target_date' => $today->copy()->addDays(5),
            'status' => 'in_progress',
        ]);
    }
}

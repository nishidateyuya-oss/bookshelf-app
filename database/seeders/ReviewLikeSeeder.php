<?php

namespace Database\Seeders;

use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewLikeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $allUserIds = User::pluck('id')->toArray();

        $reviews = Review::all();

        $totalLikesCreated = 0;

        foreach ($reviews as $review) {

            $likableUserIds = array_filter($allUserIds, function ($id) use ($review) {
                return $id !== $review->user_id;
            });

            $likableUserIds = array_values($likableUserIds);

            $likeCount = rand(0, 3);

            if ($likeCount > 0 && ! empty($likableUserIds)) {

                $actualCount = min($likeCount, count($likableUserIds));
                $randomKeys = array_rand($likableUserIds, $actualCount);

                $keys = is_array($randomKeys) ? $randomKeys : [$randomKeys];

                $userIdsToLike = [];
                foreach ($keys as $key) {
                    $userIdsToLike[] = $likableUserIds[$key];
                }

                $review->likedByUsers()->syncWithoutDetaching($userIdsToLike);

                $totalLikesCreated += count($userIdsToLike);
            }
        }
    }
}

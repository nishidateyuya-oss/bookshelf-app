<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'author' => $this->author,
            'isbn' => $this->isbn,
            'published_date' => $this->published_date,
            'average_rating' => $this->whenHas('reviews_avg_rating', function (): float {
                return (float) $this->reviews_avg_rating;
            }),
            'review_count' => $this->whenCounted('reviews'),
            'genres' => GenreResource::collection($this->whenLoaded('genres')),
            'reviews' => $this->whenLoaded('reviews', function () {
                return $this->reviews->map(function ($review) {
                    return [
                        'id' => $review->id,
                        'user_name' => $review->user->name,
                        'rating' => $review->rating,
                        'comment' => $review->comment,
                        'created_at' => $review->created_at,
                    ];
                });
            }),
        ];
    }
}

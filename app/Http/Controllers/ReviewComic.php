<?php

namespace App\Http\Controllers;

use App\Concerns\WithUserUuid;
use App\Models\Comic;
use Illuminate\Foundation\Validation\ValidatesRequests;

class ReviewComic
{
    use ValidatesRequests, WithUserUuid;

    public function __invoke(Comic $comic): array
    {
        $data = $this->validateWith([
            'rating' => ['required', 'numeric', 'min:1', 'max:5'],
            'text' => ['nullable', 'string', 'max:60000'],
        ]);

        $comic->reviews()->updateOrCreate([
            'user_id' => $this->getUserUuid(),
        ], $data);

        $comic->update([
            'average_rating' => $comic->calculateAverageRating(),
        ]);

        return $this->responseUserUuid();
    }
}

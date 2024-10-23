<?php

namespace App\Console\Commands;

use App\Models\Comic;
use App\Recombee\Recombee;
use App\Recombee\RecombeeException;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Recombee\RecommApi\Requests\Batch;
use Recombee\RecommApi\Requests\SetItemValues;

class SubmitRecombeeRecordCommand extends Command
{
    protected $signature = 'recombee:submit';

    protected $description = 'Submit Recombee record command';

    public function handle(): void
    {
        Comic::query()
            ->where('has_submitted_recommendation', 0)
            ->chunkById(2000, function (Collection $comics) {
                $responses = Recombee::send(new Batch(
                    $comics->map(fn (Comic $comic) => new SetItemValues(
                        $comic->id,
                        $comic->toRecommendableArray(),
                        ['cascadeCreate' => true],
                    ))->all()
                ));

                foreach ($comics as $index => $comic) {
                    if ($responses[$index]['code'] === 200 && $responses[$index]['json'] === 'ok') {
                        $comic->update(['has_submitted_recommendation' => 1]);

                        $this->info("Submitted comic #{$comic->id} to Recombee");
                    } else {
                        throw new RecombeeException($responses[$index]['json']['error'], $responses[$index]['code']);
                    }
                }
            });

        $this->info('Submitted all Recombee records');
    }
}

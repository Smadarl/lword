<?php

use Illuminate\Database\Seeder;

class GamesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Game::class, 10)->create()->each(function($g) {
            $p1 = App\User::find($g->started_by);
            $p2 = App\User::where('id', '<>', $g->started_by)->orderByRaw("RAND()")->first();
            $g->players()->sync([$p1->id => ['word' => self::getRandomWord($g)] ], false);
            $g->players()->sync([$p2->id => ['word' => self::getRandomWord($g)] ], false);
        });
    }

    static public function getRandomWord(App\Game $game)
    {
        $wf = env('WORD_FILE');
        $words = `shuf -n 500 $wf`;
        foreach(explode("\n", $words) as $word) {
            if ((strlen($word) > $game->max_length) || (strlen($word) < env('MIN_WORD_LENGTH'))) {
                continue;
            }
            $chars = count_chars($word, 1);
            if (max($chars) > $game->max_recurrance) {
                continue;
            }
        }
        return $word;
    }
}

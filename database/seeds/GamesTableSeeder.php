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
            $g->players()->sync([$p1->id => ['word' => $this->getRandomWord()] ], false);
            $g->players()->sync([$p2->id => ['word' => $this->getRandomWord()] ], false);
        });
    }

    private function getRandomWord()
    {
        $wf = env('WORD_FILE');
        $word = trim(`shuf -n 1 $wf`);
        return $word;
    }
}

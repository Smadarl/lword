<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        App\User::create(
            [
                'name' => 'rob', 
                'email' => 'rob_adams@hotmail.com', 
                'password' => Hash::make('magic'), 
                'role' => 'admin'
            ]
        );
        factory(App\User::class, 10)->create();
        // $this->call(UsersTableSeeder::class);
        /*
        factory(App\Player::class, 10)->create()->each(function($u) {
            $game1 = factory(App\Game::class)->make();
            $u->games()->save($game1);
            $opp1 = factory(App\Player::class)->create();
            $opp1->games()->save($game1);
            $game2 = $u->games()->save(factory(App\Game::class)->make());
            $opp2 = factory(App\Player::class)->create();
            $opp3->games()->save($game2);
        });
        */
    }
}

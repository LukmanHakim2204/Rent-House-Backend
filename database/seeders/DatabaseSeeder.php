<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Listing;
use App\Models\Transaction;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Factories\Sequence;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        User::factory()->create([
            'name' => 'Lukman Admin',
            'email' => 'admin1@gmail.com',
            'role' => 'admin',
        ]);
        $users = User::factory(10)->create();
        $listings = Listing::factory(10)->create();

        Transaction::factory(10)
            ->state(
                new Sequence(
                    fn(Sequence $sequence) => [
                        'user_id' => $users->random(),
                        'listing_id' => $listings->random(),
                    ],
                )
            )->create();
    }
}

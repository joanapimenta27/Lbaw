<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        return [
            'username' => $this->faker->unique()->userName(),
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => bcrypt('password'), // Default password
            'age' => $this->faker->numberBetween(18, 65), 
        ];
    }

    public function admin()
    {
        return $this->state([
            'is_admin' => true, // Define how you want to handle admin status if applicable
        ]);
    }


    public function createWithFriendsAndPosts(User $user, int $friendCount = 5, int $postCount = 3)
    {
        return $this->afterCreating(function (User $createdUser) use ($user, $friendCount, $postCount) {
            // Create friends for the user
            $friends = User::factory($friendCount)->create();
    
            // Attach the friends to the original user
            foreach ($friends as $friend) {
                $user->friends()->attach($friend->id);
                $friend->friends()->attach($user->id);
    
                // Create posts for the friend
                Post::factory($postCount)->create(['author_id' => $friend->id]);
            }
        });
    }

}

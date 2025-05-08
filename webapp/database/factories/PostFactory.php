<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    protected $model = \App\Models\Post::class;

    public function definition()
    {
        return [
            'author_id' => \App\Models\User::factory(), 
            'content' => $this->faker->boolean(70) ? $this->faker->imageUrl(800, 600, 'nature', true, 'Random Image') : null, // 70% chance of an image
            'date' => $this->faker->dateTimeThisYear(),
            'is_public' => true,
            'title' => $this->faker->sentence(),
            'description' => $this->faker->text(200),
            'like_num' => $this->faker->numberBetween(0, 100),
            'flick_num' => $this->faker->numberBetween(0, 50),
            'share_num' => $this->faker->numberBetween(0, 50),
        ];
    }
}

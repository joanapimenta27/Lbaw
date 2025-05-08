<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Post;
use Database\Factories\FriendFactory;

class CreateFriendsWithPosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:friends-posts {userId}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create friends with posts for a specified user';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $userId = $this->argument('userId');

        $user = User::find($userId);
        if (!$user) {
            $this->error("User with ID $userId not found.");
            return 1;
        }

        // Use FriendFactory to create friends with posts
        FriendFactory::new()
            ->count(3) // Create 3 friends
            ->withPostsForFriend($user) // Attach friends and create posts for them
            ->create(); // Persist to the database

        $this->info("Successfully created 3 friends with posts for user ID $userId.");
        return 0;
    }
}

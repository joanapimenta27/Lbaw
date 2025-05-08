<?php

namespace App\Providers;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Models\Post;
use App\Policies\PostPolicy;
use App\Policies\UserPolicy;


// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Post::class => PostPolicy::class,
        User::class => UserPolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Define the 'edit-profile' Gate
        Gate::define('edit-profile', function ($currentUser, $userId) {
            \Log::info("Gate Check - Current User: {$currentUser->id}, Target User: " . json_encode($userId) . ", Is Admin: " . ($currentUser->isAdmin() ? 'Yes' : 'No'));
        
            if (!$userId) {
                \Log::error('Gate Check Failed - Missing Target User ID');
                return false;
            }
        
            $user = User::find($userId);

        
            return ($currentUser->id === (int)$userId || $currentUser->isAdmin());
        });
    }
}

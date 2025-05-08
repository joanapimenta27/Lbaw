<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use ZxcvbnPhp\Zxcvbn;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use App\Models\Post;
use App\Models\Admin;
use App\Models\PostMedia; 

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
class RegisterController extends Controller
{
    /**
     * Display the registration form.
     */
    public function showRegistrationForm()
    {
        if(!Auth::check()|| Auth::user()->isAdmin()){
            return view('auth.register');
        }
        else{

            return redirect()->route('home', ['type' => 'public']);
        }

        
    }

   
    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:250|unique:users',
            'email' => 'required|email|max:250|unique:users',
            'name' => 'required|string|max:250',
            'age' => 'required|numeric|min:13',
            'password' => 'required|min:8|confirmed',
        ]);
    
        $passwordStrength = (new Zxcvbn())->passwordStrength($request->password);

        if ($passwordStrength['score'] < 3) {
        
        $errorMessages = $this->getPasswordStrengthFeedback($passwordStrength['score']);
        return back()->withErrors(['password' => $errorMessages]);
        }

       
        $user = User::create([
            'username' => $request->username,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'age' => $request->age,
        ]);


        $userDir = 'uploads/' . $user->id . '/profile_picture';
        $defaultImagePath = public_path('images/DefaultProfile.png');
        $newImagePath = $userDir . '/DefaultProfile.png';
        Storage::disk('public')->put($newImagePath, File::get($defaultImagePath));
        $user->profile_picture = $newImagePath;
        $user->save();
      

        if(!auth()->check()){
              
            $credentials = $request->only('email', 'password');
            Auth::attempt($credentials);

        
            $request->session()->regenerate();
            return  redirect()->route('home', ['type' => 'public'])->withSuccess('You have successfully registered & logged in!');
        }
        else{
            if($request->has('admin_checkbox')){
                Admin::create([
                    'user_id' => $user->id ,  
                    'is_super' => false,
              
                ]);
                
            }

            return redirect()->route('profile',['userId' => $user->id]);
        }
           
        
    }


    private function getPasswordStrengthFeedback($score)
    {
        switch ($score) {
            case 0:
                return 'Your password is very weak. Please include a mix of upper and lower case letters, numbers, and special characters.';
            case 1:
                return 'Your password is weak. Try making it longer and include a mix of characters and numbers.';
            case 2:
                return 'Your password is fair. Consider adding more characters or using a mix of words, symbols, and numbers.';
            case 3:
                return 'Your password is strong, but you can still improve it by adding more variety or length.';
            case 4:
                return 'Your password is very strong!';
            default:
                return 'Your password is too weak. Please choose a stronger password.';
        }
    }
}
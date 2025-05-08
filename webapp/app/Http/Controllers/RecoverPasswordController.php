<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

use App\Mail\MailModel;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\View\View;
use Illuminate\Support\Facades\Hash;


class RecoverPasswordController extends Controller
{

    public function showRecoverPasswordForm() :  View {
        return view('auth.recoverPass');      
    }
   
    public function generateCode()
    {
        return mt_rand(100000, 999999);
    }
    
    
    public function sendy(Request $request,$code){

        $mailData = [
          
            'email' => $request->email,
            'code'=> $code,
        ];

        Mail::to($request->email)->send(new MailModel($mailData));
        session()->flash('status', 'An email with your password recovery instructions has been sent.');

        return back();
    }

    public function recoverPass(Request $request)  {
        //to the redidirect
        session()->forget(['recovery_code', 'recovery_code_expiry','userEmail']);


        $request->validate([
            'email' => 'required|email', 
        ]);

        $user = User::findByEmail($request->email);

        if ($user) {
            $code=$this->generateCode();

            session([
                'recovery_code' => $code,
                'recovery_code_expiry' => now()->addMinutes(5),  // Code expires after 5 minutes to make it safer
                'userEmail'=>$request->email
            ]);

            $this->sendy($request, $code);
            return redirect()->route('verifyCode');

        } 
        else {
            return back()->withErrors(['email' => 'No user found with this email.']);
        }
        
    }



    public function changePassword($password,$email){
        $user = User::findByEmail($email);
        if ($user) {
            $user->password = Hash::make($password);
            $user->save();
            session()->flash('status', 'Your password has been successfully changed.');
           
        }
        else {
          
            return back()->withErrors(['Recover password went wrong,try it again']);
        }
    }


    public function verifyCode(Request $request)  {
        $request->validate([
            'code' => 'required|numeric|digits:6',
            'newPassword' =>'required|min:6|confirmed'
        ]);

        $sentCode= session('recovery_code');
        $expiryTime = session('recovery_code_expiry');
        $userEmail=session('userEmail');

        if (!$sentCode || now()->greaterThan($expiryTime)) {
            
            return back()->withErrors(['code' => 'The recovery code has expired or is invalid,you need to do the process again.']);
        }
        if($request->code==$sentCode){
            $this->changePassword($request->newPassword,$userEmail);
            session()->forget(['recovery_code', 'recovery_code_expiry','userEmail']);
            session()->flash('status', 'Your password has been successfully changed.');
            return redirect()->route('login');


        }else {
            return back()->withErrors(['code' => 'The recovery code is invalid.']);
        }
    }
    public function showVerifyCodeForm() : View{
        return view('auth.verifycode'); 
   
    }

}

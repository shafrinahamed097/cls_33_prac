<?php

namespace App\Http\Controllers;
use App\Helper\JWTToken;
use Exception;
use App\Mail\OTPMail;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use Illuminate\Http\Request;


class UserController extends Controller
{
    // User Registration Function
    function UserRegistration(Request $request){
        try{
            User::create([
                'firstName' => $request->input('firstName'),
                'lastName' => $request->input('lastName'),
                'email' => $request->input('email'),
                'mobile'=>$request->input('mobile'),
                'password' => $request->input('password'),
                
            ]);
             return response()->json([
                'status' =>'success',
                'message'=>'User Registration Successfully :)'
             ]);
        }

        catch (Exception $e){
            return response()->json([
                'status' =>'failed',
                'message'=> $e->getMessage()
            ]);

        }
    }

    // User Login Function
    function UserLogin(Request $request){
      $count = User::where('email','=',$request->input('email'))
        ->where('password','=', $request->input('password'))
        ->count();

        if($count==1){
            // User Login ->JWT Token Issue
            $token=JWTToken::CreateToken($request->input('email'));
            return response()->json([
                'status'=>'Success',
                'message'=>'Successfully Login :)',
                'token' => $token
            ]);

        }
        else{
            return response()->json([
                'status'=>'failed',
                'message'=>'Successfully Failed :('
            ]);

        }

    }

    function SendOTPCode(Request $request){
        $email=$request->input('email');
        $otp=rand(1000, 9999);
        $count=User::where('email','=',$email)->count();

        if($count==1){
            // OTP Email Address
            Mail::to($email)->send(new OTPMail($otp));

            // OTP Code Table Update
            User::where('email','=',$email)->update(['otp'=>$otp]);
            return response()->json([
                'status'=>'Success',
                'message'=>'4 Digit OTP Code has been send to your e-mail'
               ]);

        }
        else{
           return response()->json([
            'status'=>'Failed',
            'message'=>'OTP Does not send'
           ]);
        }

    }
}

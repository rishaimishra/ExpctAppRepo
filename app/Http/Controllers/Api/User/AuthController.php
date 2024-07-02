<?php

namespace App\Http\Controllers\Api\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use App\Mail\VerifyOtp;
use Mail;


class AuthController extends Controller
{
    

    public function register(Request $request)
    {
         $validator =  Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'address' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'otp' => 'required',
            'phone' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }

        $otp = rand(100000, 999999);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'address' => $request->address,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'otp'=>$otp,
            'phone'=>$request->phone,
        ]);

        $token = JWTAuth::fromUser($user);
        
        //email
          $mailData = [
            'title' => 'Verify your account',
            'otp'=>$otp,
            'user'=>$user
        ];
           
        Mail::to($request->email)->send(new VerifyOtp($mailData));

        // return response()->json(compact('user', 'token'), 201);
        return response()->json(['message'=>'Registration is successfully done.please verify otp to login','otp'=>$otp], 201);
    }







// enter email where otp will go
    public function send_otp(Request $request){
         $validator =  Validator::make($request->all(), [
            'email' => 'required|string|email|max:255|unique:users',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }

        $otp = rand(100000, 999999);

        //chk
        $chk=User::where('email',$request->email)->first();
        if($chk){
            $updt=User::where('email',$request->email)->update(['otp'=>$otp]);
             return response()->json(['message'=>'Otp sent successfully'], 201);
        }else{
            return response()->json(['message'=>'Email is incorrect, otp not able to send'], 400);
        }
    }








    public function verify_otp(Request $request){
         $validator =  Validator::make($request->all(), [
            'email' => 'required|string|email|max:255|unique:users',
            'otp' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }

        //chk
        $chk=User::where('email',$request->email)->where('otp',$request->otp)->first();
        if($chk){
            $updt=User::where('email',$request->email)->where('otp',$request->otp)->update(['otp'=>null,'is_verified'=>"Y"]);
             return response()->json(['message'=>'Otp verified successfully'], 201);
        }else{
            return response()->json(['message'=>'Otp is not verified'], 400);
        }
    }










    public function fgp_enter_email(Request $request){
         $validator =  Validator::make($request->all(), [
            'email' => 'required|string|email|max:255|unique:users',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }

        $forget_pass_otp = rand(100000, 999999);

        //chk
        $chk=User::where('email',$request->email)->first();
        if($chk){
            $updt=User::where('email',$request->email)->update(['forget_pass_otp'=>$forget_pass_otp]);
             return response()->json(['message'=>'Otp sent successfully'], 201);
        }else{
            return response()->json(['message'=>'Email is incorrect,Forget password otp not able to send'], 400);
        }
    }







    public function update_password(Request $request){
        $validator =  Validator::make($request->all(), [
            'password' => 'required|string|min:6|confirmed',
            'forget_pass_otp' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }

        //chk
        $chk=User::where('forget_pass_otp',$request->forget_pass_otp)->first();
        if($chk){
            $updt=User::where('forget_pass_otp',$request->forget_pass_otp)->update(['forget_pass_otp'=>null,'password'=>bcrypt($request->password)]);
             return response()->json(['message'=>'Password updated successfully'], 201);
        }else{
            return response()->json(['message'=>'Forget password otp is not correct'], 400);
        }
    }























    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        $user = Auth::user(); // Fetch authenticated user details
        if($user->is_verified=="N"){
            return response()->json([
            'message' => "Your account is not verified",
            ],400);
        }

        return response()->json([
            'token' => $token,
            'user' => $user
        ],201);
    }





    public function logout()
    {
        Auth::logout();
        return response()->json(['message' => 'Successfully logged out']);
    }






    public function me()
    {
        return response()->json(Auth::user());
    }






public function updateProfile(Request $request)
{
    $user = auth()->user();
    // dd($user);

    //  $validator =  Validator::make($request->all(), [
    //     'name' => 'sometimes|string|max:255',
    //     'password' => 'sometimes|string|min:6|confirmed',
    //     'address' => 'sometimes|string|max:255',
    //     'latitude' => 'sometimes|numeric|between:-90,90',
    //     'longitude' => 'sometimes|numeric|between:-180,180',
    // ]);
      
    //   if ($validator->fails()) {
    //         return response()->json(['error' => $validator->messages()], 200);
    //     }

    if ($request->has('name')) {
        $user->name = $request->name;
    }

     if ($request->has('phone')) {
        $user->phone = $request->phone;
    }

    if ($request->has('password')) {
        $user->password = bcrypt($request->password);
    }

    if ($request->has('address')) {
        $user->address = $request->address;
    }

    if ($request->has('latitude')) {
        $user->latitude = $request->latitude;
    }

    if ($request->has('longitude')) {
        $user->longitude = $request->longitude;
    }

    $user->save();

    return response()->json(['message' => 'Profile updated successfully', 'user' => $user], 200);
}




















}

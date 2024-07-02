<?php

namespace App\Http\Controllers\Api\Business;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BusinessAuthModel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use Auth;


class BusinessAuthController extends Controller
{
    
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:191',
            'first_name' => 'required|string|max:191',
            'last_name' => 'required|string|max:191',
            'email' => 'required|string|email|max:191|unique:businesses',
            'password' => 'required|string|min:6|confirmed',
            'company_name' => 'required|string|max:191',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
         $otp = rand(100000, 999999);

        $business = BusinessAuthModel::create([
            'name' => $request->name,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'company_name' => $request->company_name,
            'is_active' => 1, //0,
            'is_paid' => 0,
             'otp'=>$otp,
        ]);

        

        return response()->json([
            'message'=>'Business Registration is successfully done.'
        ], 201);
    }






 public function login(Request $request)
    {
         $credentials = $request->only('email', 'password');

    if (!$token = Auth::guard('business_api')->attempt($credentials)) {
        return response()->json(['error' => 'Invalid credentials'], 401);
    }
     // Authentication successful, retrieve authenticated user
    $user = Auth::guard('business_api')->user();

    // Authentication successful, return the token
     return response()->json([
        'token' => $token,
        'user' => $user
    ], 200);
    }







   public function profile(Request $request)
    {
        $business = Auth::guard('business_api')->user();
        return response()->json(['business' => $business], 200);
    }






    public function updateProfile(Request $request)
    {
        $business =Auth::guard('business_api')->user();

        $request->validate([
            'name' => 'sometimes|string|max:191',
            'first_name' => 'sometimes|string|max:191',
            'last_name' => 'sometimes|string|max:191',
            'password' => 'sometimes|string|min:6|confirmed',
            'company_name' => 'sometimes|string|max:191',
            'profile_img' => 'sometimes|string|max:191',
            'is_active' => 'sometimes|boolean',
            'is_paid' => 'sometimes|boolean',
        ]);

        if ($request->has('name')) {
            $business->name = $request->name;
        }

        if ($request->has('first_name')) {
            $business->first_name = $request->first_name;
        }

        if ($request->has('last_name')) {
            $business->last_name = $request->last_name;
        }

     

        if ($request->has('password')) {
            $business->password = bcrypt($request->password);
        }

        if ($request->has('company_name')) {
            $business->company_name = $request->company_name;
        }

        if ($request->has('profile_img')) {
            $business->profile_img = $request->profile_img;
        }

        if ($request->has('is_active')) {
            $business->is_active = $request->is_active;
        }

        if ($request->has('is_paid')) {
            $business->is_paid = $request->is_paid;
        }

        $business->save();

        return response()->json(['message' => 'Profile updated successfully', 'business' => $business], 200);
    }






   public function logout(Request $request)
    {
           Auth::guard('business_api')->logout(); 
         return response()->json(['message' => 'Successfully logged out'], 200);
    }


}

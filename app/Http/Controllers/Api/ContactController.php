<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Contact;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    //
}



class ContactController extends Controller
{
    public function store(Request $request)
    {
       $validator =  Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string',
            'phone' => 'required|string',
        ]);

       if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }

        $contact = new Contact();
        $contact->name = $request->name;
        $contact->email = $request->email;
        $contact->subject = $request->subject;
        $contact->message = $request->message;
        $contact->phone = $request->phone;
        $contact->save();

        return response()->json(['message' => 'Contact message sent successfully'], 201);
    }
}

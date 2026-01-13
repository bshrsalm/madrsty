<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
class Register_Controller extends Controller
{
   

    function register(Request $request)
    {
        $request->validate([
     "name" =>['required','string','min:3','max:105'],
   "email" =>['required','string','min:3','max:150'],
   "password" =>['required','string','min:3','max:100']
        ]);
            $user=User::create([
            'name'=>$request->name,
            'email'=>$request->email,
             'password'=>Hash::make($request->password) 
            ]);
             
            return response()->json([
                'message'=>'User registered successfully',
                'user'=>$user
            ],201);

    }

    

 function login(Request $request)
{
    $request->validate([
  
   "email" =>['required','string'],
   "password" =>['required','string']
      ]);
   if(!Auth::attempt($request->only('email','password')))
       return response()->json(['message'=>'not found email or password']);
      $user=User::where( 'email',$request->email)->FirstOrFail();
        $token=$user->createToken('auth_token')->plainTextToken;
        return response()->json(['message'=>'login successful',$user, $token],202);
}
function logout(Request $request)
{
 return $request->user()->currentAccessToken()->delete();
    return response()->json(['message'=>'logout successful']);
}
function show(Request $request)
{
    $user=User::all();
    return response()->json(['message'=>'all user data',$user],200);
}
function edit(Request $request, $id)
{
    $User=User::find($id);

    if(!$User)
    {
   return response()->json(['message'=>'user not found'],404);
    }
    $request->validate([
        "name" =>['required','string','min:3','max:105'],
      "email" =>['required','string','min:3','max:150'],
      "password" =>['required','string','min:3','max:100']
           ]);
       $User=$User->update([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>Hash::make($request->password)
       ]);

           return response()->json(['message'=>'user updated successfully',$User],200);
}


function delete(Request $request ,$id)
{
$user=User::find($id);
if(!$user)
{
return response()->json('not found');
}
$user->delete();
}
 function generateTestUsers(Request $request)
    {
        $users = [];
        
        for($i = 101; $i <= 150; $i++) {
            $user = User::create([
                'name' => 'Test User ' . $i,
                'email' => 'testuser' . $i . '@example.com',
                'password' => Hash::make('password123')
            ]);
            
            $users[] = $user;
        }
        
        return response()->json([
            'message' => '100 test users created successfully',
            'count' => count($users),
            'users' => $users
        ], 201);
    }

    
    function getCurrentUser(Request $request)
    {
        return response()->json([
            'user' => $request->user()
        ], 200);
    }
}
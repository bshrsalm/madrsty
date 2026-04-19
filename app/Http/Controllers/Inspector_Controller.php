<?php

namespace App\Http\Controllers;

use App\Models\Inspector;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class Inspector_Controller extends Controller
{
   
    public function index()
    {
        $inspectors = Inspector::with('user')->get();

        return response()->json([
            'message'    => 'All inspectors',
            'inspectors' => $inspectors,
        ], 200);
    }

    
    public function store(Request $request)
    {
        $request->validate([
            'name'     => ['required', 'string', 'min:3', 'max:105'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'image'    => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        
        $user           = new User();
        $user->name     = $request->name;
        $user->email    = $request->email;
        $user->password = Hash::make($request->password);
        $user->role     = 'inspector';
        $user->save();

        // 2. ارفع الصورة لو موجودة
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('inspectors', 'public');
        }

       
        $inspector        = new Inspector();
        $inspector->image = $imagePath;

        $user->inspector()->save($inspector); 

        return response()->json([
            'message'   => 'Inspector created successfully',
            'inspector' => $inspector->load('user'),
        ], 201);
    }

   
    public function show($id)
    {
        $inspector = Inspector::with('user')->find($id);

        if (!$inspector) {
            return response()->json(['message' => 'Inspector not found'], 404);
        }

        return response()->json([
            'message'   => 'Inspector data',
            'inspector' => $inspector,
        ], 200);
    }


    public function update(Request $request, $id)
    {
        $inspector = Inspector::with('user')->find($id);

        if (!$inspector) {
            return response()->json(['message' => 'Inspector not found'], 404);
        }

        $request->validate([
            'name'     => ['sometimes', 'string', 'min:3', 'max:105'],
            'email'    => ['sometimes', 'email', 'unique:users,email,' . $inspector->user_id],
            'password' => ['sometimes', 'string', 'min:6'],
            'image'    => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

       
        $user = $inspector->user;

        if ($request->filled('name'))     $user->name     = $request->name;
        if ($request->filled('email'))    $user->email    = $request->email;
        if ($request->filled('password')) $user->password = Hash::make($request->password);

        $user->save();

      
        if ($request->hasFile('image')) {
            if ($inspector->image) {
                Storage::disk('public')->delete($inspector->image);
            }
            $inspector->image = $request->file('image')->store('inspectors', 'public');
            $inspector->save();
        }

        return response()->json([
            'message'   => 'Inspector updated successfully',
            'inspector' => $inspector->load('user'),
        ], 200);
    }

   
    public function destroy($id)
    {
        $inspector = Inspector::with('user')->find($id);

        if (!$inspector) {
            return response()->json(['message' => 'Inspector not found'], 404);
        }

        if ($inspector->image) {
            Storage::disk('public')->delete($inspector->image);
        }

        $inspector->user->delete();

        return response()->json([
            'message' => 'Inspector deleted successfully',
        ], 200);
    }
}
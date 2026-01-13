<?php

namespace App\Http\Controllers;
use App\Models\Profile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class Profile_controller extends Controller
{

    function show_all(Request $request)
    {
          $Profiler = Profile::with('user')->get();
         return response()->json($Profiler);
    }
   
function index(Request $request)
{
     $user=$request->user();
     $Profiler=$user->profile;

     return response()->json($Profiler);

}
function show(Request $request ,$id)
{
      $Profile=Profile::find($id);
      if(!$Profile)
      {
        return response()->json('not found');

      }
      return response()->json($Profile);
}

  function store(Request $request)
{
   $data =  $request->validate([
       'phone'   => ['nullable','string','max:255'],
        'Residential_addreas' => ['nullable','string','max:255'],
        
  
        'image'   => ['nullable','image','mimes:jpeg,png,jpg,gif,svg','max:2048'], 
    ]);
 $user = $request->user();
    if ($user->profile) {
        return response()->json(['error' => 'Profile already exists. Use update instead.'], 400); 
    }
    if($request->hasFile('image'))
    {
        $path=$request->file('image')->store('my phone','public');
        $data['image'] = $path;
    }
      $data['user_id'] = $user->id;
  
  $profile = Profile::create($data);
   return response()->json($profile,201);
}

function update(Request $request)
{
    $user = $request->user();
    $profile = $user->profile;

    if (! $profile) {
        return response()->json([
            'message' => 'ما عندك بروفايل لتعدله',
            'exists' => false
        ], 404);
    }

    $data = $request->validate([
        'phone'               => ['nullable','string','max:255'],
        'Residential_addreas' => ['nullable','string','max:255'],
        'image'               => ['nullable','image','mimes:jpeg,png,jpg,gif,svg','max:2048'],
    ]);

    if ($request->hasFile('image')) {
        
        if ($profile->image && Storage::disk('public')->exists($profile->image)) {
            Storage::disk('public')->delete($profile->image);
        }

        $path = $request->file('image')->store('my phone', 'public');
        $data['image'] = $path;
    }

   
    $profile->update($data);

     return response()->json([
        'profile' => $profile,
        'exists' => true 
    ], 200);
}
function destroy($id)
    {
        $profile = Profile::find($id);

        if (!$profile) {
            return response()->json(['message' => 'البروفايل غير موجود'], 404);
        }

        if ($profile->image && Storage::disk('public')->exists($profile->image)) {
            Storage::disk('public')->delete($profile->image);
        }

        $profile->delete();

        return response()->json(['message' => 'تم حذف البروفايل بنجاح'], 200);
    }

}

<?php

namespace App\Http\Controllers;

use App\Models\Governorate;
use Illuminate\Http\Request;

class GovernorateController extends Controller
{
 
    public function index()
    {
        $govs = Governorate::with('schools')->get();

        return response()->json($govs);
    }

   
    public function show($id)
    {
        $gov = Governorate::with('schools')->find($id);

        if (!$gov) {
            return response()->json(['message' => 'Not found'], 404);
        }

        return response()->json($gov);
    }

    
    public function store(Request $request)
    {
     

        $data = $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $gov = Governorate::create($data);

        return response()->json([
            'message' => 'Governorate created',
            'data' => $gov
        ], 201);
    }

   
    public function update(Request $request, $id)
    {
    

        $gov = Governorate::find($id);

        if (!$gov) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $data = $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $gov->update($data);

        return response()->json([
            'message' => 'Governorate updated',
            'data' => $gov
        ]);
    }

  
    public function destroy(Request $request, $id)
    {
        

        $gov = Governorate::find($id);

        if (!$gov) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $gov->delete();

        return response()->json([
            'message' => 'Governorate deleted'
        ]);
    }


  
}
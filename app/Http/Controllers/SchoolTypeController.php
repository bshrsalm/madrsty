<?php

namespace App\Http\Controllers;

use App\Models\SchoolType;
use Illuminate\Http\Request;

class SchoolTypeController extends Controller
{
  
    public function index()
    {
        return response()->json(SchoolType::all());
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:school_types,name'
        ]);

        $type = SchoolType::create([
            'name' => $request->name
        ]);

        return response()->json([
            'message' => 'تم إضافة النوع',
            'data' => $type
        ], 201);
    }

    
    public function show($id)
    {
        $type = SchoolType::findOrFail($id);

        return response()->json($type);
    }

    
    public function update(Request $request, $id)
    {
        $type = SchoolType::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:school_types,name,' . $id
        ]);

        $type->update([
            'name' => $request->name
        ]);

        return response()->json([
            'message' => 'تم التعديل',
            'data' => $type
        ]);
    }

   
    public function destroy($id)
    {
        $type = SchoolType::findOrFail($id);
        $type->delete();

        return response()->json([
            'message' => 'تم الحذف'
        ]);
    }
}
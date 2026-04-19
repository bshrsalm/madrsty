<?php
namespace App\Http\Controllers;

use App\Models\EducationalStage;
use App\Models\StageType;
use Illuminate\Http\Request;

class StageController extends Controller
{
    public function index()
    {
        return EducationalStage::with('types')->get();
    }

    public function storeStage(Request $request)
    {
        

        return EducationalStage::create([
            'name' => $request->name
        ]);
    }

    public function storeType(Request $request)
    {
       
        return StageType::create([
            'name' => $request->name,
            'educational_stage_id' => $request->educational_stage_id
        ]);
    }

    public function deleteStage(Request $request, $id)
    {
       
        EducationalStage::findOrFail($id)->delete();

        return ['message'=>'deleted'];
    }

    public function deleteType(Request $request, $id)
    {
       
        StageType::findOrFail($id)->delete();

        return ['message'=>'deleted'];
    }

 
}
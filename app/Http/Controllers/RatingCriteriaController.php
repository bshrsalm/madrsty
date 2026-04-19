<?php
namespace App\Http\Controllers;

use App\Models\RatingCriteria;
use Illuminate\Http\Request;

class RatingCriteriaController extends Controller
{
    public function index()
    {
        return response()->json([
            'message'  => 'All rating criteria',
            'criteria' => RatingCriteria::orderBy('created_at', 'desc')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:100', 'unique:rating_criteria,name'],
        ]);

        $criterion = RatingCriteria::create(['name' => $request->name]);

        return response()->json([
            'message'   => 'Criterion added successfully',
            'criterion' => $criterion,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $criterion = RatingCriteria::find($id);

        if (!$criterion) {
            return response()->json(['message' => 'Criterion not found'], 404);
        }

        $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:100', 'unique:rating_criteria,name,' . $id],
        ]);

        $criterion->update(['name' => $request->name]);

        return response()->json([
            'message'   => 'Criterion updated successfully',
            'criterion' => $criterion,
        ]);
    }

    public function destroy($id)
    {
        $criterion = RatingCriteria::find($id);

        if (!$criterion) {
            return response()->json(['message' => 'Criterion not found'], 404);
        }

        $criterion->delete();

        return response()->json(['message' => 'Criterion deleted successfully']);
    }
}
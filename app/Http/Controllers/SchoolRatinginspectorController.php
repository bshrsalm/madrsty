<?php
namespace App\Http\Controllers;

use App\Models\SchoolRatinginspector;
use App\Models\SchoolRatingScore;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SchoolRatinginspectorController extends Controller
{
    // Admin: كل التقييمات
    public function index()
    {
        $ratings = SchoolRatinginspector::with(['school', 'inspector.user', 'scores.criteria'])->get();

        return response()->json([
            'message' => 'All school ratings',
            'ratings' => $ratings->map(fn($r) => array_merge(
                $r->toArray(), ['average' => $r->average]
            )),
        ]);
    }

    // Admin: تفاصيل تقييم واحد
    public function show($id)
    {
        $rating = SchoolRatinginspector::with(['school', 'inspector.user', 'scores.criteria'])->find($id);

        if (!$rating) {
            return response()->json(['message' => 'Rating not found'], 404);
        }

        return response()->json([
            'message' => 'Rating details',
            'rating'  => array_merge($rating->toArray(), ['average' => $rating->average]),
        ]);
    }

    // Inspector: يضيف تقييم
    public function store(Request $request)
    {
        $inspector = auth()->user()->inspector;

        if (!$inspector) {
            return response()->json(['message' => 'Inspectors only'], 403);
        }

        $request->validate([
            'school_id' => [
                'required',
                'exists:schools,id',
                Rule::unique('school_ratings_inspectors')->where(fn($q) => $q->where('inspector_id', $inspector->id)),
            ],
            'scores'                      => ['required', 'array', 'min:1'],
            'scores.*.rating_criteria_id' => ['required', 'exists:rating_criteria,id'],
            'scores.*.score'              => ['required', 'integer', 'min:1', 'max:100'],
        ]);

        $rating = SchoolRatinginspector::create([
            'school_id'    => $request->school_id,
            'inspector_id' => $inspector->id,
        ]);

        foreach ($request->scores as $s) {
            SchoolRatingScore::create([
                'school_rating_id'   => $rating->id,
                'rating_criteria_id' => $s['rating_criteria_id'],
                'score'              => $s['score'],
            ]);
        }

        return response()->json([
            'message' => 'School rated successfully',
            'rating'  => array_merge(
                $rating->load(['school', 'inspector.user', 'scores.criteria'])->toArray(),
                ['average' => $rating->average]
            ),
        ], 201);
    }

    // Inspector: يعدل تقييمه
    public function update(Request $request, $id)
    {
        $inspector = auth()->user()->inspector;

        if (!$inspector) {
            return response()->json(['message' => 'Inspectors only'], 403);
        }

        $rating = SchoolRatinginspector::where('id', $id)
                                       ->where('inspector_id', $inspector->id)
                                       ->first();

        if (!$rating) {
            return response()->json(['message' => 'Rating not found or not yours'], 404);
        }

        $request->validate([
            'scores'                      => ['required', 'array', 'min:1'],
            'scores.*.rating_criteria_id' => ['required', 'exists:rating_criteria,id'],
            'scores.*.score'              => ['required', 'integer', 'min:1', 'max:100'],
        ]);

        $rating->scores()->delete();

        foreach ($request->scores as $s) {
            SchoolRatingScore::create([
                'school_rating_id'   => $rating->id,
                'rating_criteria_id' => $s['rating_criteria_id'],
                'score'              => $s['score'],
            ]);
        }

        return response()->json([
            'message' => 'Rating updated successfully',
            'rating'  => array_merge(
                $rating->load(['school', 'inspector.user', 'scores.criteria'])->toArray(),
                ['average' => $rating->average]
            ),
        ]);
    }

    // Inspector: يشوف تقييماته
    public function myRatings()
    {
        $inspector = auth()->user()->inspector;

        if (!$inspector) {
            return response()->json(['message' => 'Inspectors only'], 403);
        }

        $ratings = SchoolRatinginspector::with(['school', 'scores.criteria'])
                                        ->where('inspector_id', $inspector->id)
                                        ->get();

        return response()->json([
            'message' => 'My ratings',
            'ratings' => $ratings->map(fn($r) => array_merge(
                $r->toArray(), ['average' => $r->average]
            )),
        ]);
    }

    // Admin: حذف تقييم
    public function destroy($id)
    {
        $rating = SchoolRatinginspector::find($id);

        if (!$rating) {
            return response()->json(['message' => 'Rating not found'], 404);
        }

        $rating->delete();

        return response()->json(['message' => 'Rating deleted successfully']);
    }
}
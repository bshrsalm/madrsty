<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SchoolRating;
use App\Models\School;

class SchoolRatingController extends Controller
{
    public function getUserRating(Request $request, $school_id)
    {
        $user = $request->user();

        $rating = SchoolRating::where('user_id', $user->id)
            ->where('school_id', $school_id)
            ->first();

        if (!$rating) {
            return response()->json(['message' => 'No rating found'], 404);
        }

        return response()->json([
            'rating' => $rating
        ]);
    }
public function store(Request $request, $school_id)
{
    $user = $request->user();

    $validated = $request->validate([
        'education_level' => 'required|integer|min:0|max:100',
        'support' => 'required|integer|min:0|max:100',
        'teachers' => 'required|integer|min:0|max:100',
        'follow_up' => 'required|integer|min:0|max:100',
        'trips' => 'required|integer|min:0|max:100',
        'parent_communication' => 'required|integer|min:0|max:100',
        'exams' => 'required|integer|min:0|max:100',
        'enrichment_curriculum' => 'required|integer|min:0|max:100',
        'school_management' => 'required|integer|min:0|max:100',
        'school_environment' => 'required|integer|min:0|max:100',
    ]);

   
    $existingRating = SchoolRating::where('user_id', $user->id)
        ->where('school_id', $school_id)
        ->first();

    if ($existingRating) {
     
        $existingRating->update($validated);
        $rating = $existingRating;
        $message = 'Rating updated successfully';
    } else {
       
        $rating = SchoolRating::create(array_merge(
            ['user_id' => $user->id, 'school_id' => $school_id],
            $validated
        ));
        $message = 'Rating saved successfully';
    }

    $stats = $this->calculateAverages($school_id);

    return response()->json([
        'message' => $message,
        'user_rating' => $rating,
        'statistics' => $stats
    ]);
}
    public function destroy(Request $request, $school_id)
    {
        $user = $request->user();

        $rating = SchoolRating::where('user_id', $user->id)
            ->where('school_id', $school_id)
            ->first();

        if (!$rating) {
            return response()->json(['message' => 'No rating found to delete'], 404);
        }

        $rating->delete();

        return response()->json(['message' => 'Rating deleted successfully']);
    }

    public function show($school_id)
    {
        $ratingsCount = SchoolRating::where('school_id', $school_id)->count();

        if ($ratingsCount === 0) {
            return response()->json(['message' => 'No ratings found for this school']);
        }

        $stats = $this->calculateAverages($school_id);

        return response()->json([
            'school_id' => $school_id,
            'statistics' => $stats
        ]);
    }

    public function index(Request $request, $school_id)
    {
        $user = $request->user();

        $school = School::find($school_id);
        
        if (!$school) {
            return response()->json(['message' => 'School not found'], 404);
        }

        $isAdmin = in_array(strtolower($user->role), ['admin']);
        $isManagerOfThisSchool = in_array(strtolower($user->role), ['manager']) && $user->school_id == $school_id;

        if (!$isAdmin && !$isManagerOfThisSchool) {
            return response()->json(['message' => 'Unauthorized access'], 403);
        }

        $ratings = SchoolRating::where('school_id', $school_id)
            ->with('user:id,name,email')
            ->get();

        if ($ratings->isEmpty()) {
            return response()->json(['message' => 'No ratings found']);
        }

        $stats = $this->calculateAverages($school_id);

        return response()->json([
            'school_id' => $school_id,
            'statistics' => $stats,
            'detailed_ratings' => $ratings
        ]);
    }

    private function calculateAverages($school_id)
    {
        $ratings = SchoolRating::where('school_id', $school_id);
        $count = $ratings->count();

        if ($count === 0) {
            return null;
        }

        $avgEducation = round($ratings->avg('education_level'), 2);
        $avgSupport = round($ratings->avg('support'), 2);
        $avgTeachers = round($ratings->avg('teachers'), 2);
        $avgFollowUp = round($ratings->avg('follow_up'), 2);
        $avgTrips = round($ratings->avg('trips'), 2);
        $avgParentCommunication = round($ratings->avg('parent_communication'), 2);
        $avgExams = round($ratings->avg('exams'), 2);
        $avgEnrichmentCurriculum = round($ratings->avg('enrichment_curriculum'), 2);
        $avgSchoolManagement = round($ratings->avg('school_management'), 2);
        $avgSchoolEnvironment = round($ratings->avg('school_environment'), 2);

        return [
            'total_raters' => $count,
            'averages' => [
                'education_level' => $avgEducation,
                'support' => $avgSupport,
                'teachers' => $avgTeachers,
                'follow_up' => $avgFollowUp,
                'trips' => $avgTrips,
                'parent_communication' => $avgParentCommunication,
                'exams' => $avgExams,
                'enrichment_curriculum' => $avgEnrichmentCurriculum,
                'school_management' => $avgSchoolManagement,
                'school_environment' => $avgSchoolEnvironment,
            ]
        ];
    }

    public function compareRatings(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'school1' => 'required|exists:schools,id',
            'school2' => 'required|exists:schools,id',
        ]);

        if ($request->school1 == $request->school2) {
            return response()->json([
                'message' => 'Please choose two different schools'
            ], 422);
        }

        $school1 = School::find($request->school1);
        $school2 = School::find($request->school2);

        $stats1 = $this->calculateAveragesForCompare($school1->id);
        $stats2 = $this->calculateAveragesForCompare($school2->id);

        return response()->json([
            'school_1' => [
                'id' => $school1->id,
                'name' => $school1->name,
                'ratings' => $stats1
            ],
            'school_2' => [
                'id' => $school2->id,
                'name' => $school2->name,
                'ratings' => $stats2
            ]
        ]);
    }

    private function calculateAveragesForCompare($school_id)
    {
        $ratings = SchoolRating::where('school_id', $school_id);
        $count = $ratings->count();

        if ($count === 0) {
            return [
                'overall_average' => 0,
                'teaching_quality_average' => 0,
                'facilities_average' => 0,
                'cleanliness_average' => 0,
                'follow_up_average' => 0,  
                'activities_average' => 0,
                'parent_communication_average' => 0,
                'exams_average' => 0,
                'enrichment_curriculum_average' => 0,
                'school_management_average' => 0,
                'school_environment_average' => 0,
                'total_raters' => 0
            ];
        }

        $avgEducation = round(($ratings->avg('education_level') / 20), 1);
        $avgSupport = round(($ratings->avg('support') / 20), 1);
        $avgTeachers = round(($ratings->avg('teachers') / 20), 1);
        $avgFollowUp = round(($ratings->avg('follow_up') / 20), 1);
        $avgTrips = round(($ratings->avg('trips') / 20), 1);
        $avgParentCommunication = round(($ratings->avg('parent_communication') / 20), 1);
        $avgExams = round(($ratings->avg('exams') / 20), 1);
        $avgEnrichmentCurriculum = round(($ratings->avg('enrichment_curriculum') / 20), 1);
        $avgSchoolManagement = round(($ratings->avg('school_management') / 20), 1);
        $avgSchoolEnvironment = round(($ratings->avg('school_environment') / 20), 1);

        $overallAvg = round(($avgEducation + $avgSupport + $avgTeachers + $avgFollowUp + $avgTrips + 
                            $avgParentCommunication + $avgExams + $avgEnrichmentCurriculum + 
                            $avgSchoolManagement + $avgSchoolEnvironment) / 10, 1);

        return [
            'overall_average' => $overallAvg,
            'teaching_quality_average' => $avgEducation, 
            'facilities_average' => $avgSupport, 
            'cleanliness_average' => $avgTeachers, 
            'follow_up_average' => $avgFollowUp,  
            'activities_average' => $avgTrips,
            'parent_communication_average' => $avgParentCommunication,
            'exams_average' => $avgExams,
            'enrichment_curriculum_average' => $avgEnrichmentCurriculum,
            'school_management_average' => $avgSchoolManagement,
            'school_environment_average' => $avgSchoolEnvironment,
            'total_raters' => $count
        ];
    }

    public function compareWithMySchool(Request $request)
    {
        $user = $request->user();

        if (strtolower($user->role) !== 'manager') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if (!$user->school_id) {
            return response()->json(['message' => 'Manager has no school assigned'], 422);
        }

        $request->validate([
            'other_school_id' => 'required|exists:schools,id',
        ]);

        if ($user->school_id == $request->other_school_id) {
            return response()->json([
                'message' => 'You cannot compare your school with itself'
            ], 422);
        }

        $mySchool = School::find($user->school_id);
        $otherSchool = School::find($request->other_school_id);

        $myStats = $this->calculateAveragesForCompare($mySchool->id);
        $otherStats = $this->calculateAveragesForCompare($otherSchool->id);

        return response()->json([
            'my_school' => [
                'id' => $mySchool->id,
                'name' => $mySchool->name,
                'ratings' => $myStats
            ],
            'other_school' => [
                'id' => $otherSchool->id,
                'name' => $otherSchool->name,
                'ratings' => $otherStats
            ]
        ]);
    }
}
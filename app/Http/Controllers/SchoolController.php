<?php

namespace App\Http\Controllers;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

use Illuminate\Support\Facades\Storage;

use App\Models\School;
use App\Models\User;
use Illuminate\Http\Request;

class SchoolController extends Controller
{

public function indexForComparison(Request $request)
{
    $user = $request->user();
    
    // للـ Manager: نرجع كل المدارس ما عدا مدرسته (لأنه سيقارن مدرسته بمدرسة أخرى)
    if ($user->role === 'Manager') {
        $schools = School::with('manager')
                        ->where('id', '!=', $user->school_id)
                        ->latest()
                        ->get();
        return response()->json($schools);
    }
    
    // للـ Admin والـ User: نرجع كل المدارس
    if (in_array($user->role, ['Admin', 'user'])) {
        $schools = School::with('manager')->latest()->get();
        return response()->json($schools);
    }
    
    return response()->json(['message' => 'Unauthorized'], 403);
}
   public function index(Request $request)
{
    $user = $request->user();


    if ($user->role === 'Admin') {
        $schools = School::with('manager')->latest()->get();
        return response()->json($schools);
    }

  
    if ($user->role === 'Manager') {
        $school = School::where('id', $user->school_id)
                        ->with('manager')
                        ->first();

        if (!$school) {
            return response()->json(['message' => 'No school assigned to this manager'], 404);
        }

        return response()->json([$school]);
    }
if ($user->role === 'user') {
            $schools = School::with('manager')->latest()->get();
            return response()->json($schools);
        }
  
    return response()->json(['message' => 'Unauthorized'], 403);
}
public function search(Request $request)
{
    $name   = $request->input('name');
    $gender = $request->input('student_gender'); 

    $query = School::query();

   
    if (!empty($name)) {
        $query->where('name', 'like', '%' . $name . '%');
    }

   
    if (!empty($gender)) {
        $query->where('student_gender', $gender);
    }

    $schools = $query->with('manager')->get();

    if ($schools->isEmpty()) {
        return response()->json([
            'message' => 'لا يوجد نتائج مطابقة'
        ], 404);
    }

    return response()->json([
        'results' => $schools
    ]);
}

public function show(School $school)
{
   return response()->json([
    'id' => $school->id,
    'name' => $school->name,
    'student_gender' => $school->student_gender, 
    'address' => $school->address,
    'phone' => $school->phone,
    'registration_fee' => $school->registration_fee,
    'tuition' => $school->tuition,
    'description' => $school->description,
    'website' => $school->website,
    'instagram' => $school->instagram,
    'facebook' => $school->facebook,
    'google_map' => $school->google_map,
    'manager' => $school->manager,
    'barcode_image' => $school->barcode_image,
]);

}



public function store(Request $request)
{
    $this->authorizeAdmin($request);

    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'student_gender' => 'nullable|in:male,female,mixed',
        'address' => 'nullable|string',
        'phone' => 'nullable|string|max:50',
        'registration_fee' => 'nullable|numeric|min:0',
        'tuition' => 'nullable|numeric|min:0',
        'description' => 'nullable|string',
        'link_web' => 'nullable|url',
        'instagram' => 'nullable|string|max:255',
        'facebook'  => 'nullable|string|max:255',
        'google_map' => 'nullable|string|max:255',
        'manager_id' => 'nullable|exists:users,id',
    ]);

   
    $school = School::create($validated);
$token = bin2hex(random_bytes(16));
$school->barcode_token = $token;

$scanUrl = url('/api/qr/'.$token);


$qr = new QrCode($scanUrl);
$writer = new PngWriter();

$result = $writer->write($qr);


$fileName = "qr/qr_{$school->id}.png";


Storage::disk('public')->put($fileName, $result->getString());


$school->barcode_image = asset("storage/".$fileName);
$school->save();


if (!empty($validated['manager_id'])) {
    $manager = User::find($validated['manager_id']);
    $manager->update([
        'role' => 'Manager',
        'school_id' => $school->id,
    ]);
}
   
    return response()->json([
        'message' => 'School created successfully with QR',
        'data' => $school->load('manager'),
    ], 201);
}
public function update(Request $request, School $school)
{
    $this->authorizeAdmin($request);

    
    $validated = $request->validate([
        'name' => 'sometimes|string|max:255',
         'student_gender' => 'sometimes|nullable|in:male,female,mixed',
        'address' => 'sometimes|nullable|string',
        'phone' => 'sometimes|nullable|string|max:50',
        'registration_fee' => 'sometimes|nullable|numeric|min:0',
        'tuition' => 'sometimes|nullable|numeric|min:0',
        'description' => 'sometimes|nullable|string',
        'link_web' => 'sometimes|nullable|url',
        'instagram' => 'sometimes|nullable|string|max:255',
        'facebook'  => 'sometimes|nullable|string|max:255',
        'google_map' => 'sometimes|nullable|string|max:255',
        'manager_id' => 'sometimes|nullable|exists:users,id',
    ]);


    $school->update($validated);

 
    $token = bin2hex(random_bytes(16));
    $school->barcode_token = $token;


    $scanUrl = url('/api/qr/' . $token);


    $qr = new QrCode($scanUrl);
    $writer = new PngWriter();
    $result = $writer->write($qr);

   
    $fileName = "qr/qr_{$school->id}.png";
    Storage::disk('public')->put($fileName, $result->getString());


    $school->barcode_image = asset("storage/".$fileName);
    $school->save();

   
    if ($request->has('manager_id') && !empty($validated['manager_id'])) {
        $manager = User::find($validated['manager_id']);
        $manager->update([
            'role' => 'Manager',
            'school_id' => $school->id,
        ]);
    }

    return response()->json([
        'message' => 'School updated successfully with new QR',
        'data' => $school->load('manager'),
    ]);
}


 

    public function destroy(Request $request, School $school)
    {
        $this->authorizeAdmin($request);
        $school->delete();

        return response()->json(['message' => 'School deleted successfully']);
    }

    private function authorizeAdmin(Request $request)
    {
        $user = $request->user();
        if (!$user || $user->role !== 'Admin') {
            abort(403, 'Unauthorized. Admins only.');
        }
    }

public function compare(Request $request)
{
   
    $validated = $request->validate([
        'school1' => 'required|integer|exists:schools,id',
        'school2' => 'required|integer|exists:schools,id',
    ]);

    
    if ($validated['school1'] == $validated['school2']) {
        return response()->json([
            'message' => 'You must select two different schools'
        ], 422);
    }


    $school1 = School::with('manager')->find($validated['school1']);
    $school2 = School::with('manager')->find($validated['school2']);


    if (!$school1 || !$school2) {
        return response()->json([
            'message' => 'One or both schools not found'
        ], 404);
    }

    return response()->json([
        'school_1' => [
            'id' => $school1->id,
            'name' => $school1->name,
            'registration_fee' => $school1->registration_fee ?? 0,
            'tuition' => $school1->tuition ?? 0,
            'address' => $school1->address ?? 'غير محدد',
            'phone' => $school1->phone ?? 'غير محدد',
            'manager' => optional($school1->manager)->name ?? 'غير محدد',
        ],
        'school_2' => [
            'id' => $school2->id,
            'name' => $school2->name,
            'registration_fee' => $school2->registration_fee ?? 0,
            'tuition' => $school2->tuition ?? 0,
            'address' => $school2->address ?? 'غير محدد',
            'phone' => $school2->phone ?? 'غير محدد',
            'manager' => optional($school2->manager)->name ?? 'غير محدد',
        ],
    ]);
}
}

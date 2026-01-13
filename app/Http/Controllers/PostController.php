<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index($school_id)
    {
        $posts = Post::where('school_id', $school_id)
            ->with('user')
            ->latest()
            ->get();

        return response()->json($posts);
    }
    public function allPosts(Request $request)
{
    $user = $request->user();
    
   
    if ($user->role !== 'Admin') {
        abort(403, 'Unauthorized. Admin access only.');
    }
    
    $posts = Post::with(['user', 'school'])
                ->latest()
                ->get();
    
    return response()->json($posts);
}

    public function store(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'Manager') {
            abort(403, 'Only managers can post.');
        }

 
        $validated = $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
            'image'   => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

     
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('posts', 'public');
            $validated['image'] = $path;
        }

        $validated['school_id'] = $user->school_id;
        $validated['user_id']   = $user->id;

        $post = Post::create($validated);

        return response()->json([
            'message' => 'Post created successfully',
            'data'    => $post
        ]);
    }

    public function update(Request $request, Post $post)
    {
        $user = $request->user();

        if ($user->id !== $post->user_id) {
            abort(403, 'You can only edit your own posts.');
        }

        $validated = $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
            'image'   => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('posts', 'public');
            $validated['image'] = $path;
        }

        $post->update($validated);

        return response()->json([
            'message' => 'Post updated successfully',
            'data'    => $post
        ]);
    }

    public function destroy(Request $request, Post $post)
    {
        $user = $request->user();

        if ($user->id !== $post->user_id) {
            abort(403, 'You can only delete your own posts.');
        }

        $post->delete();

        return response()->json(['message' => 'Post deleted successfully']);
    }
}

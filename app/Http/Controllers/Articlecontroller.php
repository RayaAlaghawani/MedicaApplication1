<?php

namespace App\Http\Controllers;

use App\Models\article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Articlecontroller extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // عرض مقالات طبية
    public function indexallArticle()
    {
        $doctor_id = Auth::user()->id;
        $articles = article::where('doctor_id', $doctor_id)->get();

        if ($articles->isEmpty()) {
            return response()->json([
                'message' => 'No articles available currently.',
            ], 404);
        }

        return response()->json([
            'message' => 'SUCCESS.',
            'data' => \App\Http\Resources\article::collection($articles),
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    // إضافة مقالة
    public function createArticle(Request $request)
    {
        $doctor_id = Auth::user()->id;
        $specialization_id = Auth::user()->specialization->id;

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content'=> 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'status' => 'required|in:draft,published,reviewing',
            'summary' => 'required|string',
            'category' =>'required|string',
        ]);

        $imagePath = null;
        if ($request->has('image')) {
            $imagePath = $request->file('image')->store('article_images', 'public');
        }

        $article = article::create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'image' => $imagePath,
            'category' => $validated['category'],
            'published_at' => now(),
            'status' => $validated['status'],
            'summary' => $validated['summary'],
            'doctor_id' => $doctor_id,
            'specialization_id' => $specialization_id,
        ]);

        return response()->json([
            'message' => 'Article added successfully.',
            'article' => new \App\Http\Resources\article($article),
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    // تعديل مقالة
    public function update(Request $request, $id)
    {
        $doctor_id = Auth::user()->id;

        $data = $request->validate([
            'title' => 'string|max:255',
            'content'=> 'required|string',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'status' => 'in:draft,published,reviewing',
            'summary' => 'nullable|string',
            'category' =>'string',
            'specialization_id'=> 'nullable|exists:specializations,id',
        ]);

        $article = article::where('id', $id)->first();
        if (!$article) {
            return response()->json([
                'message' => 'This article does not exist.',
            ], 404);
        }

        if ($request->has('image')) {
            $data['image'] = $request->file('image')->store('article_images', 'public');
        }

        if ($request->has('content_pdf_file')) {
            $data['content_pdf_file'] = $request->file('content_pdf_file')->store('cv_files', 'public');
        }

        $article->update($data);
        $article->refresh();

        return response()->json([
            'message' => 'Article updated successfully.',
            'article' => new \App\Http\Resources\article($article),
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    // حذف مقالة
    public function destroyArticle($id)
    {
        $doctor_id = Auth::user()->id;

        $article = article::where('id', $id)->first();
        if (!$article) {
            return response()->json([
                'message' => 'This article does not exist.',
            ], 404);
        }

        $deleted = article::where('doctor_id', $doctor_id)->where('id', $id)->delete();

        return response()->json([
            'message' => 'Article deleted successfully.',
        ], 200);
    }}

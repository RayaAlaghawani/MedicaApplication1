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
    //عرض مقالات طبية
    public function indexallArticle()
    {
        $doctor_id=Auth::user()->id;
        $articles=article::where('doctor_id',$doctor_id)->get();
        if($articles->isEmpty()){
            return response()->json([
                'message' => 'لا يوجد مقالات حاليا.',
            ], 404);

        }
        return response()->json([
            'message' => 'SUCCESS.',
            'date'=>\App\Http\Resources\article::collection($articles),
        ], 200);

    }

    /**
     * Show the form for creating a new resource.
     */
    //اضافة مقالة
    public function createArticle(Request $request)
    {
        $doctor_id=Auth::user()->id;
        $specialization_id=Auth::user()->specialization->id;
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content_pdf_file'=> 'required|file|mimes:pdf,doc,docx|max:5120',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'status' => 'required|in:draft,published,reviewing',
            'summary' => 'required|string',
            'category' =>'required|string',


        ]);


        if($request->has('image')) {
            $imagePath = $request->file('image')->store('article_images', 'public');
        }

        if($request->has('content_pdf_file')) {
            $pdfPath = $request->file('content_pdf_file')->store('cv_files', 'public');
        }

        $article = Article::create([
            'title' => $validated['title'],
            'content_pdf_file' => $pdfPath,
            'image' => $imagePath ,
            'category' => $validated['category'],
            'published_at' => now(),
'status' => $validated['status'],
            'summary' => $validated['summary'],
            'doctor_id' => $doctor_id,
            'specialization_id' =>$specialization_id,
        ]);

        return response()->json([
            'message' => 'تم إضافة المقال بنجاح',
                'article' => new \App\Http\Resources\article($article)

        ], 201);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    //تعديل مقالة

    public function update(Request $request,  $id)
    {
        $doctor_id=Auth::user()->id;

        $data = $request->validate([
            'title' => 'string|max:255',
            'content_pdf_file'=> 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'status' => 'in:draft,published,reviewing',
            'summary' => 'nullable|string',
            'category' =>'string',
            'specialization_id'=> 'nullable|exists:specializations,id',

        ]);
        $article=article::where('id',$id)->first();
        if(!$article){
            return response()->json([
                'message' => 'هذه المقالة غير موجودة.',
            ], 404);

        }

        if($request->has('image')) {
            $data['image'] = $request->file('image')->store('article_images', 'public');
        }


        if($request->has('content_pdf_file')) {
            $data['content_pdf_file'] = $request->file('content_pdf_file')->store('cv_files', 'public');

        }

        $article->update($data);
        $article->refresh();
        return response()->json([
            'message' => 'تم تعديل المقالة  بنجاح',
            'article' => new \App\Http\Resources\article($article)
        ], 200);


    }

    /**
     * Remove the specified resource from storage.
     */
    //حذف مقالة
    public function destroyArticle( $id)
    {
        $doctor_id=Auth::user()->id;
        $article=article::where('id',$id)->first();
if(!$article){
    return response()->json([
        'message' => 'غير موجودة هذه المقالة.',
    ], 404);

}
        $article=article::where('doctor_id',$doctor_id)->where('id',$id)->delete();

        return response()->json([
            'message' => 'تم حذف المقالة بنجاح.',
        ], 200);

    }

}

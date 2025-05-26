<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Middlewares\AccessMiddleware;

class ArticleController extends Controller
{
    public function __construct()
    {
        foreach (AccessMiddleware::permissions() as $rule) {
            $this->middleware($rule['middleware'])->only($rule['only']);
        }
    }
    public function index()
    {
        $articles = Article::latest()->paginate(25);
        return response()->json([
            'status' => true,
            'articles' => $articles
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|min:5',
            'author' => 'required|min:5',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $article = Article::create([
            'title' => $request->title,
            'text' => $request->text,
            'author' => $request->author,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Article added successfully.',
            'article' => $article
        ]);
    }

    public function show($id)
    {
        $article = Article::findOrFail($id);
        return response()->json([
            'status' => true,
            'article' => $article
        ]);
    }

    public function update(Request $request, $id)
    {
        $article = Article::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'required|min:5',
            'author' => 'required|min:5',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $article->update([
            'title' => $request->title,
            'text' => $request->text,
            'author' => $request->author,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Article updated successfully.',
            'article' => $article
        ]);
    }

    public function destroy($id)
    {
        $article = Article::find($id);

        if (!$article) {
            return response()->json(['status' => false, 'message' => 'Article not found'], 404);
        }

        $article->delete();

        return response()->json([
            'status' => true,
            'message' => 'Article deleted successfully.'
        ]);
    }
}

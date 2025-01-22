<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CreateArticleRequest;
use App\Http\Requests\CommonArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Http\Resources\ArticleResource;


class ArticleController extends Controller
{
    public function createArticle(CreateArticleRequest $request)
    {
        try {
            DB::beginTransaction();

            $title = $request->title;
            $content = $request->content;
            $status = $request->status;
            $userId = $request->userId;
            $categoryIds = $request->categoryIds;

            $createArticle = Article::create([
                "title" => $title,
                "content" => $content,
                "status" => $status,
                "user_id" => $userId,
            ]);

            $createArticle->categories()->attach($categoryIds);

            DB::commit();

            return response()->json([
                "message" => "The Article is created successfully",
                "data" => $createArticle
            ], 201);
        }
        catch(Exception $e){
            DB::rollBack();

            return response()->json([
                "message" => "There was an error while creating the article",
                "code" => $e->getCode(),
                "error" => $e->getMessage(),
                "file" => $e->getFile(),
                "line" => $e->getLine(),
            ], 500);
        }
    }

    public function getArticles(Request $request)
    {
        try {
            $articles = Article::all();

            return response()->json([
                "data" => ArticleResource::collection($articles),
            ], 200);
        }
        catch(Exception $e) {
            return response()->json([
                "message" => "There was an error while fetching the articles",
                "code" => $e->getCode(),
                "error" => $e->getMessage(),
                "file" => $e->getFile(),
                "line" => $e->getLine(),
            ], 500);
        }
    }


    public function getArticle(CommonArticleRequest $request, $id)
    {
        try {
            $article = Article::find($id);

            return response()->json([
                "data" => new ArticleResource($article),
            ], 200);
        }
        catch(Exception $e) {
            return response()->json([
                "message" => "There was an error while fetching the article",
                "code" => $e->getCode(),
                "error" => $e->getMessage(),
                "file" => $e->getFile(),
                "line" => $e->getLine(),
            ], 500);
        }
    }


    public function updateArticle(UpdateArticleRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $categoryName = $request->name;

            $article = Article::find($id);

            $article->update([
                "name" => $categoryName,
            ]);

            DB::commit();

            return response()->json([
                "message" => "The Article is updated successfully",
            ], 200);
        }
        catch(Exception $e){
            DB::rollback();

            return response()->json([
                "message" => "There was an error while updating the article",
                "code" => $e->getCode(),
                "error" => $e->getMessage(),
                "file" => $e->getFile(),
                "line" => $e->getLine(),
            ], 500);
        }
    }


    public function deleteArticle(CommonArticleRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $article = Article::find($id);

            $article->delete();

            DB::commit();

            return response()->json([
                "message" => "The Article is deleted successfully",
            ], 200);
        }
        catch(Exception $e) {
            DB::rollBack();

            return response()->json([
                "message" => "There was an error while deleting the article",
                "code" => $e->getCode(),
                "error" => $e->getMessage(),
                "file" => $e->getFile(),
                "line" => $e->getLine(),
            ], 500);
        }
    }
}

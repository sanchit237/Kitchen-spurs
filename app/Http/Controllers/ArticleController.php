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
use App\Jobs\ArticleSlug;
use App\Jobs\ArticleSummary;
use App\Enums\RoleEnum;
use App\Enums\ArticleStatusEnum;


class ArticleController extends Controller
{
    //Create Article Function
    public function createArticle(CreateArticleRequest $request)
    {
        try {
            DB::beginTransaction();

            $title = $request->title;
            $content = $request->content;
            $status = $request->status;
            $userId = Auth::id();
            $categoryIds = $request->categoryIds;
            $publishedDate = null;

            if ($status === ArticleStatusEnum::Published->value) {
                $publishedDate = now();
            }

            $createArticle = Article::create([
                "title" => $title,
                "content" => $content,
                "status" => $status,
                "published_date" => $publishedDate,
                "user_id" => $userId,
            ]);

            $createArticle->categories()->attach($categoryIds);

            //Dispatch jobs for slug and summary generation
            // ArticleSlug::dispatch($createArticle);
            // ArticleSummary::dispatch($createArticle);

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

    //Fetch All Articles Function
    public function getArticles(Request $request)
    {
        try {
            $user = Auth::user();
            $perPage = $request->query('perPage', 10);
            $status = $request->status;
            $categoryIds = $request->categoryIds;
            $startDate = $request->startDate;
            $endDate = $request->endDate;

            $articles = Article::with(['categories', 'user']);

            //check if role is author
            if ($user->role === RoleEnum::Author->value) {
                $articles->where('user_id', $user->id);
            }

            // Filter by status
            $articles->when($status, function($query) use ($status) {
                $query->where('status', $status);
            });

            //Filter by categories
            $articles->when($categoryIds, function($query) use ($categoryIds) {
                $query->whereHas('categories', function($query) use ($categoryIds) {
                    $query->whereIn('categories.id', $categoryIds);
                });
            });

            // Conditional check if start and end date is passed or either is passed
            $articles->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                $query->whereBetween("published_date", [$startDate, date('Y-m-d 23:59:59', strtotime($endDate))]);
            })
            ->when($startDate && !$endDate, function ($query) use ($startDate) {
                $query->where("published_date", ">=", $startDate);
            })
            ->when(!$startDate && $endDate, function ($query) use ($endDate) {
                $query->where("published_date", "<=", date('Y-m-d 23:59:59', strtotime($endDate)));
            });

            //Final query
            $articlesResult = $articles->latest()->paginate($perPage);

            return response()->json([
                "data" => ArticleResource::collection($articlesResult)->response()->getData(true),
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

    //Fetch Single Article Function
    public function getArticle(CommonArticleRequest $request, $id)
    {
        try {
            $user = Auth::user();

            $article = Article::find($id);

            if ($user->role !== RoleEnum::Admin->value && $article->user_id !== $user->id) {
                return response()->json([
                    "message" => "You do not have permission to access this article",
                ], 403);
            }

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

    //Update Article Function
    public function updateArticle(UpdateArticleRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $title = $request->title;
            $content = $request->content;
            $status = $request->status;
            $categoryIds = $request->categoryIds;
            $publishedDate = null;
            $user = Auth::user();

            $article = Article::find($id);

            if ($user->role !== RoleEnum::Admin->value && $article->user_id !== $user->id) {
                return response()->json([
                    "message" => "You do not have permission to update this article",
                ], 403);
            }

            if ($status === ArticleStatus::Published->value && !$article->published_date) {
                $publishedDate = now();
            }

            $article->update([
                "title" => $title,
                "content" => $content,
                "status" => $status,
                "published_date" => $publishedDate,
            ]);

            $article->categories()->sync($categoryIds);

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

    //Delete Article Function
    public function deleteArticle(CommonArticleRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $user = Auth::user();

            $article = Article::find($id);

            if ($user->role !== RoleEnum::Admin->value && $article->user_id !== $user->id) {
                return response()->json([
                    "message" => "You do not have permission to delete this article",
                ], 403);
            }

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

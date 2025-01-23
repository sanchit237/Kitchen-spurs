<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Exception;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\CategoryResource;
use App\Http\Requests\CreateCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Requests\CommonCategoryRequest;

class CategoryController extends Controller
{
    // Creates a new category in the database and handles errors with transaction support.
    public function createCategory(CreateCategoryRequest $request)
    {
        DB::beginTransaction();

        try {
            $createCategory = Category::create([
                "name" => $request->name,
            ]);

            DB::commit();

            return response()->json([
                "message" => "The Todo is created successfully",
                "data" => $createCategory
            ], 201);
        }
        catch(Exception $e){
            DB::rollBack();

            return response()->json([
                "message" => "There was an error while creating the category",
                "code" => $e->getCode(),
                "error" => $e->getMessage(),
                "file" => $e->getFile(),
                "line" => $e->getLine(),
            ], 500);
        }
    }

    // Retrieves all categories
    public function getCategories(Request $request)
    {
        try {
            $categories = Category::all();

            return response()->json([
                "data" => CategoryResource::collection($categories),
            ], 200);
        }
        catch(Exception $e) {
            return response()->json([
                "message" => "There was an error while fetching the categories",
                "code" => $e->getCode(),
                "error" => $e->getMessage(),
                "file" => $e->getFile(),
                "line" => $e->getLine(),
            ], 500);
        }
    }

    // Retrieves a specific category by its ID
    public function getCategory(CommonCategoryRequest $request, $id)
    {
        try {
            $category = Category::find($id);

            return response()->json([
                "data" => new CategoryResource($category),
            ], 200);
        }
        catch(Exception $e) {
            return response()->json([
                "message" => "There was an error while fetching the category",
                "code" => $e->getCode(),
                "error" => $e->getMessage(),
                "file" => $e->getFile(),
                "line" => $e->getLine(),
            ], 500);
        }
    }

    // Updates an existing category by its ID with the provided data and handles errors with transaction management.
    public function updateCategory(UpdateCategoryRequest $request, $id)
    {
        DB::beginTransaction();

        try {
            $categoryName = $request->name;

            $category = Category::find($id);

            $category->update([
                "name" => $categoryName,
            ]);

            DB::commit();

            return response()->json([
                "message" => "The Category is updated successfully",
            ], 200);
        }
        catch(Exception $e){
            DB::rollback();

            return response()->json([
                "message" => "There was an error while updating the category",
                "code" => $e->getCode(),
                "error" => $e->getMessage(),
                "file" => $e->getFile(),
                "line" => $e->getLine(),
            ], 500);
        }
    }

    // Deletes a specific category by its ID and handles errors with transaction management.
    public function deleteCategory(CommonCategoryRequest $request, $id)
    {
        DB::beginTransaction();

        try {
            $category = Category::find($id);

            $category->delete();

            DB::commit();

            return response()->json([
                "message" => "The Category is deleted successfully",
            ], 200);
        }
        catch(Exception $e) {
            DB::rollBack();

            return response()->json([
                "message" => "There was an error while deleting the category",
                "code" => $e->getCode(),
                "error" => $e->getMessage(),
                "file" => $e->getFile(),
                "line" => $e->getLine(),
            ], 500);
        }
    }
}

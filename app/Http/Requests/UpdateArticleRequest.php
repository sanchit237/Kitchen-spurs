<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UpdateArticleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }


    public function prepareForValidation()
    {
        $this->merge([
            'id' => $this->route('id'),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "id" => "required|exists:articles,id",
            "title" => "required|string",
            "content" => "required|string",
            "status" => ["required", "integer", Rule::in([1, 2, 3])],
            "categoryIds" => "required|array",
            "categoryIds.*" => Rule::exists('categories', 'id')->withoutTrashed(),
        ];
    }


    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            "id.required" => "The article id is required",
            "id.exists" => "The article id does not exist",
            "title.required" => "The article title is required",
            "title.string" => "The article status must be a valid string",
            "content.required" => "The article content is required",
            "content.string" => "The article content must be a valid string",
            "status.required" => "The article status is required",
            "status.integer" => "The article status must be an integer.",
            "status.in" => "The article status must be one of the following: draft (1), published (2), or archived (3).",
            "categoryIds.required" => "Category IDs are required.",
            "categoryIds.array" => "Category IDs must be an array.",
            "categoryIds.*.exists" => "The selected category is invalid.",
        ];
    }


    public function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();

        throw new HttpResponseException(response()->json([
            'message' => 'Validation failed',
            'errors' => $errors,
        ], 422));
    }
}

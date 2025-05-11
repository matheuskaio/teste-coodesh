<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => 'in:draft,trash,published',
            'product_name' => 'string',
            'quantity' => 'string',
            'brands' => 'string',
            'categories' => 'string',
            'labels' => 'string',
            'cities' => 'string',
            'purchase_places' => 'string',
            'stores' => 'string',
            'ingredients_text' => 'string',
            'traces' => 'string',
            'serving_size' => 'string',
            'serving_quantity' => 'numeric',
            'nutriscore_score' => 'numeric',
            'nutriscore_grade' => 'string',
        ];
    }
}

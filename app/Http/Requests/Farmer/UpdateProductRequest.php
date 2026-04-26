<?php

namespace App\Http\Requests\Farmer;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->route('product')->user_id === auth()->id();
    }

    public function rules(): array
    {
        return [
            'name'           => ['required', 'string', 'max:255'],
            'category_id'    => ['required', 'exists:categories,id'],
            'description'    => ['required', 'string', 'max:5000'],
            'price'          => ['required', 'numeric', 'min:0', 'max:999999'],
            'original_price' => ['nullable', 'numeric', 'min:0', 'max:999999'],
            'unit'           => ['required', 'string', 'max:50'],
            'stock'          => ['required', 'integer', 'min:0', 'max:999999'],
            'location'       => ['nullable', 'string', 'max:255'],
            'is_organic'      => ['boolean'],
            'image'           => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'harvest_date'    => ['nullable', 'date', 'before_or_equal:today'],
            'shelf_life_days' => ['nullable', 'integer', 'min:1', 'max:365'],
        ];
    }
}

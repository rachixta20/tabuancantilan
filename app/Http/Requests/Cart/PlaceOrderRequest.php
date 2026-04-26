<?php

namespace App\Http\Requests\Cart;

use App\Enums\PaymentMethod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PlaceOrderRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'payment_method'    => ['required', Rule::enum(PaymentMethod::class)],
            'delivery_address'  => ['nullable', 'required_unless:payment_method,walkin', 'string', 'max:500'],
            'payment_reference' => ['nullable', 'required_if:payment_method,gcash', 'required_if:payment_method,maya', 'string', 'max:100'],
            'notes'             => ['nullable', 'string', 'max:500'],
        ];
    }
}

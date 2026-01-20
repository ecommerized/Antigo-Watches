<?php

namespace App\Http\Requests;

use App\CentralLogics\Helpers;
use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use function App\CentralLogics\translate;

class StoreOrder extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'cart' => 'required|array|min:1',
            'cart.*.product_id' => 'required|numeric|exists:products,id',
            'cart.*.quantity' => 'required|numeric|min:1',
            'cart.*.variant' => 'nullable|string',
            'coupon_code' => 'nullable|string|exists:coupons,code',
            'payment_method' => 'required|string',
            'payment_platform' => 'required|string|in:web,app',
            'callback' => 'required|url',
            'customer_id' => 'nullable|required_unless:is_guest,1|exists:users,id',
            'is_guest' => 'nullable|in:0,1',
        ];
    }

    public function messages(): array
    {
        return [
            'cart.required' => translate('cart is empty'),
            'cart.array' => translate('cart must be an array'),
            'cart.min' => translate('cart must have at least one item'),
            'cart.*.product_id.required' => translate('product id is required'),
            'cart.*.product_id.numeric' => translate('product id must be a number'),
            'cart.*.product_id.exists' => translate('product not found'),
            'cart.*.quantity.required' => translate('quantity is required'),
            'cart.*.quantity.numeric' => translate('quantity must be a number'),
            'cart.*.quantity.min' => translate('quantity must be at least 1'),
            'cart.*.variant.string' => translate('variant must be a string'),
            'coupon_code.string' => translate('coupon code must be a string'),
            'coupon_code.exists' => translate('coupon code not found'),
            'payment_method.required' => translate('payment method is required'),
            'payment_method.string' => translate('payment method must be a string'),
            'payment_platform.required' => translate('payment platform is required'),
            'payment_platform.string' => translate('payment platform must be a string'),
            'payment_platform.in' => translate('payment platform must be either web or app'),
            'callback.required' => translate('callback url is required'),
            'callback.url' => translate('callback must be a valid url'),
            'customer_id.required_unless' => translate('customer id is required unless is_guest is 1'),
            'customer_id.exists' => translate('customer not found'),
            'is_guest.in' => translate('is_guest must be either 0 or 1')
        ];
    }

    public function withValidator($validator) {
        $validator->after(function ($validator)  {
            foreach ($this->input('cart', []) as $c) {
                $product = Product::find($c['product_id']);

                if (!$product) {
                    continue;
                }

                $variations = json_decode($product->variations, true);

                if (!empty($variations)) {
                    $type = $c['variation'][0]['type'] ?? null;

                    foreach ($variations as $var) {
                        if ($type == $var['type'] && $var['stock'] < $c['quantity']) {
                            $validator->errors()->add('stock', translate('One or more product stock is insufficient!'));
                        }
                    }
                } else {
                    if ($product->total_stock < $c['quantity']) {
                        $validator->errors()->add('stock', translate('One or more product stock is insufficient!'));
                    }
                }
            }
        });
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'errors' => Helpers::error_processor($validator)
        ], 403));
    }
}

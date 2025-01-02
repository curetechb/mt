<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerCancelList extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "date" => ["required"],
            "total_order" => ["required","integer"],
            "cancel" => ["required","integer"],
            "delivery" => ["required","integer"],
            "nextday" =>["required","integer"],
            "processing" => ["required","integer"]
        ];
    }
}

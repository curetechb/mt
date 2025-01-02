<?php

namespace App\Http\Requests\V3;

use Illuminate\Foundation\Http\FormRequest;

class StoreRiderRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => 'required',
            // 'avatar' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg,webp|max:512',
            // 'nid_image' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg,webp|max:512',
            // 'nid_image_backpart' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg,webp|max:512',
            // 'license_image' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg,webp|max:512',
            // 'license_image_backpart' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg,webp|max:512',
            'phone'         => ["required", "string" ,"min: 11", "max: 11"],
            'password' => ['required', 'min:8', 'max:20', 'string'],
            'area'   	=> 'required',
        ];
    }
}

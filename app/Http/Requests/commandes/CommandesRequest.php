<?php

namespace App\Http\Requests\commandes;

use Illuminate\Foundation\Http\FormRequest;

class CommandesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // dont' forget to set this as true
        return true;
    }

    public function rules(): array
    {
        // make all of the fields required, set featured image to accept only images
        return [
            'nom' => 'required|string|min:3|max:250',
            'prenom' => 'required|string|min:3|max:250',
            //'image' => 'required|image|max:1024|mimes:jpg,jpeg,png',
           // 'category_id' => 'exists:categories,id',
        ];
    }
}
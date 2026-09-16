<?php

namespace App\Http\Requests\Front;

use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
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
        return  [
            'sujet' => 'required|max:100',
            'message' => 'required|max:1000',
            'nom' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255',
        ];
    }
}
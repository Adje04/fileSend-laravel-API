<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class fileRequest extends FormRequest
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
           
                'file' => 'required|file|max:10240|mimes:jpg,jpeg,png,pdf,docx,xlsx', 
                'group_id' => 'exists:groups,id',
          
        ];
    }

    public function messages()
    {
        return [
          
            'file.max' => 'la taille du fichier ne doit pas dépasser 10MB.',
            'file.mimes' => 'Ce format de fichier n\'est pas pris en charge',
            'group_id.exists' => 'Ce groupe n\'existe pas.',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success'   => false,
            'message'   => 'Echec de validation.',
            'data'      => $validator->errors()
        ]));
    }
}



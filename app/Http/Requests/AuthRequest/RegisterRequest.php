<?php
namespace App\Http\Requests\AuthRequest;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegisterRequest extends FormRequest
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
            'name' =>'required|string|max:255|min:4',
            'email' =>'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'passwordConfirm' => 'required|same:password',
          
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'le nom est requis',
            'name.min' => 'le nom doit etre compris entre 4 et 255 caractères',
            'name.max' => 'le nom doit etre compris entre 4 et 255 caractères',
            'email.required' => 'l\'email est requis',
            'email.unique' => 'cet email a déja été utilisé',
            'password.required' => 'le mot de passe est requis',
            'passwordConfirm.save' => 'la confirmation du mot de passe n\'est pas conforme',
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


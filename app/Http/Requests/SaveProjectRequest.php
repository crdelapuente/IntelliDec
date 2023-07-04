<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveProjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // AQUI HAY QUE CONTROLAR SI LOS USUARIOS ESTÁN AUTORIZADOS
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required'],
            'description' => ['required'],
            'csv-file' => 'required|file|mimes:csv,txt',
            'columns' => 'required|array|min:4',
        ];
    }
}

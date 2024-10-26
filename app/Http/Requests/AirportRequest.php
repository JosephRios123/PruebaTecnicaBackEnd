<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AirportRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Cambia a false si deseas restringir el acceso
    }

    public function rules()
    {
        return [
            'code' => 'required|string'
        ];
    }

    public function messages()
    {
        return [
            'code.required' => 'El parámetro code es requerido.'
        ];
    }
}

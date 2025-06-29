<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMilestoneRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }


    // public function validationData()
    // {
    //     return $this->json();
    // }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'tid' => 'integer|required',
            'hold_duration' => 'integer',
            'needs_approval' => 'bool',
            'name' => 'string|required',
            'is_any' => 'boolean|required',
            'do_map' => 'boolean|required',
            'color' => 'string|required'
        ];
    }
}

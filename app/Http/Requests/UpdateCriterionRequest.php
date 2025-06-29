<?php

namespace App\Http\Requests;

use App\Enums\DType;
use App\Enums\Operator;
use App\Enums\CType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCriterionRequest extends FormRequest
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
            "operator" => [Rule::in(array_column(Operator::cases(), 'value')), "required"],
            "type" => [Rule::in(array_column(DType::cases(), 'value')), "required"],
            "constant_type" => [Rule::in(array_column(CType::cases(), 'value')), "required"],
            "constant" => "required",
            "name" => "string",
            "unit" => "string",
        ];
    }
}

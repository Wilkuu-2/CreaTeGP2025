<?php

namespace App\Http\Requests;

use App\Enums\DType;
use App\Enums\Operator;
use App\Enums\CType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StoreCriterionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // TODO: Admin priviledges
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
            "milestone_id" => "required|exists:milestones,id",
            "operator" => [Rule::in(array_column(Operator::cases(), 'value')), "required"],
            "type" => [Rule::in(array_column(DType::cases(), 'value')), "required"],
            "constant_type" => [Rule::in(array_column(CType::cases(), 'value')), "required"],
            "constant" => "required",
            "name" => "string|required",
            "unit" => "string|required",
        ];
    }
}

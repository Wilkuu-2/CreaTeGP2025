<?php

namespace App\Http\Requests;

use Clickbar\Magellan\Data\Geometries\Polygon;
use Clickbar\Magellan\Rules\GeometryGeojsonRule;
use Illuminate\Foundation\Http\FormRequest;

class GetFarmsteadLocationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * The data to be validated should be processed as JSON.
     * @return array
     */
    public function validationData()
    {
        return $this->json()->all();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'blatA' => 'required|numeric',
            'blonA' => 'required|numeric',
            'blatB' => 'required|numeric',
            'blonB' => 'required|numeric',
            'tid' => 'integer|exists:teams,id',
            'exclude' => new GeometryGeojsonRule([Polygon::class]),
        ];
    }
}

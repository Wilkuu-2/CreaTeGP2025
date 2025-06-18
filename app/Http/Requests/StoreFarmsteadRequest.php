<?php

namespace App\Http\Requests;

use Clickbar\Magellan\Data\Geometries\Point;
use Clickbar\Magellan\Http\Requests\TransformsGeojsonGeometry;
use Clickbar\Magellan\Rules\GeometryGeojsonRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreFarmsteadRequest extends FormRequest
{
    use TransformsGeojsonGeometry;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // TODO: Authorization for create_for
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
            'name' => "string|max:255|required",
            'email' => "email:rfc:spoof:dns|required",
            'phone_number' => "string|max:255",
            'show_email' => "boolean|required",
            'show_number' => "boolean|required",
            'location' => [new GeometryGeojsonRule([Point::class]), "required"],
            'create_for' => "exits:user,id"
        ];
    }

    public function geometries(): array
    {
        return ['location'];
    }
}

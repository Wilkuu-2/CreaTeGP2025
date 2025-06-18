<?php

namespace App\Http\Requests;

use App\Models\User;
use Clickbar\Magellan\Data\Geometries\Point;
use Clickbar\Magellan\Http\Requests\TransformsGeojsonGeometry;
use Clickbar\Magellan\Rules\GeometryGeojsonRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateFarmsteadRequest extends FormRequest
{
    use TransformsGeojsonGeometry;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /** @var User $user */
        $user = Auth::user();

        // TODO: Admin priviledges and checking the farmstead
        // return $user->farmstead_id == $this->route('farmstead')->id;
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
            'phone_number' => "string|max:255|required",
            'show_email' => "boolean|required",
            'show_number' => "boolean|required",
            'location' => [new GeometryGeojsonRule([Point::class]), "required"],
        ];
    }

    public function geometries(): array
    {
        return ['location'];
    }

}

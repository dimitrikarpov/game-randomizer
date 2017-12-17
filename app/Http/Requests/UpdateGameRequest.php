<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Platform;
use App\Game;


class UpdateGameRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $platformsIds = Platform::all()->pluck('id')->toArray();

        return [
            'name' => 'required',
            'platform_id' => ['required', Rule::in($platformsIds)]
        ];
    }
}
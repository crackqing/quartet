<?php

namespace App\Http\Requests\Game;

use Illuminate\Foundation\Http\FormRequest;

class AutoBindInviteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'bind_id'  => 'required|min:1',
            'agent_id'  => 'required|min:4',
            'agent_nickname'  => 'required|min:2',
        ];
    }
}

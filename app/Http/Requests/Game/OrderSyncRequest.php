<?php

namespace App\Http\Requests\Game;

use Illuminate\Foundation\Http\FormRequest;

class OrderSyncRequest extends FormRequest
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
            'price'  => 'required|min:2',
            'order_id'  => 'required|min:2',
            'agent_id'  => 'required|min:2',
            'pay_type'  => 'required',
            'coins' => 'required',
            'bank'  => 'required',
        ];
    }
}

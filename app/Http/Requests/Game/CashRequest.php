<?php

namespace App\Http\Requests\Game;

use Illuminate\Foundation\Http\FormRequest;

class CashRequest extends FormRequest
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
            // 'order_id'  => 'required|min:2',
            'agent_id'  => 'required|min:2',
            'agent_nickname'  => 'required',
            'cash_money'  => 'required',
            'coins' => 'required',
            'bank'  => 'required',
            'exchangeType'  => 'required',
            'realname'  => 'required',
            'account'   => 'required'
        ];
    }
}

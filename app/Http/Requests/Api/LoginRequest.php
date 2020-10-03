<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        ///false代表权限验证不通过，返回403错误 true 代表权限认证通过.
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        switch ($this->method()){
            case 'GET':
            case 'POST':
                {
                    if ($this->social_type == 'weixin'){
                        return [
                            'openid'    => 'required'
                        ];
                    }
                    return [
                        'email'  => 'required|min:2',
                        'password'  => 'required|min:2'
                    ];
                }
            case 'PUT':
            case 'PATCH':
            case 'DELETE':
            default :
                {
                    return [

                    ];
                }
        }
    }

    /**
     * 返回对应的错误消息验证规则
     * @return array
     */
    public function messages()
    {
        return [
            'email.required'    => '用户名必须填写',
            'email.min' => '用户不能小于2位',

            'password.required'  => '密码必须填写',
            'password.min'  => '密码不能小于2位'
        ];
    }
}

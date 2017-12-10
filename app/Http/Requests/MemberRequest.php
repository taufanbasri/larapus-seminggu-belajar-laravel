<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MemberRequest extends FormRequest
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
      switch ($this->method()) {
        case 'POST':
            return [
              'name' => 'required|max:255',
              'email' => 'required|email|max:255|unique:users'
            ];
            break;

        case 'PUT':
            return [
              'name' => 'required|max:255',
              'email' => 'required|unique:users,email,' . $this->route('member')
            ];
            break;

        default:
            return [];
            break;
      }
    }
}

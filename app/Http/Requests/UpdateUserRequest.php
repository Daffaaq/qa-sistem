<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        $userId = $this->route('user');  // Get user ID from route if available

        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:user_doc,email,' . $userId,  // Allow the current email for updating
            'role' => 'required|in:superadmin,user',
            'password' => 'nullable|string|min:8|confirmed',  // Make password optional during update
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Name is required',
            'email.required' => 'Email is required',
            'email.unique' => 'Email is already in use by another user',
            'role.required' => 'Role is required',
            'role.in' => 'The selected role is invalid. Please choose either Superadmin or User.',
            'password.min' => 'Password must be at least 8 characters',
            'password.confirmed' => 'Password confirmation does not match',
        ];
    }
}

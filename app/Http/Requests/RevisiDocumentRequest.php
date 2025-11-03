<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RevisiDocumentRequest extends FormRequest
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
            'file_document' => 'required|file|mimetypes:application/pdf|max:102400', // max 2MB
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'file_document.required' => 'File dokumen harus diunggah.',
            'file_document.file' => 'File dokumen harus berupa file.',
            'file_document.mimetypes' => 'Format file dokumen harus PDF.',
            'file_document.max' => 'Ukuran file dokumen tidak boleh lebih dari 100MB.',
        ];
    }
}

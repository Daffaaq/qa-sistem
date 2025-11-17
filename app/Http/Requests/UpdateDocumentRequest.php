<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDocumentRequest extends FormRequest
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
        return [
            'title_document' => 'required|string|max:255',
            'file_document' => 'nullable|file|mimetypes:application/pdf|max:102400', // max 2MB
            'keterangan' => 'nullable|string',
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
            'title_document.required' => 'Judul dokumen harus diisi.',
            'title_document.string' => 'Judul dokumen harus berupa teks.',
            'title_document.max' => 'Judul dokumen tidak boleh lebih dari 255 karakter.',

            'file_document.required' => 'File dokumen harus diunggah.',
            'file_document.file' => 'File dokumen harus berupa file.',
            'file_document.mimetypes' => 'Format file dokumen harus PDF.',
            'file_document.max' => 'Ukuran file dokumen tidak boleh lebih dari 100MB.',

            'keterangan.string' => 'Keterangan harus berupa teks.',
        ];
    }
}

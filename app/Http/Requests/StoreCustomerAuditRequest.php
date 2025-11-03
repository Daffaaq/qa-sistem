<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerAuditRequest extends FormRequest
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
            'nama_event' => 'required|string|max:255',
            'tanggal_mulai_event' => 'required|date',
            'tanggal_selesai_event' => 'nullable|date|after_or_equal:tanggal_mulai_event',
            'deskripsi_event' => 'required|string|max:1000',
            'file_evident' => 'nullable|file|mimes:pdf|max:2048',
        ];
    }

    public function messages()
    {
        return [
            // nama event
            'nama_event.required' => 'Nama event harus diisi.',
            'nama_event.string' => 'Nama event harus berupa teks.',
            'nama_event.max' => 'Nama event tidak boleh lebih dari 255 karakter.',

            // tanggal mulai event
            'tanggal_mulai_event.required' => 'Tanggal mulai event harus diisi.',
            'tanggal_mulai_event.date' => 'Tanggal mulai event harus berupa tanggal.',

            // tanggal selesai event
            'tanggal_selesai_event.date' => 'Tanggal selesai event harus berupa tanggal.',
            'tanggal_selesai_event.after_or_equal' => 'Tanggal selesai event harus setelah tanggal mulai event.',

            // deskripsi event
            'deskripsi_event.required' => 'Deskripsi event harus diisi.',
            'deskripsi_event.string' => 'Deskripsi event harus berupa teks.',
            'deskripsi_event.max' => 'Deskripsi event tidak boleh lebih dari 1000 karakter.',

            // file evident
            'file_evident.file' => 'File eviden event harus berupa file.',
            'file_evident.mimes' => 'Format file eviden event harus PDF.',
            'file_evident.max' => 'Ukuran file eviden event tidak boleh lebih dari 2MB.',
        ];
    }
}

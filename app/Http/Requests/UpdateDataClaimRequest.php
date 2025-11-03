<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDataClaimRequest extends FormRequest
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
            'tanggal_claim' => 'required|date',
            'customer'      => 'required|string|max:255',
            'part_no'       => 'required|string|max:255',
            'problem'       => 'required|string|max:255',
            'quantity'      => 'required|integer|min:1',
            'klasifikasi' => 'required|string|in:Function,Appearance,Dimension,Other',
            'kategori'      => 'required|string|in:Non Official,Official',
            'file_evident'  => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ];
    }

    public function messages()
    {
        return [
            'tanggal_claim.required' => 'Tanggal claim wajib diisi.',
            'tanggal_claim.date'     => 'Tanggal claim harus berupa tanggal yang valid.',

            'customer.required'      => 'Nama customer wajib diisi.',
            'customer.string'        => 'Nama customer harus berupa teks.',
            'customer.max'           => 'Nama customer maksimal 255 karakter.',

            'part_no.required'       => 'Part No wajib diisi.',
            'part_no.string'         => 'Part No harus berupa teks.',
            'part_no.max'            => 'Part No maksimal 255 karakter.',

            'problem.required'       => 'Problem wajib diisi.',
            'problem.string'         => 'Problem harus berupa teks.',
            'problem.max'            => 'Problem maksimal 255 karakter.',

            'quantity.required'      => 'Quantity wajib diisi.',
            'quantity.integer'       => 'Quantity harus berupa angka.',
            'quantity.min'           => 'Quantity minimal 1.',

            'klasifikasi.required'   => 'Klasifikasi wajib dipilih.',
            'klasifikasi.in'         => 'Klasifikasi harus salah satu dari: function, appearance, dimension, other.',

            'kategori.required'      => 'Kategori wajib dipilih.',
            'kategori.in'            => 'Kategori harus salah satu dari: Non Official, Official.',

            'file_evident.file'      => 'File harus berupa file yang valid.',
            'file_evident.mimes'     => 'File harus bertipe: jpg, jpeg, png, atau pdf.',
            'file_evident.max'       => 'Ukuran file maksimal 2MB.',
        ];
    }
}

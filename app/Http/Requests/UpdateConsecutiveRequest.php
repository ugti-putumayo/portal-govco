<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateConsecutiveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'subject' => 'required|string|max:1000',
            'recipient' => 'required|string|max:255',
            'document_type' => 'nullable|string|max:100',
            'internal_reference' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:2000',
            'attachment_url' => 'nullable|file|mimes:pdf,jpg,png,doc,docx|max:10240', 
        ];
    }
}
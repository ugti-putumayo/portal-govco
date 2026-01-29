<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class StoreConsecutiveRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'series_id' => 'required|integer|exists:series,id',
            'person_id' => 'nullable|integer|exists:persons,id',
            'subject' => 'required|string|max:1000',
            'recipient' => 'required|string|max:255',
            'document_type' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'attachment_url' => 'nullable|file|max:10240',
        ];
    }
}
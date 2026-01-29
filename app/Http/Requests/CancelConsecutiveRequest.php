<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class CancelConsecutiveRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'cancellation_reason' => 'required|string|min:10|max:1000',
        ];
    }
}
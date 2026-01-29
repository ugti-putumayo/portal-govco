<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSeriesRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'prefix' => 'required|string|max:50|unique:series,prefix',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'dependency_id' => 'required|exists:dependencies,id',
        ];
    }
}
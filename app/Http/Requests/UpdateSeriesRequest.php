<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSeriesRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $seriesId = $this->route('series')->id;

        return [
            'name' => 'required|string|max:255',
            'prefix' => [
                'required',
                'string',
                'max:50',
                Rule::unique('series')->ignore($seriesId),
            ],
            'dependency_id' => 'required|exists:dependencies,id',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ];
    }
}
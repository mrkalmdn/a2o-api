<?php

namespace App\Http\Requests;

use App\Enums\Report;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReportRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'start' => [
                'required',
                'date'
            ],
            'end' => [
                'required',
                'date',
                'after_or_equal:start'
            ],
            'type' => [
                'required',
                Rule::in(Report::values())
            ],
            'markets' => ['required', 'array', 'min:1'],
            'markets.*' => ['required', 'integer', Rule::exists('markets', 'id')],
        ];
    }
}

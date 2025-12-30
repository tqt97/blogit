<?php

namespace Modules\Categories\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
            'is_active' => 'nullable|boolean|default:true',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'parent_id' => $this->parent_id === 'root' ? null : $this->parent_id,
        ]);
    }
}

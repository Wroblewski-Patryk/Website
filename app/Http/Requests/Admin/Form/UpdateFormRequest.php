<?php

namespace App\Http\Requests\Admin\Form;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFormRequest extends StoreFormRequest
{
    public function rules(): array
    {
        return [
            ...parent::rules(),
            'optimistic_lock' => 'nullable|date',
        ];
    }
}

<?php

namespace App\Http\Requests\Admin\Template;

class UpdateTemplateRequest extends StoreTemplateRequest
{
    public function rules(): array
    {
        return [
            ...parent::rules(),
            'optimistic_lock' => 'nullable|date',
        ];
    }
}

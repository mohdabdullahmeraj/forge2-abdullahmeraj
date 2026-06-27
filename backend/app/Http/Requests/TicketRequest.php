<?php

namespace App\Http\Requests;

use App\Enums\Priority;
use App\Enums\TicketStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isUpdate = $this->isMethod('patch') || $this->isMethod('put');

        return [
            'subject' => ($isUpdate ? 'sometimes|' : 'required|') . 'string|max:255',
            'description' => 'nullable|string',
            'status' => ($isUpdate ? 'sometimes|' : 'nullable|') . 'string|in:' . implode(',', array_column(TicketStatus::cases(), 'value')),
            'priority' => ($isUpdate ? 'sometimes|' : 'nullable|') . 'string|in:' . implode(',', array_column(Priority::cases(), 'value')),
            'assignee_id' => 'nullable|exists:users,id',
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'duration' => 'integer',
            'is_done' => 'boolean',
            'assignees' => 'array|max:4',
            'assignees.*' => 'string|max:255',
            'name' => 'required|string|max:255',
            'priority' => 'in:alacsony,normál,magas',
            'scheduled_day' => 'required|date',
        ];
    }

    public function messages(): array
    {
        return [
            'duration.integer' => 'Az időtartamnak számnak kell lennie.',
            'is_done.boolean' => 'A kész állapotnak igaznak vagy hamisnak kell lennie.',
            'assignees.array' => 'A megbízottak mező nem megfelelő.',
            'assignees.max' => 'Legfeljebb 4 megbízott adható meg.',
            'assignees.*.string' => 'Minden megbízott neve szöveg típusú kell legyen.',
            'name.required' => 'A megnevezés mező kitöltése kötelező.',
            'name.string' => 'A megnevezés mezőnek szövegnek kell lennie.',
            'priority.in' => 'A prioritás csak az alábbi lehet: alacsony, normál vagy magas.',
            'scheduled_day.required' => 'Az ütemezett nap megadása kötelező.',
            'scheduled_day.date' => 'Az ütemezett nap mezőnek dátumnak kell lennie.',
        ];
    }
}

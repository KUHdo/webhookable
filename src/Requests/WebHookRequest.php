<?php

namespace KUHdo\Webhookable\Requests;

use KUHdo\Webhookable\WebHook;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class WebHookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'event' => [
                'required',
                'max:255',
                Rule::in(WebHook::getPossibleEventsAttribute()),
            ],
            'url' => 'required|url',
        ];
    }
}

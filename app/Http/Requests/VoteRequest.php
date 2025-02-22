<?php

namespace App\Http\Requests;

use App\Models\Poll;
use App\Traits\HasVoting;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class VoteRequest extends FormRequest
{
    use HasVoting;

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
            'poll_id' => [
                'required',
                'exists:polls,id',
                function ($attribute, $value, $fail) {
                    $poll = Poll::find($value);
                    if ($poll && $poll->ends_at < now()) {
                        $fail('The poll has expired.');
                    }

                    $ip = $this->ip();
                    $userId = Auth::id();

                    if ($this->isVoted($value, $ip, $userId)) {
                        $fail('You have already voted for this poll.');
                    }
                }
            ],
            'option_id' => ['required', 'exists:poll_options,id']
        ];
    }
}

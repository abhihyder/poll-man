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

                    $deviceId = $this->cookie('device_id') ?? null;
                    $sessionId = session()->getId();
                    $fingerprint = $this->input('fingerprint');
                    $voterId = Auth::id();

                    if ($this->isVoted($value, $deviceId, $sessionId, $fingerprint, $voterId)) {
                        $fail('You have already voted for this poll.');
                    }

                    $this->merge([
                        'user_id' => $poll->user_id,
                        'voter_id' => $voterId,
                        'session_id' => $sessionId,
                        'fingerprint' => $fingerprint
                    ]);
                }
            ],
            'poll_option_id' => ['required', 'exists:poll_options,id']
        ];
    }
}

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Poll View') }}
        </h2>
    </x-slot>

    <div class="bg-gradient-to-br from-indigo-50 to-purple-50 p-6" x-data="pollComponent()" x-init="initEcho()">
        <div class="max-w-4xl mx-auto">
            <div class=" flex justify-center p-6">
                <div class="bg-white rounded-2xl shadow-xl p-8 max-w-2xl w-full">
                    <div class="flex justify-between items-center mb-8">
                        <a href="{{ route('home') }}"
                            class="flex items-center gap-2 text-gray-600 hover:text-gray-800 font-medium">
                            &larr; Back to Polls
                        </a>
                    </div>

                    <h1 class="text-3xl font-bold text-gray-900 mb-8" x-text="poll.question"></h1>

                    <div class="space-y-4">
                        <template x-for="option in poll.options" :key="option.id">
                            <div>
                                <template x-if="isVoted">
                                    <div class="rounded-xl bg-gray-50 p-4">
                                        <div class="flex justify-between mb-2">
                                            <span class="font-medium text-gray-700" x-text="option.title"></span>
                                            <span class="font-semibold text-indigo-600" x-text="getPercentage(poll, option) + '%' "></span>
                                        </div>
                                        <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                                            <div class="h-full bg-gradient-to-r from-indigo-500 to-blue-500 rounded-full transition-all duration-500"
                                                :style="'width: ' + getPercentage(poll, option) + '%'">
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                <template x-if="!isVoted">
                                    <button @click="submitVote(option.id)"
                                        class="w-full p-4 rounded-xl border-2 border-gray-200 hover:border-indigo-500 hover:bg-indigo-50 transition-all duration-200 text-left">
                                        <span x-text="option.title"></span>
                                    </button>
                                </template>
                            </div>
                        </template>

                        <template x-if="isVoted">
                            <div class="space-y-3">
                                <div class="flex items-center gap-2 text-green-600 mt-4">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.707a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 10-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-sm">You've voted</span>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function pollComponent() {
            return {
                isVoted: @json($isVoted),
                poll: @json($poll),
                submitVote(optionId) {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                    fetch('{{ route("poll.vote") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            poll_id: this.poll.id,
                            option_id: optionId
                        })
                    }).then((response) => {
                        if (response.ok) {
                            this.poll.total_votes++;
                            this.poll.options.forEach(option => {
                                if (option.id === optionId) option.total_votes++;
                            });
                            this.isVoted = true;
                        }
                    });
                },
                getPercentage(poll, option) {
                    const total_votes = poll.total_votes;
                    return total_votes ? ((option.total_votes / total_votes) * 100).toFixed(1) : 0;
                },
                initEcho() {
                    if (window.Echo) {
                        window.Echo.channel('polls.' + this.poll.id)
                            .listen('VoteUpdated', (data) => {
                                this.poll = data;
                            });
                    }
                },
            }
        }
    </script>
</x-app-layout>
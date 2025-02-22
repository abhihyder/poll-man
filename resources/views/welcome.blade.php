<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Polls') }}
        </h2>
    </x-slot>

    <div class="min-h-screen bg-gradient-to-br from-indigo-50 to-purple-50 p-6" x-data="pollComponent()">
        <div class="max-w-4xl mx-auto">
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-4xl font-bold text-gray-900">Public Polls</h1>
            </div>

            <template x-if="view === 'public'">
                <div class="grid gap-6 md:grid-cols-2">
                    <template x-for="poll in polls" :key="poll.id">
                        <div class="bg-white rounded-2xl shadow-xl p-6 hover:shadow-2xl transition-all duration-300">
                            <h3 class="text-xl font-bold text-gray-900 mb-4" x-text="poll.question"></h3>

                            <template x-if="votedPolls.has(poll.id)">
                                <div class="space-y-3">
                                    <template x-for="option in poll.options" :key="option.id">
                                        <div class="space-y-2">
                                            <div class="flex justify-between text-sm">
                                                <span x-text="option.title"></span>
                                                <span class="font-medium text-indigo-600" x-text="getPercentage(poll, option) + '%' "></span>
                                            </div>
                                            <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                                                <div class="h-full bg-gradient-to-r from-indigo-500 to-purple-500 rounded-full transition-all duration-500"
                                                    :style="'width: ' + getPercentage(poll, option) + '%'">
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                    <div class="flex items-center gap-2 text-green-600 mt-4">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.707a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 10-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span class="text-sm">You've voted</span>
                                    </div>
                                </div>
                            </template>

                            <template x-if="!votedPolls.has(poll.id)">
                                <div>
                                    <p class="text-gray-500 mb-4" x-text="poll.totalVotes + ' votes so far'"></p>
                                    <button @click="viewPoll(poll)"
                                        class="w-full bg-gradient-to-r from-indigo-500 to-purple-500 text-white py-2 px-4 rounded-xl hover:from-indigo-600 hover:to-purple-600 transition-all duration-200 shadow-md hover:shadow-lg">
                                        Vote Now
                                    </button>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
            </template>

            <template x-if="view === 'vote'">
                <div class="min-h-screen flex items-center justify-center p-6">
                    <div class="bg-white rounded-2xl shadow-xl p-8 max-w-2xl w-full">
                        <div class="flex justify-between items-center mb-8">
                            <button @click="view = 'public'"
                                class="flex items-center gap-2 text-gray-600 hover:text-gray-800 font-medium">
                                &larr; Back to Polls
                            </button>
                        </div>

                        <h1 class="text-3xl font-bold text-gray-900 mb-8" x-text="activePoll.question"></h1>

                        <div class="space-y-4">
                            <template x-for="option in activePoll.options" :key="option.id">
                                <div>
                                    <template x-if="votedPolls.has(activePoll.id)">
                                        <div class="rounded-xl bg-gray-50 p-4">
                                            <div class="flex justify-between mb-2">
                                                <span class="font-medium text-gray-700" x-text="option.title"></span>
                                                <span class="font-semibold text-indigo-600" x-text="getPercentage(activePoll, option) + '%' "></span>
                                            </div>
                                            <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                                                <div class="h-full bg-gradient-to-r from-indigo-500 to-blue-500 rounded-full transition-all duration-500"
                                                    :style="'width: ' + getPercentage(activePoll, option) + '%'">
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                    <template x-if="!votedPolls.has(activePoll.id)">
                                        <button @click="submitVote(option.id)"
                                            class="w-full p-4 rounded-xl border-2 border-gray-200 hover:border-indigo-500 hover:bg-indigo-50 transition-all duration-200 text-left">
                                            <span x-text="option.title"></span>
                                        </button>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <script>
        function pollComponent() {
            return {
                view: 'public',
                activePoll: null,
                votedPolls: new Set(),
                polls: @json($polls),
                viewPoll(poll) {
                    this.activePoll = poll;
                    this.view = 'vote';
                },
                submitVote(optionId) {
                    this.activePoll.options.forEach(option => {
                        if (option.id === optionId) option.votes++;
                    });
                    this.votedPolls.add(this.activePoll.id);
                    fetch('{{ route("poll.vote") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            id: optionId
                        })
                    });
                },
                getPercentage(poll, option) {
                    const totalVotes = poll.options.reduce((sum, opt) => sum + opt.votes, 0);
                    return totalVotes ? ((option.votes / totalVotes) * 100).toFixed(1) : 0;
                }
            }
        }
    </script>
</x-app-layout>
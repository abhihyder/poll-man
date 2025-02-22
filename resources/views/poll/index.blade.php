<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Polls') }}
        </h2>
    </x-slot>

    <div class="min-h-screen bg-gradient-to-br from-indigo-50 to-purple-50 p-6" x-data="pollComponent()" x-init="initEcho()">
        <div class="max-w-4xl mx-auto">
            <div class="grid gap-6 md:grid-cols-2 mt-6">
                <template x-for="poll in polls" :key="poll.id">
                    <div class="bg-white rounded-2xl shadow-xl p-6 hover:shadow-2xl transition-all duration-300">
                        <h3 class="text-xl font-bold text-gray-900 mb-4" x-text="poll.question"></h3>
                        <div>
                            <p class="text-gray-500 mb-4" x-text="poll.total_votes + ' votes so far'"></p>
                            <button @click="viewPoll(poll.uid)"
                                class="w-full bg-gradient-to-r from-indigo-500 to-purple-500 text-white py-2 px-4 rounded-xl hover:from-indigo-600 hover:to-purple-600 transition-all duration-200 shadow-md hover:shadow-lg">
                                Vote Now
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <script>
        function pollComponent() {
            return {
                polls: @json($polls),
                viewPoll(uid) {
                    window.location.href = "{{ route('poll.show', ':uid') }}".replace(':uid', uid);
                },
                initEcho() {
                    if (window.Echo) {
                        window.Echo.channel('polls')
                            .listen('VoteUpdated', (data) => {
                                this.polls = this.polls.map((poll) => {
                                    if (poll.id === data.id) {
                                        return data;
                                    }
                                    return poll;
                                });
                            });
                    }
                },
            }
        }
    </script>
</x-app-layout>
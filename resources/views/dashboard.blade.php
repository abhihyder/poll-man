<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="min-h-screen bg-gradient-to-br from-indigo-50 to-purple-50 p-6" x-data="pollApp()">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white rounded-2xl shadow-xl p-8 mx-2">
                <div class="flex justify-between items-center mb-8">
                    <h2 class="text-2xl font-bold text-gray-900">Your Polls</h2>
                    <button @click="isModalOpen = true" class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white py-2 px-6 rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 shadow-md hover:shadow-lg">
                        New Poll
                    </button>
                </div>

                <div class="space-y-6">
                    <template x-for="poll in polls" :key="poll.id">
                        <div class="border-2 border-gray-100 rounded-xl p-6 hover:border-gray-200 transition-all duration-200">
                            <div class="flex justify-between items-start mb-4">
                                <div class="inline-block">
                                    <div class="flex items-center gap-2">
                                        <h3 class="text-xl font-semibold text-gray-800" x-text="poll.question"></h3>

                                        <!-- Active/Expired Badge -->
                                        <span
                                            class="px-3 py-1 text-xs font-semibold rounded-full"
                                            :class="!poll.isExpired ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'">
                                            <span x-text="!poll.isExpired ? 'Active' : 'Expired'"></span>
                                        </span>
                                    </div>

                                    <p class="text-gray-500" x-text="poll.total_votes + ' votes so far'"></p>
                                </div>


                                <div class="flex gap-3">
                                    <a :href="'/poll/' + poll.uid" class="text-blue-600 hover:text-blue-700 transition-colors" title="View poll">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3v18h18" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 17V9m4 8V5m4 12v-6" />
                                        </svg>
                                    </a>
                                    <button @click="copyPollLink(poll.uid)" class="text-indigo-600 hover:text-indigo-700 transition-colors" title="Copy share link">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                            <circle cx="18" cy="5" r="3" />
                                            <circle cx="6" cy="12" r="3" />
                                            <circle cx="18" cy="19" r="3" />
                                            <path d="M8.59 13.51l6.83 3.98m0-10.98l-6.82 3.98" />
                                        </svg>
                                    </button>
                                    <button @click="deletePoll(poll.id)" class="text-red-600 hover:text-red-700 transition-colors" title="Delete poll">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 9v10a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V9m-9 4v5m4-5v5M4 6h16M10 6V4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2" />
                                        </svg>

                                    </button>
                                </div>
                            </div>
                            <div class="space-y-3">
                                <template x-for="option in poll.options" :key="option.id">
                                    <div class="flex items-center gap-3 text-gray-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3v18h18" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 17V9m4 8V5m4 12v-6" />
                                        </svg>
                                        <span x-text="option.title"></span>
                                        <span class="font-semibold" x-text="option.total_votes + ' votes'"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Modal -->
            <div x-show="isModalOpen" x-cloak class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">

                <div class="bg-white rounded-2xl shadow-xl p-8 w-full max-w-lg relative" @click.outside="isModalOpen = false">
                    <button @click="isModalOpen = false" class="absolute top-3 right-3 text-gray-500 hover:text-gray-700">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Create New Poll</h2>
                    <span class="text-red-400 mb-6" x-text="validationError"></span>
                    <div class="max-h-80 overflow-y-auto space-y-4 p-2 ">
                        <div class="mb-6">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Question</label>
                            <input type="text" x-model="newPoll.question" class="w-full p-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring focus:ring-blue-200 transition-all duration-200" placeholder="Enter your question here..." required>
                        </div>
                        <div class="mb-6">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Ends At</label>
                            <input type="datetime-local" x-model="newPoll.ends_at" class="w-full p-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring focus:ring-blue-200 transition-all duration-200" required>
                        </div>
                        <template x-for="(option, index) in newPoll.options" :key="index">
                            <div class="mb-4 flex items-center gap-2">
                                <div class="w-full">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Option <span x-text="index + 1"></span></label>
                                    <input type="text" x-model="newPoll.options[index]"
                                        class="w-full p-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring focus:ring-blue-200 transition-all duration-200"
                                        placeholder="Enter option text..." required>
                                </div>
                                <!-- Remove Button (Visible only if options are more than 2) -->
                                <button @click="removeOption(index)"
                                    class="bg-red-500 hover:bg-red-600 text-white rounded-full p-2 mt-6 transition-colors flex items-center justify-center"
                                    x-show="index > 1" style="display: inline-flex;">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </template>

                    </div>
                    <div class="flex gap-4 mt-6">
                        <button type="button" @click="addOption" class="flex items-center gap-2 text-blue-600 hover:text-blue-700 font-medium">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                <circle cx="12" cy="12" r="10" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v8m-4-4h8" />
                            </svg> Add Option
                        </button>
                        <button @click="createPoll" type="button" class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white py-2 px-6 rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 shadow-md hover:shadow-lg">
                            Create Poll
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script>
        function pollApp() {
            return {
                isModalOpen: false,
                polls: @json($polls),
                newPoll: {
                    question: "",
                    ends_at: "",
                    options: ["", ""]
                },
                validationError: "",
                addOption() {
                    this.newPoll.options.push("");
                },
                removeOption(index) {
                    if (this.newPoll.options.length > 1) {
                        this.newPoll.options.splice(index, 1);
                    }
                },
                createPoll() {
                    const newPollData = {
                        question: this.newPoll.question,
                        ends_at: this.newPoll.ends_at,
                        options: this.newPoll.options.filter(opt => opt.trim()).map((title) => title)
                    };
                    this.validationError = "";
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                    fetch('{{ route("poll.store") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify(newPollData)
                    }).then(async (response) => {
                        const data = await response.json();
                        if (response.ok) {
                            this.polls.unshift(data.poll);
                            this.newPoll = {
                                question: "",
                                ends_at: "",
                                options: ["", ""]
                            };
                            this.isModalOpen = false;
                        } else {
                            this.validationError = data.message || "Something went wrong!";
                        }
                    }).catch((error) => {
                        console.error('Error creating poll:', error);
                    });

                },
                deletePoll(pollId) {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    const url = '{{route("poll.delete", ":id")}}'.replace(':id', pollId);
                    fetch(url, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                    }).then(async (response) => {
                        const data = await response.json();
                        if (response.ok) {
                            this.polls = this.polls.filter(poll => poll.id !== pollId);
                        }
                    });
                    this.polls = this.polls.filter(poll => poll.id !== pollId);
                },
                copyPollLink(pollUid) {
                    const link = `${window.location.origin}/poll/${pollUid}`;
                    navigator.clipboard.writeText(link).then(() => {
                        window.dispatchEvent(new CustomEvent('toast', {
                            detail: 'Copied to clipboard!'
                        }));

                    });
                }
            };
        }
    </script>
</x-app-layout>
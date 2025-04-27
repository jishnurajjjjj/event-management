<div x-data="{ showModal: @entangle('showModal') }"
     x-show="showModal"
     x-transition
     @keydown.escape.window="showModal = false"
     class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50"
     style="display: none;">

    <div class="bg-white rounded-lg shadow-xl w-full max-w-md" @click.stop>
        <div class="p-6">
            <h3 class="text-lg font-semibold mb-4">{{ $eventId ? 'Edit Event' : 'Create New Event' }}</h3>
            <form wire:submit.prevent="save">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Title</label>
                        <input wire:model="title" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        @error('title') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Date</label>
                            <input wire:model="date" type="date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            @error('date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Time</label>
                            <input wire:model="time" type="time" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            @error('time') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Type</label>
                        <select wire:model="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            <option value="Meeting">Meeting</option>
                            <option value="Celebration">Celebration</option>
                            <option value="Seminar">Seminar</option>
                            <option value="Workshop">Workshop</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Assign To</label>
                        <select wire:model="event_for_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            <option value="">Select User</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                        @error('event_for_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Guidelines (Optional)</label>
                        <textarea wire:model="guidelines" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
                    </div>
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" @click="showModal = false" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                        Save Event
                    </button>
                </div>
            </form>
            
        </div>
    </div>
</div>

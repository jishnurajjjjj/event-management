<div>
<div class="space-y-6">
    <!-- Event Details Card -->
    <div class="bg-white overflow-hidden shadow-lg rounded-2xl mb-8">
        <div class="p-8 space-y-8">
            <!-- Event Header -->
            <div class="border-b pb-6">
                <h1 class="text-3xl font-bold text-gray-900">{{ $event->title }}</h1>
                <span class="inline-block mt-3 px-4 py-1 rounded-full text-sm font-semibold
                    @switch($event->type)
                        @case('Meeting') bg-blue-100 text-blue-800 @break
                        @case('Celebration') bg-green-100 text-green-800 @break
                        @case('Seminar') bg-purple-100 text-purple-800 @break
                        @case('Workshop') bg-yellow-100 text-yellow-800 @break
                        @default bg-gray-100 text-gray-800
                    @endswitch">
                    {{ $event->type }}
                </span>
            </div>

            <!-- Event Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Left -->
                <div class="space-y-4">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Date & Time</h3>
                        <p class="mt-1 text-lg text-gray-900">
                            {{ $event->date->format('l, F j, Y') }}
                            <br>
                            <span class="text-gray-600 text-base">{{ \Carbon\Carbon::parse($event->time)->format('g:i A') }}</span>
                        </p>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Created By</h3>
                        <p class="mt-1 text-lg text-gray-900">{{ $event->creator->name }}</p>
                    </div>
                </div>

                <!-- Right -->
                <div class="space-y-4">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Assigned To</h3>
                        <p class="mt-1 text-lg text-gray-900">{{ $event->assignedTo->name }}</p>
                    </div>
                </div>
            </div>

            <!-- Guidelines -->
            @if($event->guidelines)
                <div class="border-t pt-6">
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-2">Guidelines</h3>
                    <div class="p-5 bg-gray-50 rounded-lg">
                        <p class="text-gray-700 leading-relaxed whitespace-pre-line">{{ $event->guidelines }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Invitations Section -->
    <div class="bg-white overflow-hidden shadow-lg rounded-2xl">
        <div class="p-8 space-y-6">
            <div class="flex justify-between items-center">
                <h2 class="text-2xl font-bold text-gray-900">Invited Users</h2>
                @can('invite', $event)
                <button wire:click="$set('showInviteModal', true)" 
                        class="flex items-center gap-2 px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition">
                    + Invite User
                </button>
                @endcan
            </div>

            <div class="space-y-4">
                @forelse($event->invitations as $invitation)
                    <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:shadow transition">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center mr-4">
                                <span class="text-base font-bold text-gray-700">
                                    {{ strtoupper(substr($invitation->user->name, 0, 1)) }}
                                </span>
                            </div>
                            <div>
                                <p class="text-base font-semibold text-gray-900">{{ $invitation->user->name }}</p>
                                <p class="text-xs text-gray-500">{{ $invitation->user->email }}</p>
                            </div>
                        </div>
                        <span class="text-xs px-3 py-1 rounded-full font-semibold
                            @if($invitation->status === 'accepted') bg-green-100 text-green-800
                            @elseif($invitation->status === 'rejected') bg-red-100 text-red-800
                            @else bg-yellow-100 text-yellow-800 @endif">
                            {{ ucfirst($invitation->status) }}
                        </span>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-400 text-sm">
                        No users have been invited yet.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Invite Modal -->
    @if($showInviteModal)
        <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-40 p-4 z-50">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md">
                <div class="p-6 space-y-6">
                    <h3 class="text-xl font-bold text-gray-900">Invite a User</h3>

                    <div>
                        <select wire:model="selectedUser" 
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
                            <option value="">Select a user</option>
                            @foreach($availableUsers as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                        @error('selectedUser')
                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="flex justify-end gap-3">
                        <button wire:click="$set('showInviteModal', false)" 
                                class="px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-800 bg-gray-100 hover:bg-gray-200 rounded-lg">
                            Cancel
                        </button>
                        <button wire:click="inviteUser"
                                class="px-4 py-2 text-sm font-semibold bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                            Send Invitation
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="p-8 space-y-6">
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-900">Gallery</h2>

        <div>
            <input type="file" wire:model="photos" id="photo" class="hidden" multiple />
            <label for="photo" class="flex items-center gap-2 px-5 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-lg cursor-pointer transition">
                + Upload Photos
            </label>
        </div>
    </div>

    @error('photos.*')
        <div class="text-red-500 text-xs mt-2">{{ $message }}</div>
    @enderror

    @if ($photos)
        <div class="flex flex-wrap gap-4 mt-4">
            @foreach ($photos as $index => $photo)
                <div class="relative">
                    <img src="{{ $photo->temporaryUrl() }}" alt="Preview" class="w-32 h-32 object-cover rounded-lg border">
                    <button wire:click="removePhoto({{ $index }})"
                            class="absolute -top-2 -right-2 bg-red-500 hover:bg-red-600 text-white rounded-full p-1 text-xs">
                        ✕
                    </button>
                </div>
            @endforeach
        </div>

        <button wire:click="uploadPhotos" 
                class="mt-4 px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg">
            Upload All
        </button>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
    @forelse($event->images as $image)
    <div class="relative border border-gray-200 rounded-lg overflow-hidden shadow hover:shadow-md transition">
        <a href="{{ asset('storage/' . $image->image_path) }}" target="_blank">
            <img src="{{ asset('storage/' . $image->image_path) }}" alt="Event Image" class="w-full h-48 object-cover cursor-pointer">
        </a>

        <div class="p-4 flex justify-between items-center">
            <p class="text-sm text-gray-700 font-semibold">{{ $image->user->name }}</p>

            @if(auth()->id() === $image->user_id)
                <button wire:click="confirmDeleteImage({{ $image->id }})"
                    class="text-red-600 hover:text-red-800 text-sm font-bold">
                    🗑️ Delete
                </button>
            @endif

            @if($image->trashed())
                <span class="text-red-500 text-xs">Deleted</span>
            @endif
        </div>
    </div>
@empty
    <div class="col-span-full text-center text-gray-400 text-sm py-8">
        No images uploaded yet.
    </div>
@endforelse

    </div>
</div>
@if($confirmingImageDelete)
    <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-40 p-4 z-50">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Confirm Deletion</h3>
            <p class="text-gray-700 mb-6">Are you sure you want to delete this image? This action cannot be undone.</p>

            <div class="flex justify-end gap-4">
                <button wire:click="$set('confirmingImageDelete', false)"
                    class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg text-sm">
                    Cancel
                </button>
                <button wire:click="deleteImage"
                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm">
                    Yes, Delete
                </button>
            </div>
        </div>
    </div>
@endif

@livewire('events.event-requisition', ['event' => $event])
</div>

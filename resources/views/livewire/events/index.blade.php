<div>
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold">Events</h3>
        <button wire:click="openCreateModal" 
                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
            Create Event
        </button>
    </div>
  
    @livewire('events.create')
    
    <div class="space-y-6">
        @forelse($events as $event)
            <div class="bg-white rounded-xl shadow-md overflow-visible hover:shadow-lg transition-shadow duration-300 border-l-4 
                @switch($event->type)
                    @case('Meeting') border-blue-500 @break
                    @case('Celebration') border-green-500 @break
                    @case('Seminar') border-purple-500 @break
                    @case('Workshop') border-yellow-500 @break
                    @default border-gray-500
                @endswitch">
                
                <div class="p-6">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <div class="flex items-center space-x-2 mb-2">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold 
                                    @switch($event->type)
                                        @case('Meeting') bg-blue-100 text-blue-800 @break
                                        @case('Celebration') bg-green-100 text-green-800 @break
                                        @case('Seminar') bg-purple-100 text-purple-800 @break
                                        @case('Workshop') bg-yellow-100 text-yellow-800 @break
                                        @default bg-gray-100 text-gray-800
                                    @endswitch " >
                                    {{ $event->type }}
                                </span>
                                <span class="text-sm text-gray-500">
                                    {{ \Carbon\Carbon::parse($event->date)->format('M d, Y') }} • 
                                    {{ \Carbon\Carbon::parse($event->time)->format('h:i A') }}
                                </span>
                            </div>
                            
                            <h4 class="text-xl font-bold text-gray-800 mb-1">{{ $event->title }}</h4>
                            
                            <div class="flex items-center space-x-4 text-sm text-gray-600 mt-3">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    {{ $event->creator->name }}
                                </div>
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                    </svg>
                                    {{ $event->assignedTo->name }}
                                </div>
                            </div>
                        </div>
                     
                        <!-- Enhanced Dropdown Menu -->
                        <div class="relative z-20" x-data="{ open: false }">
                         @can('view', $event)
                            <button @click="open = !open" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z"></path>
                                </svg>
                            </button>
                            @endcan
                            <div x-show="open" 
                                 @click.away="open = false"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-30">
                                <div class="py-1">
                                    <a href="{{ route('events.show', $event) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        View Details
                                    </a>
                                    @can('update', $event)
                                    <a href="#" wire:click.prevent="editEvent({{ $event->id }})" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        Edit
                                    </a>
                                    @endcan
                                    @can('delete', $event)
                                    <a href="#" wire:click.prevent="confirmDelete({{ $event->id }})" 
                                    class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        Delete
                                    </a>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    </div>
                  
                    @if($event->guidelines)
                        <div class="mt-4 p-3 bg-gray-50 rounded-lg border border-gray-200">
                            <p class="text-sm font-medium text-gray-700 mb-1">Guidelines:</p>
                            <p class="text-sm text-gray-600">{{ $event->guidelines }}</p>
                        </div>
                    @endif
                    @if($event->hasPendingInvitationForUser())
                        <div class="mt-4 flex justify-end space-x-2">
                            <button wire:click="confirmAccept({{ $event->id }})" 
                                    wire:loading.attr="disabled"
                                    class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm">
                              Accept
                              
                            </button>
                            <button wire:click="confirmReject({{ $event->id }})" 
                                    wire:loading.attr="disabled"
                                    class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm">
                                Decline
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center py-8">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="mt-2 text-lg font-medium text-gray-900">No events found</h3>
                <p class="mt-1 text-gray-500">Create your first event to get started</p>
            </div>
        @endforelse
    </div>

    <div x-data="{ open: @entangle('confirmingEventDeletion') }" 
     x-show="open"
     class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50"
     style="display: none;">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
        <div class="p-6">
            <h3 class="text-lg font-semibold mb-4">Delete Event</h3>
            <p class="mb-6">Are you sure you want to delete this event? This action can't be undone.</p>
            
            <div class="flex justify-end space-x-3">
                <button @click="open = false" 
                        class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
                    Cancel
                </button>
                <button wire:click="deleteEvent"
                        class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                    Delete Event
                </button>
            </div>
        </div>
    </div>
</div>

<div x-data="{ open: @entangle('confirmingInvitationAccept') }" 
     x-show="open"
     class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
        <div class="p-6">
            <h3 class="text-lg font-semibold mb-4">Confirm Acceptance</h3>
            <p class="mb-6">Are you sure you want to accept this event invitation?</p>
            
            <div class="flex justify-end space-x-3">
                <button @click="open = false" 
                        wire:click="cancelResponse"
                        class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
                    Cancel
                </button>
                <button wire:click="acceptInvitation"
                        wire:loading.attr="disabled"
                        class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                    <span wire:loading.remove>Accept</span>
                    <span wire:loading>Processing...</span>
                </button>
            </div>
        </div>
    </div>
</div>
<div x-data="{ open: @entangle('confirmingInvitationReject') }" 
     x-show="open"
     class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
        <div class="p-6">
            <h3 class="text-lg font-semibold mb-4">Confirm Rejection</h3>
            <p class="mb-6">Are you sure you want to decline this event invitation?</p>
            
            <div class="flex justify-end space-x-3">
                <button @click="open = false" 
                        wire:click="cancelResponse"
                        class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
                    Cancel
                </button>
                <button wire:click="rejectInvitation"
                        wire:loading.attr="disabled"
                        class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                    <span wire:loading.remove>Decline </span>
                    <span wire:loading>Processing...</span>
                </button>
            </div>
        </div>
    </div>
</div>
</div>
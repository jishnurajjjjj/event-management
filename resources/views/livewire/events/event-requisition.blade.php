<div>
   
    <div class="bg-white overflow-hidden shadow-lg rounded-2xl">
        <div class="p-8 space-y-6">
            <div class="flex justify-between items-center">
                <h2 class="text-2xl font-bold text-gray-900">Requisitions</h2>
           
                <button wire:click="$set('showRequisitionModal', true)"
                        class="flex items-center gap-2 px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition">
                    + Create Requisition
                </button>
              
            </div>

            <div class="space-y-4">
            @forelse($requisitions as $requisition)
    <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:shadow transition">
        <div class="flex items-center">
            <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center mr-4">
                {{-- You can put an icon here if you want --}}
            </div>

            <div class="flex items-center">
                <p class="text-base font-semibold text-gray-900 mr-2">{{ $requisition->details }}</p>

                <span class="text-xs px-3 py-1 rounded-full font-semibold
                    @if($requisition->is_gift) bg-green-100 text-green-800 @else bg-yellow-100 text-yellow-800 @endif">
                    {{ $requisition->is_gift ? 'Gift' : 'Normal' }}
                </span>
            </div>
        </div>

        <div class="flex items-center gap-2">
            @if($requisition->claimed_by)
                <div class="text-right">
                    <p class="text-xs text-gray-500">Claimed by: {{ $requisition->claimedBy->name }}</p>

                    @if($requisition->is_gift && $requisition->claimed_by == auth()->id())
                        <button wire:click="confirmUnclaim({{ $requisition->id }})" class="px-4 py-2 bg-red-500 text-white rounded-md">
                            Unclaim
                        </button>
                    @endif
                </div>
            @else
                @if($event->date && now()->greaterThanOrEqualTo(\Carbon\Carbon::parse($event->date)->addDay()))
                    <span class="text-xs text-red-500">Event has ended</span>
                @else
                    <div class="flex gap-2">
                        <button wire:click="confirmClaim({{ $requisition->id }})" class="px-4 py-2 bg-green-500 text-white rounded-md">
                            Claim
                        </button>
                        @can('update', $event)
                        <button wire:click="openEditModal({{ $requisition->id }})" class="px-4 py-2 bg-yellow-400 hover:bg-yellow-500 text-white rounded-md">
                            Edit
                        </button>
                        @endcan
                    </div>
                @endif
            @endif
        </div>
    </div>
@empty
    <p>No requisitions available.</p>
@endforelse

            </div>
        </div>
    </div>

    <!-- Requisition Modal -->
    @if($showRequisitionModal)
        <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-40 p-4 z-50">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md">
                <div class="p-6 space-y-6">
                    <h3 class="text-xl font-bold text-gray-900">Create Requisition</h3>

                    <div>
                        <label for="details" class="text-sm font-semibold text-gray-500">Requisition Details</label>
                        <input wire:model="details" type="text" id="details" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-400 focus:outline-none" placeholder="Enter details">
                        @error('details') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex items-center">
                        <input wire:model="isGift" type="checkbox" id="isGift" class="mr-2" />
                        <label for="isGift" class="text-sm text-gray-700">Is this a gift?</label>
                    </div>

                    <div class="flex justify-end gap-3">
                        <button wire:click="closeModal"
                                class="px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-800 bg-gray-100 hover:bg-gray-200 rounded-lg">
                            Cancel
                        </button>
                        <button wire:click="createRequisition"
                                class="px-4 py-2 text-sm font-semibold bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                            Create Requisition
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
    @if($confirmingClaim)
    <div class="fixed inset-0 bg-gray-500 bg-opacity-50 flex justify-center items-center z-50">
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-lg font-semibold">Are you sure you want to claim this requisition?</h3>
            <div class="mt-4">
                <button wire:click="claimRequisition" class="px-4 py-2 bg-blue-500 text-white rounded-md mr-2">Confirm</button>
                <button wire:click="cancelClaim" class="px-4 py-2 bg-gray-500 text-white rounded-md">Cancel</button>
            </div>
        </div>
    </div>
@endif

@if($confirmingUnclaim)
    <div class="fixed inset-0 bg-gray-500 bg-opacity-50 flex justify-center items-center z-50">
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-lg font-semibold">Are you sure you want to unclaim this requisition?</h3>
            <div class="mt-4">
                <button wire:click="unclaimRequisition" class="px-4 py-2 bg-blue-500 text-white rounded-md mr-2">Confirm</button>
                <button wire:click="cancelUnclaim" class="px-4 py-2 bg-gray-500 text-white rounded-md">Cancel</button>
            </div>
        </div>
    </div>
@endif

@if($showEditModal)
    <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-40 p-4 z-50">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md">
            <div class="p-6 space-y-6">
                <h3 class="text-xl font-bold text-gray-900">Edit Requisition</h3>

                <div>
                    <label for="editingDetails" class="text-sm font-semibold text-gray-500">Requisition Details</label>
                    <input wire:model="editingDetails" type="text" id="editingDetails" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-400 focus:outline-none" placeholder="Enter updated details">
                    @error('editingDetails') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="flex items-center">
                    <input wire:model="editingIsGift" type="checkbox" id="editingIsGift" class="mr-2" />
                    <label for="editingIsGift" class="text-sm text-gray-700">Is this a gift?</label>
                </div>

                <div class="flex justify-end gap-3">
                    <button wire:click="closeEditModal"
                            class="px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-800 bg-gray-100 hover:bg-gray-200 rounded-lg">
                        Cancel
                    </button>
                    <button wire:click="updateRequisition"
                            class="px-4 py-2 text-sm font-semibold bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                        Update Requisition
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif


</div>

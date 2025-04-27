<?php

namespace App\Livewire\Events;

use Livewire\Component;
use App\Models\Event;
use App\Models\Requisition;

class EventRequisition extends Component
{
    public $event;
    public $details;
    public $isGift = false;
    public $showRequisitionModal = false;
    public $requisitions;
    public $confirmingClaim = false; 
    public $claimingRequisitionId = null; 
    public $confirmingUnclaim = false; 
    public $unclaimingRequisitionId = null; 

    public $editingRequisitionId;
    public $editingDetails;
    public $editingIsGift;
    public $showEditModal = false;

    protected $rules = [
        'details' => 'required|string|max:255',
        'isGift' => 'boolean', 
    ];

    protected $listeners = ['refreshRequisitions' => 'loadRequisitions'];

    public function mount(Event $event)
    {
        $this->event = $event;
        $this->loadRequisitions();  
    }

    public function createRequisition()
    {
        $this->validate();

        try {
           
            Requisition::create([
                'event_id' => $this->event->id,
                'details' => $this->details,
                'is_gift' => $this->isGift,
                'created_by' => auth()->id(),
            ]);

        
            $this->reset(['details', 'isGift']);
            $this->showRequisitionModal = false;

          
            $this->dispatch('refreshRequisitions');
            $this->dispatch('notify', 
                type: 'success', 
                message: 'Requisition item added successfully!'
            );

        } catch (\Exception $e) {
          
            $this->dispatch('notify', 
                type: 'error', 
                message: $e->getMessage()
            );
        }
    }

    public function closeModal()
    {
        $this->reset(['details', 'isGift']);
        $this->showRequisitionModal = false;
    }

 
    public function loadRequisitions()
    {
       
        $this->requisitions = $this->event->requisitions()->with('claimedBy')->get();
    }

    public function render()
    {
       
        return view('livewire.events.event-requisition', [
            'requisitions' => $this->requisitions,
        ]);
    }

    public function confirmClaim($requisitionId)
    {
        $this->claimingRequisitionId = $requisitionId;
        $this->confirmingClaim = true;
    }

  
    public function claimRequisition()
    {
        $requisition = Requisition::find($this->claimingRequisitionId);

        if (!$requisition) {
            $this->dispatch('notify', type: 'error', message: 'Requisition not found.');
            return;
        }

        if ($requisition->claimed_by) {
            $this->dispatch('notify', type: 'error', message: 'This requisition has already been claimed.');
            return;
        }

       
        $requisition->update([
            'claimed_by' => auth()->id(),
        ]);

        $this->dispatch('notify', type: 'success', message: 'Requisition claimed successfully!');
        
        $this->confirmingClaim = false; 
        $this->loadRequisitions(); 
    }

  
    public function cancelClaim()
    {
        $this->confirmingClaim = false; 
    }
    public function confirmUnclaim($requisitionId)
    {
        $this->unclaimingRequisitionId = $requisitionId;
        $this->confirmingUnclaim = true; 
    }
    
    public function unclaimRequisition()
    {
        $requisition = Requisition::find($this->unclaimingRequisitionId);
    
        if (!$requisition) {
            $this->dispatch('notify', type: 'error', message: 'Requisition not found.');
            return;
        }
    
      
        if ($requisition->is_gift && $requisition->claimed_by == auth()->id()) {
          
            $requisition->update([
                'claimed_by' => null,  
            ]);
    
            $this->dispatch('notify', type: 'success', message: 'Requisition unclaimed successfully!');
        } else {
          
            $this->dispatch('notify', type: 'error', message: 'You can only unclaim a gift you have already claimed.');
        }
    
        $this->confirmingUnclaim = false; 
        $this->loadRequisitions(); 
    }
    
    public function cancelUnclaim()
    {
        $this->confirmingUnclaim = false;  
    }

    
public function startEdit($requisitionId)
{
    $requisition = Requisition::find($requisitionId);

    if (!$requisition) {
        $this->dispatch('notify', type: 'error', message: 'Requisition not found.');
        return;
    }

    if ($requisition->claimed_by) {
        $this->dispatch('notify', type: 'error', message: 'Cannot edit a claimed requisition.');
        return;
    }

    $this->editingRequisitionId = $requisition->id;
    $this->editingDetails = $requisition->details;
    $this->editingIsGift = $requisition->is_gift;
    $this->showEditModal = true;
}

public function updateRequisition()
{
    $this->validate([
        'editingDetails' => 'required|string|max:255',
        'editingIsGift' => 'boolean',
    ]);

    $requisition = Requisition::find($this->editingRequisitionId);

    if (!$requisition) {
        $this->dispatch('notify', type: 'error', message: 'Requisition not found.');
        return;
    }

    if ($requisition->claimed_by) {
        $this->dispatch('notify', type: 'error', message: 'Cannot edit a claimed requisition.');
        return;
    }

    $requisition->update([
        'details' => $this->editingDetails,
        'is_gift' => $this->editingIsGift,
    ]);

    $this->showEditModal = false;
    $this->dispatch('notify', type: 'success', message: 'Requisition updated successfully.');
    $this->loadRequisitions();
}
public function openEditModal($requisitionId)
{
    $requisition = Requisition::find($requisitionId);

    if (!$requisition || $requisition->claimed_by) {
        $this->dispatch('notify', type: 'error', message: 'Cannot edit a claimed requisition.');
        return;
    }

    $this->editingRequisitionId = $requisition->id;
    $this->editingDetails = $requisition->details;
    $this->editingIsGift = (bool) $requisition->is_gift;
    $this->showEditModal = true;
}
public function closeEditModal()
{
    $this->showEditModal = false;
}
}

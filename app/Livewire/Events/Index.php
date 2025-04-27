<?php

namespace App\Livewire\Events;

use Livewire\Component;
use App\Models\Event;
use App\Models\EventInvitation;
use Illuminate\Support\Facades\Gate;


class Index extends Component
{
    public $events;
    public $confirmingEventDeletion = null;
    public $confirmingInvitationAccept = null;
    public $confirmingInvitationReject = null;
    
   protected $listeners = [
        'refreshEvents' => 'refreshEvents',
        'eventCreated' => 'refreshEvents',
         'invitationResponded' => 'refreshEvents'
    ];

    public function mount()
    {
        $this->refreshEvents();
    }

    public function refreshEvents()
    {
        $this->events = Event::with(['creator', 'assignedTo', 'invitations' => function($query) {
            $query->where('user_id', auth()->id());
        }])
        ->latest()
        ->get();
    }

    public function render()
    {
        return view('livewire.events.index');
    }

 public function openCreateModal()
{
    $this->dispatch('openCreateModal')->to('events.create');
}

public function editEvent($eventId)
{
    $event = Event::findOrFail($eventId);

    if (Gate::denies('update', $event)) {
        $this->dispatch('notify',
            type: 'error',
            message: 'You are not authorized to edit this event'
        );
        return;
    }

    $this->dispatch('editEvent', eventId: $event->id)->to('events.create');
}

public function confirmDelete($eventId)
{
    $this->confirmingEventDeletion = $eventId;
}

public function deleteEvent()
{
    $event = Event::findOrFail($this->confirmingEventDeletion);
    
    if (Gate::denies('delete', $event)) {
        $this->dispatch('notify',
            type: 'error',
            message: 'You are not authorized to delete this event'
        );
        return;
    }

    $event->delete();
    
    $this->confirmingEventDeletion = null;
    $this->dispatch('notify',
        type: 'success',
        message: 'Event deleted successfully'
    );
    $this->dispatch('refreshEvents');
}
public function confirmAccept($eventId)
{
    
    $invitation = EventInvitation::where('event_id', $eventId)
                               ->where('user_id', auth()->id())
                               ->first();
    
    if (!$invitation) {
        $this->dispatch('notify', [
            'type' => 'error',
            'message' => 'No pending invitation found'
        ]);
        return;
    }

    $this->confirmingInvitationAccept = $eventId;
}

public function confirmReject($eventId)
{

    $invitation = EventInvitation::where('event_id', $eventId)
                               ->where('user_id', auth()->id())
                               ->first();
    
    if (!$invitation) {
        $this->dispatch('notify', [
            'type' => 'error',
            'message' => 'No pending invitation found'
        ]);
        return;
    }

    $this->confirmingInvitationReject = $eventId;
}

public function acceptInvitation()
{
    try {
        $invitation = EventInvitation::where('event_id', $this->confirmingInvitationAccept)
                                   ->where('user_id', auth()->id())
                                   ->firstOrFail();

        $invitation->update(['status' => 'accepted']);
        
        $this->reset(['confirmingInvitationAccept']);
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Invitation accepted successfully'
        ]);
        
        $this->refreshEvents();
        
    } catch (\Exception $e) {
        $this->dispatch('notify', [
            'type' => 'error',
            'message' => 'Failed to accept invitation'
        ]);
    }
}

public function rejectInvitation()
{
    try {
        $invitation = EventInvitation::where('event_id', $this->confirmingInvitationReject)
                                   ->where('user_id', auth()->id())
                                   ->firstOrFail();

        $invitation->update(['status' => 'rejected']);
        
        $this->reset(['confirmingInvitationReject']);
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Invitation declined'
        ]);
        
        $this->refreshEvents();
        
    } catch (\Exception $e) {
        $this->dispatch('notify', [
            'type' => 'error',
            'message' => 'Failed to decline invitation'
        ]);
    }
}
public function cancelResponse()
{
    $this->reset(['confirmingInvitationAccept', 'confirmingInvitationReject']);
}
}

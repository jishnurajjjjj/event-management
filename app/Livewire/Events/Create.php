<?php

namespace App\Livewire\Events;

use Livewire\Component;
use App\Models\Event;
use App\Models\User;

use Illuminate\Support\Facades\Gate;

class Create extends Component
{

    
    public $users;
    public $showModal = false;
    public $title;
    public $date;
    public $time;
    public $type = 'Meeting';
    public $guidelines;
    public $event_for_id;
    public $eventId = null; 
    

    protected $rules = [
        'title' => 'required|string|max:255',
        'date' => 'required|date|after_or_equal:today',
        'time' => 'required',
        'type' => 'required|string|in:Meeting,Celebration,Seminar,Workshop,Other',
        'guidelines' => 'nullable|string|max:1000',
        'event_for_id' => 'required|exists:users,id'
    ];

    protected $messages = [
        'date.after_or_equal' => 'The event date must be today or in the future.',
        'event_for_id.required' => 'Please select a user to assign the event to.'
    ];
    protected $listeners = ['openCreateModal' => 'openModal',
    'editEvent' => 'loadForEdit',];

    public function mount()
    {
       
        $this->date = now()->format('Y-m-d');
        $this->time = now()->format('H:i');
        $this->users = User::get();
    }

    public function openModal()
    {
        $this->showModal = true;
        $this->eventId = null; 
        $this->users = User::get(); 
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->reset([
            'title',
            'date',
            'time',
            'type',
            'guidelines',
            'event_for_id'
        ]);
        $this->resetErrorBag();
        $this->date = now()->format('Y-m-d');
        $this->time = now()->format('H:i');
    }

    public function save()
    {
        $validated = $this->validate();
    
        try {
            if ($this->eventId) {
                $event = Event::findOrFail($this->eventId);
                
                if (Gate::denies('update', $event)) {
                    throw new \Exception('You are not authorized to edit this event');
                }
                
                $event->update($validated);
                $message = 'Event updated successfully';
            } else {
                Event::create($validated + ['user_id' => auth()->id()]);
                $message = 'Event created successfully';
            }
    
            $this->closeModal();
            $this->dispatch('refreshEvents');
            $this->dispatch('notify', 
                type: 'success', 
                message: $message
            );
    
        } catch (\Exception $e) {
            $this->dispatch('notify',
                type: 'error',
                message: $e->getMessage()
            );
        }
    }

    public function render()
    {
        return view('livewire.events.create');
    }

    public function loadForEdit($eventId)
    {
        $event = Event::find($eventId);
        
        $this->eventId = $event->id;
        $this->title = $event->title;
        $this->date = \Carbon\Carbon::parse($event->date)->format('Y-m-d');
        $this->time = $event->time;
        $this->type = $event->type;
        $this->guidelines = $event->guidelines;
        $this->event_for_id = $event->event_for_id;
        $this->showModal = true;
    }
}
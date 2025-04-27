<?php

namespace App\Livewire\Events;

use Livewire\Component;
use App\Models\Event;
use App\Models\User;
use App\Models\EventImage;
use Livewire\WithFileUploads;


class ShowEvent extends Component
{

    use WithFileUploads;
    public Event $event;
    public $showInviteModal = false;
    public $selectedUser;
    public $availableUsers;
    public $photos = [];
    public $confirmingImageDelete = false;
    public $imageToDeleteId;


    protected $rules = [
        'photos.*' => 'image|max:2048', 
    ];

    public function mount(Event $event) {
        $this->event = $event->load('invitations.user');
        $this->loadAvailableUsers();
    }

    public function loadAvailableUsers() {
        $excludedUserIds = $this->event->invitations->pluck('user_id')
        ->push($this->event->user_id)
        ->push($this->event->event_for_id)
        ->push(auth()->id())
        ->unique();
    
    $this->availableUsers = User::whereNotIn('id', $excludedUserIds)->get();
    }

    public function inviteUser() {
        $this->validate(['selectedUser' => 'required|exists:users,id']);
        
        $this->event->invitations()->create([
            'user_id' => $this->selectedUser,
            'status' => 'pending'
        ]);
        
        $this->reset(['showInviteModal', 'selectedUser']);
        $this->event->refresh();
        $this->loadAvailableUsers();
    }

    public function updatedPhotos()
    {
        $this->validateOnly('photos.*');
    }

    public function uploadPhotos()
    {
        $this->validate();

        foreach ($this->photos as $photo) {
            $path = $photo->store('event_images', 'public');

            $this->event->images()->create([
                'image_path' => $path,
                'user_id' => auth()->id(),
            ]);
        }

        $this->reset('photos');
        session()->flash('success', 'Photos uploaded successfully!');
    }

    public function removePhoto($index)
    {
        $photos = $this->photos;
        unset($photos[$index]);
        $this->photos = array_values($photos); 

        $this->resetValidation();
        $this->validateOnly('photos.*');
    }
    public function render()
    {
        return view('livewire.events.show-event')
            ->layout('layouts.app'); 
    }
    public function confirmDeleteImage($imageId)
    {
        $this->confirmingImageDelete = true;
        $this->imageToDeleteId = $imageId;
    }
    
    
    public function deleteImage()
    {
        
        $image = EventImage::where('id', $this->imageToDeleteId)
                    ->where('user_id', auth()->id()) 
                    ->firstOrFail();
    
      
        $image->delete();
    
        $this->event->refresh(); 
        $this->confirmingImageDelete = false;
        $this->imageToDeleteId = null;
    
        session()->flash('success', 'Image has been marked as deleted.');
    }

}

<?php

namespace App\Livewire\Admin;

use App\Models\ContactSubmission;
use Livewire\Component;
use Livewire\WithPagination;

class ContactSubmissions extends Component
{
    use WithPagination;

    public $selectedSubmission = null;
    public $adminNotes = '';
    public $filterStatus = 'all';

    protected $listeners = ['refreshSubmissions' => '$refresh'];

    public function mount()
    {
        //
    }

    public function viewSubmission($id)
    {
        $submission = ContactSubmission::find($id);
        if ($submission && $submission->status === 'new') {
            $submission->markAsRead();
        }
        $this->selectedSubmission = $submission;
        $this->adminNotes = $submission->admin_notes ?? '';
    }

    public function closeModal()
    {
        $this->selectedSubmission = null;
        $this->adminNotes = '';
    }

    public function markAsReplied()
    {
        if ($this->selectedSubmission) {
            $submission = ContactSubmission::find($this->selectedSubmission->id);
            $submission->markAsReplied();
            $submission->update(['admin_notes' => $this->adminNotes]);

            session()->flash('message', 'Submission marked as replied.');
            $this->closeModal();
        }
    }

    public function deleteSubmission($id)
    {
        ContactSubmission::find($id)->delete();
        session()->flash('message', 'Submission deleted successfully.');
    }

    public function render()
    {
        $query = ContactSubmission::query()->latest();

        if ($this->filterStatus !== 'all') {
            $query->where('status', $this->filterStatus);
        }

        $submissions = $query->paginate(10);

        return view('livewire.admin.contact-submissions', [
            'submissions' => $submissions,
        ])->layout('layouts.admin');
    }
}

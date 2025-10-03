<?php

namespace App\Livewire\Admin;

use App\Models\SalonSetting;
use Livewire\Component;
use Livewire\WithFileUploads;

class AboutUsManager extends Component
{
    use WithFileUploads;

    public $settings;
    public $about_us_title;
    public $about_us_content;
    public $about_us_mission;
    public $about_us_vision;
    public $show_team_on_about;
    public $about_us_image;
    public $new_image;

    public function mount()
    {
        $this->settings = SalonSetting::getDefault();
        $this->about_us_title = $this->settings->about_us_title ?? 'About Us';
        $this->about_us_content = $this->settings->about_us_content ?? '';
        $this->about_us_mission = $this->settings->about_us_mission ?? '';
        $this->about_us_vision = $this->settings->about_us_vision ?? '';
        $this->show_team_on_about = $this->settings->show_team_on_about ?? true;
        $this->about_us_image = $this->settings->about_us_image;
    }

    public function save()
    {
        $this->validate([
            'about_us_title' => 'required|string|max:255',
            'about_us_content' => 'nullable|string',
            'about_us_mission' => 'nullable|string',
            'about_us_vision' => 'nullable|string',
            'show_team_on_about' => 'boolean',
            'new_image' => 'nullable|image|max:2048',
        ]);

        $data = [
            'about_us_title' => $this->about_us_title,
            'about_us_content' => $this->about_us_content,
            'about_us_mission' => $this->about_us_mission,
            'about_us_vision' => $this->about_us_vision,
            'show_team_on_about' => $this->show_team_on_about,
        ];

        if ($this->new_image) {
            $path = $this->new_image->store('about-us', 'public');
            $data['about_us_image'] = $path;
            $this->about_us_image = $path;
        }

        $this->settings->update($data);

        session()->flash('message', 'About Us page updated successfully!');
    }

    public function render()
    {
        return view('livewire.admin.about-us-manager')->layout('layouts.admin');
    }
}

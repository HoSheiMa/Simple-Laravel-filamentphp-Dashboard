<?php

namespace App\Notifications;

use App\Models\Skill;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SkillCreatedNotification extends Notification
{
    use Queueable;

    public function __construct(public Skill $skill)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'skill_id' => $this->skill->id,
            'skill_name' => $this->skill->name,
            'category' => $this->skill->category->name,
            'message' => "New skill '{$this->skill->name}' has been created",
            'action_url' => route('filament.admin.resources.skills.view', $this->skill),
        ];
    }
}

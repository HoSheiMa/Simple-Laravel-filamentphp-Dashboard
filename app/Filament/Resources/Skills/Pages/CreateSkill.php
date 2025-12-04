<?php

namespace App\Filament\Resources\Skills\Pages;

use App\Filament\Resources\Skills\SkillResource;
use Filament\Resources\Pages\CreateRecord;
use App\Notifications\SkillCreatedNotification;
use Filament\Notifications\Notification;

class CreateSkill extends CreateRecord
{
    protected static string $resource = SkillResource::class;
      protected function afterCreate(): void
    {
        // Send flash notification
        Notification::make()
            ->title('Skill Created Successfully')
            ->body("'{$this->record->name}' has been added to the system")
            ->success()
            ->send();

        // Send database notification
        if ($user = auth()->user()) {
            $user->notify(new SkillCreatedNotification($this->record));
        }
    }
}

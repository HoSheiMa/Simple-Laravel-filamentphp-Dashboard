<?php

namespace App\Filament\Resources\Skills\Pages;

use App\Filament\Resources\Skills\SkillResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;
use App\Notifications\SkillUpdatedNotification;
use Filament\Actions;
use Filament\Notifications\Notification;
class EditSkill extends EditRecord
{
    protected static string $resource = SkillResource::class;
   protected function afterSave(): void
    {
        // Send flash notification
        Notification::make()
            ->title('Skill Updated Successfully')
            ->body("Changes to '{$this->record->name}' have been saved")
            ->success()
            ->send();

        // Send database notification
        if ($user = auth()->user()) {
            $user->notify(new SkillUpdatedNotification($this->record));
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}

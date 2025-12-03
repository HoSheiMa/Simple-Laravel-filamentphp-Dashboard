<?php

namespace App\Filament\Resources\Skills\Tables;

use App\Models\Skill;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification;

class SkillsTable
{
    public static function configure(Table $table): Table
    {
        return $table
                    ->modifyQueryUsing(fn ($query) => $query->with('category')->notArchived())
            ->columns([
                  TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->icon('heroicon-o-light-bulb')
                    ->iconColor('primary'),

                TextColumn::make('category.name')
                    ->label('Category')
                    ->badge()
                    ->sortable()
                    ->searchable()
                    ->color('success'),

                TextColumn::make('proficiency_level')
                    ->label('Proficiency')
                    ->sortable()
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state ? "{$state}/5" : '-')
                    ->color(fn ($state) => match(true) {
                        $state >= 4 => 'success',
                        $state >= 3 => 'warning',
                        $state >= 1 => 'gray',
                        default => null,
                    })
                    ->alignCenter(),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                  SelectFilter::make('category')
                    ->relationship('category', 'name')
                    ->multiple()
                    ->preload()
                    ->label('Category'),

               SelectFilter::make('proficiency_level')
                    ->label('Proficiency')
                    ->options([
                                1 => 'Level 1+',
                                2 => 'Level 2+',
                                3 => 'Level 3+',
                                4 => 'Level 4+',
                                5 => 'Level 5',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (isset($data['min'])) {
                            $query->where('proficiency_level', '>=', $data['min']);
                        }
                    }),

                TernaryFilter::make('is_active')
                    ->label('Status')
                    ->placeholder('All Skills')
                    ->trueLabel('Active Only')
                    ->falseLabel('Inactive Only'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
                 Action::make('toggle_active')
                    ->label('Toggle Status')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Toggle Skill Status')
                    ->modalDescription(fn (Skill $record) => 
                        $record->is_active 
                            ? "Mark '{$record->name}' as inactive?" 
                            : "Mark '{$record->name}' as active?"
                    )
                    ->action(function (Skill $record) {
                        $record->update(['is_active' => !$record->is_active]);
                        
                        Notification::make()
                            ->title('Status Updated')
                            ->body("Skill is now " . ($record->is_active ? 'active' : 'inactive'))
                            ->success()
                            ->send();
                    }),

                Action::make('archive')
                    ->label('Archive')
                    ->icon('heroicon-o-archive-box')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Archive Skill')
                    ->modalDescription(fn (Skill $record) => "Are you sure you want to archive '{$record->name}'? This will mark it as inactive.")
                    ->visible(fn (Skill $record) => !$record->archived)
                    ->action(function (Skill $record) {
                        $record->update([
                            'is_active' => false,
                            'archived' => true,
                        ]);

                        Notification::make()
                            ->title('Skill Archived')
                            ->body("'{$record->name}' has been archived successfully")
                            ->success()
                            ->send();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

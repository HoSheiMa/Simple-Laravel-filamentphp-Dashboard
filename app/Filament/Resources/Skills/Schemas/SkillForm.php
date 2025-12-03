<?php

namespace App\Filament\Resources\Skills\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Forms\ComponentContainer;

class SkillForm
{
    public static function configure($form)
    {
        return $form
            
            ->schema([
                Wizard::make([
                    Step::make('Basic')
                        ->schema([
                            TextInput::make('name')
                                ->label('Name')
                                ->required()
                                ->unique(ignoreRecord: true),

                            TextInput::make('category')
                                ->label('Category')
                                ->required(),

                            Toggle::make('is_active')
                                ->label('Active')
                                ->default(true),
                        ]),

                    Step::make('Details')
                        ->schema([
                            Select::make('proficiency_level')
                                ->label('Proficiency (1–5)')
                                ->options([
                                    1 => '1',
                                    2 => '2',
                                    3 => '3',
                                    4 => '4',
                                    5 => '5',
                                ])
                                ->native(false)
                                ->visibleJs(<<<'JS'
                                    $get('category') === 'Technical'
                                    JS),

                            MarkdownEditor::make('description')
                                ->label('Description')
                                ->columnSpanFull(),
                        ]),

                    Step::make('Extras')
                        ->schema([
                            FileUpload::make('attachments')
                                ->label('Attachments')
                                ->multiple()
                                ->disk('public'),

                            Repeater::make('tags')
                                ->label('Tags')
                                ->schema([
                                    TextInput::make('value')
                                        ->label('Tag'),
                                ]),

                            Textarea::make('notes')
                                ->label('Notes')
                                ->rows(3)
                                ->extraAttributes([
                                    'x-data'     => '{ count: 0 }',
                                    'x-init'     => 'count = $el.value.length',
                                    'x-on:input' => 'count = $el.value.length',
                                ])
                                ->helperText('Character counter above.'),
                        ]),
                ]),
            ]);
    }
}

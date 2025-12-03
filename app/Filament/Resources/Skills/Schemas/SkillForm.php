<?php

namespace App\Filament\Resources\Skills\Schemas;

use App\Models\Category;
use App\Models\Skill;
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
                Wizard\Step::make('Basic Information')
                    ->icon('heroicon-o-information-circle')
                    ->description('Enter the skill name and category')
                    ->schema([
                        TextInput::make('name')
                            ->label('Skill Name')
                            ->required()
                            ->unique(Skill::class, 'name', ignoreRecord: true)
                            ->maxLength(255)
                            ->autocomplete(false)
                            ->placeholder('e.g., Laravel Development'),

                        Select::make('category_id')
                            ->label('Category')
                            ->required()
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->required()
                                    ->unique(Category::class, 'name')
                                    ->maxLength(255),
                            ])
                            ->native(false)
                            ->live()
                            ->helperText('Select a category or create a new one'),

                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->inline(false)
                            ->helperText('Mark this skill as active or inactive'),
                    ]),

                Wizard\Step::make('Proficiency & Details')
                    ->icon('heroicon-o-star')
                    ->description('Set proficiency level and add description')
                    ->schema([
                        Select::make('proficiency_level')
                            ->label('Proficiency Level')
                            ->options([
                                1 => '⭐ Level 1 - Novice',
                                2 => '⭐⭐ Level 2 - Beginner',
                                3 => '⭐⭐⭐ Level 3 - Intermediate',
                                4 => '⭐⭐⭐⭐ Level 4 - Advanced',
                                5 => '⭐⭐⭐⭐⭐ Level 5 - Expert',
                            ])
                            ->visible(function ($get) {
                                $categoryId = $get('category_id');
                                if (!$categoryId) return false;
                                
                                $category = Category::find($categoryId);
                                return $category && in_array($category->name, [
                                    'Developer', 
                                    'Engineer', 
                                    'Technician',
                                    'Architect',
                                    'Designer'
                                ]);
                            })
                            ->required(function ($get) {
                                $categoryId = $get('category_id');
                                if (!$categoryId) return false;
                                
                                $category = Category::find($categoryId);
                                return $category && in_array($category->name, [
                                    'Developer', 
                                    'Engineer', 
                                    'Technician',
                                    'Architect',
                                    'Designer'
                                ]);
                            })
                            ->native(false)
                            ->helperText('Required for technical roles like Developer, Engineer, etc.'),

                        MarkdownEditor::make('description')
                            ->label('Description')
                            ->columnSpanFull()
                            ->toolbarButtons([
                                'bold',
                                'bulletList',
                                'heading',
                                'italic',
                                'link',
                                'orderedList',
                                'redo',
                                'undo',
                            ])
                            ->placeholder('Describe this skill in detail...'),
                    ]),

                Wizard\Step::make('Tags & Attachments')
                    ->icon('heroicon-o-tag')
                    ->description('Add tags and upload files')
                    ->schema([
                        Repeater::make('tags')
                            ->label('Tags')
                            ->schema([
                                TextInput::make('value')
                                    ->label('Tag')
                                    ->required()
                                    ->maxLength(50)
                                    ->placeholder('e.g., PHP, Backend'),
                            ])
                            ->defaultItems(0)
                            ->addActionLabel('Add Tag')
                            ->reorderable()
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['value'] ?? null)
                            ->columnSpanFull(),

                        FileUpload::make('attachments')
                            ->label('Attachments')
                            ->multiple()
                            ->directory('skills/attachments')
                            ->downloadable()
                            ->openable()
                            ->maxSize(5120)
                            ->maxFiles(5)
                            ->reorderable()
                            ->acceptedFileTypes(['image/*', 'application/pdf'])
                            ->helperText('Upload up to 5 files (images or PDFs, max 5MB each)')
                            ->columnSpanFull(),
                    ]),

                Wizard\Step::make('Notes')
                    ->icon('heroicon-o-pencil-square')
                    ->description('Add internal notes (optional)')
                    ->schema([
                        Textarea::make('notes')
                            ->label('Internal Notes')
                            ->rows(5)
                            ->maxLength(500)
                            ->extraAttributes([
                                'x-data' => '{ count: $el.value.length }',
                                'x-on:input' => 'count = $el.value.length',
                            ])
                            ->hint(fn () => new \Illuminate\Support\HtmlString(
                                '<span class="text-sm" x-text="count"></span><span class="text-sm"> / 500 characters</span>'
                            ))
                            ->hintColor('primary')
                            ->placeholder('Add any internal notes or comments...'),
                    ]),
            ])
                ->columnSpanFull()
                ->skippable()
                ->submitAction(new \Illuminate\Support\HtmlString(
                    '<button type="submit" class="filament-button filament-button-size-md inline-flex items-center justify-center py-1 gap-1 font-medium rounded-lg border transition-colors outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset min-h-[2.25rem] px-4 text-sm text-white shadow focus:ring-white border-transparent bg-primary-600 hover:bg-primary-500 focus:bg-primary-700 focus:ring-offset-primary-700">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        Save Skill
                    </button>'
                )),
        ]);
    }
}

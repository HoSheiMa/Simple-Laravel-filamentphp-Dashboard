<?php

namespace App\Filament\Resources\Categories;

use App\Filament\Resources\Categories\Pages\ManageCategories;
use App\Models\Category;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use UnitEnum;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static  BackedEnum|string|null $navigationIcon = 'heroicon-o-tag';

    protected static  UnitEnum|string|null$navigationGroup = 'Settings';

    protected static ?int $navigationSort = 10;
    public static function form(Schema $form): Schema
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->unique(Category::class, 'name', ignoreRecord: true)
                    ->maxLength(255),

                TextInput::make('api_id')
                    ->label('API ID')
                    ->disabled()
                    ->dehydrated(false),
            ]);
    }
    public static function getNavigationBadge(): ?string
    {
        return (string) Category::count();
    }
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('skills_count')
                    ->counts('skills')
                    ->label('Skills')
                    ->badge()
                    ->color('success')
                    ->sortable(),

                TextColumn::make('api_id')
                    ->label('API ID')
                    ->toggleable()
                    ->searchable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('has_skills')
                    ->label('Has Skills')
                    ->query(fn ($query) => $query->has('skills')),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageCategories::route('/'),
        ];
    }
}

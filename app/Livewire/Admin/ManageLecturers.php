<?php

namespace App\Livewire\Admin;

use App\Models\Department;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Livewire\Component;


class ManageLecturers extends Component implements HasForms, HasTable, HasActions
{
    use InteractsWithForms;
    use InteractsWithTable;
    use InteractsWithActions;

    public function table(Table $table): Table
    {
        return $table
            ->query(User::query()
            ->role('lecturer'))
            ->columns([
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                TextColumn::make('department.name')
                    ->label('Department')
                    ->badge(),
            ])
            ->filters([
                
            ])
            ->headerActions([
                CreateAction::make('create')
                    ->label('Create Lecturer')
                    ->schema([
                        TextInput::make('name')
                            ->required(),
                        TextInput::make('email')
                            ->required()
                            ->email(),
                        Select::make('department')
                            ->options(Department::query()->pluck('name', 'id'))
                            ->required()
                            ->searchable(),
                    ])
                    ->modalWidth(Width::TwoExtraLarge)
                    ->modalHeading('Create Lecturer')
            ])
            ->recordActions([
                EditAction::make('edit')
                    ->label('Edit')
                    ->schema([
                        TextInput::make('name')
                            ->required(),
                        TextInput::make('email')
                            ->required()
                            ->email(),
                        Select::make('department')
                            ->options(Department::query()->pluck('name', 'id'))
                            ->required()
                            ->searchable(),
                    ])
                    ->modalWidth(Width::TwoExtraLarge)
                    ->modalHeading('Edit Lecturer'),
                    DeleteAction::make('delete')
            ])
            ->toolbarActions([
                
            ]);
    }

    public function render()
    {
        return view('livewire.admin.manage-lecturers');
    }
}

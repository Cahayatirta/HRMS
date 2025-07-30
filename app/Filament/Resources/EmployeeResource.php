<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeResource\Pages;
use App\Filament\Resources\EmployeeResource\RelationManagers;
use App\Models\Employee;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static ?string $navigationIcon = 'heroicon-o-identification';

    protected static ?string $navigationGroup = 'Human Resources';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('user_id')
                    ->label('User')
                    ->required()
                    ->options(function () {
                        $editingId = request()->route('record');
                        if ($editingId) {
                            // Edit mode: show all users, but mark the current one as selected
                            $employee = Employee::find($editingId);
                            $users = User::where(function ($query) use ($employee) {
                                $query->whereDoesntHave('employee')
                                      ->orWhere('id', $employee->user_id);
                            })->pluck('name', 'id');
                        } else {
                            // Create mode: only users without employee
                            $users = User::doesntHave('employee')->pluck('name', 'id');
                        }

                        return $users->isNotEmpty()
                            ? $users
                            : ['' => 'Semua user sudah memiliki data employee'];
                    })
                    ->searchable()
                    ->disabled(fn (string $context): bool => $context === 'edit'),
                Select::make('division_id')
                    ->label('Division')
                    ->required()
                    ->relationship('division', 'division_name'),
                TextInput::make('full_name')
                    ->required()
                    ->placeholder('Masukkan nama lengkap...'),
                Select::make('gender')
                    ->label('Gender')
                    ->required()
                    ->options([
                        'male' => 'Male',
                        'female' => 'Female',
                    ]),
                DatePicker::make('birth_date')
                    ->required(),
                TextInput::make('phone_number')
                    ->numeric()
                    ->tel()
                    ->required()
                    ->placeholder('e.g. +1234567890'),
                Textarea::make('address')
                    ->required()
                    ->columnSpanFull()
                    ->placeholder('Masukkan alamat...'),
                FileUpload::make('image_path')
                    ->image()
                    ->directory('employees'),
                Select::make('status')
                    ->required()
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image_path')
                    ->label('Profile Picture')
                    ->circular()
                    ->size(50),
                TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('division.division_name')
                    ->label('Division')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('full_name')
                    ->searchable()
                    ->sortable(),
                ToggleColumn::make('is_deleted')
                    ->label('Deleted')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('Deleted Status')
                ->options([
                    'active' => 'Active',
                    'deleted' => 'Deleted',
                ])
                ->query(function (Builder $query, array $data): Builder {
                    if ($data['value'] === 'deleted') {
                        return $query->where('is_deleted', true);
                    }
                    return $query->where('is_deleted', false);
                }),
            ])
            ->actions([
                EditAction::make(),
                // ViewAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }
}

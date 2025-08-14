<?php

namespace App\Filament\Resources;

// Resources & Pages
use App\Filament\Resources\ServiceTypeResource\Pages;
use App\Filament\Resources\ServiceTypeResource\Pages\CreateServiceType;
use App\Filament\Resources\ServiceTypeResource\Pages\EditServiceType;
use App\Filament\Resources\ServiceTypeResource\Pages\ListServiceTypes;
use App\Filament\Resources\ServiceTypeResource\RelationManagers;

// Models
use App\Models\ServiceType;

// Filament Resource
use Filament\Resources\Resource;

// Filament Forms
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextArea;
use Filament\Forms\Components\TextInput;

// Filament Tables
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

// Eloquent
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

// Filament Plugin - Shield
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;

class ServiceTypeResource extends Resource
{
    protected static ?string $model = ServiceType::class;

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected static ?string $navigationGroup = 'Client And Service Management';

        /**
     * Shield permission prefixes
     */
    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'delete',
            'delete_any',
        ];
    }

    /**
     * Check if user can view any records
     */
    public static function canViewAny(): bool
    {
        $user = auth()->user();
        
        // Super admin can access everything
        if ($user && $user->hasRole('super_admin')) {
            return true;
        }

        // Check specific permission
        return $user && $user->can('view_any_' . strtolower(class_basename(static::$model)));
    }

    /**
     * Check if user can create records
     */
    public static function canCreate(): bool
    {
        $user = auth()->user();
        
        if ($user && $user->hasRole('super_admin')) {
            return true;
        }

        return $user && $user->can('create_' . strtolower(class_basename(static::$model)));
    }

    /**
     * Check if user can edit specific record
     */
    public static function canEdit($record): bool
    {
        $user = auth()->user();
        
        if ($user && $user->hasRole('super_admin')) {
            return true;
        }

        // Basic permission check
        if (!$user || !$user->can('update_' . strtolower(class_basename(static::$model)))) {
            return false;
        }

        // CUSTOM LOGIC: Tambahkan logic khusus jika diperlukan
        // Contoh: hanya bisa edit data divisi sendiri
        // $userDivision = $user->employee?->division_id;
        // return $userDivision && $record->division_id === $userDivision;

        return true;
    }

    /**
     * Check if user can delete specific record
     */
    public static function canDelete($record): bool
    {
        $user = auth()->user();
        
        if ($user && $user->hasRole('super_admin')) {
            return true;
        }

        return $user && $user->can('delete_' . strtolower(class_basename(static::$model)));
    }

    /**
     * Check if user can delete any records (bulk delete)
     */
    public static function canDeleteAny(): bool
    {
        $user = auth()->user();
        
        if ($user && $user->hasRole('super_admin')) {
            return true;
        }

        return $user && $user->can('delete_any_' . strtolower(class_basename(static::$model)));
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Select::make('status')
                    ->required()
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                    ])
                    ->default('active'),
                Textarea::make('description')
                    ->columnSpanFull(),
                Repeater::make('fields')
                    ->relationship('fields')
                    ->simple(
                        TextInput::make('field_name')
                            ->label('Field Name')
                            ->required(),
                    )
                    ->defaultItems(1)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('status')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => ListServiceTypes::route('/'),
            'create' => CreateServiceType::route('/create'),
            'edit' => EditServiceType::route('/{record}/edit'),
        ];
    }
}

<?php

namespace App\Filament\Resources;

// Laravel
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

// Filament - Core
use Filament\Resources\Resource;

// Filament - Forms
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

// Filament - Tables
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Filters\SelectFilter;

// App - Resources
use App\Filament\Resources\AccessResource\Pages;
use App\Filament\Resources\AccessResource\Pages\ListAccesses;
use App\Filament\Resources\AccessResource\Pages\CreateAccess;
use App\Filament\Resources\AccessResource\Pages\EditAccess;
use App\Filament\Resources\AccessResource\RelationManagers;

// App - Models
use App\Models\Access;

// Filament Plugin - Shield
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;

class AccessResource extends Resource
{
    protected static ?string $model = Access::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?int $navigationSort = 8;

    protected static ?string $navigationGroup = 'System Settings';

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

        if (!$user) {
            return false;
        }
        
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
        
        if (!$user) {
            return false;
        }

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

        if (!$user) {
            return false;
        }
        
        if ($user && $user->hasRole('super_admin')) {
            return true;
        }

        // Basic permission check
        if (!$user || !$user->can('update_' . strtolower(class_basename(static::$model)))) {
            return false;
        }

        return true;
    }

    /**
     * Check if user can delete specific record
     */
    public static function canDelete($record): bool
    {
        $user = auth()->user();
     
        if (!$user) {
            return false;
        }

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

        if (!$user) {
            return false;
        }

        if ($user && $user->hasRole('super_admin')) {
            return true;
        }

        return $user && $user->can('delete_any_' . strtolower(class_basename(static::$model)));
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('access_name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('access_description')
                    ->maxLength(500),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('access_name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('access_description')
                    ->searchable()
                    ->limit(50),
                ToggleColumn::make('is_deleted')
                    ->label('Deleted')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                SelectFilter::make('deleted_status')
                    ->label('Status')
                    ->options([
                        'active' => 'Active',
                        'deleted' => 'Deleted',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if ($data['value'] === 'deleted') {
                            return $query->where('is_deleted', true);
                        }
                        if ($data['value'] === 'active') {
                            return $query->where('is_deleted', false);
                        }
                        return $query;
                    }),
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => ListAccesses::route('/'),
            'create' => CreateAccess::route('/create'),
            'edit' => EditAccess::route('/{record}/edit'),
        ];
    }
}
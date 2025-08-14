<?php

namespace App\Filament\Resources;

// Resource & Pages
use App\Filament\Resources\MeetingResource\Pages;
use App\Filament\Resources\MeetingResource\Pages\ListMeetings;
use App\Filament\Resources\MeetingResource\Pages\CreateMeeting;
use App\Filament\Resources\MeetingResource\Pages\EditMeeting;
use App\Filament\Resources\MeetingResource\RelationManagers;

// Models
use App\Models\Meeting;

// Filament Resource
use Filament\Resources\Resource;

// Filament Forms
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TextArea;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;

// Filament Tables
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;

// Eloquent
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

// Filament Plugin - Shield
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;

class MeetingResource extends Resource
{
    protected static ?string $model = Meeting::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Project Management';

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
                TextInput::make('meeting_title')
                    ->required(),
                DatePicker::make('date')
                    ->required(),
                Textarea::make('meeting_note')
                    ->columnSpanFull(),
                TextInput::make('start_time')
                    ->required(),
                TextInput::make('end_time')
                    ->required(),
                Toggle::make('is_deleted')
                    ->hidden(),
                Select::make('users')
                    ->relationship('users', 'name') 
                    ->multiple()
                    ->label('Assigned Users')
                    ->nullable()
                    ->default(0)
                    ->preload(),
                Select::make('clients')
                    ->relationship('clients', 'name') 
                    ->multiple()
                    ->label('Assigned clients')
                    ->nullable()
                    ->default(0)
                    ->preload(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('meeting_title')
                    ->searchable(),
                TextColumn::make('date')
                    ->date()
                    ->sortable(),
                TextColumn::make('start_time'),
                TextColumn::make('end_time'),
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
            'index' => ListMeetings::route('/'),
            'create' => CreateMeeting::route('/create'),
            'edit' => EditMeeting::route('/{record}/edit'),
        ];
    }
}

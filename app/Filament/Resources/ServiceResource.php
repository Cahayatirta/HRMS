<?php

namespace App\Filament\Resources;

// Resources & Pages
use App\Filament\Resources\ServiceResource\Pages;
use App\Filament\Resources\ServiceResource\Pages\ListServices;
use App\Filament\Resources\ServiceResource\Pages\CreateService;
use App\Filament\Resources\ServiceResource\Pages\EditService;
use App\Filament\Resources\ServiceResource\RelationManagers;

// Models
use App\Models\Client;
use App\Models\Service;
use App\Models\ServiceType;
use App\Models\ServiceTypeField;

// Filament Resource
use Filament\Resources\Resource;

// Filament Forms
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

// Filament Tables
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;

// Eloquent
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

// Filament Plugin - Shield
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

    protected static ?string $navigationIcon = 'heroicon-o-wrench';

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
                Select::make('client_id')
                    ->options(Client::all()->pluck('name', 'id'))
                    ->searchable()
                    ->required(),
                Select::make('service_type_id')
                    ->options(ServiceType::all()->pluck('name', 'id'))
                    ->searchable()
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                        if ($state) {
                            $fields = ServiceTypeField::where('service_type_id', $state)->get();
                            $fieldData = $fields->map(function ($field) {
                                return [
                                    'id' => null,
                                    'field_id' => $field->id,
                                    'value' => '',
                                ];
                            })->toArray();
                            $set('serviceTypeData', $fieldData);
                        } else {
                            $set('serviceTypeData', []);
                        }
                    }),
                Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'ongoing' => 'Ongoing',
                        'expired' => 'Expired',
                    ])
                    ->required()
                    ->default('pending'),
                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->prefix('Rp. '),
                DateTimePicker::make('start_time')
                    ->required()
                    ->default(now()),
                DateTimePicker::make('expired_time')
                    ->required()
                    ->default(now()->addDays(7)),
                Repeater::make('serviceTypeData')
                    ->schema([
                        Hidden::make('id'),
                        Select::make('field_id')
                            ->label('Field')
                            ->options(function (Forms\Get $get) {
                                $serviceTypeId = $get('../../service_type_id');
                                if (!$serviceTypeId) {
                                    return [];
                                }
                                $fields = ServiceTypeField::where('service_type_id', $serviceTypeId)
                                    ->pluck('field_name', 'id');
                                return $fields;
                            })
                            ->searchable()
                            ->required(),
                        TextInput::make('value')->required(),
                    ])
                    ->columns(2)
                    ->columnSpanFull()
                    ->addable(false)
                    ->deletable(false)
                    ->reorderable(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('client.name')
                    ->label('Client')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('serviceType.name')
                    ->label('Service Type')
                    ->sortable(),
                TextColumn::make('status')
                    ->searchable(),
                TextColumn::make('price')
                    ->money('Rp.')
                    ->sortable(),
                TextColumn::make('start_time')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('expired_time')
                    ->dateTime()
                    ->sortable(),
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
                //
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
            'index' => ListServices::route('/'),
            'create' => CreateService::route('/create'),
            'edit' => EditService::route('/{record}/edit'),
        ];
    }
}
<?php

namespace App\Filament\Resources;

// Laravel - Eloquent
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Crypt;

// Filament - Core
use Filament\Resources\Resource;

// Filament - Forms
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Repeater;

// Filament - Tables
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;

// App - Filament Resources
use App\Filament\Resources\ClientResource\Pages;
use App\Filament\Resources\ClientResource\Pages\CreateClient;
use App\Filament\Resources\ClientResource\Pages\EditClient;
use App\Filament\Resources\ClientResource\Pages\ListClients;
use App\Filament\Resources\ClientResource\RelationManagers;

// App - Models
use App\Models\Client;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    protected static ?string $navigationGroup = 'Client And Service Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required(),
                TextInput::make('phone_number')
                    ->numeric()
                    ->tel()
                    ->required(),
                TextInput::make('email')
                    ->email()
                    ->required(),
                TextInput::make('address')
                    ->required(),
                Repeater::make('clientData')
                    ->relationship('clientData')
                    ->label('Data Client')
                    ->schema([
                        TextInput::make('account_type')
                            ->required()
                            ->label('Account Type'),
                        TextInput::make('account_credential')
                            ->required()
                            ->label('Account Credential'),
                        TextInput::make('account_password')
                            ->label('Account Password')
                            ->afterStateHydrated(function ($component, $state) {
                                try {
                                    $component->state(Crypt::decryptString($state));
                                } catch (\Exception $e) {
                                    $component->state($state); // fallback kalau gagal decrypt
                                }
                            })
                            ->dehydrateStateUsing(fn ($state) => Crypt::encryptString($state))
                            ->password()
                            ->revealable()
                            ->required(),
                    ])
                    ->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('phone_number')
                    ->searchable(),
                TextColumn::make('email')
                    ->searchable(),
                TextColumn::make('address')
                    ->searchable(),
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
            'index' => ListClients::route('/'),
            'create' => CreateClient::route('/create'),
            'edit' => EditClient::route('/{record}/edit'),
        ];
    }
}

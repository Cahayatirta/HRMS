<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceResource\Pages;
use App\Filament\Resources\ServiceResource\RelationManagers;
use App\Models\Service;
use App\Models\ServiceType;
use App\Models\ServiceTypeField;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

    protected static ?string $navigationIcon = 'heroicon-o-wrench';

    protected static ?string $navigationGroup = 'Client And Service Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('client_id')
                    ->options(\App\Models\Client::all()->pluck('name', 'id'))
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('service_type_id')
                    ->options(\App\Models\ServiceType::all()->pluck('name', 'id'))
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
                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'ongoing' => 'Ongoing',
                        'expired' => 'Expired',
                    ])
                    ->required()
                    ->default('pending'),
                Forms\Components\TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->prefix('Rp. '),
                Forms\Components\DateTimePicker::make('start_time')
                    ->required()
                    ->default(now()),
                Forms\Components\DateTimePicker::make('expired_time')
                    ->required()
                    ->default(now()->addDays(7)),
                Forms\Components\Repeater::make('serviceTypeData')
                    ->schema([
                        Forms\Components\Hidden::make('id'),
                        Forms\Components\Select::make('field_id')
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
                        Forms\Components\TextInput::make('value')->required(),
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
                Tables\Columns\TextColumn::make('client.name')
                    ->label('Client')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('serviceType.name')
                    ->label('Service Type')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->money('Rp.')
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_time')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('expired_time')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_deleted')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
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
            'index' => Pages\ListServices::route('/'),
            'create' => Pages\CreateService::route('/create'),
            'edit' => Pages\EditService::route('/{record}/edit'),
        ];
    }
}
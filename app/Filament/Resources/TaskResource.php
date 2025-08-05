<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TaskResource\Pages;
use App\Models\Task;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\View;
use Filament\Forms\Components\Repeater;

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;

    protected static ?string $navigationIcon = 'heroicon-o-check-circle';

    protected static ?string $navigationGroup = 'Project Management';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('task_name')
                ->required()
                ->columnSpanFull(),

            Textarea::make('task_description')
                ->columnSpanFull(),

            DatePicker::make('deadline')
                ->required(),

            Select::make('status')
                ->options([
                    'pending' => 'Pending',
                    'in_progress' => 'In Progress',
                    'completed' => 'Completed',
                    'issue' => 'Issue',
                    'cancelled' => 'Cancelled',
                ])
                ->default('pending')
                ->required(),

            Textarea::make('note')
                ->columnSpanFull(),

            Select::make('Employee')
                ->relationship('employees', 'full_name') 
                ->multiple()
                ->label('Assigned Employee')
                ->nullable()
                ->default(0)
                ->columnSpanFull()
                ->preload(),

            Repeater::make('subtasks')
                ->relationship('subtasks')
                ->label('Subtasks')
                ->schema([
                    TextInput::make('task_name')
                        ->required()
                        ->label('Subtask Name'),

                    Textarea::make('task_description')
                        ->label('Subtask Description'),

                    DatePicker::make('deadline')
                        ->label('Subtask Deadline'),

                    Select::make('status')
                        ->label('Subtask Status')
                        ->options([
                            'pending' => 'Pending',
                            'in_progress' => 'In Progress',
                            'completed' => 'Completed',
                            'issue' => 'Issue',
                            'cancelled' => 'Cancelled',
                        ])
                        ->default('pending')
                        ->required(),

                    Textarea::make('note')
                        ->label('Subtask Note'),
                ])
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(Task::query()
                ->where('parent_task_id', '=', null)
                ->where('is_deleted', '=', 0)
            )
            ->columns([
                TextColumn::make('task_name')
                    ->searchable(),

                TextColumn::make('deadline')
                    ->date()
                    ->sortable(),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'gray',
                        'in_progress' => 'warning',
                        'completed' => 'success',
                        'issue' => 'danger',
                        'cancelled' => 'secondary',
                        default => 'gray',
                    }),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
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
                ViewAction::make(),
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
            // Tambahkan RelationManager di sini jika diperlukan
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTasks::route('/'),
            'view' => Pages\ViewTask::route('/{record}'),
            'edit' => Pages\EditTask::route('/{record}/edit'),
        ];
    }
}

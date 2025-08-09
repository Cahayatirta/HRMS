<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\Attendance;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;

class AttendanceWidget extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    protected function getTablePollingInterval(): ?string
    {
        return '10s'; 
    }

    public function getListeners(): array
    {
        return [
            'attendance-updated' => '$refresh'
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Attendance::Query()
            )
            ->groups([
            'employee.full_name',
            ])
            ->columns([
                ImageColumn::make('image_path')
                    ->label('Image')
                    ->circular()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('employee.full_name')
                    ->label('Employee')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('start_time')
                    ->label('Start Time'),
                TextColumn::make('end_time')
                    ->label('End Time'),
                TextColumn::make('work_location')
                    ->label('Work Location')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'office' => 'success',
                        'anywhere' => 'info',
                        default => 'gray',
                    })
                    ->searchable(),
                TextColumn::make('longitude')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('latitude')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('task_link')
                    ->label('Task Link')
                    ->searchable()
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('attendanceTasks.task.task_name')
                    ->label('Completed Tasks')
                    ->badge()
                    ->separator(',')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),
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
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from')->label('From'),
                        DatePicker::make('created_until')->label('Until'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['created_from'], fn ($q, $date) => $q->whereDate('created_at', '>=', $date))
                            ->when($data['created_until'], fn ($q, $date) => $q->whereDate('created_at', '<=', $date));
                    }),
            ]);
    }
}

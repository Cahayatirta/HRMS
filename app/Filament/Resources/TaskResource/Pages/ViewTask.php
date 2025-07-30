<?php

namespace App\Filament\Resources\TaskResource\Pages;

use App\Filament\Resources\TaskResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Tables\Concerns\InteractsWithTable;

class ViewTask extends ViewRecord
{
    protected static string $resource = TaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('viewSubtasks')
                ->label('Lihat Subtask')
                ->tooltip(fn ($record) => $record->subtasks()->where('is_delete', 0)->doesntExist() ? 'Subtask tidak ada' : null)
                // ->disabled(fn ($record) => $record->subtasks()->where('is_delete', 0)->doesntExist())
                ->modalHeading('Daftar Subtask')
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Tutup')
                ->modalContent(function ($record) {
                    // Hanya ambil subtasks yang belum dihapus
                    $subtasks = $record->subtasks()->where('is_delete', 0)->get();
                    return view('filament.pages.components.subtasks-table', compact('subtasks'));
                }),
        ];
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('task_name'),
            Tables\Columns\TextColumn::make('status'),
            Tables\Columns\TextColumn::make('deadline'),
        ];
    }
}

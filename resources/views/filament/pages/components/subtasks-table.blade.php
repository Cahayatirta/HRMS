@livewireScripts
@livewireStyles

<div class="filament-tables-component space-y-4">
    <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
        <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-800">
                <tr>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">
                        Task Name
                    </th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">
                        Status
                    </th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">
                        Deadline
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse ($subtasks as $subtask)
                    <tr> {{-- HAPUS hover:bg-gray-50 --}}
                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                            {{ $subtask->task_name }}
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <span
                                class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium 
                                {{ $subtask->status === 'completed' ? 'bg-success-100 text-success-800 dark:bg-success-900 dark:text-success-200' : 'bg-warning-100 text-warning-800 dark:bg-warning-900 dark:text-warning-200' }}">
                                {{ ucfirst($subtask->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                            {{ \Carbon\Carbon::parse($subtask->deadline)->format('d M Y') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-4 py-3 text-center text-sm text-gray-500 dark:text-gray-400">
                            No subtasks found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

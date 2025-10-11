<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <x-heroicon-o-clock class="w-5 h-5" />
                Attendance System
            </div>
        </x-slot>

        <div class="space-y-6">
            {{-- Header with Time (Left) and Status Action (Right) --}}
            <div class="flex justify-between items-start">
                {{-- Current Time (Left) --}}
                <div class="space-y-1">
                    <div class="text-2xl font-bold text-gray-900 dark:text-gray-100" id="current-time">
                        {{ now()->format('H:i:s') }}
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        {{ now()->format('l, F j, Y') }}
                    </div>
                </div>

                {{-- Attendance Status Action (Right) --}}
                <div class="text-right">
                    @if($attendanceType === 'check_in')
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-1">Check In</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Start your work day</p>
                    @elseif($attendanceType === 'check_out')
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-1">Check Out</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">End your work day</p>
                    @endif
                </div>
            </div>

            {{-- Attendance Status Banner --}}
            @php
                $status = $this->getAttendanceStatus();
            @endphp
            
            <div class="p-4 rounded-lg border 
                @if($status['color'] === 'success') 
                    border-green-200 bg-green-50 dark:border-green-800 dark:bg-green-900/20
                @elseif($status['color'] === 'info') 
                    border-blue-200 bg-blue-50 dark:border-blue-800 dark:bg-blue-900/20
                @elseif($status['color'] === 'warning') 
                    border-yellow-200 bg-yellow-50 dark:border-yellow-800 dark:bg-yellow-900/20
                @else 
                    border-gray-200 dark:border-gray-700 dark:bg-gray-800/50
                @endif">
                <div class="flex items-center gap-3">
                    @if($status['color'] === 'success')
                        <x-heroicon-s-check-circle class="w-6 h-6 text-green-600 dark:text-green-400" />
                    @elseif($status['color'] === 'info')
                        <x-heroicon-s-information-circle class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                    @elseif($status['color'] === 'warning')
                        <x-heroicon-s-exclamation-triangle class="w-6 h-6 text-yellow-600 dark:text-yellow-400" />
                    @else
                        <x-heroicon-s-clock class="w-6 h-6 text-gray-600 dark:text-gray-400" />
                    @endif
                    
                    <div>
                        <h3 class="font-semibold 
                            @if($status['color'] === 'success') 
                                text-green-800 dark:text-green-200
                            @elseif($status['color'] === 'info') 
                                text-blue-800 dark:text-blue-200
                            @elseif($status['color'] === 'warning') 
                                text-yellow-800 dark:text-yellow-200
                            @else 
                                text-gray-800 dark:text-gray-200
                            @endif">
                            Today's Status
                        </h3>
                        <p class="text-sm 
                            @if($status['color'] === 'success') 
                                text-green-600 dark:text-green-400
                            @elseif($status['color'] === 'info') 
                                text-blue-600 dark:text-blue-400
                            @elseif($status['color'] === 'warning') 
                                text-yellow-600 dark:text-yellow-400
                            @else 
                                text-gray-600 dark:text-gray-400
                            @endif">
                            {{ $status['message'] }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Attendance Form --}}
            @if($attendanceType === 'check_in')
                <div class="space-y-4">
                    <form wire:submit="checkIn">
                        {{ $this->form }}
                        
                        <div class="mt-6 flex justify-center">
                            <x-filament::button 
                                type="submit" 
                                size="lg"
                                color="success"
                            >
                                <x-heroicon-m-arrow-right-on-rectangle class="w-5 h-5 mr-2" />
                                Check In
                            </x-filament::button>
                        </div>
                    </form>
                </div>

            @elseif($attendanceType === 'check_out')
                <div class="space-y-4">
                    <form wire:submit="checkOut">
                        {{ $this->form }}
                        
                        <div class="mt-6 flex justify-center">
                            <x-filament::button 
                                type="submit" 
                                size="lg"
                                color="danger"
                            >
                                <x-heroicon-m-arrow-left-on-rectangle class="w-5 h-5 mr-2" />
                                Check Out
                            </x-filament::button>
                        </div>
                    </form>
                </div>
            @endif

            {{-- Quick Stats (if checked out today) --}}
            @if($todayAttendance && $todayAttendance->end_time)
                @php
                    $user = Auth::user();
                    $employee = App\Models\Employee::where('user_id', $user->id)->first();
                    
                    if ($employee) {
                        // Calculate total work hours from all sessions today
                        $todayAttendances = $employee->attendances()
                            ->whereDate('created_at', today())
                            ->where('is_deleted', false)
                            ->whereNotNull('end_time')
                            ->get();

                        $totalMinutes = 0;
                        foreach ($todayAttendances as $attendance) {
                            $startTime = \Carbon\Carbon::parse($attendance->start_time);
                            $endTime = \Carbon\Carbon::parse($attendance->end_time);
                            $totalMinutes += $startTime->diffInMinutes($endTime);
                        }
                        
                        $totalTodayHours = $totalMinutes / 60;
                        $totalHours = floor($totalTodayHours);
                        $totalMins = round(($totalTodayHours - $totalHours) * 60);
                        
                        $sessionsCount = $todayAttendances->count();
                        
                        // Count all tasks from all today's attendances
                        $tasksCount = 0;
                        foreach ($todayAttendances as $attendance) {
                            $tasksCount += $attendance->attendanceTasks->count();
                        }
                    } else {
                        $totalHours = 0;
                        $totalMins = 0;
                        $sessionsCount = 0;
                        $tasksCount = 0;
                    }
                @endphp
                
                <div class="grid grid-cols-3 gap-4 mt-6">
                    <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg text-center">
                        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                            {{ $totalHours }}h {{ $totalMins }}m
                        </div>
                        <div class="text-sm text-blue-800 dark:text-blue-300">Total Work Hours</div>
                    </div>
                    
                    <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg text-center">
                        <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                            {{ $tasksCount }}
                        </div>
                        <div class="text-sm text-green-800 dark:text-green-300">Tasks Completed</div>
                    </div>
                    
                    <div class="bg-purple-50 dark:bg-purple-900/20 p-4 rounded-lg text-center">
                        <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                            {{ $sessionsCount }}
                        </div>
                        <div class="text-sm text-purple-800 dark:text-purple-300">Work Sessions</div>
                    </div>
                </div>
            @endif
        </div>
    </x-filament::section>

    {{-- Real-time clock update --}}
    <script>
        function updateTime() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('en-US', { 
                hour12: false,
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
            
            const timeElement = document.getElementById('current-time');
            if (timeElement) {
                timeElement.textContent = timeString;
            }
        }

        // Update time every second
        const timeInterval = setInterval(updateTime, 1000);
        
        // Update immediately
        updateTime();

        // Clean up interval when widget is destroyed
        document.addEventListener('livewire:navigated', function() {
            clearInterval(timeInterval);
        });

        // Re-initialize when page loads
        document.addEventListener('DOMContentLoaded', function() {
            updateTime();
        });

        // Handle Livewire updates
        document.addEventListener('livewire:updated', function() {
            updateTime();
        });
    </script>
</x-filament-widgets::widget>
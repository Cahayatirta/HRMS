<div class="space-y-2">
    <label class="text-sm font-medium">
        Assigned Users
    </label>

    @php
        $record = $getRecord();
    @endphp

    @if ($record && $record->users->isNotEmpty())
        <ul class="list-disc list-inside text-sm">
            @foreach ($record->users as $user)
                <li>{{ $user->name }}</li>
            @endforeach
        </ul>
    @else
        <p class="text-sm">No users assigned.</p>
    @endif
</div>

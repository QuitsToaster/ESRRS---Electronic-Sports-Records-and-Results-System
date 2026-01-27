<form action="{{ route('brackets.store') }}" method="POST">
    @csrf

    {{-- Event --}}
    <div class="mb-4">
        <label class="block text-gray-700 font-semibold mb-1">Event</label>
        <select name="event_id"
                class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-500">
            <option value="">Select Event</option>
            @foreach($events as $event)
                <option value="{{ $event->id }}">{{ $event->name }}</option>
            @endforeach
        </select>
        @error('event_id')<p class="text-red-600 mt-1">{{ $message }}</p>@enderror
    </div>

    {{-- Dynamic Team List --}}
    <div class="mb-4">
        <label class="block text-gray-700 font-semibold mb-1">Teams</label>
        <div id="teamsContainer">
            <div class="flex mb-2">
                <input type="text" name="teams[]" placeholder="Team Name" class="border px-2 py-1 rounded w-full">
                <button type="button" onclick="removeTeam(this)" class="ml-2 px-3 py-1 bg-red-500 text-white rounded">Remove</button>
            </div>
        </div>
        <button type="button" onclick="addTeam()" class="mt-2 px-4 py-2 bg-green-600 text-white rounded">Add Another Team</button>
    </div>

    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow">
        Save Bracket
    </button>
</form>

<script>
function addTeam() {
    const container = document.getElementById('teamsContainer');
    const div = document.createElement('div');
    div.classList.add('flex', 'mb-2');
    div.innerHTML = `
        <input type="text" name="teams[]" placeholder="Team Name" class="border px-2 py-1 rounded w-full">
        <button type="button" onclick="removeTeam(this)" class="ml-2 px-3 py-1 bg-red-500 text-white rounded">Remove</button>
    `;
    container.appendChild(div);
}

function removeTeam(button) {
    button.parentElement.remove();
}
</script>

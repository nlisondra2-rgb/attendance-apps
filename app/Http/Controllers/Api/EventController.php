<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index()
    {
        return Event::query()
            ->orderBy('date')
            ->orderBy('name')
            ->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'              => ['required', 'string', 'max:255'],
            'date'              => ['required', 'date'],
            'starts_at'         => ['nullable', 'date'],
            'ends_at'           => ['nullable', 'date', 'after_or_equal:starts_at'],
            'description'       => ['nullable', 'string', 'max:1000'],
            'target_year_level' => ['sometimes', 'string', 'max:50'],
            'venue'             => ['nullable', 'string', 'max:255'],
            'latitude'          => ['nullable', 'numeric', 'between:-90,90'],
            'longitude'         => ['nullable', 'numeric', 'between:-180,180'],
            'radius_meters'     => ['nullable', 'integer', 'min:10', 'max:10000'],
        ]);

        $event = Event::create($validated);

        return response()->json($event, 201);
    }

    public function show(Event $event)
    {
        return $event;
    }

    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'name'              => ['sometimes', 'required', 'string', 'max:255'],
            'date'              => ['sometimes', 'required', 'date'],
            'starts_at'         => ['sometimes', 'nullable', 'date'],
            'ends_at'           => ['sometimes', 'nullable', 'date', 'after_or_equal:starts_at'],
            'description'       => ['sometimes', 'nullable', 'string', 'max:1000'],
            'target_year_level' => ['sometimes', 'string', 'max:50'],
            'venue'             => ['sometimes', 'nullable', 'string', 'max:255'],
            'latitude'          => ['sometimes', 'nullable', 'numeric', 'between:-90,90'],
            'longitude'         => ['sometimes', 'nullable', 'numeric', 'between:-180,180'],
            'radius_meters'     => ['sometimes', 'nullable', 'integer', 'min:10', 'max:10000'],
        ]);

        $event->update($validated);

        return $event;
    }

    public function destroy(Event $event)
    {
        $event->delete();

        return response()->noContent();
    }
}

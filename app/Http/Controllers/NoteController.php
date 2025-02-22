<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NoteController extends Controller
{
    public function index(Request $request)
    {
        $notes = $request->session()->get('notes', []);
        return response()->json(array_values($notes));
    }

    public function show(Request $request, $id)
    {
        $notes = $request->session()->get('notes', []);
        return isset($notes[$id])
            ? response()->json($notes[$id])
            : response()->json(['error' => 'Note not found'], 404);
    }

    public function store(Request $request)
    {
        $data = $request->json()->all();

        if (!isset($data['title']) || !isset($data['content'])) {
            return response()->json(['error' => 'Title and content required'], 400);
        }

        $notes = $request->session()->get('notes', []);
        $newId = count($notes) + 1;

        $notes[$newId] = [
            'id' => $newId,
            'title' => $data['title'],
            'content' => $data['content']
        ];

        $request->session()->put('notes', $notes);
        return response()->json($notes[$newId], 201);
    }

    public function update(Request $request, $id)
    {
        $notes = $request->session()->get('notes', []);

        if (!isset($notes[$id])) {
            return response()->json(['error' => 'Note not found'], 404);
        }

        $data = $request->json()->all();
        $notes[$id]['title'] = $data['title'] ?? $notes[$id]['title'];
        $notes[$id]['content'] = $data['content'] ?? $notes[$id]['content'];

        $request->session()->put('notes', $notes);
        return response()->json($notes[$id]);
    }

    public function destroy(Request $request, $id)
    {
        $notes = $request->session()->get('notes', []);

        if (!isset($notes[$id])) {
            return response()->json(['error' => 'Note not found'], 404);
        }

        unset($notes[$id]);
        $request->session()->put('notes', $notes);
        return response()->json(['message' => 'Note deleted']);
    }
}

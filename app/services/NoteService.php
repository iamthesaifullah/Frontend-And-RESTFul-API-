<?php

namespace App\Services;

use Illuminate\Support\Facades\Session;

class NoteService
{
    protected const SESSION_KEY = 'notes';

    public function all()
    {
        return Session::get(self::SESSION_KEY, []);
    }

    public function find($id)
    {
        return collect($this->all())->firstWhere('id', $id);
    }

    public function create($data)
    {
        $notes = $this->all();
        $note = [
            'id' => uniqid(),
            'title' => $data['title'],
            'content' => $data['content'],
            'created_at' => now()->toDateTimeString(),
            'updated_at' => now()->toDateTimeString(),
        ];
        array_push($notes, $note);
        Session::put(self::SESSION_KEY, $notes);
        return $note;
    }

    public function update($id, $data)
    {
        $notes = $this->all();
        foreach ($notes as &$note) {
            if ($note['id'] == $id) {
                $note = array_merge($note, $data);
                $note['updated_at'] = now()->toDateTimeString();
                break;
            }
        }
        Session::put(self::SESSION_KEY, $notes);
        return $this->find($id);
    }

    public function delete($id)
    {
        $notes = array_filter($this->all(), function ($note) use ($id) {
            return $note['id'] != $id;
        });
        Session::put(self::SESSION_KEY, array_values($notes));
        return true;
    }
}

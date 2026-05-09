<?php

namespace App\Controllers;

use App\Models\CalendarEventModel;
use App\Models\TreatmentModel;

class CalendarController extends BaseController
{
    public function index()
    {
        return view('calendar/index', [
            'treatments' => (new TreatmentModel())->listWithPeople()->where('treatments.status', 'active')->findAll(),
        ]);
    }

    public function events()
    {
        $events = [];
        foreach ((new CalendarEventModel())->findAll() as $event) {
            $events[] = [
                'id' => $event['id'],
                'title' => $event['title'],
                'start' => $event['starts_at'],
                'end' => $event['ends_at'],
                'className' => 'event-' . $event['category'],
            ];
        }

        return $this->response->setJSON($events);
    }

    public function store()
    {
        helper('format');

        (new CalendarEventModel())->insert([
            'treatment_id' => $this->request->getPost('treatment_id') ?: null,
            'source_type' => 'manual',
            'title' => $this->request->getPost('title'),
            'category' => $this->request->getPost('category'),
            'starts_at' => datetime_local_to_sql($this->request->getPost('starts_at')),
            'ends_at' => datetime_local_to_sql($this->request->getPost('ends_at')),
            'notes' => $this->request->getPost('notes'),
        ]);

        return redirect()->to('agenda')->with('success', 'Evento criado.');
    }

    public function move(int $id)
    {
        helper('format');

        $event = (new CalendarEventModel())->find($id);
        if (! $event) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Evento nao encontrado.']);
        }

        (new CalendarEventModel())->update($id, [
            'starts_at' => datetime_local_to_sql($this->request->getPost('starts_at')),
            'ends_at' => datetime_local_to_sql($this->request->getPost('ends_at')),
        ]);

        return $this->response->setJSON(['ok' => true]);
    }
}

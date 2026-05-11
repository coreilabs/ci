<?php

namespace App\Controllers;

use App\Models\DocumentTemplateModel;

class AdminClinicalController extends BaseController
{
    public function index()
    {
        return view('clinical_admin/index', [
            'templates' => (new DocumentTemplateModel())->orderBy('category')->findAll(),
        ]);
    }

    public function editTemplate(int $id)
    {
        $template = (new DocumentTemplateModel())->find($id);
        if (! $template) {
            return redirect()->to('administrativo-clinico')->with('error', 'Modelo nao encontrado.');
        }

        return view('clinical_admin/template', ['template' => $template]);
    }

    public function updateTemplate(int $id)
    {
        (new DocumentTemplateModel())->update($id, [
            'name' => $this->request->getPost('name'),
            'category' => $this->request->getPost('category'),
            'body' => $this->request->getPost('body'),
            'version' => (int) $this->request->getPost('version'),
            'is_required_admission' => $this->request->getPost('is_required_admission') ? 1 : 0,
        ]);

        return redirect()->to('administrativo-clinico')->with('success', 'Modelo atualizado.');
    }
}

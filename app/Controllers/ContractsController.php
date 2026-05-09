<?php

namespace App\Controllers;

use App\Libraries\PdfService;
use App\Models\ContractModel;

class ContractsController extends BaseController
{
    public function pdf(int $id)
    {
        $contract = (new ContractModel())->find($id);
        if (! $contract) {
            return redirect()->to('tratamentos')->with('error', 'Contrato nao encontrado.');
        }

        $html = view('pdf/document', [
            'title' => $contract['title'],
            'body' => $contract['body_snapshot'],
        ]);

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'inline; filename="contrato.pdf"')
            ->setBody((new PdfService())->stream($html, 'contrato.pdf'));
    }
}

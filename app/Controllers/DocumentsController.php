<?php

namespace App\Controllers;

use App\Libraries\PdfService;
use App\Models\DocumentModel;
use App\Models\TreatmentModel;

class DocumentsController extends BaseController
{
    public function uploadSigned(int $documentId)
    {
        $document = (new DocumentModel())->find($documentId);
        if (! $document) {
            return redirect()->to('tratamentos')->with('error', 'Documento nao encontrado.');
        }

        $file = $this->request->getFile('signed_file');
        if ($file && $file->isValid() && ! $file->hasMoved()) {
            $dir = WRITEPATH . 'uploads/documents';
            if (! is_dir($dir)) {
                mkdir($dir, 0775, true);
            }

            $name = $file->getRandomName();
            $file->move($dir, $name);
            (new DocumentModel())->update($documentId, ['signed_file_path' => 'uploads/documents/' . $name]);
        }

        return redirect()->to('tratamentos/' . $document['treatment_id'])->with('success', 'Documento assinado anexado.');
    }

    public function pdf(int $documentId)
    {
        $document = (new DocumentModel())->find($documentId);
        if (! $document) {
            return redirect()->to('tratamentos')->with('error', 'Documento nao encontrado.');
        }

        $pdf = (new PdfService())->stream($this->pdfShell($document['name'], $document['body_snapshot']), $document['name'] . '.pdf');

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'inline; filename="' . url_title($document['name'], '-', true) . '.pdf"')
            ->setBody($pdf);
    }

    private function pdfShell(string $title, string $body): string
    {
        return view('pdf/document', ['title' => $title, 'body' => $body]);
    }
}

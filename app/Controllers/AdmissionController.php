<?php

namespace App\Controllers;

use App\Libraries\TemplateRenderer;
use App\Models\AdmissionDraftModel;
use App\Models\AuditLogModel;
use App\Models\CalendarEventModel;
use App\Models\ContractModel;
use App\Models\DocumentModel;
use App\Models\DocumentTemplateModel;
use App\Models\FinancialEntryModel;
use App\Models\GuardianModel;
use App\Models\PatientModel;
use App\Models\TreatmentModel;

class AdmissionController extends BaseController
{
    private array $steps = ['responsavel', 'paciente', 'financeiro', 'confirmacao'];

    public function index()
    {
        $drafts = (new AdmissionDraftModel())
            ->where('finished_at', null)
            ->orderBy('updated_at', 'DESC')
            ->findAll();

        return view('admissions/index', ['drafts' => $drafts]);
    }

    public function start()
    {
        $id = (new AdmissionDraftModel())->insert([
            'step' => 'responsavel',
            'payload' => json_encode([]),
            'created_by' => session('user.id'),
        ]);

        return redirect()->to('admissoes/' . $id . '/responsavel');
    }

    public function step(int $id, string $step)
    {
        $draft = (new AdmissionDraftModel())->find($id);
        if (! $draft || ! in_array($step, $this->steps, true)) {
            return redirect()->to('admissoes')->with('error', 'Admissao nao encontrada.');
        }

        $payload = $this->payload($draft);
        $contract = '';

        if ($step === 'confirmacao') {
            $contract = $this->buildContract($payload);
        }

        return view('admissions/step', [
            'draft' => $draft,
            'step' => $step,
            'steps' => $this->steps,
            'payload' => $payload,
            'contract' => $contract,
        ]);
    }

    public function save(int $id, string $step)
    {
        helper('format');

        $draftModel = new AdmissionDraftModel();
        $draft = $draftModel->find($id);
        if (! $draft || ! in_array($step, $this->steps, true)) {
            return redirect()->to('admissoes')->with('error', 'Admissao invalida.');
        }

        $payload = $this->payload($draft);
        $post = $this->request->getPost();
        unset($post[csrf_token()]);

        if ($step === 'responsavel') {
            $post['cpf'] = only_digits($post['cpf'] ?? '');
            $post['phone'] = only_digits($post['phone'] ?? '');
        }

        if ($step === 'paciente') {
            $post['cpf'] = only_digits($post['cpf'] ?? '');
            $post['phone'] = only_digits($post['phone'] ?? '');
        }

        if ($step === 'financeiro') {
            $post['registration_amount'] = money_to_float($post['registration_amount'] ?? '0');
            $post['monthly_amount'] = money_to_float($post['monthly_amount'] ?? '0');
            $post['stay_months'] = max(1, (int) ($post['stay_months'] ?? 1));
            $post['billing_day'] = min(28, max(1, (int) ($post['billing_day'] ?? 10)));
        }

        $payload[$step] = $post;

        if ($step === 'confirmacao') {
            return $this->finish($draft, $payload);
        }

        $next = $this->steps[array_search($step, $this->steps, true) + 1] ?? 'confirmacao';

        $draftModel->update($id, [
            'step' => $next,
            'payload' => json_encode($payload),
        ]);

        return redirect()->to('admissoes/' . $id . '/' . $next);
    }

    private function finish(array $draft, array $payload)
    {
        foreach (['responsavel', 'paciente', 'financeiro'] as $required) {
            if (empty($payload[$required])) {
                return redirect()->to('admissoes/' . $draft['id'] . '/' . $required)
                    ->with('error', 'Complete esta etapa antes da confirmacao.');
            }
        }

        $db = db_connect();
        $db->transStart();

        $guardianId = (new GuardianModel())->insert([
            'name' => trim($payload['responsavel']['name']),
            'cpf' => $payload['responsavel']['cpf'] ?: null,
            'phone' => $payload['responsavel']['phone'] ?: null,
            'email' => $payload['responsavel']['email'] ?: null,
            'relationship' => $payload['responsavel']['relationship'] ?: null,
            'address' => $payload['responsavel']['address'] ?: null,
        ]);

        $patientId = (new PatientModel())->insert([
            'guardian_id' => $guardianId,
            'name' => trim($payload['paciente']['name']),
            'cpf' => $payload['paciente']['cpf'] ?: null,
            'birth_date' => $payload['paciente']['birth_date'] ?: null,
            'phone' => $payload['paciente']['phone'] ?: null,
            'address' => $payload['paciente']['address'] ?: null,
        ]);

        $finance = $payload['financeiro'];
        $treatmentId = (new TreatmentModel())->insert([
            'patient_id' => $patientId,
            'guardian_id' => $guardianId,
            'admission_date' => $finance['admission_date'] ?: date('Y-m-d'),
            'monthly_amount' => $finance['monthly_amount'],
            'registration_amount' => $finance['registration_amount'],
            'stay_months' => $finance['stay_months'],
            'billing_day' => $finance['billing_day'],
            'captor_name' => $finance['captor_name'] ?: null,
            'status' => 'active',
            'notes' => $finance['notes'] ?? null,
        ]);

        $contractHtml = $payload['confirmacao']['contract_body'] ?? $this->buildContract($payload);
        (new ContractModel())->insert([
            'treatment_id' => $treatmentId,
            'title' => 'Contrato de admissao',
            'body_snapshot' => $contractHtml,
        ]);

        $this->createAdmissionFinancialEntries($treatmentId, $finance);
        $this->createRequiredDocuments($treatmentId, $payload);

        (new AdmissionDraftModel())->update($draft['id'], [
            'payload' => json_encode($payload),
            'finished_at' => date('Y-m-d H:i:s'),
        ]);

        (new AuditLogModel())->write($treatmentId, 'admission.created');
        $db->transComplete();

        return redirect()->to('tratamentos/' . $treatmentId)
            ->with('success', 'Admissao concluida. Contrato e termos foram gerados.');
    }

    private function createAdmissionFinancialEntries(int $treatmentId, array $finance): void
    {
        $model = new FinancialEntryModel();
        $admissionDate = $finance['admission_date'] ?: date('Y-m-d');
        $competence = date('Y-m', strtotime($admissionDate));

        if ((float) $finance['registration_amount'] > 0) {
            $model->insert([
                'treatment_id' => $treatmentId,
                'competence' => $competence,
                'type' => 'matricula',
                'description' => empty($finance['captor_name']) ? 'Matricula' : 'Matricula - comissao captador',
                'amount' => $finance['registration_amount'],
                'due_date' => $admissionDate,
                'status' => 'open',
            ]);
        }

        $firstMonthlyMonth = empty($finance['captor_name'])
            ? date('Y-m-01', strtotime($admissionDate))
            : date('Y-m-01', strtotime($admissionDate . ' +1 month'));

        $billingDay = min(28, max(1, (int) ($finance['billing_day'] ?? 10)));
        $stayMonths = max(1, (int) ($finance['stay_months'] ?? 1));

        if ((float) $finance['monthly_amount'] > 0) {
            for ($i = 0; $i < $stayMonths; $i++) {
                $month = date('Y-m-01', strtotime($firstMonthlyMonth . ' +' . $i . ' month'));
                $dueDate = $this->dueDateForMonth($month, $billingDay);
                $entryId = $model->insert([
                    'treatment_id' => $treatmentId,
                    'competence' => date('Y-m', strtotime($month)),
                    'type' => 'mensalidade',
                    'description' => 'Mensalidade ' . ($i + 1) . '/' . $stayMonths,
                    'amount' => $finance['monthly_amount'],
                    'due_date' => $dueDate,
                    'status' => 'open',
                ]);

                (new CalendarEventModel())->insert([
                    'treatment_id' => $treatmentId,
                    'source_type' => 'finance',
                    'source_id' => $entryId,
                    'title' => 'Receber mensalidade',
                    'category' => 'financeiro',
                    'starts_at' => $dueDate . ' 09:00:00',
                    'notes' => 'Mensalidade ' . ($i + 1) . '/' . $stayMonths,
                ]);
            }
        }
    }

    private function dueDateForMonth(string $month, int $billingDay): string
    {
        $lastDay = (int) date('t', strtotime($month));
        $day = min($billingDay, $lastDay);

        return date('Y-m-', strtotime($month)) . str_pad((string) $day, 2, '0', STR_PAD_LEFT);
    }

    private function createRequiredDocuments(int $treatmentId, array $payload): void
    {
        $templates = (new DocumentTemplateModel())->where('is_required_admission', 1)->findAll();
        $renderer = new TemplateRenderer();
        $vars = $this->templateVars($payload);
        $documentModel = new DocumentModel();

        foreach ($templates as $template) {
            $documentModel->insert([
                'treatment_id' => $treatmentId,
                'template_id' => $template['id'],
                'category' => $template['category'],
                'name' => $template['name'],
                'body_snapshot' => $renderer->render($template['body'], $vars),
                'version' => $template['version'],
            ]);
        }
    }

    private function buildContract(array $payload): string
    {
        $template = (new DocumentTemplateModel())->where('category', 'contrato')->orderBy('id')->first();
        $body = $template['body'] ?? '<h1>Contrato</h1><p>Paciente {{paciente}}</p>';

        return (new TemplateRenderer())->render($body, $this->templateVars($payload));
    }

    private function templateVars(array $payload): array
    {
        $finance = $payload['financeiro'] ?? [];

        return [
            'paciente' => $payload['paciente']['name'] ?? '',
            'responsavel' => $payload['responsavel']['name'] ?? '',
            'admissao' => $finance['admission_date'] ?? date('Y-m-d'),
            'mensalidade' => 'R$ ' . number_format((float) ($finance['monthly_amount'] ?? 0), 2, ',', '.'),
            'permanencia_meses' => $finance['stay_months'] ?? 1,
            'dia_cobranca' => $finance['billing_day'] ?? 10,
        ];
    }

    private function payload(array $draft): array
    {
        return json_decode($draft['payload'] ?? '{}', true) ?: [];
    }
}

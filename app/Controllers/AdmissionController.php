<?php

namespace App\Controllers;

use App\Libraries\TemplateRenderer;
use App\Libraries\CurrentContractTemplate;
use App\Models\AdmissionDraftModel;
use App\Models\AuditLogModel;
use App\Models\CalendarEventModel;
use App\Models\ContractModel;
use App\Models\DocumentModel;
use App\Models\DocumentTemplateModel;
use App\Models\FinancialEntryModel;
use App\Models\FamilyPortalAccessModel;
use App\Models\GuardianModel;
use App\Models\PatientModel;
use App\Models\PayableEntryModel;
use App\Models\AppSettingModel;
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

    public function delete(int $id)
    {
        $draft = (new AdmissionDraftModel())->find($id);
        if (! $draft || $draft['finished_at']) {
            return redirect()->to('admissoes')->with('error', 'Admissão não encontrada.');
        }

        (new AdmissionDraftModel())->delete($id);

        return redirect()->to('admissoes')->with('success', 'Admissão excluída.');
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
            return redirect()->to('admissoes')->with('error', 'Admissão não encontrada.');
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
            'maxContractMonths' => $this->maxContractMonths(),
            'templateVariables' => $this->templateVariableHelp(),
        ]);
    }

    public function save(int $id, string $step)
    {
        helper('format');

        $draftModel = new AdmissionDraftModel();
        $draft = $draftModel->find($id);
        if (! $draft || ! in_array($step, $this->steps, true)) {
            return redirect()->to('admissoes')->with('error', 'Admissão inválida.');
        }

        $payload = $this->payload($draft);
        $post = $this->request->getPost();
        unset($post[csrf_token()]);

        if ($step === 'responsavel') {
            $post['cpf'] = only_digits($post['cpf'] ?? '');
            $post['phone'] = only_digits($post['phone'] ?? '');
            $post['zip_code'] = only_digits($post['zip_code'] ?? '');
            $post['nationality'] = trim($post['nationality'] ?? '') ?: 'brasileiro(a)';
            if ($post['cpf'] && ! is_valid_cpf($post['cpf'])) {
                return redirect()->back()->withInput()->with('error', 'CPF do responsável inválido.');
            }
        }

        if ($step === 'paciente') {
            $post['cpf'] = only_digits($post['cpf'] ?? '');
            $post['phone'] = only_digits($post['phone'] ?? '');
            $post['zip_code'] = only_digits($post['zip_code'] ?? '');
            $post['nationality'] = trim($post['nationality'] ?? '') ?: 'brasileiro(a)';
            if ($post['cpf'] && ! is_valid_cpf($post['cpf'])) {
                return redirect()->back()->withInput()->with('error', 'CPF do acolhido inválido.');
            }
        }

        if ($step === 'financeiro') {
            $post['registration_amount'] = money_to_float($post['registration_amount'] ?? '0');
            $post['monthly_amount'] = money_to_float($post['monthly_amount'] ?? '0');
            $post['stay_months'] = min($this->maxContractMonths(), max(1, (int) ($post['stay_months'] ?? 1)));
            $post['billing_day'] = min(28, max(1, (int) ($post['billing_day'] ?? 10)));
            $post['cid_code'] = $this->upperText(trim((string) ($post['cid_code'] ?? '')));
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
                    ->with('error', 'Complete esta etapa antes da confirmação.');
            }
        }

        $db = db_connect();
        $db->transStart();

        $guardianId = (new GuardianModel())->insert([
            'name' => trim($payload['responsavel']['name']),
            'cpf' => $payload['responsavel']['cpf'] ?: null,
            'nationality' => $payload['responsavel']['nationality'] ?: null,
            'phone' => $payload['responsavel']['phone'] ?: null,
            'email' => $payload['responsavel']['email'] ?: null,
            'relationship' => $payload['responsavel']['relationship'] ?: null,
            'address' => $payload['responsavel']['address'] ?: null,
            'zip_code' => $payload['responsavel']['zip_code'] ?: null,
        ]);

        $patientId = (new PatientModel())->insert([
            'guardian_id' => $guardianId,
            'name' => trim($payload['paciente']['name']),
            'cpf' => $payload['paciente']['cpf'] ?: null,
            'nationality' => $payload['paciente']['nationality'] ?: null,
            'birth_date' => $payload['paciente']['birth_date'] ?: null,
            'phone' => $payload['paciente']['phone'] ?: null,
            'address' => $payload['paciente']['address'] ?: null,
            'zip_code' => $payload['paciente']['zip_code'] ?: null,
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
            'cid_code' => $finance['cid_code'] ?: null,
            'captor_name' => $finance['captor_name'] ?: null,
            'status' => 'active',
            'notes' => $finance['notes'] ?? null,
        ]);

        $contractHtml = $payload['confirmacao']['contract_body'] ?? $this->buildContract($payload);
        (new ContractModel())->insert([
            'treatment_id' => $treatmentId,
            'title' => 'Contrato de admissão',
            'body_snapshot' => $contractHtml,
        ]);

        $this->createAdmissionFinancialEntries($treatmentId, $finance);
        $this->createRequiredDocuments($treatmentId, $payload);
        $this->createOptionalAdmissionDocuments($treatmentId, $payload);

        (new AdmissionDraftModel())->update($draft['id'], [
            'payload' => json_encode($payload),
            'finished_at' => date('Y-m-d H:i:s'),
        ]);

        $portalInfo = null;
        if (! empty($payload['confirmacao']['create_family_portal'])) {
            $portalInfo = $this->createFamilyPortal($treatmentId, $guardianId);
        }

        (new AuditLogModel())->write($treatmentId, 'admission.created');
        $db->transComplete();

        $message = 'Admissão concluída. Contrato e termos foram gerados.';
        if ($portalInfo) {
            $message .= ' Área da Família: ' . $portalInfo['url'] . ' senha: ' . $portalInfo['password'];
        }

        return redirect()->to('tratamentos/' . $treatmentId)->with('success', $message);
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
                'description' => 'Matrícula',
                'amount' => $finance['registration_amount'],
                'due_date' => $admissionDate,
                'status' => 'open',
                'created_by' => session('user.id'),
            ]);
        }

        if (! empty($finance['captor_name']) && (float) $finance['registration_amount'] > 0) {
            $payableId = (new PayableEntryModel())->insert([
                'treatment_id' => $treatmentId,
                'competence' => $competence,
                'category' => 'Comissão',
                'payee_name' => $finance['captor_name'],
                'description' => 'Comissão ' . $finance['captor_name'],
                'amount' => $finance['registration_amount'],
                'due_date' => $admissionDate,
                'status' => 'open',
                'created_by' => session('user.id'),
            ]);

            (new CalendarEventModel())->insert([
                'treatment_id' => $treatmentId,
                'source_type' => 'payable',
                'source_id' => $payableId,
                'title' => 'Pagar comissão ' . $finance['captor_name'],
                'category' => 'financeiro',
                'starts_at' => $admissionDate . ' 09:00:00',
                'notes' => 'Comissão do captador sobre matrícula.',
                'created_by' => session('user.id'),
            ]);
        }

        $firstMonthlyMonth = date('Y-m-01', strtotime($admissionDate . ' +1 month'));

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
                    'created_by' => session('user.id'),
                ]);

                (new CalendarEventModel())->insert([
                    'treatment_id' => $treatmentId,
                    'source_type' => 'finance',
                    'source_id' => $entryId,
                    'title' => 'Receber mensalidade',
                    'category' => 'financeiro',
                    'starts_at' => $dueDate . ' 09:00:00',
                    'notes' => 'Mensalidade ' . ($i + 1) . '/' . $stayMonths,
                    'created_by' => session('user.id'),
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
        $templates = (new DocumentTemplateModel())
            ->where('is_required_admission', 1)
            ->orderBy('category', 'ASC')
            ->orderBy('version', 'DESC')
            ->orderBy('id', 'DESC')
            ->findAll();
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

    private function createOptionalAdmissionDocuments(int $treatmentId, array $payload): void
    {
        if (empty($payload['confirmacao']['generate_adendo'])) {
            return;
        }

        $template = (new DocumentTemplateModel())
            ->where('category', 'adendo')
            ->orderBy('version', 'DESC')
            ->orderBy('id', 'DESC')
            ->first();

        if (! $template) {
            return;
        }

        (new DocumentModel())->insert([
            'treatment_id' => $treatmentId,
            'template_id' => $template['id'],
            'category' => $template['category'],
            'name' => $template['name'],
            'body_snapshot' => (new TemplateRenderer())->render($template['body'], $this->templateVars($payload)),
            'version' => $template['version'],
        ]);
    }

    private function buildContract(array $payload): string
    {
        $template = (new DocumentTemplateModel())
            ->where('category', 'contrato')
            ->orderBy('version', 'DESC')
            ->orderBy('id', 'DESC')
            ->first();
        $body = $template['body'] ?? CurrentContractTemplate::html();

        return (new TemplateRenderer())->render($body, $this->templateVars($payload));
    }

    private function templateVars(array $payload): array
    {
        helper('format');

        $finance = $payload['financeiro'] ?? [];
        $patient = $payload['paciente'] ?? [];
        $guardian = $payload['responsavel'] ?? [];
        $admissionDate = $finance['admission_date'] ?? date('Y-m-d');
        $monthlyAmount = (float) ($finance['monthly_amount'] ?? 0);
        $registrationAmount = (float) ($finance['registration_amount'] ?? 0);
        $stayMonths = max(1, (int) ($finance['stay_months'] ?? 1));
        $billingDay = min(28, max(1, (int) ($finance['billing_day'] ?? 10)));

        return [
            'contratado_razao_social' => 'CENTRO TERAPÊUTICO AMOR FRATERNO LTDA',
            'contratado_cnpj' => '30.558.573/0001-04',
            'contratado_endereco' => 'Rua Angélica, Qd. 30, Lt. 10, Setor Jardim Rosa do Sul, Aparecida de Goiânia - GO',
            'cidade_contrato' => 'Aparecida de Goiânia',
            'paciente' => $patient['name'] ?? '',
            'acolhido' => $patient['name'] ?? '',
            'acolhido_cpf' => cpf_br($patient['cpf'] ?? ''),
            'acolhido_nascimento' => date_br($patient['birth_date'] ?? null),
            'acolhido_telefone' => phone_br($patient['phone'] ?? ''),
            'acolhido_endereco' => $patient['address'] ?? '',
            'acolhido_cep' => cep_br($patient['zip_code'] ?? ''),
            'acolhido_nacionalidade' => $patient['nationality'] ?? 'brasileiro(a)',
            'responsavel' => $guardian['name'] ?? '',
            'responsavel_cpf' => cpf_br($guardian['cpf'] ?? ''),
            'responsavel_telefone' => phone_br($guardian['phone'] ?? ''),
            'responsavel_email' => $guardian['email'] ?? '',
            'responsavel_endereco' => $guardian['address'] ?? '',
            'responsavel_cep' => cep_br($guardian['zip_code'] ?? ''),
            'cep' => cep_br($guardian['zip_code'] ?? ''),
            'responsavel_parentesco' => $guardian['relationship'] ?? '',
            'responsavel_nacionalidade' => $guardian['nationality'] ?? 'brasileiro(a)',
            'cid' => $finance['cid_code'] ?? '',
            'admissao' => date_br($admissionDate),
            'admissao_extenso' => date_long_ptbr($admissionDate),
            'matricula' => money_br($registrationAmount),
            'matricula_extenso' => $this->upperText(money_to_words_ptbr($registrationAmount)),
            'mensalidade' => money_br($monthlyAmount),
            'mensalidade_extenso' => $this->upperText(money_to_words_ptbr($monthlyAmount)),
            'permanencia_meses' => $stayMonths,
            'permanencia_meses_2digitos' => str_pad((string) $stayMonths, 2, '0', STR_PAD_LEFT),
            'permanencia_meses_extenso' => number_to_words_ptbr($stayMonths),
            'dia_cobranca' => $billingDay,
            'vencimentos_mensalidades' => $this->monthlyDueDatesHtml($admissionDate, $billingDay, $stayMonths),
        ];
    }

    private function monthlyDueDatesHtml(string $admissionDate, int $billingDay, int $stayMonths): string
    {
        $dates = [];
        $firstMonthlyMonth = date('Y-m-01', strtotime($admissionDate . ' +1 month'));

        for ($i = 0; $i < $stayMonths; $i++) {
            $month = date('Y-m-01', strtotime($firstMonthlyMonth . ' +' . $i . ' month'));
            $dates[] = date_br($this->dueDateForMonth($month, $billingDay));
        }

        return implode('<br>', $dates);
    }

    private function upperText(string $value): string
    {
        return function_exists('mb_strtoupper') ? mb_strtoupper($value, 'UTF-8') : strtoupper($value);
    }

    private function maxContractMonths(): int
    {
        return max(1, (int) (new AppSettingModel())->value('admission_max_contract_months', '3'));
    }

    private function templateVariableHelp(): array
    {
        return [
            '{{responsavel}}',
            '{{responsavel_cpf}}',
            '{{responsavel_telefone}}',
            '{{responsavel_email}}',
            '{{responsavel_endereco}}',
            '{{responsavel_cep}}',
            '{{cep}}',
            '{{responsavel_nacionalidade}}',
            '{{acolhido}}',
            '{{acolhido_cpf}}',
            '{{acolhido_nascimento}}',
            '{{acolhido_telefone}}',
            '{{acolhido_endereco}}',
            '{{acolhido_cep}}',
            '{{acolhido_nacionalidade}}',
            '{{cid}}',
            '{{admissao}}',
            '{{admissao_extenso}}',
            '{{matricula}}',
            '{{matricula_extenso}}',
            '{{mensalidade}}',
            '{{mensalidade_extenso}}',
            '{{permanencia_meses}}',
            '{{permanencia_meses_extenso}}',
            '{{dia_cobranca}}',
            '{{vencimentos_mensalidades}}',
        ];
    }

    private function payload(array $draft): array
    {
        return json_decode($draft['payload'] ?? '{}', true) ?: [];
    }

    private function createFamilyPortal(int $treatmentId, int $guardianId): array
    {
        $token = bin2hex(random_bytes(20));
        $password = substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZ23456789'), 0, 8);

        $url = base_url('familia/' . $token);

        (new FamilyPortalAccessModel())->insert([
            'treatment_id' => $treatmentId,
            'guardian_id' => $guardianId,
            'token' => $token,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'initial_password' => $password,
            'access_url' => $url,
            'created_by' => session('user.id'),
        ]);

        return [
            'url' => $url,
            'password' => $password,
        ];
    }
}

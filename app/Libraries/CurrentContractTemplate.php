<?php

namespace App\Libraries;

class CurrentContractTemplate
{
    public static function html(): string
    {
        return <<<'HTML'
<h1 style="text-align:center;">CONTRATO DE PRESTAÇÃO DE SERVIÇOS TERAPÊUTICOS</h1>
<h2 style="text-align:center;">CENTRO TERAPÊUTICO AMOR FRATERNO</h2>

<p>Pelo presente instrumento particular de contrato de prestação de serviços terapêuticos, entre as partes, de um lado o <strong>{{contratado_razao_social}}</strong>, inscrito no CNPJ nº <strong>{{contratado_cnpj}}</strong>, sediado à <strong>{{contratado_endereco}}</strong>, daqui por diante designado simplesmente <strong>CONTRATADO</strong>, e, de outro lado, <strong>{{responsavel}}</strong>, {{responsavel_nacionalidade}}, inscrito(a) no CPF nº <strong>{{responsavel_cpf}}</strong>, residente no endereço <strong>{{responsavel_endereco}}</strong>, CEP <strong>{{responsavel_cep}}</strong>, telefone <strong>{{responsavel_telefone}}</strong>, doravante designado(a) simplesmente <strong>CONTRATANTE</strong>, ficam justos e convencionados nos termos seguintes.</p>

<h3>CLÁUSULA 1ª – DO OBJETO DO CONTRATO</h3>
<p>O presente contrato tem por objeto a prestação de serviços de tratamento da dependência química, por meio de Programa Terapêutico de modelo psicossocial, na modalidade de Comunidade Terapêutica, com foco na abstinência total, no desenvolvimento pessoal, social e familiar do residente, considerando suas potencialidades individuais e a força do convívio coletivo.</p>
<p>O residente é o principal responsável por seu processo de recuperação, comprometendo-se a participar das atividades propostas e a seguir as normas institucionais.</p>
<p><strong>Parágrafo único.</strong> O residente <strong>{{acolhido}}</strong>, {{acolhido_nacionalidade}}, inscrito no CPF nº <strong>{{acolhido_cpf}}</strong>, nascido em <strong>{{acolhido_nascimento}}</strong>, residente no endereço <strong>{{acolhido_endereco}}</strong>, CEP <strong>{{acolhido_cep}}</strong>, com CID informado <strong>{{cid}}</strong>, declara ter ciência e concordar com o Regimento Interno do Centro Terapêutico, comprometendo-se a cumpri-lo integralmente.</p>

<h3>CLÁUSULA 2ª – DO PRAZO E REGIME DO TRATAMENTO</h3>
<p>O Programa Terapêutico terá duração de <strong>{{permanencia_meses_2digitos}} ({{permanencia_meses_extenso}}) meses</strong>, com início em <strong>{{admissao}}</strong>, sendo realizado em regime de residência.</p>
<p>O tratamento prevê, sempre que possível, o envolvimento da família, com o objetivo de fortalecer vínculos e favorecer a recuperação do residente.</p>
<p><strong>§1º Serviços inclusos:</strong></p>
<ul>
    <li>admissão, avaliação e acolhimento terapêutico;</li>
    <li>hospedagem completa;</li>
    <li>quatro refeições diárias;</li>
    <li>acompanhamento psicológico e psiquiátrico;</li>
    <li>atendimentos individuais e em grupo;</li>
    <li>atividades terapêuticas, ocupacionais e de espiritualidade;</li>
    <li>demais atividades propostas pela equipe técnica.</li>
</ul>
<p><strong>§2º Visitas.</strong> As visitas ocorrerão conforme regras da instituição, sendo a primeira visita após 30 (trinta) dias, mediante agendamento prévio.</p>
<p><strong>§3º Prorrogação.</strong> Caso o residente não apresente evolução suficiente no prazo previsto, o tratamento poderá ser prorrogado, mediante avaliação da equipe técnica e concordância da contratante.</p>

<h3>CLÁUSULA 3ª – DA FUGA OU EVASÃO</h3>
<p>Em caso de fuga ou evasão, a CONTRATADA adotará as seguintes providências:</p>
<ul>
    <li>buscas nas imediações por até 48 horas;</li>
    <li>comunicação imediata à família;</li>
    <li>registro de Boletim de Ocorrência, se necessário.</li>
</ul>
<p>Ocorrida a evasão, o desligamento será automático, ficando a CONTRATADA isenta de responsabilidade por atos praticados pelo residente após sua saída, conforme RDC nº 29.</p>
<p>Caso o residente seja localizado e recuse retornar, a família será informada, aplicando-se as regras de rescisão contratual previstas neste instrumento.</p>

<h3>CLÁUSULA 4ª – DO VALOR DE ADMISSÃO, AVALIAÇÃO E ACOLHIMENTO TERAPÊUTICO</h3>
<p>No ato da assinatura deste contrato, a CONTRATANTE pagará o valor de <strong>{{matricula}} ({{matricula_extenso}})</strong>, referente ao procedimento de admissão/entrada/matrícula, avaliação e acolhimento terapêutico, compreendendo:</p>
<ul>
    <li>triagem inicial;</li>
    <li>entrevistas com residente e familiares;</li>
    <li>avaliação terapêutica e psicossocial;</li>
    <li>elaboração do Plano Terapêutico Individual (PTI);</li>
    <li>abertura de prontuário;</li>
    <li>integração inicial à rotina da comunidade.</li>
</ul>
<p>Trata-se de etapa inicial obrigatória do tratamento, com natureza autônoma, não se confundindo com as parcelas mensais.</p>
<p><strong>Parágrafo único.</strong> Por corresponder a serviços efetivamente prestados desde o ingresso do residente, este valor não é reembolsável, independentemente do tempo de permanência.</p>

<h3>CLÁUSULA 5ª – DO VALOR DO TRATAMENTO E FORMA DE PAGAMENTO</h3>
<p>Além do valor inicial previsto na cláusula anterior, a CONTRATANTE pagará o Programa Terapêutico em <strong>{{permanencia_meses_2digitos}} ({{permanencia_meses_extenso}}) parcelas mensais e consecutivas</strong>, no valor de <strong>{{mensalidade}} ({{mensalidade_extenso}})</strong> cada, com vencimento em:</p>
<p>{{vencimentos_mensalidades}}</p>
<p>O pagamento poderá ser realizado por depósito bancário: Caixa Econômica Federal – Agência 4614 – Conta Corrente PJ 354-2.</p>

<h3>CLÁUSULA 6ª – DO INADIMPLEMENTO</h3>
<p>O atraso no pagamento implicará multa de 2% e juros de 1% ao mês. Após 05 (cinco) dias úteis de atraso, o contrato poderá ser rescindido, com inscrição do nome do contratante nos órgãos de proteção ao crédito.</p>

<h3>CLÁUSULA 7ª – DA RESCISÃO ANTECIPADA E RETENÇÃO DE 20%</h3>
<p>Caso a CONTRATANTE solicite a rescisão do contrato antes do término do período de <strong>{{permanencia_meses_2digitos}} ({{permanencia_meses_extenso}}) meses</strong>, sem culpa da CONTRATADA, será devido:</p>
<ul>
    <li>20% (vinte por cento) sobre o valor das parcelas vincendas, ainda não vencidas;</li>
    <li>pagamento das diárias correspondentes ao mês em curso.</li>
</ul>
<p>O valor possui natureza compensatória, destinado a cobrir custos administrativos, operacionais e terapêuticos, devendo ser pago no ato da rescisão.</p>

<h3>DAS OBRIGAÇÕES DAS PARTES</h3>
<h3>CLÁUSULA 8ª – DAS DESPESAS E RESPONSABILIDADES</h3>
<p>As despesas pessoais do residente com roupas, produtos de higiene pessoal, locomoção, dentre outros, serão de responsabilidade exclusiva da contratante.</p>
<p><strong>§1º.</strong> As despesas com translado do residente para perícias, consultas médicas e dentárias, audiências judiciais ou quaisquer outras necessidades serão pagas antecipadamente pela contratante, mediante comprovação, sob pena de não comparecimento do residente.</p>
<p><strong>§2º.</strong> Eventuais danos contra o patrimônio da instituição e a terceiros que o residente der causa durante o período de internação serão de responsabilidade exclusiva da contratante, que se compromete a ressarcir, independentemente de notificação.</p>
<p><strong>§3º.</strong> O CENTRO TERAPÊUTICO AMOR FRATERNO LTDA não se responsabilizará por objetos de uso pessoal dos residentes ou acolhidos, incluindo roupas, materiais de higiene pessoal e limpeza.</p>

<h3>CLÁUSULA 9ª – DAS OBRIGAÇÕES DO RESIDENTE</h3>
<p>O residente deverá participar de todas as atividades prescritas pela equipe técnica, previstas ou não no cronograma diário de tratamento, respeitando-se seus limites e condições físicas e psicológicas, salvo em caso de problemas de saúde que impossibilitem a prática das atividades.</p>
<p><strong>§1º.</strong> No ato da internação, a contratante prestará todas as informações referentes à saúde do residente, notadamente sobre eventuais doenças de natureza crônica e/ou incapacitante, fornecendo os atestados médicos respectivos, se for o caso, não se responsabilizando a contratada por intercorrências relacionadas a doenças e limitações não informadas oportunamente.</p>
<p><strong>§2º.</strong> A participação em eventos e palestras fora da comunidade terapêutica será facultada ao residente, mediante avaliação da equipe técnica.</p>
<p><strong>§3º.</strong> Caso haja resultado econômico do trabalho desenvolvido pelo residente como parte do tratamento, o valor auferido será revertido em favor da comunidade terapêutica, para custeio das vagas sociais de que dispõe a instituição.</p>

<h3>CLÁUSULA 10ª – DAS PROIBIÇÕES E PENALIDADES</h3>
<p>É expressamente proibida a prática de relação sexual, agressão física ou verbal, ameaças, bem como o uso de substâncias psicoativas nas dependências da comunidade terapêutica.</p>
<p><strong>Parágrafo único.</strong> O descumprimento desta cláusula ensejará alta administrativa e rescisão do contrato, com aplicação dos encargos contratuais cabíveis.</p>

<h3>CLÁUSULA 11ª – DO USO DE MEDICAMENTOS E ATENDIMENTO MÉDICO</h3>
<p>Durante o tratamento, somente será permitido ao residente o uso de medicamentos mediante apresentação de receita médica.</p>
<p><strong>§1º.</strong> Em caso de necessidade de atendimento médico ambulatorial, a contratada encaminhará o residente ao UPA 24h Geraldo Magela (Parque Flamboyant), Rua W 3-A, 93-127, Parque Flamboyant, Aparecida de Goiânia - GO, CEP 74922-485, sendo eventual custo de exclusiva responsabilidade do contratante.</p>

<h3>CLÁUSULA 12ª – DO DESLIGAMENTO A PEDIDO</h3>
<p>Ao decidir desligar-se do tratamento, o residente deverá comunicar por escrito sua decisão à instituição, no prazo mínimo de 24 (vinte e quatro) horas, para que sejam tomadas as providências necessárias.</p>
<p><strong>Parágrafo único.</strong> Caso o residente opte pela desistência, a família será comunicada imediatamente e deverá comparecer ao CENTRO TERAPÊUTICO AMOR FRATERNO LTDA para buscá-lo no dia da comunicação, efetuando no mesmo ato os pagamentos devidos conforme regras de rescisão previstas neste contrato.</p>

<h3>CLÁUSULA 13ª – DA READMISSÃO</h3>
<p>O residente desligado do tratamento somente poderá ser readmitido no Centro de Reabilitação após avaliação favorável da equipe técnica.</p>

<h3>CLÁUSULA 14ª – DAS ADVERTÊNCIAS</h3>
<p>O residente reincidente que contar com mais de 3 (três) advertências por escrito será automaticamente desligado do programa terapêutico, sem ponderações.</p>

<h3>CLÁUSULA 15ª – DA COMUNICAÇÃO TELEFÔNICA</h3>
<p>A contratante poderá comunicar-se com o residente por telefone após completados 15 (quinze) dias de internação, por meio da secretaria da instituição.</p>
<p><strong>Parágrafo único.</strong> As ligações telefônicas poderão ser recebidas semanalmente, sempre às terças-feiras e quartas-feiras, das 08:00 às 18:00 horas, pelo período de 10 (dez) minutos, com avaliação prévia da equipe técnica sobre a possibilidade de realização do contato telefônico e acompanhamento durante a ligação.</p>

<h3>CLÁUSULA 16ª – DAS VISITAS</h3>
<p>A contratante terá direito à visita a partir de 30 (trinta) dias, de acordo com a data da internação. Posteriormente, as visitas ocorrerão uma vez quinzenalmente.</p>
<p><strong>§1º.</strong> A primeira visita será feita entre familiares e residente de forma individual e assistida, acompanhada por membro da equipe técnica, em data previamente estabelecida, com duração de até 02 (duas) horas, salvo em casos de pandemias ou epidemias, seguindo orientações dos órgãos de saúde competentes.</p>
<p><strong>§2º.</strong> Caso o residente responda satisfatoriamente ao tratamento, será designada visita assistida em grupo reduzido de familiares, podendo comparecer o número máximo de 05 (cinco) visitantes, desde que previamente cadastrados junto à instituição.</p>
<p><strong>§3º.</strong> No dia da visita, é proibida a utilização de celulares e quaisquer aparelhos eletrônicos pelo residente, e toda alimentação consumida será por conta dos familiares e responsáveis, não sendo permitida a saída do residente das dependências da instituição.</p>

<h3>CLÁUSULA 17ª – DO REGIMENTO INTERNO</h3>
<p>A contratante declara ciência e compromisso de cumprimento do regimento interno e das normas disciplinares da comunidade terapêutica, sob pena de aplicação das medidas cabíveis, inclusive desligamento do residente.</p>

<h3>CLÁUSULA 18ª – DA INTERRUPÇÃO DO TRATAMENTO</h3>
<p>Caso o tratamento seja interrompido por culpa do residente ou do responsável financeiro, incluindo a não devolução do residente à comunidade terapêutica no prazo determinado após atividades externas autorizadas, aplicam-se as regras de rescisão previstas neste contrato, com cobrança dos valores indicados na Cláusula 7ª.</p>

<h3>CLÁUSULA 19ª – DOS PERTENCES PESSOAIS</h3>
<p>Em caso de fuga ou desligamento, os pertences pessoais do residente ficarão à disposição dos responsáveis para retirada, não se responsabilizando a contratada pelos itens não retirados.</p>

<h3>CLÁUSULA 20ª – DAS QUESTÕES ADMINISTRATIVAS E JUDICIAIS</h3>
<p>A contratada não se responsabiliza pela tramitação de processos perante o INSS para pleitear ou receber qualquer tipo de benefício, ou por outras ações judiciais e procedimentos administrativos de interesse do residente, sendo exclusiva a responsabilidade do acolhido e de seus responsáveis.</p>
<p><strong>Parágrafo único.</strong> O translado do residente para compromissos externos poderá ser realizado pela contratada, mediante solicitação prévia mínima de 05 (cinco) dias e ressarcimento dos custos.</p>

<h3>CLÁUSULA 21ª – DAS DISPOSIÇÕES FINAIS</h3>
<p>A CONTRATANTE declara que recebeu todas as informações necessárias, compreendeu claramente os valores cobrados, está ciente da natureza do valor inicial e concorda com as regras de rescisão e retenção de valores.</p>

<h3>CLÁUSULA 22ª – DO FORO</h3>
<p>Fica eleito o foro da Comarca de Aparecida de Goiânia – GO, para dirimir quaisquer controvérsias decorrentes deste contrato.</p>

<p>{{cidade_contrato}}, {{admissao_extenso}}.</p>
<p style="margin-top:40px;">______________________________________________________________<br>{{responsavel}}</p>
<p style="margin-top:40px;">______________________________________________________________<br>BEATRIZ GONÇALVES FERRAZ</p>
<p style="margin-top:40px;">Testemunha: ________________________________ &nbsp;&nbsp;&nbsp; Testemunha: ________________________________</p>
<p>CPF: ______________________________________ &nbsp;&nbsp;&nbsp; CPF: ______________________________________</p>

<h2>Relação de Documentos</h2>
<p><strong>Nome do Acolhido:</strong> {{acolhido}}</p>
<p><strong>CPF:</strong> ( &nbsp; ) Sim &nbsp;&nbsp; ( &nbsp; ) Não</p>
<p><strong>Carteira de Trabalho:</strong> ( &nbsp; ) Sim &nbsp;&nbsp; ( &nbsp; ) Não</p>
<p><strong>Cartão do SUS:</strong> ( &nbsp; ) Sim &nbsp;&nbsp; ( &nbsp; ) Não</p>
<p><strong>Certidão de Nascimento (cópia):</strong> ( &nbsp; ) Sim &nbsp;&nbsp; ( &nbsp; ) Não</p>
<p><strong>Pertences:</strong></p>
<p>1. _____________________________________________________________</p>
<p>2. _____________________________________________________________</p>
<p>3. _____________________________________________________________</p>
<p>4. _____________________________________________________________</p>
<p style="margin-top:40px;">Carimbo da Clínica e Assinatura do Responsável</p>
HTML;
    }
}

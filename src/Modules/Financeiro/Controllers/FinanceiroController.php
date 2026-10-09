<?php

class FinanceiroController extends Controller
{
    public function index(): void
    {
        $this->auth()->check() ?: App::redirect('/login');

        LancamentoFinanceiro::atualizarAtrasados();
        $this->seedCategorias();

        $obraId = (int) Request::get('obra_id', 0);
        $tipo = Request::get('tipo', '');
        $status = Request::get('status', '');
        $mes = Request::get('mes', date('m'));
        $ano = Request::get('ano', date('Y'));
        $page = max(1, (int) Request::get('page', 1));
        $perPage = 15;

        $where = [];
        $params = [];

        if ($obraId > 0) {
            $where[] = 'lf.obra_id = ?';
            $params[] = $obraId;
        }
        if ($tipo !== '' && in_array($tipo, ['receita', 'despesa'], true)) {
            $where[] = 'lf.tipo = ?';
            $params[] = $tipo;
        }
        if ($status !== '' && isset(LancamentoFinanceiro::STATUS[$status])) {
            $where[] = 'lf.status = ?';
            $params[] = $status;
        }
        if ($mes !== '' && $ano !== '') {
            $where[] = 'MONTH(lf.data_vencimento) = ? AND YEAR(lf.data_vencimento) = ?';
            $params[] = (int) $mes;
            $params[] = (int) $ano;
        }
        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        $pdo = Database::getInstance();

        $stmt = $pdo->prepare("SELECT COUNT(*) c FROM lancamentos_financeiros lf {$whereSql}");
        $stmt->execute($params);
        $total = (int) $stmt->fetch()['c'];

        $offset = ($page - 1) * $perPage;
        $stmt = $pdo->prepare(
            "SELECT lf.*, o.nome AS obra_nome, o.codigo AS obra_codigo, cf.nome AS categoria_nome
             FROM lancamentos_financeiros lf
             LEFT JOIN obras o ON o.id = lf.obra_id
             LEFT JOIN categorias_financeiras cf ON cf.id = lf.categoria_id
             {$whereSql}
             ORDER BY lf.data_vencimento DESC, lf.id DESC
             LIMIT {$perPage} OFFSET {$offset}"
        );
        $stmt->execute($params);
        $lancamentos = $stmt->fetchAll(\PDO::FETCH_OBJ);

        $pagination = [
            'data' => $lancamentos,
            'current_page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'last_page' => max(1, (int) ceil($total / $perPage)),
            'from' => $total > 0 ? $offset + 1 : 0,
            'to' => min($page * $perPage, $total),
        ];

        $kpis = $this->kpis($mes, $ano, $obraId);

        $this->view('financeiro/index', [
            'title' => 'Financeiro - Hermes',
            'pageTitle' => 'Financeiro',
            'currentRoute' => 'financeiro',
            'lancamentos' => $lancamentos,
            'pagination' => $pagination,
            'kpis' => $kpis,
            'obras' => Obra::query()->select(['id', 'codigo', 'nome'])->orderBy('nome', 'ASC')->get(),
            'categorias' => CategoriaFinanceira::query()->select(['id', 'nome', 'tipo'])->orderBy('nome', 'ASC')->get(),
            'filtros' => ['obra_id' => $obraId, 'tipo' => $tipo, 'status' => $status, 'mes' => $mes, 'ano' => $ano],
            'statusList' => LancamentoFinanceiro::STATUS,
        ]);
    }

    public function store(): void
    {
        $this->auth()->check() ?: App::redirect('/login');
        $this->verifyCsrf();

        $data = $this->validate(Request::all(), [
            'obra_id' => 'nullable|integer|exists:obras,id',
            'tipo' => 'required|in:receita,despesa',
            'descricao' => 'required|min:2|max:200',
            'valor' => 'required|numeric|min:0.01',
            'data_vencimento' => 'required|date',
            'status' => 'required|in:pendente,pago',
            'categoria_id' => 'nullable|integer|exists:categorias_financeiras,id',
        ]);

        LancamentoFinanceiro::create([
            'obra_id' => $data['obra_id'] ? (int) $data['obra_id'] : null,
            'tipo' => $data['tipo'],
            'descricao' => $data['descricao'],
            'valor' => (float) $data['valor'],
            'data_vencimento' => $data['data_vencimento'],
            'data_pagamento' => $data['status'] === 'pago' ? date('Y-m-d') : null,
            'status' => $data['status'],
            'categoria_id' => $data['categoria_id'] ? (int) $data['categoria_id'] : null,
        ]);

        Session::putFlash('success', 'Lançamento financeiro criado com sucesso!');
        App::redirect('/financeiro');
    }

    public function baixarLancamento(int $id): void
    {
        $this->auth()->check() ?: App::redirect('/login');
        $this->verifyCsrf();

        $lancamento = LancamentoFinanceiro::find($id);
        if (!$lancamento) {
            App::abort(404, 'Lançamento não encontrado');
        }

        $lancamento->status = 'pago';
        $lancamento->data_pagamento = date('Y-m-d');
        $lancamento->save();

        Session::putFlash('success', 'Lançamento baixado com sucesso!');
        App::redirect('/financeiro');
    }

    public function relatorios(): void
    {
        $this->auth()->check() ?: App::redirect('/login');

        LancamentoFinanceiro::atualizarAtrasados();

        $mes = Request::get('mes', date('m'));
        $ano = Request::get('ano', date('Y'));

        $pdo = Database::getInstance();

        $stmt = $pdo->prepare(
            "SELECT tipo, status, COALESCE(SUM(valor), 0) total, COUNT(*) qtd
             FROM lancamentos_financeiros
             WHERE MONTH(data_vencimento) = ? AND YEAR(data_vencimento) = ?
             GROUP BY tipo, status"
        );
        $stmt->execute([(int) $mes, (int) $ano]);
        $resumo = $stmt->fetchAll(\PDO::FETCH_OBJ);

        $totais = ['receita_pendente' => 0, 'receita_pago' => 0, 'despesa_pendente' => 0, 'despesa_pago' => 0];
        $qtdades = ['receita_pendente' => 0, 'receita_pago' => 0, 'despesa_pendente' => 0, 'despesa_pago' => 0];
        foreach ($resumo as $r) {
            $chave = ($r->tipo === 'receita' ? 'receita_' : 'despesa_') . ($r->status === 'atrasado' ? 'pendente' : $r->status);
            if (isset($totais[$chave])) {
                $totais[$chave] += (float) $r->total;
                $qtdades[$chave] += (int) $r->qtd;
            }
        }

        $stmt = $pdo->prepare(
            "SELECT cf.nome, lf.tipo, SUM(lf.valor) total
             FROM lancamentos_financeiros lf
             INNER JOIN categorias_financeiras cf ON cf.id = lf.categoria_id
             WHERE MONTH(lf.data_vencimento) = ? AND YEAR(lf.data_vencimento) = ?
             GROUP BY cf.id, cf.nome, lf.tipo
             ORDER BY total DESC"
        );
        $stmt->execute([(int) $mes, (int) $ano]);
        $porCategoria = $stmt->fetchAll(\PDO::FETCH_OBJ);

        $stmt = $pdo->prepare(
            "SELECT o.codigo, o.nome, lf.tipo, SUM(lf.valor) total
             FROM lancamentos_financeiros lf
             INNER JOIN obras o ON o.id = lf.obra_id
             WHERE MONTH(lf.data_vencimento) = ? AND YEAR(lf.data_vencimento) = ?
             GROUP BY o.id, o.codigo, o.nome, lf.tipo
             ORDER BY total DESC"
        );
        $stmt->execute([(int) $mes, (int) $ano]);
        $porObra = $stmt->fetchAll(\PDO::FETCH_OBJ);

        $stmt = $pdo->prepare(
            "SELECT lf.*, cf.nome AS categoria_nome
             FROM lancamentos_financeiros lf
             LEFT JOIN categorias_financeiras cf ON cf.id = lf.categoria_id
             WHERE lf.data_vencimento <= CURDATE() AND lf.status != 'pago'
             ORDER BY lf.data_vencimento ASC"
        );
        $stmt->execute();
        $vencidos = $stmt->fetchAll(\PDO::FETCH_OBJ);

        $this->view('financeiro/relatorios', [
            'title' => 'Relatórios Financeiros - Hermes',
            'pageTitle' => 'Relatórios Financeiros',
            'currentRoute' => 'financeiro',
            'totais' => $totais,
            'qtdades' => $qtdades,
            'porCategoria' => $porCategoria,
            'porObra' => $porObra,
            'vencidos' => $vencidos,
            'filtros' => ['mes' => $mes, 'ano' => $ano],
        ]);
    }

    private function kpis(string $mes, string $ano, int $obraId): array
    {
        $where = ["MONTH(data_vencimento) = ?", "YEAR(data_vencimento) = ?"];
        $params = [(int) $mes, (int) $ano];

        if ($obraId > 0) {
            $where[] = 'obra_id = ?';
            $params[] = $obraId;
        }
        $whereSql = 'WHERE ' . implode(' AND ', $where);

        $stmt = Database::getInstance()->prepare(
            "SELECT
                COALESCE(SUM(CASE WHEN tipo = 'despesa' AND status != 'pago' THEN valor END), 0) a_pagar,
                COALESCE(SUM(CASE WHEN tipo = 'receita' AND status != 'pago' THEN valor END), 0) a_receber,
                COALESCE(SUM(CASE WHEN tipo = 'receita' AND status = 'pago' THEN valor END), 0) recebido,
                COALESCE(SUM(CASE WHEN tipo = 'despesa' AND status = 'pago' THEN valor END), 0) pago
             FROM lancamentos_financeiros {$whereSql}"
        );
        $stmt->execute($params);
        $row = $stmt->fetch();

        $aPagar = (float) $row['a_pagar'];
        $aReceber = (float) $row['a_receber'];
        $recebido = (float) $row['recebido'];
        $pago = (float) $row['pago'];

        return [
            'a_pagar' => $aPagar,
            'a_receber' => $aReceber,
            'recebido' => $recebido,
            'pago' => $pago,
            'saldo' => ($recebido + $aReceber) - ($pago + $aPagar),
        ];
    }

    private function seedCategorias(): void
    {
        $pdo = Database::getInstance();
        $existentes = (int) $pdo->query("SELECT COUNT(*) c FROM categorias_financeiras")->fetch()['c'];
        if ($existentes > 0) {
            return;
        }
        $stmt = $pdo->prepare("INSERT INTO categorias_financeiras (nome, tipo) VALUES (?, ?)");
        foreach ([
            ['Materiais', 'despesa'],
            ['Mão de Obra', 'despesa'],
            ['Equipamentos', 'despesa'],
            ['Serviços Terceirizados', 'despesa'],
            ['Impostos e Taxas', 'despesa'],
            ['Outras Despesas', 'despesa'],
            ['Medição/Aditivos', 'receita'],
            ['Outras Receitas', 'receita'],
        ] as [$nome, $tipo]) {
            $stmt->execute([$nome, $tipo]);
        }
    }

    private function verifyCsrf(): void
    {
        $token = Request::post('_token', '');
        if (!App::verifyCsrf((string) $token)) {
            App::abort(403, 'Token de segurança inválido.');
        }
    }
}

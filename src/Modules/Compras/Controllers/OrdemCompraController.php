<?php

class OrdemCompraController extends Controller
{
    public function index(): void
    {
        $this->auth()->check() ?: App::redirect('/login');

        $search = Request::get('search', '');
        $status = Request::get('status', '');
        $page = max(1, (int) Request::get('page', 1));
        $perPage = 10;

        $where = [];
        $params = [];

        if ($search) {
            $where[] = '(oc.numero LIKE ? OR oc.fornecedor LIKE ? OR o.nome LIKE ? OR o.codigo LIKE ?)';
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }
        if ($status !== '' && isset(OrdemCompra::STATUS[$status])) {
            $where[] = 'oc.status = ?';
            $params[] = $status;
        }
        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        $pdo = Database::getInstance();

        $stmt = $pdo->prepare("SELECT COUNT(*) c FROM ordens_compra oc INNER JOIN obras o ON o.id = oc.obra_id {$whereSql}");
        $stmt->execute($params);
        $total = (int) $stmt->fetch()['c'];

        $offset = ($page - 1) * $perPage;
        $stmt = $pdo->prepare(
            "SELECT oc.*, o.nome AS obra_nome, o.codigo AS obra_codigo
             FROM ordens_compra oc
             INNER JOIN obras o ON o.id = oc.obra_id
             {$whereSql}
             ORDER BY oc.created_at DESC, oc.id DESC
             LIMIT {$perPage} OFFSET {$offset}"
        );
        $stmt->execute($params);
        $ocs = $stmt->fetchAll(\PDO::FETCH_OBJ);

        $pagination = [
            'data' => $ocs,
            'current_page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'last_page' => (int) ceil($total / $perPage),
            'from' => $total > 0 ? $offset + 1 : 0,
            'to' => min($page * $perPage, $total),
        ];

        $this->view('compras/index', [
            'title' => 'Ordens de Compra - Hermes',
            'pageTitle' => 'Ordens de Compra',
            'currentRoute' => 'compras',
            'ocs' => $ocs,
            'pagination' => $pagination,
            'search' => $search,
            'statusFilter' => $status,
            'statusList' => OrdemCompra::STATUS,
            'obras' => $this->obrasList(),
        ]);
    }

    public function create(): void
    {
        $this->auth()->check() ?: App::redirect('/login');

        $this->view('compras/create', [
            'title' => 'Nova Ordem de Compra - Hermes',
            'pageTitle' => 'Nova Ordem de Compra',
            'currentRoute' => 'compras',
            'statusList' => OrdemCompra::STATUS,
            'obras' => $this->obrasList(),
        ]);
    }

    public function store(): void
    {
        $this->auth()->check() ?: App::redirect('/login');
        $this->verifyCsrf();

        $data = $this->validate(Request::all(), [
            'obra_id' => 'required|integer|exists:obras,id',
            'numero' => 'required|min:1|max:30',
            'fornecedor' => 'nullable|max:200',
            'valor_total' => 'required|numeric|min:0',
            'status' => 'required|in:pendente,aprovada,entregue,paga,cancelada',
            'data_pedido' => 'nullable|date',
            'observacoes' => 'nullable|max:2000',
        ]);

        OrdemCompra::create([
            'obra_id' => (int) $data['obra_id'],
            'numero' => $data['numero'],
            'fornecedor' => $data['fornecedor'] ?? null,
            'valor_total' => (float) $data['valor_total'],
            'status' => $data['status'],
            'data_pedido' => $data['data_pedido'] ?? null,
            'observacoes' => $data['observacoes'] ?? null,
        ]);

        Session::putFlash('success', 'Ordem de compra criada com sucesso!');
        App::redirect('/compras');
    }

    public function edit(int $id): void
    {
        $this->auth()->check() ?: App::redirect('/login');

        $oc = OrdemCompra::find($id);
        if (!$oc) {
            App::abort(404, 'Ordem de compra não encontrada');
        }

        $this->view('compras/edit', [
            'title' => 'Editar Ordem de Compra - Hermes',
            'pageTitle' => 'Editar Ordem de Compra',
            'currentRoute' => 'compras',
            'oc' => $oc,
            'statusList' => OrdemCompra::STATUS,
            'obras' => $this->obrasList(),
        ]);
    }

    public function update(int $id): void
    {
        $this->auth()->check() ?: App::redirect('/login');
        $this->verifyCsrf();

        $oc = OrdemCompra::find($id);
        if (!$oc) {
            App::abort(404, 'Ordem de compra não encontrada');
        }

        $data = $this->validate(Request::all(), [
            'obra_id' => 'required|integer|exists:obras,id',
            'numero' => 'required|min:1|max:30',
            'fornecedor' => 'nullable|max:200',
            'valor_total' => 'required|numeric|min:0',
            'status' => 'required|in:pendente,aprovada,entregue,paga,cancelada',
            'data_pedido' => 'nullable|date',
            'observacoes' => 'nullable|max:2000',
        ]);

        $oc->fill([
            'obra_id' => (int) $data['obra_id'],
            'numero' => $data['numero'],
            'fornecedor' => $data['fornecedor'] ?? null,
            'valor_total' => (float) $data['valor_total'],
            'status' => $data['status'],
            'data_pedido' => $data['data_pedido'] ?? null,
            'observacoes' => $data['observacoes'] ?? null,
        ]);
        $oc->save();

        Session::putFlash('success', 'Ordem de compra atualizada com sucesso!');
        App::redirect('/compras');
    }

    public function destroy(int $id): void
    {
        $this->auth()->check() ?: App::redirect('/login');
        $this->verifyCsrf();

        $oc = OrdemCompra::find($id);
        if (!$oc) {
            App::abort(404, 'Ordem de compra não encontrada');
        }

        $oc->delete();

        Session::putFlash('success', 'Ordem de compra excluída com sucesso!');
        App::redirect('/compras');
    }

    private function verifyCsrf(): void
    {
        $token = Request::post('_token', '');
        if (!App::verifyCsrf((string) $token)) {
            App::abort(403, 'Token de segurança inválido.');
        }
    }

    private function obrasList(): array
    {
        return Obra::query()->select(['id', 'codigo', 'nome'])->orderBy('nome', 'ASC')->get();
    }
}

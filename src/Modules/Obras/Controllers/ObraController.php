<?php

class ObraController extends Controller
{
    public function index(): void
    {
        $this->auth()->check() ?: App::redirect('/login');

        $search = Request::get('search', '');
        $status = Request::get('status', '');
        $page = max(1, (int) Request::get('page', 1));
        $perPage = 10;

        $query = Obra::query()->orderBy('created_at', 'DESC');

        if ($search) {
            $query->where('nome', 'LIKE', "%{$search}%")
                  ->where('codigo', 'LIKE', "%{$search}%");
        }

        if ($status) {
            $query->where('status', $status);
        }

        $pagination = $query->paginate($perPage, $page);
        $obras = $pagination['data'];

        // KPIs para o dashboard
        $kpis = $this->getKpis();

        $this->view('obras/index', [
            'title' => 'Obras - Hermes',
            'pageTitle' => 'Obras',
            'obras' => $obras,
            'pagination' => $pagination,
            'search' => $search,
            'status' => $status,
            'kpis' => $kpis,
        ]);
    }

    public function create(): void
    {
        $this->auth()->check() ?: App::redirect('/login');
        
        $this->view('obras/create', [
            'title' => 'Nova Obra - Hermes',
            'pageTitle' => 'Nova Obra',
        ]);
    }

    public function store(): void
    {
        $this->auth()->check() ?: App::redirect('/login');

        $data = $this->validate(Request::all(), [
            'codigo' => 'required|min:3|max:20|unique:obras,codigo',
            'nome' => 'required|min:3|max:200',
            'cliente_id' => 'nullable|integer|exists:clientes,id',
            'endereco' => 'nullable|max:500',
            'orcamento_total' => 'nullable|numeric|min:0',
            'data_inicio' => 'nullable|date',
            'data_fim_prevista' => 'nullable|date|after:data_inicio',
            'responsavel_tecnico' => 'nullable|max:120',
            'crea_rrt' => 'nullable|max:50',
            'observacoes' => 'nullable|max:2000',
            'status' => 'required|in:planejamento,em_andamento,paralisada,concluida,atrasada',
        ]);

        $obra = Obra::create($data);
        
        Session::putFlash('success', 'Obra criada com sucesso!');
        App::redirect('/obras/' . $obra->id);
    }

    public function show(int $id): void
    {
        $this->auth()->check() ?: App::redirect('/login');

        $obra = Obra::find($id);
        if (!$obra) {
            App::abort(404, 'Obra não encontrada');
        }

        $etapas = $obra->etapas();

        $this->view('obras/show', [
            'title' => $obra->nome . ' - Hermes',
            'pageTitle' => $obra->nome,
            'obra' => $obra,
            'etapas' => $etapas,
        ]);
    }

    public function edit(int $id): void
    {
        $this->auth()->check() ?: App::redirect('/login');

        $obra = Obra::find($id);
        if (!$obra) {
            App::abort(404, 'Obra não encontrada');
        }

        $this->view('obras/edit', [
            'title' => 'Editar ' . $obra->nome . ' - Hermes',
            'pageTitle' => 'Editar Obra',
            'obra' => $obra,
        ]);
    }

    public function update(int $id): void
    {
        $this->auth()->check() ?: App::redirect('/login');

        $obra = Obra::find($id);
        if (!$obra) {
            App::abort(404, 'Obra não encontrada');
        }

        $data = $this->validate(Request::all(), [
            'codigo' => "required|min:3|max:20|unique:obras,codigo,{$id},id",
            'nome' => 'required|min:3|max:200',
            'cliente_id' => 'nullable|integer|exists:clientes,id',
            'endereco' => 'nullable|max:500',
            'orcamento_total' => 'nullable|numeric|min:0',
            'data_inicio' => 'nullable|date',
            'data_fim_prevista' => 'nullable|date|after:data_inicio',
            'data_fim_real' => 'nullable|date',
            'responsavel_tecnico' => 'nullable|max:120',
            'crea_rrt' => 'nullable|max:50',
            'observacoes' => 'nullable|max:2000',
            'status' => 'required|in:planejamento,em_andamento,paralisada,concluida,atrasada',
        ]);

        $obra->fill($data);
        $obra->save();

        Session::putFlash('success', 'Obra atualizada com sucesso!');
        App::redirect('/obras/' . $obra->id);
    }

    public function destroy(int $id): void
    {
        $this->auth()->check() ?: App::redirect('/login');

        $obra = Obra::find($id);
        if (!$obra) {
            App::abort(404, 'Obra não encontrada');
        }

        $obra->delete();

        Session::putFlash('success', 'Obra excluída com sucesso!');
        App::redirect('/obras');
    }

    private function getKpis(): array
    {
        $pdo = Database::getInstance();
        
        $total = $pdo->query("SELECT COUNT(*) as c FROM obras")->fetch()['c'];
        $emAndamento = $pdo->query("SELECT COUNT(*) as c FROM obras WHERE status = 'em_andamento'")->fetch()['c'];
        $concluidas = $pdo->query("SELECT COUNT(*) as c FROM obras WHERE status = 'concluida'")->fetch()['c'];
        $atrasadas = $pdo->query("SELECT COUNT(*) as c FROM obras WHERE status = 'atrasada'")->fetch()['c'];
        $orcado = $pdo->query("SELECT COALESCE(SUM(orcamento_total), 0) as total FROM obras")->fetch()['total'];

        return [
            'total' => (int)$total,
            'em_andamento' => (int)$emAndamento,
            'concluidas' => (int)$concluidas,
            'atrasadas' => (int)$atrasadas,
            'orcado_total' => (float)$orcado,
        ];
    }
}
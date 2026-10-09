<?php

class FornecedorController extends Controller
{
    public function store(): void
    {
        $this->auth()->check() ?: App::redirect('/login');
        $this->verifyCsrf();

        $data = $this->validateFornecedor();

        Fornecedor::create($data);

        Session::putFlash('success', 'Fornecedor cadastrado com sucesso!');
        App::redirect('/compras?fornecedores=1');
    }

    public function update(int $id): void
    {
        $this->auth()->check() ?: App::redirect('/login');
        $this->verifyCsrf();

        $fornecedor = Fornecedor::find($id);
        if (!$fornecedor) {
            App::abort(404, 'Fornecedor não encontrado');
        }

        $data = $this->validateFornecedor($id);
        $fornecedor->fill($data);
        $fornecedor->save();

        Session::putFlash('success', 'Fornecedor atualizado com sucesso!');
        App::redirect('/compras?fornecedores=1');
    }

    public function destroy(int $id): void
    {
        $this->auth()->check() ?: App::redirect('/login');
        $this->verifyCsrf();

        $fornecedor = Fornecedor::find($id);
        if (!$fornecedor) {
            App::abort(404, 'Fornecedor não encontrado');
        }

        $fornecedor->delete();

        Session::putFlash('success', 'Fornecedor excluído com sucesso!');
        App::redirect('/compras?fornecedores=1');
    }

    private function validateFornecedor(?int $excludeId = null): array
    {
        $uniqueRule = $excludeId
            ? "required|cpf_cnpj|unique:fornecedores,cnpj_cpf,{$excludeId},id"
            : 'required|cpf_cnpj|unique:fornecedores,cnpj_cpf';

        return $this->validate(Request::all(), [
            'razao_social' => 'required|min:2|max:150',
            'cnpj_cpf' => $uniqueRule,
            'contato_nome' => 'nullable|max:100',
            'telefone' => 'nullable|max:30',
            'email' => 'nullable|email|max:120',
            'categoria' => 'nullable|max:50',
        ]);
    }

    private function verifyCsrf(): void
    {
        $token = Request::post('_token', '');
        if (!App::verifyCsrf((string) $token)) {
            App::abort(403, 'Token de segurança inválido.');
        }
    }
}

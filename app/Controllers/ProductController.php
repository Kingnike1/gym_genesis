<?php

namespace App\Controllers;

use App\Services\ProdutoService;
use App\Repositories\ProdutoRepository;

class ProductController extends Controller
{
    private ProdutoService $produtoService;

    public function __construct()
    {
        $this->produtoService = new ProdutoService(new ProdutoRepository());
    }

    public function index(): void
    {
        $produtos = $this->produtoService->getAllProdutos();
        $this->render("admin/products/index", ["produtos" => $produtos]);
    }

    public function create(): void
    {
        $this->render("admin/products/create");
    }

    public function store(): void
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $nome = $_POST["nome"] ?? "";
            $descricao = $_POST["descricao"] ?? "";
            $preco = (float)($_POST["preco"] ?? 0);
            $estoque = (int)($_POST["estoque"] ?? 0);
            $categoria = $_POST["categoria"] ?? "";

            $produtoId = $this->produtoService->createProduto($nome, $descricao, $preco, $estoque, $categoria);

            if ($produtoId) {
                $this->redirect("/admin/products");
            } else {
                $this->render("admin/products/create", ["errorMessage" => "Erro ao criar produto."]);
            }
        }
    }

    public function edit(int $id): void
    {
        $produto = $this->produtoService->getProdutoById($id);
        if (!$produto) {
            $this->handleNotFound();
        }
        $this->render("admin/products/edit", ["produto" => $produto]);
    }

    public function update(int $id): void
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $nome = $_POST["nome"] ?? "";
            $descricao = $_POST["descricao"] ?? "";
            $preco = (float)($_POST["preco"] ?? 0);
            $estoque = (int)($_POST["estoque"] ?? 0);
            $categoria = $_POST["categoria"] ?? "";

            $updated = $this->produtoService->updateProduto($id, $nome, $descricao, $preco, $estoque, $categoria);

            if ($updated) {
                $this->redirect("/admin/products");
            } else {
                $produto = $this->produtoService->getProdutoById($id);
                $this->render("admin/products/edit", ["produto" => $produto, "errorMessage" => "Erro ao atualizar produto."]);
            }
        }
    }

    public function delete(int $id): void
    {
        $deleted = $this->produtoService->deleteProduto($id);
        if ($deleted) {
            $this->redirect("/admin/products");
        } else {
            $this->redirect("/admin/products");
        }
    }

    protected function handleNotFound(): void
    {
        http_response_code(404);
        echo '<h1>404 - Produto Não Encontrado</h1>';
        exit();
    }
}

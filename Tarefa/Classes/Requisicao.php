<?php

namespace Tarefa\Classes;

/**
 * Lida com a requisição em si
 */
class Requisicao
{
    /**
     * Tipo de requisicao
     * Valores possiveis:
     *     listar (get sem item especificado)
     *     retornar (get para 1 item)
     *     atualizar (patch para 1 item)
     *     criar (post para 1 item)
     *     deletar (delete para 1 item)
     *     limpar (delete sem item especificado)
     */
    public $tipoRequisicao;

    public $id;
    public $dados;
    private $metodo;

    /**
     * boolean, verdadeiro se é uma requisição válida
     */
    public $valida;

    public function __construct()
    {
        $this->determinaRequisicao();
    }

    /**
     * Determina se o método usado é viável, salva no objeto
     *
     * @param string $metodo
     */
    public function setMetodo($metodo)
    {
        $metodosAceitaveis = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'];
        if (in_array($metodo, $metodosAceitaveis)) {
            $this->metodo = $metodo;
        }
    }

    /**
     * Se houver um id válido, salva no objeto
     *
     * @param string $pathInfo
     */
    public function setId($pathInfo)
    {
        $requestExplodido = explode('/', trim($pathInfo, '/'));
        if (isset($requestExplodido[0]) && !empty($requestExplodido[0])) {
            $this->id = intval($requestExplodido[0]);
        }
    }

    /**
     * Determina o tipo de requisição feita, e se ela é suportada
     */
    public function determinaRequisicao()
    {
        $this->setMetodo($_SERVER['REQUEST_METHOD']);
        $this->setId($_SERVER['PATH_INFO']);
        $this->valida = true;

        switch ($this->metodo) {
            case 'GET':
                if ($this->id) {
                    $this->tipoRequisicao = 'retornar';
                } else {
                    $this->tipoRequisicao = 'listar';
                }
                break;
            case 'POST':
                if ($this->id) {
                    $this->valida = false;
                } else {
                    $this->tipoRequisicao = 'criar';
                }
                break;
            case 'PUT':
                if ($this->id) {
                    $this->valida = false;
                    // NOTE implementar substituição?
                } else {
                    $this->valida = false;
                    // NOTE implementar substituição em massa?
                }
                break;
            case 'PATCH':
                if ($this->id) {
                    $this->tipoRequisicao = 'atualizar';
                } else {
                    $this->valida = false;
                    // NOTE implementar atualização em massa?
                }
                break;
            case 'DELETE':
                if ($this->id) {
                    $this->tipoRequisicao = 'deletar';
                } else {
                    $this->tipoRequisicao = 'limpar';
                }
                break;
            default:
                $this->valida = false;
        }

        $this->dados = json_decode(file_get_contents('php://input'), true);
    }
}
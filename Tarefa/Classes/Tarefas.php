<?php

namespace Tarefa\Classes;

use Tarefa\Classes\Banco;

class Tarefas
{
    /**
     * objeto de conexão com banco
     */
    private $banco;

    /**
     * objeto de dados da requisição
     */
    private $requisicao;

    /**
     * Dados a retornar
     */
    private $retorno;

    public function __construct($dbInfo)
    {
        $this->banco = new Banco($dbInfo);
        $this->requisicao = new Requisicao();
    }

    /**
     * Executa a ação da requisição e retorna os dados resultantes
     *
     * @return string dados encodados em JSON
     */
    public function respondeRequisicao()
    {
        if (!$this->requisicao->valida) {
            return false;
        }
        $tipoRequisicao = $this->requisicao->tipoRequisicao;
        $this->$tipoRequisicao();

        return json_encode($this->retorno);
    }

    /**
     * Formata dados vindos do banco para um formato exportável
     *
     * @param array $dados
     *
     * @return array
     */
    private function formataDados($dados)
    {
        return empty($dados) ? null : [
            'id' => $dados[0],
            'titulo' => $dados[1],
            'descricao' => $dados[2],
            'ordem' => $dados[3],
        ];
    }

    /**
     * Consulta dados de todas as tarefas no banco
     * Para a ação de get sem item especificado
     */
    private function listar()
    {
        $dados = $this->banco->query(
            'SELECT id, titulo, descricao, ordem FROM tarefas ORDER BY ordem ASC',
            true
        );
        $this->retorno = ['tarefas' => []];
        foreach ($dados as $atual) {
            $this->retorno['tarefas'][] = $this->formataDados($atual);
        }
    }

    /**
     * Consulta dados de uma tarefa especifica banco
     * Para a ação de get com item especificado.
     */
    private function retornar()
    {
        $dados = $this->banco->query(
            "SELECT id, titulo, descricao, ordem
            FROM tarefas
            WHERE id = {$this->requisicao->id}",
            true
        );

        $this->retorno = ['tarefas' => $this->formataDados($dados[0])];
    }

    /**
     * Retorna a maior ordem de tarefa existente no banco
     *
     * @return int
     */
    private function maiorOrdem()
    {
        $maiorOrdem = $this->banco->query('SELECT max(ordem) FROM tarefas ORDER BY ordem ASC', true);

        return isset($maiorOrdem[0][0]) ? $maiorOrdem[0][0] : 0;
    }

    /**
     * Atualiza dados de uma tarefa existente no banco
     * Para a ação de patch com item especificado
     */
    private function atualizar()
    {
        $updatedColumns = [];

        // reordenando ordem das tarefas de acordo com necessidade
        // TODO refatorar
        if (isset($this->requisicao->dados['ordem'])) {
            $ordemOrigem = $this->banco->query('SELECT ordem FROM tarefas WHERE id='.$this->requisicao->id, true);
            $ordemOrigem = isset($ordemOrigem[0][0]) ? $ordemOrigem[0][0] : 0;
            $ordemDestino = intval($this->requisicao->dados['ordem']);

            $maiorOrdem = $this->maiorOrdem();
            if (!$maiorOrdem) {
                $ordemDestino = 1;
            } elseif ($maiorOrdem < $ordemDestino || !$ordemDestino) {
                $ordemDestino = $maiorOrdem;
            }
            if ($ordemOrigem != $ordemDestino) {
                $updatedColumns[] = "ordem='" . $ordemDestino . "'";

                $sqlReordenação = '';
                if ($ordemOrigem < $ordemDestino) {
                    $sqlReordenação =
                        'UPDATE tarefas
                        SET ordem = ordem - 1
                        WHERE ordem > ' . $ordemOrigem . ' AND ordem <= ' .$ordemDestino
                    ;
                } elseif ($ordemOrigem > $ordemDestino) {
                    $sqlReordenação =
                        'UPDATE tarefas
                        SET ordem = ordem + 1
                        WHERE ordem < ' . $ordemOrigem . ' AND ordem >= ' .$ordemDestino
                    ;
                }
                if ($sqlReordenação) {
                    $ok = $this->banco->query($sqlReordenação, false);
                }
            }
        }

        if (isset($this->requisicao->dados['titulo'])) {
            $updatedColumns[] = "titulo='" . $this->requisicao->dados['titulo'] . "'";
        }
        if (isset($this->requisicao->dados['descricao'])) {
            $updatedColumns[] = "descricao='" . $this->requisicao->dados['descricao'] . "'";
        }

        if (empty($updatedColumns)) {
            return;
        }
        $queryUpdate = 'UPDATE tarefas SET '.implode(', ', $updatedColumns) . ' WHERE id = ' . $this->requisicao->id;


        // NOTE está sem proteção contra sql injections
        $resultado = $this->banco->query($queryUpdate);
        $resultado = false;
        $this->retorno = $resultado ? 1 : 0;
    }

    /**
     * Cria uma nova tarefa no banco
     * Para a ação de post, sem item especificado
     */
    private function criar()
    {
        $ordem = intval($this->requisicao->dados['ordem']);

        // checando se ordem passada faz sentido
        $maiorOrdem = $this->maiorOrdem();

        if (!$maiorOrdem) {
            $ordem = 1;
        } elseif ($maiorOrdem < $ordem || !$ordem) {
            $ordem = $maiorOrdem + 1;
        } else {
            // atualizando ordens para manter ordenado e sem repetição
            $this->banco->query('UPDATE tarefas SET ordem = ordem + 1 WHERE ordem >= '.$ordem, false);
        }

        // NOTE está sem proteção contra sql injections
        $createQuery = "INSERT INTO tarefas(titulo, descricao, ordem) VALUES('%s', '%s', %s)";
        $createQuery = sprintf($createQuery, $this->requisicao->dados['titulo'], $this->requisicao->dados['descricao'], $ordem);
        $resultado = $this->banco->query($createQuery);
        $this->retorno = $resultado ? 1 : 0;
    }

    /**
     * Deleta uma tarefa no banco
     * Para a ação de delete com item especificado
     */
    private function deletar()
    {
        $resultado = $this->banco->query("DELETE FROM tarefas  WHERE id={$this->requisicao->id};");
        $this->retorno = $resultado ? 1 : 0;
    }

    /**
     * Deleta todas as tarefas no banco
     * Para a ação de delete, sem item especificado
     */
    private function limpar()
    {
        $resultado = $this->banco->query("DELETE FROM tarefas  WHERE 1=1;");
        $this->retorno = $resultado ? 1 : 0;
    }
}
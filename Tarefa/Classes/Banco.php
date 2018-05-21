<?php

namespace Tarefa\Classes;

use Tarefa\Exceptions\DatabaseProblemException;

/**
 * Classe auxiliar para facilitar o acesso ao banco
 */
class Banco
{
    /**
     * Dados de conexão ao banco.
     */
    private $dbInfo;

    /**
     * SQL de criação de schema
     */
    private $sqlSchema = "CREATE SCHEMA `%s`";

    /**
     * SQL de criação de tabela
     */
    private $sqlTable =
        "CREATE TABLE `tarefas` (
            `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `titulo` VARCHAR(255) NOT NULL,
            `descricao` VARCHAR(255) NOT NULL,
            `ordem` INT NOT NULL
        );";

    public function __construct($dbInfo)
    {
        $this->setDbInfo($dbInfo);
        $this->configuraConexao();
    }

    /**
     * Seta dados de acesso ao banco
     *
     * @throws DatabaseProblemException
     */
    public function setDbInfo($dbInfo)
    {
        $this->dbInfo = $dbInfo;
        $aChecar = ['hostname', 'username', 'password', 'database'];

        $configsIndefinidas = '';
        foreach ($aChecar as $checar) {
            if (!isset($this->dbInfo[$checar]) || empty($this->dbInfo[$checar])) {
                $configsIndefinidas .= $checar." ";
            }
        }
        if (!empty($configsIndefinidas)) {
            throw new DatabaseProblemException("Configurações não encontradas: " . $configsIndefinidas);
        }
    }

    /**
     * Checa se o banco está acessível e configurado
     *
     * @throws DatabaseProblemException
     */
    private function configuraConexao()
    {
        $this->db = mysqli_connect(
            $this->dbInfo['hostname'],
            $this->dbInfo['username'],
            $this->dbInfo['password'],
            $this->dbInfo['database']
        );
        if (empty($this->db)) {
            throw new DatabaseProblemException(
                'Não foi possível conectar no banco de dados. Por favor cheque as configurações e crie schema com o seguinte comando: '
                . sprintf($this->sqlSchema, $this->dbInfo['database'])
            );
        }
        $tabelaExiste = $this->query('SELECT 1 FROM tarefas LIMIT 1');

        if (empty($tabelaExiste)) {
            $this->criaTabela();
        }
    }

    /**
     * Cria a tabela de tarefas
     *
     * @throws DatabaseProblemException
     */
    private function criaTabela()
    {
        $tabelaCriada = mysqli_query($this->db, $this->sqlTable);
        if (!$tabelaCriada) {
            throw new DatabaseProblemException(
                'Não foi possivel criar a tabela no banco.'
            );
        }
    }

    /**
     * Executa uma query no banco
     *
     * @param string $query
     *
     * @return mixed
     */
    public function query($query, $retorno = false)
    {
        $resultado = mysqli_query($this->db, $query);
        if (!$retorno) {
            return $resultado;
        }

        return mysqli_fetch_all($resultado);
    }
}
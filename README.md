# Teste de Conhecimentos - Analista Desenvolvedor

## Exercícios 1, 2 e 3
As respostas para as questoes 1, 2 e 3 se encontram nos arquivos questao1.php, questao2.php e questao3.php, e não precisam de configurações adicionais.

## Exercícios 4
A api questão 4 encontra-se no diretório Tarefa.
####### Configuração
Espera-se um banco de dados mysql em localhost com usuário 'externo' e senha 'externo'.
Criar schema no banco dados com o seguinte comando:
    "CREATE SCHEMA `tarefa` ;"
A classe Tarefa/Classes/Banco cria a tabela necessária automaticamente, só o schema precisa ser criado manualmente.
Estas configurações podem ser alteradas no arquivo Tarefas/config.php.
####### Uso
Utilizei o postman para testar, com solicitações.
Exemplos de requisições:
Tarefa/api.php (GET):
    {"tarefas":[{"id":"3","titulo":"aa","descricao":"bbb","ordem":"1"},{"id":"4","titulo":"cc","descricao":"dd","ordem":"2"}]}
Tarefa/api.php/3 (GET):
    {"tarefas":{"id":"3","titulo":"aa","descricao":"bbb","ordem":"1"}}
Tarefa/api.php (DELETE):
    1
Tarefa/api.php/1 (DELETE):
    1
Tarefa/api.php (POST):
    Usando os seguintes dados: {"titulo": "aa","descricao": "bb","ordem": 1}
    1
Tarefa/api.php/1 (PATCH):
    Usando os seguintes dados: {"titulo": "aa2","descricao": "bb2","ordem": 2}
    1
####### Notas
    Não tive tempo para fazer uma interface que permita o uso desta api na prática.

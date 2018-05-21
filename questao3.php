<?php

// questão 2, exercício de refatoração
class MyUserClass
{
    private $dbConfig;

    public function setConfig($localhost, $user, $password)
    {
        $this->dbConfig = [
            'localhost' => $localhost,
            'user' => $user,
            'password' => $password,
        ];
    }

    public function getUserList()
    {
        $dbconn = new DatabaseConnection(
            $this->dbConfig['localhost'],
            $this->dbConfig['user'],
            $this->dbConfig['password']
        );
        $sql = 'SELECT name FROM user ORDER BY name';

        return $dbconn->query($sql);
    }
}

<?php
namespace App\Databases;

class Database
{
    private $connection, $host, $port, $database, $username, $password, $table, $where, $limit, $order;
    private $select = [];

    public function __construct()
    {
        $configs = config('database');
        if ($configs['default'] == 'mysql') {
            $this->host     = $configs['drivers']['mysql']['host'];
            $this->port     = $configs['drivers']['mysql']['port'];
            $this->database = $configs['drivers']['mysql']['database'];
            $this->username = $configs['drivers']['mysql']['username'];
            $this->password = $configs['drivers']['mysql']['password'];
        }
        $this->connect();
    }

    public function connect()
    {
        try {
            $this->connection = new \PDO('mysql:host=' . $this->host . ';db=' . $this->database, $this->username, $this->password);
            $this->connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $exception) {
            die('<strong>Error:</strong> Connection failed: ' . $exception->getMessage());
        }
    }

    public function select()
    {
        $this->select = func_get_args();
        return $this;
    }

    public function from($table)
    {
        $this->table = $table;
        return $this;
    }

    public function where($where = null)
    {
        $this->where = $where;
        return $this;
    }

    public function limit($limit)
    {
        $this->limit = $limit;
        return $this;
    }

    public function order($order)
    {
        $this->order = $order;
        return $this;
    }

    public function get()
    {
        $query = 'SELECT ' . (empty($this->select) ? '*' : implode(',', $this->select));
        $query .= ' FROM ' . $this->database . '.' . $this->table;
        if (!empty($this->where)) {
            $query .= ' WHERE ' . $this->where . '';
        }
        if (!empty($this->order)) {
            $query .= ' ORDER BY (' . $this->order . ')';
        }
        if (!empty($this->limit)) {
            $query .= ' LIMIT (' . $this->limit . ')';
        }
        $statementHandler = $this->connection->query($query);
        $statementHandler->setFetchMode(\PDO::FETCH_OBJ);
        return $statementHandler->fetch();
    }

    public function trigger($query, array $params)
    {
        try {
            $this->connection->beginTransaction();
            $statementHandler = $this->connection->prepare($query);
            $statementHandler->execute($params);
            $this->connection->commit();

        } catch(Exception $e) {
            $this->connection->rollback();
            throw $e;
        }
    }

    public function getDatabase()
    {
        return $this->database;
    }
}
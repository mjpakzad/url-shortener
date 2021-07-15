<?php
namespace App\Databases;

use App\Interfaces\CRUD;

class Domain extends Database implements CRUD
{
    public function create($params)
    {
        $database   = $this->getDatabase();
        $this->trigger("INSERT INTO $database.domains(user_id, domain, status) values (:user_id, :domain, :status)", $params);
    }

    public function read($params)
    {
        $query = $this->from('domains');
        if(isset($params['domain'])) {
            return $query->where('`domain` = \'' . $params['domain'] . '\'')->get();
        }
        return $query->all();
    }

    public function update($id, $params)
    {
        $params['id']       = $id;
        $database           = $this->getDatabase();
        $this->trigger("UPDATE $database.domains SET status=:status WHERE id=:id", $params);
    }

    public function delete($id)
    {

    }
}
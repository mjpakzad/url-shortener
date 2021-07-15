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
        return $this->from('domains')->where('`domain` = \'' . $params['domain'] . '\'')->get();
    }

    public function update($id, $params)
    {

    }

    public function delete($id)
    {

    }
}
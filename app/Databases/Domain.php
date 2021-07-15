<?php
namespace App\Databases;

use App\Interfaces\CRUD;

class Domain extends Database implements CRUD
{
    public function create($params)
    {

    }

    public function read($params)
    {
        return $this->from('domains')->where('`domain` = ' . '\'' . $params['domain'] . '\'')->get();
    }

    public function update($id, $params)
    {

    }

    public function delete($id)
    {

    }
}
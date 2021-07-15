<?php
namespace App\Databases;

use App\Interfaces\CRUD;

class Link extends Database implements CRUD
{
    public function create($params)
    {
        $database   = $this->getDatabase();
        $this->trigger("INSERT INTO $database.links(user_id, domain_id, short_url, long_url) values (:user_id, :domain_id, :short, :long)", $params);
    }

    public function read($params)
    {
        $query = $this->from('links');
        if(isset($params['short'])) {
            return $query->where('`short_url` = \'' . $params['short'] . '\'')->get();
        }
        return $query->all();
    }

    public function update($id, $params)
    {
        $params['id']       = $id;
        $database           = $this->getDatabase();
        $this->trigger("UPDATE $database.links SET short_url=:short_url WHERE id=:id", $params);
    }

    public function delete($id)
    {
        $params['id']       = $id;
        $database           = $this->getDatabase();
        $this->trigger("DELETE FROM $database.links WHERE id=:id", $params);
    }
}
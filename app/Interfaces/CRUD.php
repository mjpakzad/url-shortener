<?php
namespace App\Interfaces;

interface CRUD
{
    public function create($params);

    public function read($params);

    public function update($id, $params);

    public function delete($id);
}
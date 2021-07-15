<?php
namespace App\Databases;

use App\Interfaces\CRUD;

class User extends Database implements CRUD
{
    public function create($params)
    {
        $params['password'] = $this->hashPassword($params['password']);
        $database           = $this->getDatabase();
        $this->trigger("INSERT INTO $database.users(name, email, password) values (:name, :email, :password)", $params);
    }

    public function read($params)
    {
        $where = '';
        if(isset($params['id'])) {
            $where = '`id` = ' . '\'' . $params['id'] . '\'';
        }
        if(isset($params['email'])) {
            $where = '`email` = ' . '\'' . $params['email'] . '\'';
        }
        if (isset($params['token'])) {
            $where = '`token` = ' . '\'' . $params['token'] . '\'';
        }
        return $this->from('users')->where($where)->get();
    }

    public function update($id, $params)
    {
        $params['token']    = $this->generateToken();
        $params['id']       = $id;
        $database           = $this->getDatabase();
        $this->trigger("UPDATE $database.users SET token=:token WHERE id=:id", $params);
    }

    public function delete($id)
    {

    }

    public function hashPassword($password)
    {
        $options = [
            'cost' => 12,
        ];
        return password_hash($password, PASSWORD_BCRYPT, $options);
    }

    protected function generateToken()
    {
        return sha1(mt_rand(1, 99999) . 'Mj.Pakzad');
    }
}
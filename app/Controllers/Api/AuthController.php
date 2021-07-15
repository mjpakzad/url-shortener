<?php
namespace App\Controllers\Api;

use App\Controllers\Controller;
use App\Databases\User;

class AuthController extends Controller
{
    public function register()
    {
        // All fields are required,.
        if(!isset($_POST['name'], $_POST['email'], $_POST['password'])) {
            $this->header(403, 'Your request body must contain name, email and password!');
        }

        // Email must be valid.
        if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
            $this->header(403, 'The email you entered in invalid.');
        }
        // Email must be unique.
        $userObject = new User();
        $user = $userObject->read($_POST);
        if(!!$user) {
            $this->header(403, 'This email already registered.');
        }

        // Register user.
        $user = new User();
        $user->create([
            'name'      => $_POST['name'],
            'email'     => $_POST['email'],
            'password'  => $_POST['password'],
        ]);
        $this->header(200, 'Thank you ' . $_POST['name'] . ', You registered successfully.');
    }

    public function login()
    {
        // All fields are required,.
        if(!isset($_POST['email'], $_POST['password'])) {
            $this->header(403, 'Your request body must contain email and password!');
        }
        $userObject = new User();
        $user = $userObject->read($_POST);
        if(!$user) {
            $this->header(404, 'This does not exist.');
        }
        if(!password_verify($_POST['password'], $user->password)) {
            $this->header(403, 'This email and password does not belong to anyone in our database.');
        }
        $userObject->update($user->id, []);
        $user = $userObject->read($_POST);
        $this->header(200, 'You can use this token: ' . $user->token);
    }
}
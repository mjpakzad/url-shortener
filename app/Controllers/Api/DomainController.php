<?php
namespace App\Controllers\Api;

use App\Controllers\Controller;
use App\Databases\Domain;
use App\Databases\User;

class DomainController extends Controller
{
    public function index()
    {
        
    }

    public function store()
    {
        // All fields are required,.
        if(!isset($_POST['domain'], $_POST['token'])) {
            $this->header(403, 'Your request body must contain domain and token!');
        }

        // Email must be valid.
        if (!filter_var($_POST["domain"], FILTER_VALIDATE_URL)) {
            $this->header(403, 'The url of domain you entered in invalid.');
        }

        // Domain must be unique.
        $domainObject = new Domain();
        $domain = $domainObject->read($_POST);
        if(!!$domain) {
            $this->header(403, 'This domain already added.');
        }

        $userObject = new User();
        $user = $userObject->read(['token' => $_POST['token']]);
        if(!!$user) {
            $this->header(403, 'The token you have been inserted is not valid!');
        }

        // Add domain.
        $domain = new Domain();
        $domain->create([
            'user_id'   => 1,
            'domain'    => $_POST['domain'],
            'status'    => true,
        ]);
        $this->header(200, 'The domain added successfully.');
    }

    public function update()
    {
        
    }
}
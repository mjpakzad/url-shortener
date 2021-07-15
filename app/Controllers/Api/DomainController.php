<?php
namespace App\Controllers\Api;

use App\Controllers\Controller;
use App\Databases\Domain;
use App\Databases\User;

class DomainController extends Controller
{
    public function index()
    {
        // All fields are required.
        if(!isset($_GET['token'])) {
            $this->header(403, 'Your request body must contain token!');
        }

        $userObject = new User();
        $user = $userObject->read(['token' => $_GET['token']]);
        if(!$user) {
            $this->header(403, 'The token you have been inserted is not valid!');
        }

        if(!$user->is_admin) {
            $this->header(403, 'This user does not have permission to access this action.');
        }

        $domainObject = new Domain();
        $domains = $domainObject->read([]);
        $this->header(200, $domains);
    }

    public function store()
    {
        // All fields are required,.
        if(!isset($_POST['domain'], $_POST['token'])) {
            $this->header(403, 'Your request body must contain domain and token!');
        }

        // URL must be valid.
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
        if(!$user) {
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
        // All fields are required,.
        if(!isset($_GET['domain'], $_GET['token'], $_GET['status'])) {
            $this->header(403, 'Your request body must contain domain and token and status!');
        }

        // URL must be valid.
        if (!filter_var($_GET["domain"], FILTER_VALIDATE_URL)) {
            $this->header(403, 'The url of domain you entered in invalid.');
        }

        // Domain must be unique.
        $domainObject = new Domain();
        $domain = $domainObject->read($_GET);
        if(!$domain) {
            $this->header(403, 'This domain does not exist.');
        }

        $userObject = new User();
        $user = $userObject->read(['token' => $_GET['token']]);
        if(!$user) {
            $this->header(403, 'The token you have been inserted is not valid!');
        }

        // Change status.
        $domainObject = new Domain();
        $domainObject->update($domain->id, [
            'status'    => $_GET['status'] ? true : false,
        ]);
        $this->header(200, 'The domain status has been changed successfully.');
    }
}
<?php
namespace App\Controllers\Api;

use App\Databases\Domain;
use App\Databases\Link;
use App\Databases\User;

class LinkController extends \App\Controllers\Controller
{
    public function index()
    {
        $linkObject = new Link();
        $links = $linkObject->read([]);
        $this->header(200, $links);
    }

    public function store()
    {
        // All fields are required,.
        if(!isset($_POST['domain'], $_POST['token'], $_POST['short'], $_POST['long'])) {
            $this->header(403, 'Your request body must contain domain, token, short slug and long url!');
        }

        // URL must be valid.
        if (!filter_var($_POST["domain"], FILTER_VALIDATE_URL)) {
            $this->header(403, 'The url of domain you entered in invalid.');
        }

        // Domain must be unique.
        $domainObject = new Domain();
        $domain = $domainObject->read($_POST);
        if(!$domain) {
            $this->header(403, 'The domain you entered does not exist.');
        }

        $userObject = new User();
        $user = $userObject->read(['token' => $_POST['token']]);
        if(!$user) {
            $this->header(403, 'The token you have been inserted is not valid!');
        }

        // Add Link.
        $link = new Link();
        $link->create([
            'user_id'       => $user->id,
            'domain_id'     => $domain->id,
            'short'         => $_POST['short'],
            'long'          => $_POST['long'],
        ]);
        $this->header(200, 'The link added successfully.');
    }

    public function update()
    {
        // All fields are required,.
        if(!isset($_GET['short'], $_GET['token'], $_GET['new-short'])) {
            $this->header(403, 'Your request body must contain short url and new short and token!');
        }

        // Link must exist.
        $linkObject = new Link();
        $link = $linkObject->read($_GET);
        if(!$link) {
            $this->header(403, 'This link does not exist.');
        }

        $userObject = new User();
        $user = $userObject->read(['token' => $_GET['token']]);
        if(!$user) {
            $this->header(403, 'The token you have been inserted is not valid!');
        }

        if($link->user_id != $user->id) {
            $this->header(403, 'This link is not belongs to you');
        }

        // Change short url.
        $linkObject = new Link();
        $linkObject->update($link->id, [
            'short_url' => $_GET['new-short'],
        ]);
        $this->header(200, 'The link short url has been updated successfully.');
    }

    public function destroy()
    {
        // All fields are required,.
        if(!isset($_GET['short'], $_GET['token'])) {
            $this->header(403, 'Your request body must contain short url and token!');
        }

        // Link must exist.
        $linkObject = new Link();
        $link = $linkObject->read($_GET);
        if(!$link) {
            $this->header(403, 'This link does not exist.');
        }

        $userObject = new User();
        $user = $userObject->read(['token' => $_GET['token']]);
        if(!$user) {
            $this->header(403, 'The token you have been inserted is not valid!');
        }

        if($link->user_id != $user->id) {
            $this->header(403, 'This link is not belongs to you');
        }

        // Delete link.
        $linkObject = new Link();
        $linkObject->delete($link->id);
        $this->header(200, 'The link has been deleted successfully.');
    }

    public function show($short)
    {
        // Link must exist.
        $linkObject = new Link();
        $link = $linkObject->read(['short' => $short]);
        if(!$link) {
            $this->header(403, 'This link does not exist.');
        }
        $this->header(301, $link->long_url);
    }
}
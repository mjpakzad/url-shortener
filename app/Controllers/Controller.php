<?php
namespace App\Controllers;

class Controller
{
    public function notFound()
    {
        $this->header(404, 'Not Found!');
    }

    protected function header($code, $body = '')
    {
        http_response_code($code);
        echo json_encode(['status' => $code, 'body' => $body]);
        exit();
    }
}
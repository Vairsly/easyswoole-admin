<?php

namespace App\Base;

use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\Template\Render;

abstract class BaseController extends Controller
{
    public function index()
    {
        $this->actionNotFound('index');
    }

    public function render(string $template, array $data = [])
    {
        $this->response()->write(Render::getInstance()->render($template, $data));
    }

    public function show404()
    {
        $this->render('default.404');
    }

    public function writeJson($statusCode = 200, $msg = null, $data = null)
    {
        if (!$this->response()->isEndResponse()) {
            $result = [
                "code" => $statusCode,
                "msg"  => $msg,
                "data" => $data
            ];
            $this->response()->write(json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            $this->response()->withHeader('Content-type', 'application/json;charset=utf-8');
            return true;
        } else {
            return false;
        }
    }
}

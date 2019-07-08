<?php
namespace App\HttpController;

use EasySwoole\Http\AbstractInterface\AbstractRouter;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use FastRoute\RouteCollector;
use EasySwoole\template\Render;

class Router extends AbstractRouter
{
    public function initialize(RouteCollector $routes)
    {
        // 未找到路由对应的方法
        $this->setMethodNotAllowCallBack(function (Request $request, Response $response) {
            var_dump(111);
            $response->write(Render::getInstance()->render('default.404'));
            $response->withStatus(404);
        });

        // 未找到路由匹配
        $this->setRouterNotFoundCallBack(function (Request $request, Response $response) {
            var_dump(222);
            $response->write(Render::getInstance()->render('default.404'));
            $response->withStatus(404);
        });


        $routes->addGroup('/admin', function (RouteCollector $route) {
            $route->get('/','/Admin/Index');
            $route->get('/index_context','/Admin/Index/indexContext');

            // 管理员
            $route->addGroup('/auth',function(RouteCollector $r){
                $r->get('','/Admin/Auth/User');
                $r->post('/get_all','/Admin/Auth/User/getAll');

                $r->get('/add','/Admin/Auth/User/add');
                $r->post('/add','/Admin/Auth/User/addData');

                $r->post('/set/{id:\d+}','/Admin/Auth/User/set');
                $r->post('/del/{id:\d+}','/Admin/Auth/User/del');
            });

            // 角色
            $route->addGroup('/role',function(RouteCollector $r){
                $r->get('','/Admin/Auth/Role');
                $r->post('/get_all','/Admin/Auth/Role/getAll');

                $r->get('/add','/Admin/Auth/Role/add');
                $r->post('/add','/Admin/Auth/Role/addData');

                $r->get('/edit/{id:\d+}','/Admin/Auth/Role/edit');
                $r->post('/edit/{id:\d+}','/Admin/Auth/Role/editData');

                $r->get('/edit_rule/{id:\d+}','/Admin/Auth/Role/editRule');
                $r->post('/edit_rule/{id:\d+}','/Admin/Auth/Role/editRuleData');
                $r->post('/set/{id:\d+}','/Admin/Auth/Role/set');

                $r->post('/del/{id:\d+}','/Admin/Auth/Role/del');
            });

            // 权限
            $route->addGroup('/rule',function(RouteCollector $r){
                $r->addRoute(['GET'], '', '/Admin/Auth/Rule');
                $r->post('/get_all','/Admin/Auth/Rule/getAll');

                $r->get('/add','/Admin/Auth/Rule/add');
                $r->post('/add','/Admin/Auth/Rule/addData');

                // 添加子节点
                $r->get('/add/{id:\d+}','/Admin/Auth/Rule/addChild');
                $r->post('/add/{id:\d+}','/Admin/Auth/Rule/addChildData');

                $r->get('/edit/{id:\d+}','/Admin/Auth/Rule/edit');
                $r->post('/edit/{id:\d+}','/Admin/Auth/Rule/editData');
                $r->post('/set/{id:\d+}','/Admin/Auth/Rule/set');

                $r->post('/del/{id:\d+}','/Admin/Auth/Rule/del');
            });
        });
    }
}

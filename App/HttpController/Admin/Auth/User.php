<?php

namespace App\HttpController\Admin\Auth;

use App\Base\AdminController;
use App\Utility\Message\Status;

use App\Utility\Log\Log;

use App\Model\AdminAuth as AuthModel;
use App\Model\AdminRole as RoleModel;

use App\Common\AppFunc;

use EasySwoole\EasySwoole\Config;

class User extends AdminController
{
	public function index()
	{
		$this->render('admin.auth.user');
	}

	// 获取用户数
	public function getAll()
	{
		$data = $this->getPage();

		$auth_data = AuthModel::getInstance()
						->findAll($data['page'],$data['limit']);

		$auth_count = AuthModel::getInstance()->where('deleted',0,'=')->count();
		$data = ['code'=>Status::CODE_OK,'count'=>$auth_count,'data'=>$auth_data];
		$this->dataJson($data);
		return ;
	}

	private function fieldInfo()
	{
		$request = $this->request();
		$data = $request->getRequestParam('uname', 'pwd', 'status', 'display_name', 'role_id');

		$validate = new \EasySwoole\Validate\Validate();
		$validate->addColumn('uname')->required();
		$validate->addColumn('pwd')->required();
		$validate->addColumn('status')->required();
		$validate->addColumn('display_name')->required();
		$validate->addColumn('role_id')->required();

		if(!$validate->validate($data)) {
			$this->writeJson(Status::CODE_ERR,'请勿乱操作');
			return ;
		}

		return $data;
	}

	public function add()
	{
		$role_data = RoleModel::getInstance()->get(null, 'id,name');

		$this->render('admin.auth.userAdd',['role_data'=>$role_data]);
	}

	public function addData()
	{
		$data = $this->fieldInfo();
		if(!$data) {
			return ;
		}
		$data['encry'] = AppFunc::getRandomStr(6);
		$encry = Config::getInstance()->getConf('app.verify');
		$data['pwd'] = md5($data['encry'] . $data['pwd'] . $encry);
		if(AuthModel::getInstance()->insert($data)){
			$this->writeJson(Status::CODE_OK);
		} else {
			$this->writeJson(Status::CODE_ERR,'添加失败');
			Log::getInstance()->error( "user--addData:" . json_encode($data, JSON_UNESCAPED_UNICODE) . "添加失败");

		}

		return ;
	}

	// 多字段修改
	public function save()
	{
		return ;
	}

	// 单字段修改
	public function set()
	{
		$request = $this->request();
		$data = $request->getRequestParam('id','key','value');
		$validate = new \EasySwoole\Validate\Validate();

		$validate->addColumn('key')->required()->func(function($params, $key) {
		    return $params instanceof \EasySwoole\Spl\SplArray && $key == 'key' && in_array($params[$key], ['display_name','status']);
		}, '请勿乱操作');

		$validate->addColumn('id')->required();
		$validate->addColumn('value')->required();

		if(!$validate->validate($data)) {
			$this->writeJson(Status::CODE_ERR,'请勿乱操作');
			return ;
		}

		$bool = AuthModel::getInstance()->where('id',$data['id'],'=')
								->setValue($data['key'],$data['value']);

		if($bool) {
			$this->writeJson(Status::CODE_OK,'');
		} else {
			$this->writeJson(Status::CODE_ERR,'设置失败');
			Log::getInstance()->error("user--set:" .  json_encode($data, JSON_UNESCAPED_UNICODE) . "没有设置失败");
		}
	}

	public function del()
	{
		$request = $this->request();
		$id = $request->getRequestParam('id');
		$bool =  AuthModel::getInstance()->delId($id);
		if($bool) {
			$this->writeJson(Status::CODE_OK,'');
		} else {
			$this->writeJson(Status::CODE_ERR,'删除失败');
			Log::getInstance()->error("user--del:" .  $id . "没有删除失败");
		}
	}
}

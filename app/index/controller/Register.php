<?php

namespace app\index\controller;
use think\Db;
class Register extends IndexBase
{
	public function index(){
		$count = Db::name('register')->count('id');
		
		return $this->fetch('',['count'=>$count+32564]);

	}

	public function doRegisterData(){

		$data = $this->param['jsonData'];
		$data['create_time'] = time();
		//处理报名信息
		if(Db::name('register')->insert($data)){
			return $this->apiReturn(1,'提交成功');
		}else{
			return $this->apiReturn(0,'提交成功');

		}
	}

	public function cjcx(){
		return $this->fetch();
	}
	
	public function kemu(){
		return $this->fetch();
		
	}

}

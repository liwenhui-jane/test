<?php
declare (strict_types = 1);

namespace app\admin\controller;
use think\facade\View;
use think\facade\Session;
use app\BaseController;
use app\common\model\User as UserModel;
class Index extends BaseController
{
    public function index()
    {
        return View::fetch();
    }
    public function login()
    {
        return View::fetch();
    }
    public function doLogin()
    {
        //获取登录信息
        $post_data = $this->request->post();
        if(!captcha_check($post_data['captcha'])){
            return $this->error('验证码错误');
        };
        //获取用户登录信息
        $userinfo = UserModel::where('username',$post_data['username'])->where('status',6)->find();
        if(empty($userinfo)){
            return $this->error('账号不存在或输入用户名错误');
        }
        if($userinfo->getData('password_hash')!== md5($post_data['password'].$userinfo->getData('password_salt'))){
            return $this->error('登录密码输入错误');
        }
        //将登录成功的用户信息以adminuser索引名放置到session中
        Session::set('adminuser',$userinfo);

        //显示成功信息并跳转到后台管理首页
        //return $this->success('登录成功','Index/index');
        return redirect("/admin/index/index");
    }
    public function logout(){
        Session::clear();
        return redirect("/admin/index/login");
    }
}

<?php
declare (strict_types = 1);

namespace app\home\controller;
use app\BaseController;
use think\facade\View;
use think\Request;
use think\facade\Session;
use app\common\model\User;
use app\common\model\Shop;
use app\common\model\Member;
use app\common\model\Category;
use app\common\model\Product;
use app\common\model\Orders;
use app\common\model\Payment;
class Index extends BaseController
{
    public function index()
    {
        $shop = Session::get('shopinfo');
        $categoryList = Category::where('shop_id',$shop->id)->select();
        foreach($categoryList as $category){
            $category->productList = Product::where('category_id',$category->id)->select();
        }
        //dump($categoryList);
        View::assign('volist',$categoryList);
        return View::fetch();
    }
    public function login()
    {
        $shopList = Shop::where('status',1)->select();
        View::assign("shopList",$shopList);
        return View::fetch();
    }
    public function doLogin(Request $request)
    {
        $data = $request->post();
        if(!captcha_check($data['captcha'])){
            $this->error('验证码错误');
        }
        if($data['shop_id']<=0){
            return $this->error('请选择店铺！');
        }
        $userinfo = User::where('username',$data['username'])->find();
        if(empty($userinfo)){
            $this->error('该用户不存在或用户名错误');
        }
        if($userinfo->getData('password_hash')!==md5($data['password_hash'].$userinfo->getData('password_salt'))){
            return $this->error('登录密码输入错误');
        }
        Session::set('homeuser',$userinfo);
        Session::set('shopinfo',Shop::find($data['shop_id']));
        return redirect('/home/index/index');

    }
    public function logout()
    {
        Session::clear();
        return redirect("/home/index/login");
    }
    public function list()
    {
        $shop = Session::get('shopinfo');
        $data = Orders::where('shop_id',$shop->id)->paginate(['list_rows'=>12,'query'=>request()->param()]);
        $ordersList = $data->items();
        $page = $data->render();
        foreach($ordersList as $order){
            $order->membername = Member::where('id',$order->member_id)->value('nickname');
            $order->username = User::where('id',$order->user_id)->value('nickname');
            $order->payment = Payment::where('order_id',$order->id)->find();
        }
        //dump($ordersList);
        View::assign('ordersList',$ordersList);
        View::assign('page',$page);
        return View::fetch();
    }

}

<?php
declare (strict_types = 1);

namespace app\admin\controller;

use app\BaseController;
use think\Request;
use think\facade\View;
use app\common\model\Shop as ShopModel;
class Shop extends BaseController
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index(Request $request)
    {
        $shopModel = new ShopModel;
        $shopModel = $shopModel->where('status','<',9);
        $keyword = $request->get('keyword');
        if($keyword){
            $shopModel = $shopModel->where('name','like',"%{$keyword}%");
        }
        $data = $shopModel->paginate(['list_rows'=>2,'query'=>$request->param()]);
        $shopList = $data->items();
        $page = $data->render();
        View::assign('volist',$shopList);
        View::assign('page',$page);
        return View::fetch();
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        return View::fetch();
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        // 使用验证器验证上传的文件(大小单位b，文件后缀)
        try{
            // 获取表单上传文件
            $file1 = request()->file('cover_pic');
            validate([
                'file'=>[
                    'fileSize' => 1024000,
                    'fileExt' => 'gif,jpg,png'
                ]])->check(['file'=>$file1]);
            //上传到本地服务器
            $cover_pic = \think\facade\Filesystem::disk('shop')->putFile( '', $file1,function(){
            return md5((string)microtime(true));});
            //dump($cover_pic);
            $file2 = request()->file('banner_pic');
            validate(['file'=>['fileSize' => 1024000,'fileExt' => 'gif,jpg,png']])
                ->check(['file'=>$file2]);
            $banner_pic = \think\facade\Filesystem::disk('shop')->putFile('',$file2,function(){
                return md5((string)microtime(true));});
            //dump($banner_pic);
            $data = $request->post(['name','address','phone']);
            $data['cover_pic'] = $cover_pic;
            $data['banner_pic'] = $banner_pic;
            $data['status'] = 1;
            $data['create_at'] = date('Y-m-d H:i:s');
            $data['update_at'] = date('Y-m-d H:i:s');
            $shop = new ShopModel;
            if($shop->save($data)){
                return $this->success('添加成功','/admin/shop');
            }else{
                return $this->error('上传失败');
            }
        }catch(Exception $e){
            return $this->error($e->getMessage());
        }
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        //
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        $shop = new ShopModel;
        $data = $shop->find($id);
        View::assign('shop',$data);
        return View::fetch();
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->put(['name','phone','address','status']);
        $shop =  ShopModel::find($id);
        if($shop->save($data)){
            $this->success('修改成功','/admin/shop');
        }else{
            $this->error('修改失败');
        }
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        $shop = ShopModel::find($id);
        $res= $shop->save(['status'=>9,'update_at'=>date('Y-m-d H:i:s')]);
        if($res){
            return ['error'=>'yes'];
        }else{
            return ['error'=>'no'];
        }
    }
}

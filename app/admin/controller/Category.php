<?php
declare (strict_types = 1);

namespace app\admin\controller;

use app\BaseController;
use think\Request;
use think\facade\View;
use app\common\model\Category as CategoryModel;
use app\common\model\Shop;
class Category extends BaseController
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index(Request $request)
    {
        $categoryModel = new CategoryModel;
        $categoryModel = $categoryModel->where('status','<',9);
        $keyword = $request->get('keyword');
        if($keyword){
            $categoryModel = $categoryModel->where('name','like',"%{$keyword}%");
        }
        $data = $categoryModel->paginate(['list_rows'=>3,'query'=>$request->param()]);
        $categoryList = $data->items();
        $page = $data->render();
        foreach($categoryList as $category){
            $category->shop = Shop::where('id',$category->shop_id)->value('name');
        }
        //dump($categoryList);
        View::assign('volist',$categoryList);
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
        $shopList = Shop::where('status','<',9)->select();
        View::assign('volist',$shopList);
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
        $data = $request->post(['shop_id','name']);
        $data['status'] = 1;
        $data['create_at'] = date('Y-m-d H:i:s');
        $data['update_at'] = date('Y-m-d H:i:s');
        $category = new CategoryModel;
        if($category->save($data)){
            $this->success('添加成功','/admin/category');
        }else{
            $this->error('添加失败');
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
        $data = CategoryModel::where('status','<',9)
            ->where('shop_id',$id)
            ->column('id,name');
        return json(['data'=>$data]);
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        $category = CategoryModel::where('id',$id)->find();
        $shopList = Shop::where('status','<',9)->select();
        View::assign('volist',$shopList);
        View::assign('category',$category);
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
        $data = $request->put(['shop_id','name','status']);
        $data['update_at'] = date('Y-m-d H:i:s');
        $category = CategoryModel::find($id);
        if($category->save($data)){
            return $this->success('修改成功','/admin/category');
        }else{
            return $this->error('修改失败');
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
        $category = CategoryModel::find($id);
        $res = $category->save(['status'=>9,'update_at'=>date('Y-m-d H:i:s')]);
        if($res){
            return ['error'=>'yes'];
        }else{
            return ['error'=>'no'];
        }
    }
}

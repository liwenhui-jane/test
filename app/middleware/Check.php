<?php
declare (strict_types = 1);

namespace app\middleware;

class Check
{
    /**
     * 处理请求
     *
     * @param \think\Request $request
     * @param \Closure       $next
     * @return Response
     */
    public function handle($request, \Closure $next)
    {
        //var_dump(app('http')->getName());
        //var_dump($request->action());
        //var_dump($request->controller());
        //判断是否访问的后台应用
        if(app('http')->getName()=='admin'){
            if(!in_array($request->action(),['login','doLogin'])){
                //判断后台是否登录
                if(!session('?adminuser')){
                    return redirect('/admin/index/login');
                }
            }
        }
        //判断是否访问的前台应用
        if(app('http')->getName()=='home'){
            if(!in_array($request->action(),['login','doLogin'])){
                //判断是否登录
                if(!session('?homeuser')){
                    return redirect('/home/index/login');
                }
            }
        }
        return $next($request);
    }
}

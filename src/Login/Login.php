<?php

/**
 * composer tool class
 *
 * Author: spencerRao
 * Date: 2018/4/14
 * function:quick login interface about wx mini programs
 */

namespace Login;

use Login\userTable;

class Login{

    private $param;

    public function __construct($param)
    {
         $this->param = $param;
    }

    /**
     * @param $appid
     * @param $appsecret
     * @return mixed|void
     * @throws \Exception
     * @function  获取用户的openid
     */
    public function getUserinfo($appid='',$appsecret='')
    {
       //得到用户的code
        $code = $this->param['config']['code']??'';
        if(!$code){
            return $this->returnError("登录失败，缺少code参数。");
        }

        $appid = ($appid)?:($this->param['config']['appid']??'');
        $appsecret = ($appsecret)?:($this->param['config']['appsecret']??'');
        if(!$appid || !$appsecret)
        {
            return $this->returnError("缺少参数appid或appsecret");
        }

        //微信接口请求获取个人信息
        $url = "https://api.weixin.qq.com/sns/jscode2session?appid=$appid&secret=$appsecret&js_code=$code&grant_type=authorization_code";
        $return = json_decode($this->curlApi($url),true);
        if(!isset($return['openid']))
        {
            return $this->returnError($return['errmsg']);
        }
        return $return;
    }

    //保存用户的信息
    public function saveUserinfo()
    {

        $conninfo = $this->param['conn']??[];//连接数据库信息
        $tableinfo = $this->param['tableinfo']??[];//表格内容
        $configinfo = $this->param['config']??[];//配置信息

        $getuser = $this->getUserinfo();

        //获取用户的openi

        $openid = $getuser['openid']??'';
        if(!$openid)
            ob_clean();
        if(!count($conninfo) || !count($tableinfo))
            return $this->returnSuccess();
//        存在数据库信息，进行数据的保存、
        $user = new userTable($conninfo['host'],$conninfo['dbname'],$conninfo['username'],$conninfo['pass']);
//        因为openid的唯一性，所以先判断是否存在该用户
        $judge = '';
        if($openid)
            $judge = ['openid'=>$openid];
        $uid = ($judge)?$user->judge($tableinfo['tablename'],$judge):'';
        if(!$uid)
        {
//            进行用户的新增
            $param = $tableinfo['param'];
            $param['openid'] = $openid;
            $new = $user->insert($tableinfo['tablename'],$param);
            $uid = $new['uid'];
        }
        $info = $user->select($tableinfo['tablename'],$uid);
        return $this->returnSuccess($info);
    }

    /**
     * @param $url
     * @param $xml
     * @return mixed
     * @throws \Exception
     * funct：接口请求
     */
    public function curlApi($url, $jsoncont=[],$type='')
    {
        $ch = curl_init();
        //设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);//严格校验
        //设置header
        curl_setopt($ch, CURLOPT_HEADER, false);
        //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //post提交方式
        if($type=='post'){
            curl_setopt($ch, CURLOPT_POST, true);
            //post 提交参数
            curl_setopt($ch,CURLOPT_POSTFIELDS,$jsoncont);
        }
        //运行curl
        $data = curl_exec($ch);
        //返回结果
        if ($data) {
            curl_close($ch);
            return $data;
        } else {
            $error = curl_errno($ch);
            curl_close($ch);
            throw  new \Exception('curl出错');
        }
    }


    //返回成功
    public function returnSuccess($data='')
    {
        return json_encode(['code'=>200,'data'=>$data]);
    }

    //返回失败
    public function returnError($data='',$code= 201)
    {
        return json_encode(['code'=>$code,'data'=>$data]);
    }

}
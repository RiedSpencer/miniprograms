# miniprograms
quick install usually login api interface about mini programs

how to use ?

1.composer require miniprogram/login 1.0.2

2.include 'vendor/autoload.php'

3.use Login\Login

4.describe the param

$param['conn'] = [
    'host'=>'',
    'dbname'=>'',
    'username'=>'',
    'pass'=>''
];

$param['tableinfo'] = [
    'tablename'=>'',
    'param' => [
        'name'=>date('y-m-d H:i:s',time()),//需要添加到表格的参数
    ]
];

$param['config'] = [
	'appid'=>'',//小程序appid
	'appsecret'=>''//小程序app密钥
];

5、construct
	
	$login = new Login($param);

6、if u just want the openid,like this:
		$return = $login->getUserinfo();
		
	if u want save the userinfo,like this:
	    $return = $login->saveUserinfo();



ps: the table primary_key is uid,and u need create a filed named openid


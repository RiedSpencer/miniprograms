# miniprograms
quick install usually login api interface about mini programs

how to use ?
1.composer require miniprogram/login 1.0.2
2.include 'vendor/autoload.php'
3.use Login\Login
4.
$pdo = new Login($host,$dbname,$username,$pass);
$return = $pdo->insert($tablename);


ps: the table primary_key is uid,$return is ['affected'=>$rowcount,'uid'=>$uid]

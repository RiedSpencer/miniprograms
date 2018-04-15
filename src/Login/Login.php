<?php

/**
 * composer tool class
 *
 * Author: spencerRao
 * Date: 2018/4/14
 * function:quick login interface about wx mini programs
 */

namespace login;

class Login{

    private $pdo;

    /**
     * Login constructor.
     *
     * create a pdo
     */
    public function __construct($host,$dbname,$tablename,$pass)
    {
        $dsn = "mysql:host=$host;dbname=$dbname";
        $this->pdo = new \PDO($dsn,$tablename,$pass);
    }

    /**
     * @param $tablename
     * @return array
     * @funct insert or update userinfo
     */
    public function insert($tablename){
        $param = (isset($_GET))?$_GET:(isset($_POST)?$_POST:[]);

        $uid = $this->judge($tablename,$param);
        $rowcount = 0;
        $pdo = $this->pdo;
        $condition = $this->pack($param);
        if(!$uid){
            //进行insert操作
            $sql = 'INSERT INTO '.$tablename.' SET '.$condition;
            $sth = $pdo->prepare($sql);
            $sth = $this->bind($sth,$param);
            $sth->execute();
            $uid = $pdo->lastInsertId();
            $rowcount = $sth->rowCount();
        }
        return ['affected'=>$rowcount,'uid'=>$uid];
    }

    /**
     * @param $tablename
     * @param $param
     * @return int
     * @funct judge the user exist
     */
    public function judge($tablename,$param)
    {
        //得到数据表字段
        $where = $this->pack($param,'where');
        $pdo = $this->pdo;
        $sth = $pdo->prepare('SELECT uid from '.$tablename.' where '.$where);
        $sth = $this->bind($sth,$param);
        $sth->execute();
        $res = $sth->fetch(\PDO::FETCH_ASSOC);
        return $res['uid'];
    }


    /**
     * @param $param
     * @return string
     * @funct package the param
     */
    public function pack($param,$type='')
    {
        $p = array_flip($param);
        $where = '';

        if(count($p))
        {
            $state = ($type == 'where')?implode(' = ? AND ',$p):implode(' = ? , ',$p);
            $where = $state." = ?";
        }
        return $where;
    }

    /**
     * @param $sth
     * @param $param
     * @return mixed
     * @funct bind the value
     */
    public function bind($sth,$param)
    {
        $i = 1;
        foreach ($param as $k => $v)
        {
            $sth->bindValue($i,$v);
            ++$i;
        }
        return $sth;
    }


}
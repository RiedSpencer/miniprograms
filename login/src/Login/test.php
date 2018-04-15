<?php

/**
 * test class
 *
 * Author: spencerRao
 * Date: 2018/4/14
 * function:test login interface
 */

require 'Login.php';

$pdo = new \login\Login('127.0.0.1','composer','root','');
$_GET['name'] = 'spec';
$_GET['age'] = 19;
$return = $pdo->insert('user');
var_dump($return);die;
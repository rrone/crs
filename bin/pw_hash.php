<?php
/**
 * Created by PhpStorm.
 * User: rick
 * Date: 10/31/17
 * Time: 07:40
 */

$user= 'Admin Section 1';
$pw = 'Admin Section 1';


echo password_hash($pw, PASSWORD_BCRYPT);

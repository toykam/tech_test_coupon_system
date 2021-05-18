<?php

    require_once 'Medoo.php';

    use Medoo\Medoo;


    $conn = new Medoo([
        'type'      => 'mysql',
        'host'      => 'localhost',
        'database'  => 'tech_test',
        'username'  => 'root',
        'password'  => ''
    ]);
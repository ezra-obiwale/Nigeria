<?php

require 'bootstrap.php';
error_reporting(E_ALL);
//$result = MongoData::create('countries', [
//            'name' => 'Nigeria',
//            'continent' => 'Africa'
//        ]);
//MongoData::update('countries', '589c16f4573e0c9c1c000029/stats/gender/1', 'female');
echo '<pre>';
$result = MongoData::delete('countries', '589c16f4573e0c9c1c000029/stats/gender/0');
var_dump($result);
$result = MongoData::get('countries', '');

die(print_r($result));

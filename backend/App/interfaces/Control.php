<?php
namespace App\interfaces;
defined("APPPATH") OR die("Access denied");

interface Control
{
    public static function index();
    public static function add();
    public static function edit();
    public static function delete();
}

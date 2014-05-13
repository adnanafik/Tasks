<?php
/**
 * Created by PhpStorm.
 * User: Adnan_Khan
 * Date: 5/6/14
 * Time: 6:26 PM
 */

class Tasks extends ActiveRecord\Model
{
    public static $table_name = 'tasks'; //not required
    public static $primary_key = 'task_id';
}
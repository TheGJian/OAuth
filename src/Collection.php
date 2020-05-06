<?php
/**
 * Created by PhpStorm.
 * User: zguangjian
 * Date: 2020/3/25
 * Time: 11:32
 * Email: zguangjian@outlook.com
 */

namespace zguangjian;


class Collection implements ArrayAccess, Countable, IteratorAggregate, JsonSerializable, Serializable
{
    public function __construct()
    {
    }
}
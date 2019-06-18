<?php

/**
 * @return mixed
 * 替换路由样式
 */
function route_class()
{
    return str_replace('.', '-', Route::currentRouteName());
}

/**
 * @param $category_id
 * @return string
 * 分类的选中状态
 */
function category_nav_active($category_id)
{
    return active_class((if_route('categories.show') && if_route_param('category', $category_id)));
}

/**
 * @param $value
 * @param int $length
 * @return string
 * 截取字符
 */
function make_excerpt($value, $length = 200)
{
    $excerpt = trim(preg_replace('/\r\n|\r|\n+/', ' ', strip_tags($value)));
    return str_limit($excerpt, $length);
}
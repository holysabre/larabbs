<?php

namespace App\Models;

class Topic extends Model
{
    protected $fillable = ['title', 'body', 'user_id', 'category_id', 'reply_count', 'view_count', 'last_reply_user_id', 'order', 'excerpt', 'slug'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @param $query
     * @param $order
     * @return mixed
     * 根据排序条件返回数据
     * 利用scope本地域
     */
    public function scopeWithOrder($query, $order)
    {
        switch ($order)
        {
            case 'recent':
                $query->recent($query);
                break;

            default:
                $query->recentReplied($query);
                break;
        }
        //预防N+1问题
        return $query->with('user','category');
    }

    /**
     * @param $query
     * @return mixed
     * 按修改时间排序
     * 当有回复时，会更新修改时间
     */
    public function scopeRecentReplied($query)
    {
        return $query->orderBy('updated_at', 'desc');
    }

    /**
     * @param $query
     * @return mixed
     * 按创建时间排序
     */
    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

}

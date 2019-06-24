<?php
/**
 * Created by PhpStorm.
 * User: yingwenjie
 * Date: 2019/6/24
 * Time: 2:43 PM
 */

namespace App\Models\Traits;

use DB;
use Cache;
use App\Models\Topic;
use App\Models\Reply;
use Carbon\Carbon;

trait ActiveUserHelper
{
    // 用于存放临时用户数据
    protected $users = [];

    // 配置信息
    protected $topic_weight = 4; //话题权重
    protected $reply_weight = 1; //回复权重
    protected $pass_days = 15;     //统计天数
    protected $user_number = 6;  //统计后显示用户数量

    //缓存相关配置
    protected $cache_key = 'laravel_active_users';
    protected $cache_expire_in_minutes = 60;

    /**
     * @return mixed
     * 获取活跃用户列表
     */
    public function getActiveUsers()
    {
        // 尝试从缓存中取出 cache_key 对应的数据。如果能取到，便直接返回数据。
        // 否则运行匿名函数中的代码来取出活跃用户数据，返回的同时做了缓存。
        return Cache::remember($this->cache_key, $this->cache_expire_in_minutes, function (){
            return $this->calculateActiveUsers();
        });
    }

    /**
     * 计算活跃用户列表 并缓存
     */
    public function calculateAndCacheActiveUsers()
    {
        // 取得活跃用户列表
        $active_users = $this->calculateActiveUsers();

        // 并加以缓存
        $this->cacheActiveUsers($active_users);
    }

    private function calculateActiveUsers()
    {
        $this->calculateTopicScore();
        $this->calculateReplyScore();
        // 数组按照得分排序
        $users = array_sort($this->users, function ($user){
            return $user['score'];
        });

        // 我们需要的是倒序，高分靠前，第二个参数为保持数组的 KEY 不变
        $users = array_reverse($users, true);

        // 只获取我们想要的数量
        $users = array_slice($users, 0, $this->user_number, true);

        //创建一个空集合
        $active_users = collect();

        //因为是缓存 怕和数据库不一致 所以需要在数据库中查找一下用户 如果存在 则加入集合
        foreach ($users as $user_id => $user)
        {
            $user = $this->find($user_id);

            if($user){
                $active_users->push($user);
            }
        }
        return $active_users;
    }

    /**
     * 计算用户话题分数
     */
    private function calculateTopicScore()
    {
        // 从话题数据表里取出限定时间范围（$pass_days）内，有发表过话题的用户
        // 并且同时取出用户此段时间内发布话题的数量

        $topic_users = Topic::query()->select(DB::raw('user_id, count(*) as topic_count'))
            ->where('created_at' , '>=', Carbon::now()->subDays($this->pass_days))
            ->groupBy('user_id')->get();

        foreach ($topic_users as $user){
            $this->users[$user->user_id]['score'] = $user->topic_count * $this->topic_weight;
        }
    }

    /**
     * 计算用户回复分数
     */
    private function calculateReplyScore()
    {
        // 从回复数据表里取出限定时间范围（$pass_days）内，有发表过回复的用户
        // 并且同时取出用户此段时间内发布回复的数量

        $reply_users = Reply::query()->select(DB::raw('user_id, count(*) as reply_count'))
            ->where('created_at', '>=', Carbon::now()->subDays($this->pass_days))
            ->groupBy('user_id')->get();

        foreach ($reply_users as $user){
            $reply_score = $user->reply_count * $this->reply_weight;
            if(isset($this->users[$user->user_id])){
                $this->users[$user->user_id]['score'] += $reply_score;
            }else{
                $this->users[$user->user_id]['score'] = $reply_score;
            }
        }
    }

    /**
     * @param $active_users
     * 写入活跃用户缓存
     */
    private function cacheActiveUsers($active_users)
    {
        Cache::put($this->cache_key, $active_users, $this->cache_expire_in_minutes);
    }
}
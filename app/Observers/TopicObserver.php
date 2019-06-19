<?php

namespace App\Observers;

use App\Models\Topic;
use App\Handlers\SulgTranslateHandler;

// creating, created, updating, updated, saving,
// saved,  deleting, deleted, restoring, restored

class TopicObserver
{
    public function creating(Topic $topic)
    {

    }

    public function updating(Topic $topic)
    {
        //
    }

    public function saving(Topic $topic)
    {
        //过滤xss攻击
        $topic->body = clean($topic->body,'user_topic_body');
        //截取摘要
        $topic->excerpt = make_excerpt($topic->body);
        //友好的url 百度api翻译
        $topic->slug = app(SulgTranslateHandler::class)->translate($topic->title);
    }
}
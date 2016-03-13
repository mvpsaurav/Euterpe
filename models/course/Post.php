<?php

namespace app\models\course;

use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use Yii;
use app\models\account\User;

/*用来与数据库的表post进行数据交互：查找，增加，改变，删除
  对外提供一些接口对数据进行查询，更改
*/
class Post extends ActiveRecord
{
    public static function getPostByPostId($id)
    {
        $post = static::find()->where(['postId' => $id])->asArray()->all();
        // Yii::warning(array_map("static::parseList",$post));
        return array_map("static::parseList", $post)[0];
    }

    //得到页面左侧渲染的帖子列表的精简信息
    public static function getSimplePosts()
    {
        $lastestPosts = static::find()->where(['isPost' => 0])->asArray()->all();
        $lastestPosts = array_map("static::parseSimpleInfo", $lastestPosts);
        return $lastestPosts;
    }

    //帖子被看的时候，改变帖子的readMenList
    public static function addReadList($postId)
    {
        $selectedPost = Post::findOne($postId);
        if (User::getAppUserID() == $selectedPost->postManId)
            return;
        $selectedPost->readMenList = ($selectedPost->readMenList . '|' . User::getAppUserID());
        $selectedPost->save();
        return;
    }

    public static function changeLikemenList($postId)
    {
        $selectedPost = Post::findOne($postId);
        $selectedPost->likeMenList = ($selectedPost->likeMenList . '|' . User::getAppUserID());
        $selectedPost->save();
        return;
    }

    //把帖子变成精简信息
    private static function parseSimpleInfo($post)
    {
        $simpleInfos = explode('|', ArrayHelper::getValue($post, 'simpleInfo'));
        $indexArray = array("postManId", "postManName", "title", "content", "time", "anoymous", "shieldteacher");
        $simpleInfos = array_combine($indexArray, $simpleInfos);
        //处理匿名,判断登陆者是否是作者，若是作者则不在匿名
        //匿名状态下，更改发帖人名字为ANOYMOUS
        if (User::getAppUserID() == ArrayHelper::getValue($simpleInfos, 'postManId'))
            $simpleInfos['anoymous'] = 0;
        if ($simpleInfos['anoymous'] == 1) $simpleInfos['postManName'] = "ANOYMOUS";

        $readMenIds = explode('|', ArrayHelper::getValue($post, 'readMenList'));
        if (in_array(User::getAppUserID(), $readMenIds)) $isRead = true;
        else $isRead = false;


        $likeMenIds = explode('|', ArrayHelper::getValue($post, 'likeMenList'));
        $likeMenCount = count($likeMenIds);

        $newElements = array(
            'postId' => ArrayHelper::getValue($post, 'postId'),
            'isRead' => $isRead,
            'likeMenCount' => $likeMenCount,
        );
        //最终的数组keys："postManId","postManName","title","content","time"，"postId","isRead,likeMenCount"
        return array_merge($newElements, $simpleInfos);
    }

    //解析帖子的likeMenList和readMenList信息
    private static function parseList($post)
    {
        $likeMenIds = explode('|', ArrayHelper::getValue($post, 'likeMenList'));
        $likeMenName = array();
        $likeMenCount = count($likeMenIds);
        if ($likeMenCount == 1 && $likeMenIds[0] == '') {
            $likeMenCount = 0;
        } else {
            foreach ($likeMenIds as $likeMenId) {
                if ($likeMenId == "") continue;
                array_push($likeMenName, User::getUsernameById($likeMenId));
            }
        }

        //unset($post['likeMenList']);

        $readMenIds = explode('|', ArrayHelper::getValue($post, 'readMenList'));
        $readMenCount = count($readMenIds);
        //unset($post['readMenList']);

        $newElements = array(
            'likeMenCount' => $likeMenCount,
            'readMenCount' => $readMenCount,
            'likeMenName' => $likeMenName,
        );
        return array_merge($post, $newElements);
    }


    public static function orderByTime()
    {
        $wholePostList = static::find()->where(['isPost' => 0])->asArray()->all();
        ArrayHelper::multisort($wholePostList, 'time', SORT_DESC);
        return array_map("static::parseSimpleInfo", $wholePostList);
        return $lastestPosts;
    }

    public static function orderByHot()
    {
        $simplePostList = Post::getSimplePosts();
        ArrayHelper::multisort($simplePostList, 'likeMenCount', SORT_DESC);
        return $simplePostList;
    }
    //解析回帖
    //需要优化!
    public static function getnextPosts($post)
    {
        $nextPostIds = explode('|', ArrayHelper::getValue($post, 'nextPostId'));
        $nextPosts = array();
        foreach ($nextPostIds as $nextpostid) {//删掉判断空，改为在保存时判断。
            if($nextpostid=='')continue;
            $nextPost = static::find()->where(['postId' => intval($nextpostid)])->asArray()->one();

            $nextPostManName = User::getUsernameById(ArrayHelper::getValue($nextPost, 'postManId'));
            $nextPostTalk = static::getTalk( ArrayHelper::getValue($nextPost, 'nextPostId'));

            $newElements = array(
                'postManName' => $nextPostManName,
                'talk' => $nextPostTalk
            );

            array_push($nextPosts, array_merge($nextPost, $newElements));
        }
        //$nextPosts=array_map("static::parseSimpleInfo",$nextPosts);
        //Yii::warning($nextPosts);
        return $nextPosts;
    }


    //解析talk
    private static function getTalk($nextPostTalkId)
    {
        $TalkIds = explode('|', $nextPostTalkId);
        $Talks = array();
        foreach ($TalkIds as $TalkId) {//invantal?
            if($TalkId=='')continue;
            $Talk = static::find()->where(['postId' => intval($TalkId)])->asArray()->one();
            $TalkManName = User::getUsernameById(ArrayHelper::getValue($Talk, 'postManId'));

            $newElement = array('postManName' => $TalkManName);
            array_push($Talks, array_merge($Talk, $newElement));
        }
        return $Talks;
    }
}
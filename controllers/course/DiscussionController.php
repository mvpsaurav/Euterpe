<?php
/**
 * Created by PhpStorm.
 */

namespace app\controllers\course;

use app\models\account\User;
use app\models\course\Courseenrollment;
use app\models\course\NewPostForm;
use app\models\course\Post;
use app\models\course\Remind;
use app\models\course\ReplyPostForm;

use yii\web\Controller;
use Yii;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;

class DiscussionController extends Controller
{
    private static $orderRule = "";//储存列表排序的规则

    private static $showRules = array();//储存列表可显示的规则
    /*
     * $showRules中elements声明：
     * unread=>只显示未读的信息
     */

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionIndex()
    {
    }
    //删除帖子里面的图片
    private static function deletePicInPost($matches)
    {
        //foreach($matches[1] as $image) {
            $webPath = Yii::getAlias('@webroot');
            $imagePath = $webPath . $matches[1];
            Yii::warning($imagePath);
            //unlink($imagePath);
        //}
    }
    //讨论区的主页面
    public function actionDiscussion()
    {

        $deletePost = Post::findOne(77);
        if($deletePost) {
            preg_replace_callback(
                "|<img src=\"(.*?)\"|",
                'static::deletePicInPost',
                $deletePost->content);

            //$deletePost->delete();
        }

        $allUsername = User::getAllUsername();
        $simplePosts = Post::getSimplePosts();
        $reminded=Remind::getRemindedData(User::getAppUserID());
        $reply=Remind::getReplyedAData(User::getAppUserID());
        $talk=Remind::getReplyedBData(User::getAppUserID());

        $count=count($reminded);
        $remindedNum=0;
        $replyNum=0;
        $talkNum=0;
        foreach($reminded as $x){
            $remindedNum+=count($x);
        }
        foreach($reply as $y){
            $replyNum+=count($y);
        }
        foreach($talk as $z){
            $talkNum+=count($z);
        }
       /* return $this->render('say.php',[
            'message'=>($reply),
        ]);*/
        //Yii::warning($talk);
        return $this->render('discussion.php',[
            'simplePosts' => $simplePosts,
            'allUsername' => $allUsername,
            'reminded' => $reminded,
            'reply' => $reply,
            'talk'=>$talk,
            'remindedNum' =>$remindedNum,
            'replyNum'=>$replyNum,
            'talkNum'=>$talkNum,
        ]);
    }
    //用来显示页面右侧的帖子的完整信息
    public function actionShowWholePost()
    {
        if (Yii::$app->request->isAjax) {
            $postId = Yii::$app->request->post();
            $selectedPost = Post::getPostByPostId($postId);
            $replyPosts = Post::getnextPosts($selectedPost);
            //Yii::warning($replyPosts);
            Post::addReadList($postId);
             return $this->renderPartial('showWholePost.php',[
                 'selectedPost' => $selectedPost,
                 'replyPosts' => $replyPosts,
             ],false,true);
        }
    }

    public function actionChangeLike()
    {
        if(Yii::$app->request->isAjax){
            $message=Yii::$app->request->post();
            $postId =$message['postId'];
            Post::changeLikemenList($postId);
        }
    }

    //展示全部的提醒的帖子
   /* public function actionShowWholeRemind(){
        if(Yii::$app->request->isAjax){
            $data='';
            $reminded=Yii::$app->request->post();
            foreach($reminded as $remind){
                foreach($remind as $x=>$y){
                    $data.=User::getUsernameById($x)."在";
                }
            }

        }
    }*/
    //发新帖子
    public function actionEditNewPost()
    {
        $allUsername = User::getAllUsername();
        $model = new NewPostForm;
        if($model->load(Yii::$app->request->post()))    {
            $model->content = ArrayHelper::getValue(Yii::$app->request->post(),'content');
            //return $this->render('say', ['message' => $msg]);

            if($model->addPost())   $msg = "发帖成功";
            else    $msg = '发帖失败';
            return $this->render('say', ['message' => $msg]);
        }
        return $this->renderAjax('editNewPost.php',[
            'model' => $model,
            'allUsername' => $allUsername,
        ]);
    }

    public function actionAcceptRemindList()
    {
        if (Yii::$app->request->isAjax) {
            $session = Yii::$app->session;
            $session->open();
            $session['remindName'] = ArrayHelper::getValue(Yii::$app->request->post(), 'remindName');

            return;
        }
    }
    //删除提醒标记
    public function actionDeleteRemindedData(){
        if(Yii::$app->request->isAjax){
            $message=Yii::$app->request->post();
            $RemindedManId=$message['RemindedManId'];
            $RemindPostId=$message['RemindPostId'];
            $postId=$message['postId'];
            Remind::deleteRemindedData($RemindedManId,$RemindPostId);
            $selectedPost = Post::getPostByPostId($postId);
            $replyPosts = Post::getnextPosts($selectedPost);
            //Yii::warning($replyPosts);
            Post::addReadList($postId);
            return $this->renderPartial('showWholePost.php',[
                'RemindPostId'=>$RemindPostId,
                'selectedPost' => $selectedPost,
                'replyPosts' => $replyPosts,
            ],false,true);
        }
    }

    //删除回复标记
    public function actionDeleteReplyedData(){
        if(Yii::$app->request->isAjax){
            $message=Yii::$app->request->post();
            $ReplyedManId=$message['ReplyedManId'];
            $ReplyPostId=$message['ReplyPostId'];
            $postId=$message['postId'];
            Remind::deleteAData($ReplyedManId,$ReplyPostId);
            $selectedPost = Post::getPostByPostId($postId);
            $replyPosts = Post::getnextPosts($selectedPost);
            //Yii::warning($replyPosts);
            Post::addReadList($postId);
            return $this->renderPartial('showWholePost.php',[
                'selectedPost' => $selectedPost,
                'replyPosts' => $replyPosts,
            ],false,true);
        }
    }
    //回复帖子
    public function actionReplyPost()
    {
        if (Yii::$app->request->isAjax) {
            $session = Yii::$app->session;
            $session->open();
            $session['fatherPostId'] = ArrayHelper::getValue(Yii::$app->request->post(), 'fatherPostId');
            $session['postType'] = ArrayHelper::getValue(Yii::$app->request->post(),'postType');
            //return (Yii::$app->session->get('postType'));
        }
        $model = new ReplyPostForm();
        if($model->load(Yii::$app->request->post()))    {
            $session = Yii::$app->session;
            if($model->addReplyPost($session->get('fatherPostId'),$session->get('postType')))   $msg = "发帖成功";
            else    $msg = "发帖失败,";
            $session->close();
            return $this->render('say', ['message' => $msg]);
        }
        return $this->renderAjax('replyPost.php', [
            'model' => $model,
        ]);
        //return Yii::$app->session->get('fatherPostId')+" "+Yii::$app->session->get('postType');
    }

    public function actionModifyShowRule()
    {
        if (Yii::$app->request->isAjax) {
            $rule = Yii::$app->request->post();
            $simplePosts = Post::getSimplePosts();
            //Yii::warning($simplePosts);
            $msg = "更改成功";
            return $this->render('say', [
                'message' => $msg
            ]);
        }
    }

    public function actionModifyOrderRule()
    {

        if (Yii::$app->request->isAjax) {
            $ajaxInfo = Yii::$app->request->post();
            $orderRule = ArrayHelper::getValue($ajaxInfo,'orderRule');

            $msg = call_user_func(array("app\models\course\Post", $orderRule));


            $simplePosts = Post::getSimplePosts();
            Yii::warning($simplePosts);
            return $this->renderPartial('simplePostList.php',[
                'simplePosts' => $msg,
            ]);
        }
    }

    public function actionDeleteMainPost()
    {
        if (Yii::$app->request->isAjax) {
            $ajaxInfo = Yii::$app->request->post();
            $deletePostId = ArrayHelper::getValue($ajaxInfo,'postId');

            Post::deleteMainPost($deletePostId);

            return $this->redirect(array('course/discussion/discussion'));
        }
    }

    public function actionDeleteFollowPost()
    {
        if (Yii::$app->request->isAjax) {
            $ajaxInfo = Yii::$app->request->post();
            $followPostId = ArrayHelper::getValue($ajaxInfo,'postId');
            $mainPostId = ArrayHelper::getValue($ajaxInfo,'fatherPostId');

            Post::deleteFollowPost($followPostId,$mainPostId);
            return;
        }
    }

    public function actionDeleteTalkPost()
    {
        if (Yii::$app->request->isAjax) {
            $ajaxInfo = Yii::$app->request->post();
            $followPostId = ArrayHelper::getValue($ajaxInfo,'postId');
            $mainPostId = ArrayHelper::getValue($ajaxInfo,'fatherPostId');

            Post::deleteTalkPost($followPostId,$mainPostId);
            return;
        }

    }

    public function actionShowWholeRemind()
    {
        if(Yii::$app->request->isAjax) {
            $ManId = User::getAppUser()->id;
            $RemindDatas = Remind::getRemindedData($ManId);
            $ReplyDatas = Remind::getReplyedAData($ManId);
            $talkDatas=Remind::getReplyedBData($ManId);
            $Remind=array();
            foreach ($RemindDatas as $RemindedPostId => $RemindData) {
                foreach ($RemindData as $RemindManId => $RemindPostId) {
                    $RemindManName = User::getUsernameById($RemindManId);
                    $RemindPost = Post::find()->where(['postId' => $RemindPostId])->asArray()->one();
                    $simpleInfo = strip_tags(substr(ArrayHelper::getValue($RemindPost, 'content'), 0, 100));
                    $Remind[] = ['RemindedPostId' => $RemindedPostId, 'RemindManName' => $RemindManName, 'simpleInfo' => $simpleInfo, 'RemindPostId' => $RemindPostId, 'time' => ArrayHelper::getValue($RemindPost, 'time')];
                }

            }
            $Reply = array();
            foreach ($ReplyDatas as $ReplyedPostId => $ReplyData) {
                foreach ($ReplyData as $ReplyManId => $ReplyPostId) {
                    $ReplyManName = User::getUsernameById($ReplyManId);
                    $ReplyPost = Post::find()->where(['PostId' => $ReplyPostId])->asArray()->one();
                    $simpleInfo = strip_tags(substr(ArrayHelper::getValue($ReplyPost, 'content'), 0, 100));
                    $Reply[] = ['ReplyedPostId' => $ReplyedPostId, 'ReplyManName' => $ReplyManName, 'simpleInfo' => $simpleInfo, 'ReplyPostId' => $ReplyPostId];
                }
            }
            $Talk=array();
            foreach ($talkDatas as $ReplyedPostId => $TalkedData) {
                foreach ($TalkedData as $TalkManId => $TalkPostId) {
                    $TalkManName = User::getUsernameById($TalkManId);
                    $TalkPost = Post::find()->where(['PostId' => $TalkPostId])->asArray()->one();
                    $simpleInfo = strip_tags(substr(ArrayHelper::getValue($TalkPost, 'content'), 0, 100));
                    $Talk[] = ['ReplyedPostId' => $ReplyedPostId, 'TalkManName' => $TalkManName, 'simpleInfo' => $simpleInfo, 'TalkPostId' => $TalkPostId];
                }
            }
            return $this->renderPartial('remind', ['Remind' => $Remind, 'Reply' => $Reply,'Talk'=>$Talk]);
        }
    }

    public function beforeAction($action)
    {
        $message = '';
        if(\Yii::$app->user->isGuest)
            $message = 'Please Login First';
        else{
            $courseid = $_COOKIE['courseid'];
            $studentid = User::getAppUser()->id;
            if(!$courseid || !Courseenrollment::findOne(['courseid'=>$courseid,'studentid'=>$studentid]))
                $message = 'Please Enter Course First';
        }
        if($message!='')
            $this->redirect('/site/say?message='.urlencode($message));
        return parent::beforeAction($action);
    }

    public static function isPostDisplayable($post)
    {
        Yii::warning($post);
    }
}
<?php
/**
 * Created by PhpStorm.
 */
use yii\helpers\ArrayHelper;
use app\models\account\User;
?>

<div class="post_region_box question_note_view dashboard_element">
    <div class="post_region_header clearFix">
        <div class="post_icon note "></div>

        <div class="post_title">note</div>
        <div class="post_view_count"><span class="count"><?php echo(ArrayHelper::getValue($selectedPost,'readMenCount'));?></span> views</div>
    </div>
    <div class="post_region_content note" id="view_quesiton_note">
        <h1 class="post_region_title"><?php echo(ArrayHelper::getValue($selectedPost,'title'));?></h1>
        <div class="post_region_text" id="questionText">
            <p><?php echo(ArrayHelper::getValue($selectedPost,'content'));?></p>
            <p>&nbsp;</p>

            <p><div><div style="float:left; width:150px"><?=ArrayHelper::getValue($selectedPost,'time')?></div>
             <div style="float:left" id="like_<?=(ArrayHelper::getValue($selectedPost,'postId'))?>"
               onclick="changeLike('<?=User::getUsernameById(User::getAppUserId())?>',<?=ArrayHelper::getValue($selectedPost,'postId')?>)">
               <div class="heart" id="like1_<?=ArrayHelper::getValue($selectedPost,'postId')?>"<?php
                 if(ArrayHelper::getValue($selectedPost,'islike'))echo('rel="like" style="background-position:right">');
                 else echo('rel="unlike" style="background-position:left">');
                 ?></div>
                    </div></div>

            </p>
            <br/>
            <p>
                <div id="name<?=ArrayHelper::getValue($selectedPost,'postId')?>" align ="left">
                <?php
                $x=0;
                foreach (ArrayHelper::getValue($selectedPost,'likeMenName') as $likeMenName){
                    $x++;
                    if($x==ArrayHelper::getValue($selectedPost,'likeMenCount'))echo($likeMenName);
                    else echo($likeMenName.',');
                }
                if(ArrayHelper::getValue($selectedPost,'likeMenCount')!=0)
                    echo('等共'.ArrayHelper::getValue($selectedPost,'likeMenCount').'人赞过');
                //print_r($replyPosts);
                ?>
            </div>
           <!--删除帖子-->
            <?php if(ArrayHelper::getValue($selectedPost,'postManId') == User::getAppUserID()):?>
            <div align="right"> <a onclick="deletePost(0,-1,<?=ArrayHelper::getValue($selectedPost,'postId')?>)">删除</a></div>
            <?php endif?>
            </p>

        </div>
    </div>
</div>
<!--回复-->
<div id="clarifying_discussion" class="post_region_box clarifying_discussion dashboard_element">
    <div class="post_region_header clearFix">
        <div class="post_title">followup discussions</div>
        <div class="post_subtitle">for lingering questions and comments</div>
    </div>

    <div class="post_region_content clarifying_discussion">
        <!--B型帖子-->
        <?php foreach ($replyPosts as $replyPost): ?>
        <div class="clarifying_discussion clearFix " id="follow_post_<?=(ArrayHelper::getValue($replyPost,'postId'))?>">
            <!--这里是头像显示区-->
            <!--div class="account_image_container">
                <div class="user_pic user_pic_ie7xy5sipx51qz user_loading"><div class="white_border"><img title="Gao Tong" src="https://d1b10bmlvqabco.cloudfront.net/photos/ie7xy5sipx51qz/1442247026_35.png" onload="onImageLoad(event);" width="34" height="34" style="width: 34px; height: 34px; left: 0px; top: 0px;"></div></div>
            </div-->
            <div class="discussion_content main_followup clearFix">

                <div class="discussion_text">
                    <div style="float:left">
                    <a class="discussion_poster"><span anon="no" class="user_name user_name_ie7xy5sipx51qz user_loading"><?= ArrayHelper::getValue($replyPost,'postManName') ?></span></a>
                    <a class="dicussion_meta"><span title="Thu Jan 28 2016 18:19:11 GMT+0800 (中国标准时间)"><?= ArrayHelper::getValue($replyPost,'time') ?></span></a></div>
                    <div style="float:left" id="like_<?=(ArrayHelper::getValue($replyPost,'postId'))?>"
                         onclick="changeLike('<?=User::getUsernameById(User::getAppUserId())?>',<?=ArrayHelper::getValue($replyPost,'postId')?>)">
                        <div class="heart" id="like1_<?=ArrayHelper::getValue($replyPost,'postId')?>"<?php
                        if(ArrayHelper::getValue($replyPost,'islike'))echo('rel="like" style="background-position:right">');
                        else echo('rel="unlike" style="background-position:left">');
                        ?></div>
                </div><br/>
                    <span class="actual_text post_region_text"><p><?= ArrayHelper::getValue($replyPost,'content') ?></p></span>

                    <!--删除帖子-->
                    <?php if(ArrayHelper::getValue($replyPost,'postManId') == User::getAppUserID()):?>
                        <div align="right"> <a onclick="deletePost(1,<?=ArrayHelper::getValue($selectedPost,'postId')?>,<?=ArrayHelper::getValue($replyPost,'postId')?>)">删除</a></div>
                    <?php endif?>
                </div>

            </div>

            <!--不懂干什么的div class="discussion_content discussion_content_edit clearFix" style="display:none;" id="discussion_edit_ijy3z8w56bx3da"></div-->
            <!--talk区域-->
            <?php $talks = ArrayHelper::getValue($replyPost,'talk');?>
            <?php foreach ($talks as $talk): ?>
            <div class="all_replies" id="talk_post_<?=ArrayHelper::getValue($talk,'postId')?>">

                <div class="discussion_replies existing_reply clearFix" id="ijydlez52gn4fb">
                    <!--这里是头像显示区-->
                    <!--div class="account_image_container">
                        <div class="user_pic user_pic_ie7xy5sipx51qz user_loading"><div class="white_border"><img title="Gao Tong" src="https://d1b10bmlvqabco.cloudfront.net/photos/ie7xy5sipx51qz/1442247026_35.png" onload="onImageLoad(event);" width="34" height="34" style="width: 34px; height: 34px; left: 0px; top: 0px;"></div></div>
                    </div-->
                    <div class="discussion_content clearFix">

                        <div class="discussion_text">
                            <a class="discussion_poster"><span anon="no" class="user_name user_name_ie7xy5sipx51qz user_loading"><?= ArrayHelper::getValue($talk,'postManName') ?></span></a>
                            <a class="dicussion_meta"><span title="Thu Jan 28 2016 22:48:22 GMT+0800 (中国标准时间)"><?= ArrayHelper::getValue($talk,'time') ?></span></a>
                            <span class="actual_reply_text post_region_text"><p><?= ArrayHelper::getValue($talk,'content') ?></p></span>

                            <!--删除帖子-->
                            <?php if(ArrayHelper::getValue($talk,'postManId') == User::getAppUserID()):?>
                                <div align="right"> <a onclick="deletePost(2,<?=ArrayHelper::getValue($replyPost,'postId')?>,<?=ArrayHelper::getValue($talk,'postId')?>)">删除</a></div>
                            <?php endif?>
                        </div>

                    </div>
                    <!--不懂干什么的 div class="discussion_content discussion_content_edit clearFix" style="display:none;" id="reply_edit_ijydlez52gn4fb"></div-->
                </div>
            </div>
            <?php endforeach; ?>


                <!--talk回复-->
            <div class="compose_reply clearFix start_reply" id="start_reply_followup_<?= ArrayHelper::getValue($replyPost,'postId')?>"
                 onclick="replyPost(<?= ArrayHelper::getValue($replyPost,'postId')?>,2,'start_reply_followup_<?= ArrayHelper::getValue($replyPost,'postId')?>','create_reply_followup_<?=  ArrayHelper::getValue($replyPost,'postId')?>')">
                Reply to this followup discussion
            </div>
            <!--下面的div原先class="discussion_replies new edit_mode"，但是这样却没办法显示，暂时去掉-->
            <div id="create_reply_followup_<?=  ArrayHelper::getValue($replyPost,'postId')?>">
                <!--div class="account_image_container">
                    <div class="user_pic user_pic_ie7xy8iscsw1t7"><div class="white_border"><img title="吴行行" src="https://dvngeac8rg9mb.cloudfront.net/images/dashboard/common/default_user.png" onload="onImageLoad(event);" width="0" height="0" style="display: block; width: 0px; height: 0px; left: 0px;"></div></div>
                </div-->
                <!--div class="discussion_content edit_mode clearFix" ></div-->
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <!--回复A贴-->
    <h5 id="start_new_followup_header">Start a new followup discussion</h5>
    <div class="compose_discussion" onclick="replyPost(<?=ArrayHelper::getValue($selectedPost,'postId')?>,1,'create_new_followup','create_new_followup_div')" id="create_new_followup">Compose a new followup discussion</div>
    <div id="create_new_followup_div"></div>
</div>

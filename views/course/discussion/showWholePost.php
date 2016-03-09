<?php
/**
 * Created by PhpStorm.
 */
use yii\helpers\ArrayHelper;
?>

<div class="post_region_box question_note_view dashboard_element">
    <div class="post_region_header clearFix">
        <div class="post_icon note "></div>

        <div class="post_title">note</div>

        <div class="post_favorite is_not_favorite">
            <i class="icon_favorite is_favorite" onclick="PEM.fire('favorite', false);return false;"></i>
            <i class="icon_favorite not_favorite" onclick="PEM.fire('favorite', true);return false;" tutorial="Save this post to your favorites to find it later." original-title=""></i>
        </div>
        <div class="post_converted_message">Your question is now a note.<div class="post_converted_message_close">x</div></div>
        <div class="post_view_count"><span class="count"><?php echo(ArrayHelper::getValue($selectedPost,'readMenCount'));?></span> views</div>
        <a href="#" class="follow_link hide" onclick="PEM.fire('question_note_stop_follow');return false;">stop following</a>
    </div>
    <div class="post_region_content note" id="view_quesiton_note">

        <h1 class="post_region_title"><?php echo(ArrayHelper::getValue($selectedPost,'title'));?></h1>

        <div class="post_region_text" id="questionText">
            <p><?php echo(ArrayHelper::getValue($selectedPost,'content'));?></p>
            <p>&nbsp;</p>
            <p><?php echo(ArrayHelper::getValue($selectedPost,'time'));?></p>
        </div>
</div>

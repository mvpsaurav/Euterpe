<?php
/**
 * Created by PhpStorm.
 */
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

$this->title = 'Courseware';

$this->registerJsFile('/js/course/courseware/pdfobject.js');
$this->registerJsFile('/js/course/courseware/courseware.js');
$this->registerCssFile('/css/courseware/courseware.css');
?> 
<div id="pdf"></div>
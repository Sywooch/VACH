<?php

use yii\helpers\Html;
use yii\helpers\Url;
use app\models\Wheel;
use app\models\WheelQuestion;
use app\controllers\Utils;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */

if ($type == Wheel::TYPE_GROUP)
    $title = Yii::t('dashboard', 'Group Emergents Matrix');
else if ($type == Wheel::TYPE_ORGANIZATIONAL)
    $title = Yii::t('dashboard', 'Organizational Emergents Matrix');
else
    $title = Yii::t('dashboard', 'Individual Emergents Matrix');

$dimensions = WheelQuestion::getDimensionNames($type);
$questionCount = WheelQuestion::getQuestionCount($type) / 8;

$max_emergents = [];
$min_emergents = [];
$gap_emergents = [];

$maxValue = -100;
$minValue = 100;
$maxGap = -100;

foreach ($emergents as $emergent)
    if ($emergent['answer_order'] % $questionCount != $questionCount - 1) {
        if ($emergent['value'] > $maxValue)
            $maxValue = $emergent['value'];
        if ($emergent['value'] < $minValue)
            $minValue = $emergent['value'];
    }

foreach ($emergents as $emergent) {
    if ($emergent['value'] == $maxValue)
        $max_emergents[] = $emergent;
    if ($emergent['value'] == $minValue)
        $min_emergents[] = $emergent;
}

if ($type > Wheel::TYPE_INDIVIDUAL && $memberId > 0) {
    foreach ($emergents as $emergent) {
        $gap = abs($emergent['mine_value'] - $emergent['value']);
        if ($gap > $standar_deviation && $gap > $maxGap)
            $maxGap = $gap;
    }

    foreach ($emergents as $emergent) {
        $gap = abs($emergent['mine_value'] - $emergent['value']);
        if ($gap == $maxGap)
            $gap_emergents[] = $emergent;
    }
}

$token = rand(100000, 999999);
?>
<div class="clearfix"></div>
<h3><?= $title ?></h3>
<div id="div<?= $token ?>" class="row col-md-12">
    <h4><?= Yii::t('dashboard', 'Best emergents') ?></h4>
    <?php foreach ($max_emergents as $emergent) { ?>
        <label><?= $dimensions[$emergent['dimension']] ?> - <?= $emergent['question'] ?></label>
        <?php
        if ($emergent['value'] > Yii::$app->params['good_consciousness'])
            $color = '5cb85c';
        else if ($emergent['value'] < Yii::$app->params['minimal_consciousness'])
            $color = 'd9534f';
        else
            $color = 'f0ad4e';

        $percentage = $emergent['value'] / 4 * 100;
        if ($percentage < 6)
            $width = 6;
        else
            $width = $percentage;
        ?>
        <div style='position:relative; color: white; font-size: 20px;' class="table table-bordered">
            <div style='font-size:0px; border-top: 28px solid #<?= $color ?>; width: <?= $width ?>%;'>&nbsp;</div>
            <div style='position:absolute; top:0px; left: 5px;'><?= floor($percentage) ?>%</div>
        </div>
    <?php } ?>
    <h4><?= Yii::t('dashboard', 'Worst emergents') ?></h4>
    <?php foreach ($min_emergents as $emergent) { ?>
        <label><?= $dimensions[$emergent['dimension']] ?> - <?= $emergent['question'] ?></label>
        <?php
        if ($emergent['value'] > Yii::$app->params['good_consciousness'])
            $color = '5cb85c';
        else if ($emergent['value'] < Yii::$app->params['minimal_consciousness'])
            $color = 'd9534f';
        else
            $color = 'f0ad4e';

        $percentage = $emergent['value'] / 4 * 100;
        if ($percentage < 6)
            $width = 6;
        else
            $width = $percentage;
        ?>
        <div style='position:relative; color: white; font-size: 20px;' class="table table-bordered">
            <div style='font-size:0px; border-top: 28px solid #<?= $color ?>; width: <?= $width ?>%;'>&nbsp;</div>
            <div style='position:absolute; top:0px; left: 5px;'><?= floor($percentage) ?>%</div>
        </div>
    <?php } ?>
    <?php if ($type > Wheel::TYPE_INDIVIDUAL && $memberId > 0) { ?>
        <h4><?= Yii::t('dashboard', 'Consciousness gap emergents') ?></h4>
        <?php foreach ($gap_emergents as $emergent) { ?>
            <label><?= $dimensions[$emergent['dimension']] ?> - <?= $emergent['question'] ?></label>
            <?php
            $color = '4444ff';

            $percentage = $emergent['value'] / 4 * 100;
            if ($percentage < 11)
                $width = 11;
            else
                $width = $percentage;
            ?>
            <div style='position:relative; color: white; font-size: 14px; margin-bottom: 0px;' class="table table-bordered">
                <div style='font-size:0px; border-top: 20px solid #<?= $color ?>; width: <?= $width ?>%;'>&nbsp;</div>
                <div style='position:absolute; top:0px; left: 5px;'><?= Yii::t('dashboard', 'How they see me') . ' ' . floor($percentage) ?>%</div>
            </div>
            <?php
            $color = 'ff4444';
            $percentage = $emergent['mine_value'] / 4 * 100;
            if ($percentage < 11)
                $width = 11;
            else
                $width = $percentage;
            ?>
            <div style='position:relative; color: white; font-size: 14px;' class="table table-bordered">
                <div style='font-size:0px; border-top: 20px solid #<?= $color ?>; width: <?= $width ?>%;'>&nbsp;</div>
                <div style='position:absolute; top:0px; left: 5px;'><?= Yii::t('dashboard', 'How I see me') . ' ' . floor($percentage) ?>%</div>
            </div>
        <?php } ?>
    <?php } ?>
</div>
<?php if (strpos(Yii::$app->request->absoluteUrl, 'download') === false) { ?>
    <div class="col-md-12 text-center">
        <?= Html::button(Yii::t('app', 'Export'), ['class' => 'btn btn-default hidden-print', 'onclick' => "printDiv('div$token')"]) ?>
    </div>
<?php } ?>
<div class="clearfix"></div>

<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use app\models\WheelAnswer;
use yii\bootstrap\Button;
use app\models\Wheel;
use franciscomaya\sceditor\SCEditor;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $wheel app\models\ContactForm */



$this->title = Yii::t('report', 'Effectiveness Matrix');
$this->params['breadcrumbs'][] = ['label' => Yii::t('team', 'Teams'), 'url' => ['/team']];
$this->params['breadcrumbs'][] = ['label' => $assessment->team->fullname, 'url' => ['/team/view', 'id' => $assessment->team->id]];
$this->params['breadcrumbs'][] = ['label' => $assessment->fullname, 'url' => ['/assessment/view', 'id' => $assessment->id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('report', 'Report'), 'url' => ['/report/view', 'id' => $assessment->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<script>
    var matrixes = new Array();
    var matrixesData = new Array();
</script>
<script src="<?= Url::to('@web/js/matrix.js') ?>"></script>
<div class="report-technical">

    <h1><?= $this->title ?></h1>
    <?php
    if (count($groupRelationsMatrix) > 0) {
        echo $this->render('../dashboard/_number_matrix', [
            'data' => $groupRelationsMatrix,
            'members' => $members,
            'type' => Wheel::TYPE_GROUP,
            'memberId' => 0,
        ]);
    }
    ?>
    <?php
    if (count($organizationalRelationsMatrix) > 0) {
        echo $this->render('../dashboard/_number_matrix', [
            'data' => $organizationalRelationsMatrix,
            'members' => $members,
            'type' => Wheel::TYPE_ORGANIZATIONAL,
            'memberId' => 0,
        ]);
    }
    ?>
    <div class="row col-md-12">
        <h3>
            Descripción <a class="collapsed btn btn-default" aria-controls="collapsedDiv" aria-expanded="false" href="#collapsedDiv" data-toggle="collapse" role="button">
                <?= Yii::t('dashboard', 'Show') ?>
            </a>
        </h3>
    </div>
    <div id="collapsedDiv" class="panel-collapse collapse row col-md-12" aria-expanded="false">
        <?= $this->render('descriptions/effectiveness') ?>
    </div>
    <div class="row col-md-12">
        <h3>
            Análisis
        </h3>
        <p>
            <?php
            $form = ActiveForm::begin([
                        'id' => 'newassessment-form',
            ]);
            ?>
            <?=
            SCEditor::widget([
                'name' => 'analysis',
                'value' => $assessment->report->effectiveness,
                'options' => ['rows' => 10],
                'clientOptions' => [
                    'toolbar' => "bold,italic,underline|bulletlist,orderedlist|removeformat",
                    'width' => '100%',
                ]
            ])
            ?>
        </p>
        <div class="form-group">
            <?= Html::submitButton(\Yii::t('app', 'Save'), ['class' => 'btn btn-primary', 'name' => 'save-button']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
<script>
    window.onload = function () {
        for (var i in matrixes) {
            doMatrix(document.getElementById("canvas" + matrixes[i]).getContext("2d"), matrixesData[i]);
        }
    }
</script>

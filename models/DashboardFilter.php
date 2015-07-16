<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * DashboardForm is the model behind the login form.
 */
class DashboardFilter extends Model {

    public $companyId = 0;
    public $teamId = 0;
    public $assessmentId = 0;
    public $memberId = 0;
    public $wheelType = 0;

    /**
     * @return array the validation rules.
     */
    public function rules() {
        return [
            [['companyId', 'teamId', 'assessmentId', 'memberId', 'wheelType'], 'safe'],
        ];
    }

    public function attributeLabels() {
        return [
            'companyId' => Yii::t('company', 'Company'),
            'teamId' => Yii::t('team', 'Team'),
            'assessmentId' => Yii::t('assessment', 'Assessment'),
            'memberId' => Yii::t('team', 'Member'),
            'wheelType' => Yii::t('wheel', 'Wheel type'),
        ];
    }

}

<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\db\Query;
use \yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

class Assessment extends ActiveRecord {

    const STATUS_PENDING = 0;
    const STATUS_SENT = 1;
    const STATUS_FINISHED = 2;

    public $fullname;

    public function __construct() {
        $this->name = date("Y-m");
    }

    /**
     * @return array the validation rules.
     */
    public function rules() {
        return [
            [['name', 'team_id'], 'required'],
            [['name'], 'filter', 'filter' => 'trim'],
        ];
    }

    public function attributeLabels() {
        return [
            'name' => Yii::t('app', 'Name'),
            'team_id' => Yii::t('team', 'Team'),
            'IndividualWheelStatus' => Yii::t('wheel', 'Individual Wheels'),
            'GroupWheelStatus' => Yii::t('wheel', 'Group Wheels'),
            'OrganizationalWheelStatus' => Yii::t('wheel', 'Organizational Wheels'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            TimestampBehavior::className(),
        ];
    }

    public function afterFind() {
        $this->fullname = $this->team->company->name . ' ' . $this->team->name . ' ' . $this->name;
        parent::afterFind();
    }

    public function afterSave($insert, $changedAttributes) {
        parent::afterSave($insert, $changedAttributes);
        $this->afterFind();
    }

    public function beforeDelete() {
        Wheel::deleteAll(['assessment_id' => $this->id]);
        return parent::beforeDelete();
    }

    public static function browse() {
        return Assessment::find()
                        ->select('assessment.*')
                        ->innerJoin('team', '`team`.`id` = `assessment`.`team_id`')
                        ->where(['team.coach_id' => Yii::$app->user->id])
                        ->orderBy('assessment.id desc');
    }

    public function getTeam() {
        return $this->hasOne(Team::className(), ['id' => 'team_id']);
    }

    public function getReport() {
        return $this->hasOne(Report::className(), ['assessment_id' => 'id']);
    }

    public function wheelStatus($type) {
        return (new Query)->select('count(wheel_answer.id) as count')
                        ->from('wheel')
                        ->leftJoin('wheel_answer', 'wheel_answer.wheel_id = wheel.id')
                        ->where(['assessment_id' => $this->id, 'type' => $type])
                        ->scalar();
        ;
    }

    public function getIndividualWheelStatus() {
        $answers = $this->wheelStatus(Wheel::TYPE_INDIVIDUAL);
        $members = count($this->team->members);
        $questions = $members * WheelQuestion::getQuestionCount(Wheel::TYPE_INDIVIDUAL);
        if ($questions == 0)
            $questions = 1;

        return round($answers / $questions * 100, 1) . ' %';
    }

    public function getGroupWheelStatus() {
        $answers = $this->wheelStatus(Wheel::TYPE_GROUP);
        $members = count($this->team->members);
        $questions = $members * $members * WheelQuestion::getQuestionCount(Wheel::TYPE_GROUP);
        if ($questions == 0)
            $questions = 1;
        return round($answers / $questions * 100, 1) . ' %';
    }

    public function getOrganizationalWheelStatus() {
        $answers = $this->wheelStatus(Wheel::TYPE_ORGANIZATIONAL);
        $members = count($this->team->members);
        $questions = $members * $members * WheelQuestion::getQuestionCount(Wheel::TYPE_ORGANIZATIONAL);
        if ($questions == 0)
            $questions = 1;
        return round($answers / $questions * 100, 1) . ' %';
    }

    public function getWheels() {
        return $this->hasMany(Wheel::className(), ['assessment_id' => 'id']);
    }

    public function getIndividualWheels() {
        return $this->hasMany(Wheel::className(), ['assessment_id' => 'id'])->where(['type' => Wheel::TYPE_INDIVIDUAL]);
    }

    public function getGroupWheels() {
        return $this->hasMany(Wheel::className(), ['assessment_id' => 'id'])->where(['type' => Wheel::TYPE_GROUP]);
    }

    public function getOrganizationalWheels() {
        return $this->hasMany(Wheel::className(), ['assessment_id' => 'id'])->where(['type' => Wheel::TYPE_ORGANIZATIONAL]);
    }

}

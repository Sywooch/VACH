<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\Assessment;
use app\models\DashboardFilter;
use app\models\Wheel;

class AssessmentController extends BaseController {

    public $layout = 'inner';

    public function actionIndex() {
        if (Yii::$app->user->isGuest)
            return $this->redirect(['/site']);

        $assessments = Assessment::browse();

        return $this->render('index', [
                    'assessments' => $assessments,
        ]);
    }

    public function actionView($id) {
        $assessment = Assessment::find()
                ->where(['id' => $id])
                ->with(['individualWheels', 'groupWheels', 'organizationalWheels'])
                ->one();

        return $this->render('view', [
                    'assessment' => $assessment,
        ]);
    }

    public function actionNew($teamId) {
        $assessment = new Assessment();
        $assessment->team_id = $teamId;

        if ($assessment->load(Yii::$app->request->post()) && $assessment->save()) {
            foreach ($assessment->team->members as $observerMember) {
                $token = $this->newToken();
                $newWheel = new Wheel();

                $newWheel->observer_id = $observerMember->member->id;
                $newWheel->observed_id = $observerMember->member->id;
                $newWheel->type = Wheel::TYPE_INDIVIDUAL;
                $newWheel->token = $token;
                $newWheel->assessment_id = $assessment->id;

                $newWheel->save();

                $token = $this->newToken();
                foreach ($assessment->team->members as $observedMember) {
                    $newWheel = new Wheel();
                    $newWheel->observer_id = $observerMember->member->id;
                    $newWheel->observed_id = $observedMember->member->id;
                    $newWheel->type = Wheel::TYPE_GROUP;
                    $newWheel->token = $token;
                    $newWheel->assessment_id = $assessment->id;
                    $newWheel->save();
                }

                $token = $this->newToken();
                foreach ($assessment->team->members as $observedMember) {
                    $newWheel = new Wheel();
                    $newWheel->observer_id = $observerMember->member->id;
                    $newWheel->observed_id = $observedMember->member->id;
                    $newWheel->type = Wheel::TYPE_ORGANIZATIONAL;
                    $newWheel->token = $token;
                    $newWheel->assessment_id = $assessment->id;
                    $newWheel->save();
                }
            }
            SiteController::addFlash('success', Yii::t('app', '{name} has been successfully created.', ['name' => $assessment->fullname]));
            return $this->redirect(['/assessment/view', 'id' => $assessment->id]);
        } else {
            SiteController::FlashErrors($assessment);
        }

        return $this->render('form', [
                    'assessment' => $assessment,
        ]);
    }

    public function actionDelete($id) {
        $assessment = Assessment::findOne(['id' => $id]);
        $teamId = $assessment->team->id;
        if ($assessment->delete()) {
            SiteController::addFlash('success', Yii::t('app', '{name} has been successfully deleted.', ['name' => $assessment->fullname]));
        } else {
            SiteController::FlashErrors($assessment);
        }
        return $this->redirect(['/team/view', 'id' => $teamId]);
    }

    public function actionSendWheel($id, $memberId, $type) {
        $assessment = Assessment::findOne(['id' => $id]);

        $sent = false;
        foreach ($assessment->team->members as $teamMember) {
            if ($teamMember->user_id == $memberId) {
                $wheels = [];
                switch ($type) {
                    case Wheel::TYPE_INDIVIDUAL:
                        $wheels = $assessment->individualWheels;
                        break;
                    case Wheel::TYPE_GROUP:
                        $wheels = $assessment->groupWheels;
                        break;
                    default :
                        $wheels = $assessment->organizationalWheels;
                        break;
                }

                foreach ($wheels as $wheel)
                    if ($wheel->observer_id == $memberId && $wheel->answerStatus != '100%') {
                        $this->sendWheel($wheel);
                        $sent = true;
                        break;
                    }
            }
        }

        if ($sent == false)
            \Yii::$app->session->addFlash('info', \Yii::t('assessment', 'Wheel already fullfilled. Email not sent.'));
        return $this->redirect(['/assessment/view', 'id' => $assessment->id]);
    }

    public function actionDetailView($id, $type) {
        $assessment = Assessment::findOne(['id' => $id]);

        return $this->render('detail_view', [
                    'assessment' => $assessment,
                    'type' => $type,
        ]);
    }

    public function actionToggleAutofill($id) {
        $assessment = Assessment::findOne(['id' => $id]);

        $assessment->autofill_answers = !$assessment->autofill_answers;
        $assessment->save();

        return $this->redirect(['/assessment/view', 'id' => $assessment->id]);
    }

    public function actionGoToDashboard($id) {
        $assessment = Assessment::findOne(['id' => $id]);
        $filter = new DashboardFilter();

        $filter->companyId = $assessment->team->company_id;
        $filter->teamId = $assessment->team->id;
        $filter->assessmentId = $id;
        $filter->wheelType = Wheel::TYPE_GROUP;

        Yii::$app->session->set('DashboardFilter', $filter);
        $this->redirect(['/dashboard']);
    }

    private static function newToken() {
        $token_exists = true;
        while ($token_exists) {
            $number = rand(1000000000, 1999999999);
            $string = (string) $number;
            $newToken = $string[1] . $string[2] . $string[3] . '-' .
                    $string[4] . $string[5] . $string[6] . '-' .
                    $string[7] . $string[8] . $string[9];

            $token_exists = Wheel::doesTokenExist($newToken);
        }
        return $newToken;
    }

    private function sendWheel($wheel) {
        $wheel_type = Wheel::getWheelTypes()[$wheel->type];
        Yii::$app->mailer->compose('wheel', [
                    'wheel' => $wheel,
                ])
                ->setSubject(Yii::t('assessment', 'CPC: access to {wheel_type} of assessment {assessment}', [
                            'wheel_type' => $wheel_type,
                            'assessment' => $wheel->assessment->name,
                ]))
                ->setFrom(Yii::$app->params['adminEmail'])
                ->setTo($wheel->observer->email)
                ->setBcc(Yii::$app->params['adminEmail'])
                ->send();

        SiteController::addFlash('success', \Yii::t('assessment', '{wheel_type} sent to {user}.', ['wheel_type' => $wheel_type, 'user' => $wheel->observer->fullname]));
    }

}


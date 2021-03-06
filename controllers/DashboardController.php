<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\helpers\ArrayHelper;
use yii\filters\VerbFilter;
use app\models\Wheel;
use app\models\TeamMember;
use app\models\Team;
use app\models\Assessment;
use app\models\Company;
use app\models\DashboardFilter;

class DashboardController extends BaseController {

    public $layout = 'inner';

    public function actionIndex() {
        $filter = Yii::$app->session->get('DashboardFilter') ? : new DashboardFilter();
        if ($filter->load(Yii::$app->request->post())) {
            Yii::$app->session->set('DashboardFilter', $filter);
            $this->redirect(['/dashboard']);
        }

        $companies = [];
        $teams = [];
        $assessments = [];
        $members = [];

        $companies = ArrayHelper::map(Company::browse()->asArray()->all(), 'id', 'name');
        if (count($companies) == 1) {
            foreach ($companies as $id => $fullname) {
                $filter->companyId = $id;
                break;
            }
        }

        if ($filter->companyId > 0) {
            $teamQuery = Team::find()
                    ->where(['company_id' => $filter->companyId])
                    ->with(['coach', 'company'])
                    ->all();
            $teams = ArrayHelper::map($teamQuery, 'id', 'fullname');

            if (count($teams) == 1) {
                foreach ($teams as $id => $fullname) {
                    $filter->teamId = $id;
                    break;
                }
            } else {
                $exists = false;
                foreach ($teams as $id => $fullname)
                    if ($id == $filter->teamId) {
                        $exists = true;
                        break;
                    }

                if (!$exists) {
                    $filter->teamId = 0;
                    $filter->assessmentId = 0;
                }
            }
        }

        if ($filter->teamId > 0) {
            $assessmentQuery = Assessment::find()
                    ->where(['team_id' => $filter->teamId])
                    ->with(['team', 'team.company'])
                    ->all();
            $assessments = ArrayHelper::map($assessmentQuery, 'id', 'name');

            if (count($assessments) == 1) {
                foreach ($assessments as $id => $fullname) {
                    $filter->assessmentId = $id;
                    break;
                }
            } else {
                $exists = false;
                foreach ($assessments as $id => $name)
                    if ($id == $filter->assessmentId) {
                        $exists = true;
                        break;
                    }

                if (!$exists)
                    $filter->assessmentId = 0;
            }

            foreach (TeamMember::find()->where(['team_id' => $filter->teamId])->all() as $teamMember)
                $members[$teamMember->user_id] = $teamMember->member->fullname;
        }

        $members[0] = Yii::t('app', 'All');

        $projectedIndividualWheel = [];
        $projectedGroupWheel = [];
        $projectedOrganizationalWheel = [];
        $reflectedGroupWheel = [];
        $reflectedOrganizationalWheel = [];

        $individualPerformanceMatrix = [];
        $performanceMatrix = [];

        $relationsMatrix = [];

        $gauges = [];
        $emergents = [];

        if ($filter->memberId > 0 && $filter->wheelType == Wheel::TYPE_INDIVIDUAL) {

            $projectedIndividualWheel = Wheel::getProjectedIndividualWheel($filter->assessmentId, $filter->memberId);
            $projectedGroupWheel = Wheel::getProjectedGroupWheel($filter->assessmentId, $filter->memberId);
            $projectedOrganizationalWheel = Wheel::getProjectedOrganizationalWheel($filter->assessmentId, $filter->memberId);
            $reflectedGroupWheel = Wheel::getReflectedGroupWheel($filter->assessmentId, $filter->memberId);
            $reflectedOrganizationalWheel = Wheel::getReflectedOrganizationalWheel($filter->assessmentId, $filter->memberId);

            $emergents = Wheel::getMemberEmergents($filter->assessmentId, $filter->memberId, Wheel::TYPE_INDIVIDUAL);
        } else if ($filter->assessmentId > 0 && $filter->wheelType > 0) {
            $performanceMatrix = Wheel::getPerformanceMatrix($filter->assessmentId, $filter->wheelType);
            $relationsMatrix = Wheel::getRelationsMatrix($filter->assessmentId, $filter->wheelType);

            if ($filter->memberId > 0) {
                $gauges = Wheel::getMemberGauges($filter->assessmentId, $filter->memberId, $filter->wheelType);
                $emergents = Wheel::getMemberEmergents($filter->assessmentId, $filter->memberId, $filter->wheelType);
            } else {
                $gauges = Wheel::getGauges($filter->assessmentId, $filter->wheelType);
                $emergents = Wheel::getEmergents($filter->assessmentId, $filter->wheelType);
            }
        }

        $selected_member_index = 0;
        foreach ($members as $id => $name) {
            if ($id == $filter->memberId)
                break;
            $selected_member_index++;
        }

        return $this->render('index', [
                    'filter' => $filter,
                    'companies' => $companies,
                    'teams' => $teams,
                    'assessments' => $assessments,
                    'members' => $members,
                    // Indivudual wheel
                    'projectedIndividualWheel' => $projectedIndividualWheel,
                    'projectedGroupWheel' => $projectedGroupWheel,
                    'projectedOrganizationalWheel' => $projectedOrganizationalWheel,
                    'reflectedGroupWheel' => $reflectedGroupWheel,
                    'reflectedOrganizationalWheel' => $reflectedOrganizationalWheel,
                    'individualPerformanceMatrix' => $individualPerformanceMatrix,
                    // group wheel
                    'performanceMatrix' => $performanceMatrix,
                    'gauges' => $gauges,
                    'relationsMatrix' => $relationsMatrix,
                    'emergents' => $emergents,
        ]);
    }

}

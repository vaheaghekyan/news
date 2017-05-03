<?php

namespace backend\modules\api\controllers;

use backend\models\Continent;
use backend\models\Country;
use yii\web\Controller;
use Yii;
use yii\filters\AccessControl;
use backend\components\AccessRule;
use yii\filters\VerbFilter;

class CountryController extends Controller
{

    public $defaultAction           = "find";

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'ruleConfig' => [
                    'class' => AccessRule::className(),
                ],
                'rules' => [
                    [
                        'actions' => ['find'],
                        'allow' => true,
                        'roles' => [],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'find' => ['get'],

                ],
            ],
        ];
    }

}
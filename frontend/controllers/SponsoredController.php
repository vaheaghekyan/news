<?php

namespace frontend\controllers;

use Yii;
use backend\models\Story;
use backend\models\SponsoredStory;
use backend\models\SponsoredLevelTwo;
use yii\web\Controller;
use common\components\Helpers as CommonHelpers; 

use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\filters\AccessRule;

class SponsoredController extends \yii\web\Controller
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                /*'ruleConfig' => [
                    'class' => AccessRule::className(),
                ], */
                'rules' => [
                    [
                        'actions' => ['sponsored-level-two'],
                        'allow' => true,
                        'roles' => ['?', '@'],
                    ]
                ],
            ],

            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                    'edition' => ['post'],
                ],
            ],
        ];
    }

     /**
     * Displays a single Sponsored Story in new tab.
     * @param integer $id
     * @return mixed
     * $id is id of story in Story
     */
    public function actionSponsoredLevelTwo($id)
    {
        $sponsoredTable=SponsoredLevelTwo::tableName();
        $storyTable=Story::tableName();

        $sponsoredStoryTable=SponsoredStory::tableName();

        //find current sponsored story
        //$model = Story::find()->where([$storyTable.'.id'=>$id])->joinWith(["relationSponsoredLevelTwo"])->one();
        //$model = SponsoredLevelTwo::find()->where(['story_id'=>$id])->one();
        $model = SponsoredStory::find()->where([$sponsoredStoryTable.'.story_id'=>$id])->one();
        $sponsoredId = $model->id;
        $model = SponsoredLevelTwo::find()->where(['sponsored_story_id'=>$sponsoredId])->one();

        $image_file=CommonHelpers:: getPathToSponsoredPicture($model, Story::PATH_IMAGE, "image_file") ;
        $logo=CommonHelpers:: getPathToSponsoredPicture($model, Story::PATH_IMAGE, "logo") ;

        $image_file='<img src="'.$image_file.'" alt="'.$model->caption.'" class="img-responsive center-block main-img" />
                    <div class="caption center-block">'.$model->caption.'<div class="hr"></div></div>';
        $logo='<img src="'.$logo.'" alt="'.$model->caption.'" class="img-responsive" />';

        //wufoo code, remove new lines and be careful of quotes
        $wufoo = str_replace("'", "\"", $model->wufoo_code);
        $wufoo = str_replace(["\r", "\n"], " ", $wufoo);
        $wufoo = str_replace("</script>", "<\/script>", $wufoo);

        return $this->renderPartial('sponsored-level-two', [
            'model' => $model,
            'image_file'=>$image_file,
            'logo'=>$logo,
            'wufoo'=>$wufoo
        ]);
    }

}

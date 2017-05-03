<?php
namespace backend\controllers;

use backend\controllers\MyController;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use backend\components\DataTable;
use backend\models\Story;
use backend\models\search\StorySearch;
use backend\models\Language;
use backend\models\CategoryStory;
use backend\models\CountryStory;
use backend\models\Category;
use backend\models\Country;
use backend\models\User;
use backend\models\CountryExt;
use backend\models\StoryClipkit;
use backend\models\Story3rdPartyVideo;
use backend\models\SponsoredLevelTwo;
use backend\models\SponsoredStory;
use backend\components\AccessRule;
use backend\components\Helpers;
use common\components\Helpers as CommonHelpers;
use backend\models\StoryKeyword;
use backend\components\MyMixpanel;

use yii\web\ForbiddenHttpException;
use yii\web\UploadedFile;
use yii\helpers\FileHelper;
use yii\helpers\Url;
use backend\models\CategoriesLevelOne;
use yii\web\NotFoundHttpException;

/*
* main story controller for handling stories
*/
class StoryController extends MyController {

    //public $enableCsrfValidation = false;
    public $layout = "admin";

    public function beforeAction($action)
    {
        $action_tmp=Yii::$app->controller->action->id;

        //becuase you are posting to view via ajax
        if($action_tmp=='view' || $action_tmp=='auto-save')
        {
            $this->enableCsrfValidation = false;
        }
        return parent::beforeAction($action);
    }

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
                        'actions' => ['update', 'index', 'create', 'upload-image', 'delete-temp',
                                        'unpublished', 'pending', 'published', 'view'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['delete', 'publish', 'unpublish', 'schedule-publish', 'auto-save'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['sponsored'],
                        'allow' => true,
                        'roles' => [ User::ROLE_SUPERADMIN, User::ROLE_MARKETER],
                    ]
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'find'      => ['get'],
                    'delete'    => ['post'],
                    'unpublish' => ['post'],
                    'publish'   => ['post'],
                    'schedule-publish' => ['post'],
                    'update'    => ['get', 'post'],
                    'approve'   => ['put']
                ],
            ],
        ];
    }

    /*
    *  auto save story
    */
    public function actionAutoSave()
    {
        if(isset($_POST["Story"]["title"]) && isset($_POST["ID"]))
        {
            $return=Helpers::storySession("set", $_POST["ID"], NULL);
            echo json_encode(["return_data"=>$return]);
        }
    }


    /*
    *  Create story
    */
    public function actionCreate()
    {
        //get story type so you can set scenario
        if(Yii::$app->request->get('story_type'))
            $_SESSION['story_type']=Yii::$app->request->get("story_type");
        else if(!isset($_SESSION['story_type']))
            $_SESSION['story_type']=Story::TYPE_IMAGE;

        //set story type in variable from session
        $story_type=$_SESSION['story_type'];
        //set scenario
        $scenario=$this->setScenario($story_type);

        $model              = new Story();
        $StoryKeyword       = new StoryKeyword;
        $StoryClipkit       = new StoryClipkit;
        $Story3rdPartyVideo = new Story3rdPartyVideo;
        $SponsoredLevelTwo  = new SponsoredLevelTwo;
        $model->scenario    = $scenario;

        $mode               = Yii::$app->request->get('mode');
        $languageID         = Language::getCurrentId(); // for example: 7

        if (Yii::$app->request->isPost)
        {
            if ($model->load(Yii::$app->request->post()) && $StoryKeyword->load(Yii::$app->request->post()))
            {
                $sponsored=false;
                if(Yii::$app->request->post("sponsored") && Yii::$app->request->post("sponsored")==1)
                    $sponsored=true;

                $model->seo_url=Helpers::url_slug($model->title);
                $model->language_id = $languageID;
                $model->type = $story_type;
                if($sponsored==true)
                    $model->sponsored_story = 1;

                //since admin can choose which user he wants to choose, check if he chose any by checking is user_id is already set
                if(empty($model->user_id))
                    $model->user_id = Yii::$app->user->getId();

                $videoFile = UploadedFile::getInstanceByName("video");
                if ($videoFile)
                {
                    if ($videoFile->error == UPLOAD_ERR_FORM_SIZE || $videoFile->error == UPLOAD_ERR_INI_SIZE) {

                        $model->addError("upload_video_field", "Video is too big, max file size is " . ini_get("upload_max_filesize") . "b");

                    }
                    elseif (!in_array($videoFile->getExtension(), ['flv', 'mpg', 'mp4']) /*|| !in_array(FileHelper::getMimeType($videoFile->tempName), ['video/mpeg', 'video/x-flv', 'video/mp4', 'video/quicktime'])*/ ) {

                        $model->addError("upload_video_field", "Video file has a wrong format. Allowed formats are: flv and mpg");
                    }
                }

                if (!$model->hasErrors() && $model->validate())
                {

                    $model->status = Story::STATUS_PENDING;
                    $model->date_created = $model->date_modified = date("Y-m-d H:i:s");
                    //for schedule stories
                    if(Yii::$app->request->post("date_published")&& !empty($_POST["date_published"]))
                    {
                        $model->date_published=Yii::$app->request->post("date_published");
                        $model->status = Story::STATUS_PUBLISHED;
                    }

                    $model->save();

                    //**************Save categories**************
                    if (($category = Yii::$app->request->post("category")))
                    {

                        foreach ($category as $id)
                        {
                            $categoryStory = new CategoryStory();
                            $categoryStory->category_id = $id;
                            $categoryStory->story_id = $model->id;
                            if($categoryStory->save())
                            {
                                //Mixpanel track categories
                                MyMixpanel::TrackNewStoriesCategory($categoryStory->relationCategory->name);
                            }
                        }
                        //if this is sponsored story save it on SPONSORED
                        if($sponsored==true)
                        {
                            $sponsored_type=Yii::$app->request->post("sponsored_type");

                            $SponsoredStory=new SponsoredStory;
                            $SponsoredStory->story_id=$model->id;
                            $SponsoredStory->sponsored_type=$sponsored_type;
                            $SponsoredStory->save();

                            if($sponsored_type==SponsoredStory::SPONSORED_TYPE_IA)
                            {
                                //save sponsored story info data
                                if($SponsoredLevelTwo->load(Yii::$app->request->post()))
                                {
                                    $date=date("Y-m-d");
                                    //upload logo
                                    $logo=Helpers::fileUpload($_FILES["logo"]["name"], $_FILES["logo"]["tmp_name"], Story::PATH_IMAGE, $date);
                                    //upload image
                                    $image_file=Helpers::fileUpload($_FILES["image_file"]["name"], $_FILES["image_file"]["tmp_name"], Story::PATH_IMAGE, $date);
                                    $SponsoredLevelTwo->logo=$logo["file_name"];
                                    $SponsoredLevelTwo->image_file=$image_file["file_name"];
                                    $SponsoredLevelTwo->sponsored_story_id=$SponsoredStory->id;
                                    $SponsoredLevelTwo->type=$sponsored_type;
                                    $SponsoredLevelTwo->save();
                                }
                            }

                            //get model for sponsored category, its child
                            $SponsoredCategory= CategoriesLevelOne::getSponsoredCategory();
                            $categoryStory = new CategoryStory();
                            $categoryStory->category_id = $SponsoredCategory->id;
                            $categoryStory->story_id = $model->id;
                            if($categoryStory->save())
                            {
                                //Mixpanel track categories
                                MyMixpanel::TrackNewStoriesCategory($categoryStory->relationCategory->name);
                            }
                        }
                    }
                    //**************Save countries**************
                    if (($country = Yii::$app->request->post("country")))
                    {
                        foreach ($country as $id)
                        {
                            $countryStory = new CountryStory();
                            $countryStory->country_id = $id;
                            $countryStory->story_id = $model->id;
                            if($countryStory->save())
                            {
                                //Mixpanel track countries
                                MyMixpanel::TrackNewStoriesCountry($countryStory->relationCountry->name);
                            }
                        }
                    }

                    //****************SCENARIO image-story********************
                    //because when we upload video also image is uploaded with video
                    if($model->scenario==Story::SCENARIO_IMAGE_STORY || $model->scenario==Story::SCENARIO_VIDEO_STORY)
                    {
                        //image name, taken from input on CMS create story page
                        $image_name=Yii::$app->request->post("image_name");
                        $image_name=Helpers::url_slug($image_name);
                        $image_name=$image_name."-".$model->id;

                        //**************image file**************
                        if ( ( $imageData = Yii::$app->request->post('imagedata') ) )
                        {
                            $path=Story::getFullDirectoryToImageVideo($model->date_created, Story::PATH_IMAGE, null, true);
                            $path_thumb=Story::getFullDirectoryToImageVideo($model->date_created, Story::PATH_IMAGE_THUMB, null, true);
                            //if path doesn't exists create it
                            //always check absolute path because mk dir works with absolute path better then relative
                            if(!file_exists($path))
                                mkdir($path,0755,true);

                            if(!file_exists($path_thumb))
                                mkdir($path_thumb,0755,true);

                            //THIS IS WHERE IMAGE IS UPLOADED
                            $model->image = $model->copyImage($imageData, $image_name);
                            //create thumbnail for image
                            $model->getImage(Story::THUMB_WIDTH, Story::THUMB_HEIGHT, $model);
                            $model->save(false, array("image"));
                        }

                        //****************SCENARIO video-story********************
                        //image is required for video story
                        if($model->scenario==Story::SCENARIO_VIDEO_STORY)
                        {
                            //**************video file**************
                            $videoFile = UploadedFile::getInstanceByName("video");
                            if ($videoFile)
                            {
                                $video_name=$model->id;
                                $path=Story::getFullDirectoryToImageVideo($model->date_created, Story::PATH_VIDEO, null, true);
                                $model->video=$model->uploadVideo($model, $videoFile, $video_name);
                                $model->save(false, array("video"));
                            }
                        }

                    }
                    //****************SCENARIO clipkit-story********************
                    else if($model->scenario==Story::SCENARIO_CLIPKIT_STORY)
                    {
                        if ($StoryClipkit->load(Yii::$app->request->post()))
                        {
                            $StoryClipkit->story_id=$model->id;
                            $StoryClipkit->save();
                        }
                    }
                    //****************SCENARIO 3rd-party-video-story********************
                    else if($model->scenario==Story::SCENARIO_3RD_PARTY_VIDEO_STORY)
                    {
                        if ($Story3rdPartyVideo->load(Yii::$app->request->post()))
                        {
                            $Story3rdPartyVideo->story_id=$model->id;
                            $Story3rdPartyVideo->save();
                        }
                    }

                    //save story keywords
                    $StoryKeyword->story_id=$model->id;
                    $StoryKeyword->save();

                    //Mixpanel track new story
                    MyMixpanel::TrackNewStories(MyMixpanel::PROPERTY_CMS_TYPE_NEW);

                    //reset session files, but reset session files that are marked with -1, not with model id
                    Helpers::storySession("reset", -1, NULL);

                    Yii::$app->getSession()->setFlash('success', Yii::t("app", "You have successfully created the story"));
                    if ( $mode == 'preview' )
                    {

                        return $this->redirect(['/story/update', 'id' => $model->id, 'mode' => 'preview']);

                    }
                    else
                    {

                        return $this->redirect(['/story/pending']);
                    }

                }

            }
            else
            {

                ob_clean();
                $model->addError("upload_image_field", "Uploaded image should not be more than " . ini_get("upload_max_filesize") . "b");
                $model->addError("upload_video_field", "Uploaded video should not be more than " . ini_get("upload_max_filesize") . "b");

            }

        }


        $countries  = $this->returnCountriesForUser();
        $categories = Category::getParents();

        return $this->render("create", array(
            'model'         => $model,
            'countries'     => $countries,
            'categories'    => $categories,
            'StoryKeyword'  => $StoryKeyword,
            'StoryClipkit'  => $StoryClipkit,
            'SponsoredLevelTwo' => $SponsoredLevelTwo,
            'Story3rdPartyVideo' => $Story3rdPartyVideo

        ));
    }

    /*
    * send email about created story to eddie
    */
    private function sendEmailAboutCreatedStory($story)
    {
        /*
        Editor's Name
        Language
        Countries
        Title
        Category
        Snippet
        Link
        */
        $tableStory=Story::tableName();
        $story=Story::find()->where([$tableStory.'.id'=>$story->id])->joinWith(['relationCountries', 'relationSubCategories', 'relationLanguage', 'relationUser'])->one();

        //send all to this email
        $users[0]["email"]="news.content@born2invest.com";
        $users[0]["name"]="Eddie Rios";

        $countries=[];
        foreach($story->relationCountries as $value)
        {
            $countries[]=$value->name;
        }

        $categories=[];
        foreach($story->relationSubCategories as $value)
        {
            $categories[]=$value->name;
        }
        $subject=$story->relationLanguage->name;
        $message=NULL;
        $message.="<b>ID: </b>".$story->id."<br>";
        $message.="<b>Editor's Name:</b> ".$story->relationUser->name."<br>";
        $message.="<b>Language:</b> ".$story->relationLanguage->name."<br>";
        $message.="<b>Countries:</b> ".implode(",", $countries)."<br>";
        $message.="<b>Title:</b> ".$story->title."<br>";
        $message.="<b>Categories:</b> ".implode(",", $categories)."<br>";
        $message.="<b>Snippet:</b> ".$story->description."<br>";
        $message.="<b>Link:</b> <a href='$story->link'>$story->link</a><br>";

        $from["email"]=$story->relationUser->email;
        $from["name"]=$story->relationUser->name;

        CommonHelpers::sendEmailToMultiplePeople($subject, $message, $users, $from);
    }

    /*
    * Update story
    * $id = id in Stories
    */
    public function actionUpdate($id)
    {
        //Yii::$app->cache->flush();
        Yii::$app->user->returnUrl = Url::to(['update', 'id'=>$id]);
        $storyId=$id;
        $SponsoredLevelTwo = new SponsoredLevelTwo; //it has to be here because I'm sending this to view

        $mode   = Yii::$app->request->get('mode');
        $story = Story::findOne($storyId);

        //detect story type and set scenario
        $scenario=$this->setScenario($story->type);
        $story->scenario=$scenario;

        //find if there are any keywords there for this story
        $StoryKeyword = StoryKeyword::find()->where(['story_id'=>$storyId])->one();
        if(empty($StoryKeyword))
            $StoryKeyword = new StoryKeyword;

        //if this is clipkit story
        if($story->scenario==Story::SCENARIO_CLIPKIT_STORY)
            $StoryClipkit=StoryClipkit::find()->where(['story_id'=>$storyId])->one();
        else
            $StoryClipkit = new StoryClipkit;

        //if this is 3rd party video
        if($story->scenario==Story::SCENARIO_3RD_PARTY_VIDEO_STORY)
            $Story3rdPartyVideo=Story3rdPartyVideo::find()->where(['story_id'=>$storyId])->one();
        else
            $Story3rdPartyVideo = new Story3rdPartyVideo;

        if ($story)
        {
            $user = Yii::$app->user->getIdentity();

            if ($user->role == User::ROLE_EDITOR && $user->id != $story->user_id)
            {
                throw new ForbiddenHttpException("You are not allowed to edit this story");
            }

            //used for image/video file upload input to show name of image/video so you can preview it without uploading again
            if ($story->scenario==Story::SCENARIO_IMAGE_STORY)
            {
                $story->upload_image_field = $story->image;
            }
            else if($story->scenario==Story::SCENARIO_VIDEO_STORY)
            {
                //on video upload, upload image also
                $story->upload_image_field = $story->image;
                $story->upload_video_field = $story->video;
            }

            //if this is sponsored story load data for it from database or get data from POST
            if($story->sponsored_story==1)
            {
                //is this "investor acquisition" story
                $SponsoredLevelTwo = new SponsoredLevelTwo;
                $sponsored_type=$story->relationSponsoredStories->sponsored_type;
                if($sponsored_type==SponsoredStory::SPONSORED_TYPE_IA)
                    $SponsoredLevelTwo = $story->relationSponsoredStories->relationSponsoredLevelTwo;

               //try to load from POST and if you cannot then find in database
                if (Yii::$app->request->isPost)
                {
                    //save sponsored story if it is IA
                    if($sponsored_type==SponsoredStory::SPONSORED_TYPE_IA)
                    {
                        if($SponsoredLevelTwo->load(Yii::$app->request->post()))
                        {
                            //if logo is uploaded
                            if(isset($_FILES["logo"]["name"]) && !empty($_FILES["logo"]["name"]))
                            {
                                //delete old image
                                $old_image=Story::getFullDirectoryToImageVideo($SponsoredLevelTwo->date_created, Story::PATH_IMAGE, $SponsoredLevelTwo->logo, true);
                                unlink($old_image);

                                //upload logo
                                $logo=Helpers::fileUpload($_FILES["logo"]["name"], $_FILES["logo"]["tmp_name"], Story::PATH_IMAGE, $SponsoredLevelTwo->date_created);
                                $SponsoredLevelTwo->logo=$logo["file_name"];
                            }

                            //if image_file is uploaded
                            if(isset($_FILES["image_file"]["name"]) && !empty($_FILES["image_file"]["name"]))
                            {
                                //delete old image
                                $old_image=Story::getFullDirectoryToImageVideo($SponsoredLevelTwo->date_created, Story::PATH_IMAGE, $SponsoredLevelTwo->image_file, true);
                                unlink($old_image);

                                //upload image
                                $image_file=Helpers::fileUpload($_FILES["image_file"]["name"], $_FILES["image_file"]["tmp_name"], Story::PATH_IMAGE, $SponsoredLevelTwo->date_created);
                                $SponsoredLevelTwo->image_file=$image_file["file_name"];
                            }
                            $SponsoredLevelTwo->update();
                        }
                    }
                }
            }

            if ( Yii::$app->request->isPost )
            {
                if ($story->load(Yii::$app->request->post()) && $StoryKeyword->load(Yii::$app->request->post()))
                {

                    //for schedule stories
                    if(isset($_POST["date_published"]) && !empty($_POST["date_published"]))
                    {
                        $story->date_published=$_POST["date_published"];
                        $story->status = Story::STATUS_PUBLISHED;
                    }

                    //*************VIDEO UPLOAD - ERROR CHECK*************
                    $videoFile = UploadedFile::getInstanceByName("video");
                    if ($videoFile)
                    {

                        if ($videoFile->error == UPLOAD_ERR_FORM_SIZE || $videoFile->error == UPLOAD_ERR_INI_SIZE) {

                            $story->addError("upload_video_field", "Video is too big, max file size is " . ini_get("upload_max_filesize") . "b");

                        }
                        elseif (!in_array($videoFile->getExtension(), ['flv', 'mpg', 'mp4']) /*||
                            !in_array(FileHelper::getMimeType($videoFile->tempName), ['video/mpeg', 'video/x-flv', 'video/mp4', 'video/quicktime'])*/
                        )
                        {

                            $story->addError("upload_video_field", "Video file has a wrong format. Allowed formats are: flv and mpg");
                        }

                    }
                    //************* everything is fine save categories *************
                    if (!$story->hasErrors() && $story->validate())
                    {
                        //Save categories
                        CategoryStory::deleteAll(['story_id' => $story->id]);
                        if (($category = Yii::$app->request->post("category")))
                        {

                            foreach ($category as $id)
                            {
                                $categoryStory = new CategoryStory();
                                $categ2oryStory->category_id = $id;
                                $categoryStory->story_id = $story->id;
                                $categoryStory->save();
                            }
                            Yii::$app->getSession()->setFlash('success', Yii::t("app", "You have successfully updated the story"));

                            //if this is sponsored story save it on SPONSORED
                            if($story->sponsored_story==1)
                            {
                                //get model for sponsored category, its child
                                $SponsoredCategory= CategoriesLevelOne::getSponsoredCategory();
                                $categoryStory = new CategoryStory();
                                $categoryStory->category_id = $SponsoredCategory->id;
                                $categoryStory->story_id = $story->id;
                                if($categoryStory->save())
                                {
                                    //Mixpanel track categories
                                    MyMixpanel::TrackNewStoriesCategory($categoryStory->relationCategory->name);
                                }
                            }
                        }


                        //*************Save countries*************
                        CountryStory::deleteAll(['story_id' => $story->id]);
                        if (($country = Yii::$app->request->post("country")))
                        {
                            foreach ($country as $id) {
                                $countryStory = new CountryStory();
                                $countryStory->country_id = $id;
                                $countryStory->story_id = $story->id;
                                $countryStory->save();
                            }
                        }

                        //****************SCENARIO image-story********************
                        if($story->scenario==Story::SCENARIO_IMAGE_STORY || $story->scenario==Story::SCENARIO_VIDEO_STORY)
                        {
                            //*************IMAGE RENAMING*************
                            $imageData = Yii::$app->request->post('imagedata');

                            //image name, taken from input on CMS create story page
                            $image_name=$_POST["image_name"];

                            //check if user didn't upload image but still wants to rename image
                            if(!$imageData && $image_name!=$story->image && $story->image!=NULL)
                            {
                                //RENAME MAIN IMAGE
                                $directory=Story::getFullDirectoryToImageVideo($story->date_created, Story::PATH_IMAGE, null, true);
                                //get image extenstion
                                $ext=explode(".", $story->image);
                                //remove all non non-alphanumeric characters
                                $image_name=Helpers::url_slug($image_name);
                                //add unique id to image
                                $image_name=$image_name."-".$story->id.".".end($ext); //ths-is-image-2359.jpg
                                Helpers::renameFile($directory, $story->image, $image_name);

                                //RENAME THUMB IMAGE
                                $directory_thumb = Story::getFullDirectoryToImageVideo($story->date_created, Story::PATH_IMAGE_THUMB, null, true);
                                $thum_img_name_old=Story::getThumbImageName($story);
                                $thum_img_name_new=Story::getThumbImageName($image_name);
                                Helpers::renameFile($directory_thumb, $thum_img_name_old, $thum_img_name_new);

                                //set model attribute so you can save it to database
                                $story->image=$image_name;
                            }

                            //*************IMAGE UPLOAD*************
                            if ($imageData)
                            {
                                //check if image name from text input is the same as from database, if it is that means that editor  didn't change it
                                if($image_name==$story->image)
                                {
                                    //explode image name to get image extension
                                    $image_name_ex=explode(".", $image_name);
                                    //unset last element of array, a.k.a. image extenstion
                                    $array_keys=array_keys($image_name_ex);
                                    $last_index=end($array_keys);
                                    unset($image_name_ex[$last_index]);
                                    //set the same name as from database
                                    $image_name=implode("", $image_name_ex); //this-is-image-2225
                                }
                                else
                                    $image_name=$image_name."-".$story->id; //this-is-new-image-2225

                                //remove all non non-alphanumeric characters
                                //because of iOS url to image should be different, because iOS is caching old image on phone
                                $image_name=Helpers::url_slug($image_name).rand(1,9);

                                $story->deleteOldImages($story);
                                //THIS IS VERY IMAGE IS UPLOADED
                                $story->image = $story->copyImage($imageData, $image_name);
                                //create thumbnail for image
                                $story->getImage(Story::THUMB_WIDTH, Story::THUMB_HEIGHT, $story);
                            }

                            //****************SCENARIO video-story********************
                             //image is required for video story
                            if($story->scenario==Story::SCENARIO_VIDEO_STORY)
                            {
                                //*************VIDEO UPLOAD*************
                                if ($videoFile)
                                {
                                    $video_name=$story->id.rand(1,0);
                                    //delete any old video
                                    Story::deleteOldVideo($story);
                                    $story->video=$story->uploadVideo($story, $videoFile, $video_name);
                                }
                            }
                        }
                        //****************SCENARIO clipkit-story********************
                        else if($story->scenario==Story::SCENARIO_CLIPKIT_STORY)
                        {
                            if ($StoryClipkit->load(Yii::$app->request->post()))
                                $StoryClipkit->update();
                        }
                        //****************SCENARIO 3rd-party-video-story********************
                        else if($story->scenario==Story::SCENARIO_3RD_PARTY_VIDEO_STORY)
                        {
                            if ($Story3rdPartyVideo->load(Yii::$app->request->post()))
                            {
                                $Story3rdPartyVideo->update();
                            }
                        }

                        $story->seo_url=Helpers::url_slug($story->title);
                        $story->date_modified = date("Y-m-d H:i:s");
                        $story->update();

                        //save keywords
                        $StoryKeyword->story_id=$story->id; //because old stories don't have keywords and on update keywords won't be updated without it

                        $StoryKeyword->update();

                        //reset session files
                        Helpers::storySession("reset", $story, NULL);
                        if ( $mode == 'preview' )
                        {

                            return $this->redirect(['/story/update', 'id' => $story->id, 'mode' => 'preview']);

                        }
                        else
                        {

                            return $this->redirect(['/story/pending']);

                        }


                    }

                }
                else
                {
                    ob_clean();
                    $story->addError("upload_image_field", "Image is too big, max file size is " . ini_get("upload_max_filesize") . "b");
                    $story->addError("upload_video_field", "Uploaded video should not be more than " . ini_get("upload_max_filesize") . "b");

                }

            }

            //take all countries and categories to list it in view
            $countries  = $this->returnCountriesForUser();
            $categories = Category::getParents();
            //find all categories in  category_stories for specific story so that you can check checkboxlist
            $selectedCheckbox_category = CategoryStory::find()->where(['story_id'=>$story->id])->all();
            //find all countries in country_stories for specific story so you know which checkbox to check
            $selectedCheckbox_countries = CountryStory::find()->where(['story_id'=>$story->id])->all();


            return $this->render("update", array(
                'model'     => $story,
                'countries' => $countries,
                'categories'=> $categories,
                'mode'      => $mode,
                'selectedCheckbox_category'=>$selectedCheckbox_category,
                'selectedCheckbox_countries'=>$selectedCheckbox_countries,
                'StoryKeyword' => $StoryKeyword,
                'StoryClipkit' => $StoryClipkit,
                'SponsoredLevelTwo'=>$SponsoredLevelTwo,
                'Story3rdPartyVideo'=>$Story3rdPartyVideo
            ));
        }
        else
        {

            throw new ForbiddenHttpException("Page not found", 404);

        }
    }

    /*
    * depending on story type, set scenario
    */
    private function setScenario($switch)
    {
        switch($switch)
        {
            case Story::TYPE_IMAGE:
                $scenario=Story::SCENARIO_IMAGE_STORY;
                break;
            case Story::TYPE_VIDEO:
                $scenario=Story::SCENARIO_VIDEO_STORY;
                break;
            case Story::TYPE_CLIPKIT:
                $scenario=Story::SCENARIO_CLIPKIT_STORY;
                break;
            case Story::TYPE_3RD_PARTY_VIDEO:
                $scenario=Story::SCENARIO_3RD_PARTY_VIDEO_STORY;
                break;
            default:
                $scenario=Story::SCENARIO_IMAGE_STORY;
                break;
        }

        return $scenario;
    }

    /*
    *  find all countries where user's language is spoken and list it
    */
    private function returnCountriesForUser()
    {

        $tableCountryExt    = CountryExt::tablename();
        $tableCountry       = Country::tablename();

       /* $role=Yii::$app->user->getIdentity()->role;
        //If I'm admin just get all countries
        if($role==User::ROLE_ADMIN || $role==User::ROLE_SUPERADMIN || $role==User::ROLE_MARKETER)
        {
            $countries  = Country::listAllCountries();
        }
        //otherwise find all countries where specific lanuage(language of current user is) is spoken
        else
        {
            $languageId = Language::getCurrentId(); //for example: 7
            $countries = Country::listCountries($languageId);

        } */

        //return countries per language
        $languageId = Language::getCurrentId(); //for example: 7
        $countries = Country::listCountries($languageId);

        return $countries;
    }

    /*
    * This is function where I execute query for publishing, unpublishing and scheduling stories, since query is the same for all but with different values
    * $date_published
        date("Y-m-d- H:i:s")-published,
        null-unpublished (but leave previously date_published),
        "Story::STATUS_SCHEDULE"-scheduling, getting value from form
    */
    private function publishUnpublishSchedule($status, $date_published)
    {
        if(isset($_POST["selection"]) && !empty($_POST["selection"]))
        {
            //if user wants to schedule, get date
            if($date_published==Story::STATUS_SCHEDULE)
            {
                $date_published=$_POST["Story"]["date_published"];
                if(empty($date_published))
                {
                    Yii::$app->session->setFlash('danger', Yii::t('app', 'Select the date'));
                    return $this->goBack();
                }

            }

            $selection=$_POST["selection"]; //it is checkbox array and value is "id" in Story
            foreach ($selection as $key=>$IDstory)
            {
                $story=Story::findOne($IDstory);
                if($date_published==NULL)//unpublish story but leave last date_published
                    $date_published=$story->date_published;

                $story->status = $status;
                $story->date_published  = $date_published;
                $story->date_modified  = date("Y-m-d H:i:s"); //this is necesary because of cache dependancy so cache can change
                $story->save(false, ['status', 'date_published', 'date_modified']);
            }
            Yii::$app->session->setFlash('success', Yii::t('app', 'Everything went fine'));
        }
        else
            Yii::$app->session->setFlash('danger', Yii::t('app', 'Something was wrong'));

        return $this->goBack();
    }

    /*
    *  publish specific stories
    */
    public function actionPublish()
    {
        //Mixpanel track new published story
        //MyMixpanel::TrackNewStories(MyMixpanel::PROPERTY_CMS_TYPE_PUBLISHED);
        $this->publishUnpublishSchedule(Story::STATUS_PUBLISHED, date('Y-m-d H:i:s'));

        if(Yii::$app->request->post("selection") && !empty($_POST["selection"]))
        {
            //send email about story when story is published
            $selection=Yii::$app->request->post("selection"); //it is checkbox array and value is "id" in Story
            foreach ($selection as $key=>$IDstory)
            {
                $story=Story::findOne($IDstory);
                //send email to specific peopel about that this story was created
                $this->sendEmailAboutCreatedStory($story);
            }
        }
    }

    /**
    *  unpublish stories
    */
    public function actionUnpublish()
    {
        $this->publishUnpublishSchedule(Story::STATUS_UNPUBLISHED, null);
    }

    /*
    *  schedule stories for later
    */
    public function actionSchedulePublish()
    {
        $this->publishUnpublishSchedule(Story::STATUS_PUBLISHED, Story::STATUS_SCHEDULE);
    }

    /*
    *  delete story
    */
    public function actionDelete($id)
    {
        $user = Yii::$app->user->getIdentity();
        $story  = Story::findOne($id);

        //check if this is user's story, if not, he cannot delete it
        if($user->role==User::ROLE_EDITOR && $story->user_id!=$user->id)
            throw new \yii\web\HttpException(403, Yii::t('app', 'You cannot delete story'));

        if(empty($story))
            throw new \yii\web\HttpException(403, Yii::t('app', "Story doesn't exist, go back and try again."));

        if($story->delete())
        {
            //get files name
            $thumb_name=Story:: getThumbImageName($story);
            $path_to_image=Story::getFullDirectoryToImageVideo($story->date_created, Story::PATH_IMAGE, $story->image, true);
            $path_to_image_thumb=Story::getFullDirectoryToImageVideo($story->date_created, Story::PATH_IMAGE_THUMB, $thumb_name, true);
            $path_to_video=Story::getFullDirectoryToImageVideo($story->date_created, Story::PATH_VIDEO, $story->video, true);

            //delete all files related to the story
            if(file_exists($path_to_image_thumb) && $story->image!=NULL)
                unlink($path_to_image_thumb);

            if(file_exists($path_to_image) && $story->image!=NULL)
                unlink($path_to_image);

            if(file_exists($path_to_video) && $story->video!=NULL)
                unlink($path_to_video);

            //temp mixpanel record deleted story
            MyMixpanel::TrackNewStoriesPublished();
            Yii::$app->session->setFlash('success', Yii::t('app', 'Everything went fine'));
            return $this->redirect(['/story/published']);
        }

    }

   /* public function actionApprove()
    {
        if (( $storyId = Yii::$app->request->post("storyId")) ) {

            $story  = Story::findOne( $storyId );

            $story->status = Story::STATUS_APPROVED;
            $story->save(false, array("status"));
            return json_encode(array("success" => true));

        }
    } */

    /*public function actionPreview( $storyId )
    {
        if ( $storyId && ( $story = Story::findOne( $storyId ) ) ) {

            return $this->renderPartial("preview", ['story' => $story]);

        }
    }  */

    /*
    * used in create-story.js to delete temp image
    * after user tries to upload image it will be saved in temp folder and modal popup will show up to crop image, if user clicks on Cancel call this
    */
    public function actionDeleteTemp()
    {

        if ( Yii::$app->request->isPost &&
            ( $fileName = Yii::$app->request->post('fileName')) &&
            strstr($fileName, Story::PATH_TEMP_IMAGE) !== false &&
            file_exists(getcwd() . $fileName ) ) {

            unlink( getcwd() . $fileName );
            echo "deleted";

        }
        return false;

    }

    /*
    * upload image to temp folder so you can crop it
    * you can use it to limit image size
    */
    public function actionUploadImage()
    {

        $imageFile  = UploadedFile::getInstanceByName("file");
        $error      = null;
        $success    = false;
        $imageName  = null;
        $width      = 0;
        $height     = 0;

        if ( !$imageFile ) {

            $error = "Please choose image for uploading";

        } elseif ($imageFile->error == UPLOAD_ERR_FORM_SIZE || $imageFile->error == UPLOAD_ERR_INI_SIZE)
        {

            $error = "Image is too big, max file size is " . ini_get("upload_max_filesize");

        }
        elseif (!in_array($imageFile->getExtension(), ['jpg', 'jpeg', 'png', 'gif'])         ) {

            $error = "Image file has a wrong format. Allowed formats are: jpg, jpeg, png, gif";

        }
        elseif ( !($sizes = @getimagesize( $imageFile->tempName ) ) ||
                ( $sizes[0] < Story::THUMB_WIDTH || $sizes[1] < Story::THUMB_HEIGHT)
            )
        {
            $error = "Image should not be smaller than ".Story::THUMB_WIDTH."x".Story::THUMB_HEIGHT;
        }
        // if image os to big. larger then 3mb
        elseif($imageFile->size > Story::MAX_IMAGE_SIZE)
        {
            $image_size=Story::MAX_IMAGE_SIZE/1024/1024;
              $error = "Image should not be larger than ".round($image_size,2)."mb";
        }
        else
        {

            //Save to the temp folder
            $ext = pathinfo($imageFile->name, PATHINFO_EXTENSION);
            $imageName = time() . "_" . rand(0, 100) . "." . $ext;
            $imagePath = Yii::getAlias('@webroot'). Story::PATH_TEMP_IMAGE . $imageName;
            $imageFile->saveAs($imagePath);
            $success    = true;
            $imageName  = Story::PATH_TEMP_IMAGE . $imageName;
            $width      = $sizes[0];
            $height     = $sizes[1];

        }
        return json_encode(array(
            'success'   => $success,
            'error'     => $error,
            'fileName'  => $imageName,
            'width'     => $width,
            'height'    => $height
        ));
    }





    /**
     * Displays a single Story model.
     * @param integer $id
     * @return mixed
     */
    public function actionView()
    {
        if(isset($_POST["story_id"]))
        {
            $role=Yii::$app->user->getIdentity()->role;
            $model=$this->findModel($_POST["story_id"]);
            if($model->type==Story::TYPE_IMAGE)
            {
                $media=$this->storyPreviewContent($model, Story::TYPE_IMAGE);
            }
            else if($model->type==Story::TYPE_VIDEO)
            {
                $media=$this->storyPreviewContent($model, Story::TYPE_VIDEO);
            }
            else if($model->type==Story::TYPE_CLIPKIT)
            {
                $media='<script id="clipkit-embed" src="http://api.clipkit.de/embed/dist/clipkit-embed.js"></script>';
                $media.=$model->relationStoryClipkit->clipkit_code;
            }
            else if($model->type==Story::TYPE_3RD_PARTY_VIDEO)
            {
                $media=$model->relationStory3rdPartyVideo->video_code;
            }
            /*else if($model->sponsored_story==1)
            {
                //first check if there is video
                if($model->type==Story::TYPE_IMAGE)
                    $media=$this->storyPreviewContent($model, Story::TYPE_VIDEO);
                else if($model->type==Story::TYPE_VIDEO)
                    $media=$this->storyPreviewContent($model, Story::TYPE_IMAGE);

            } */

            $report=NULL;
            if($role==User::ROLE_SUPERADMIN || $role==User::ROLE_MARKETER)
            {
                //$report='<a class="btn btn-xs btn-danger btn-block">'.Yii::t('app', 'Report story').'</a>';
                $report.='<textarea id="report_msg" class="form-control m-b-10" placeholder="Your message"></textarea>';
                $report.='<button class="btn btn-primary" type="button" onClick="reportStory('.$model->id.')">'.Yii::t('app', 'Report story').'</button>';
            }

            $result=
            '
            <div>
                <div class="preview_story_image">
                    '.$media.'
                </div>
                <h4>'.$model->title.'</h4>
                <div style="text-align:left">'.nl2br($model->description).' </div> <br>
                '.$report.'
            </div>
            ';
        }

        echo json_encode(['result'=>$result]);
        /*return $this->render('view', [
            'model' => $this->findModel($id),
        ]);*/
    }

    /*
    * return content for actionView
    * $model = loaded Story model
    * $type = Story::TYPE_IMAGE, Story::TYPE_VIDEO
    */
    private function storyPreviewContent($model, $type)
    {
        if($type==Story::TYPE_VIDEO)
        {
            return
            '<div id="myVideo">Loading the player ...</div>
            <script type="text/javascript">
            var playerInstance = jwplayer("myVideo");
              playerInstance.setup({
                file: "'.Story::getFullDirectoryToImageVideo($model->date_created, Story::PATH_VIDEO, $model->video, false).'",
                autostart:true,
              });
            </script>';
        }
        else if($type==Story::TYPE_IMAGE)
        {
            return
            '<img src="'.Story::getFullDirectoryToImageVideo($model->date_created, Story::PATH_IMAGE, $model->image, false).'" class="img-responsive m-b-10 main_image" />
            <div class="preview_story_info">
                <span class="preview_story_info_by">'.$model->relationUser->name.'</span>
                <span>'.Helpers::dateDifferenceStory(NULL,$model->date_published).' / '.$model->seo_title.'</span>
            </div>';
        }
    }


    /*
    * Search unpublished stories
    */
    public function actionUnpublished()
    {
        $Formatter=CommonHelpers::returnFormatter();

        //set returnUrl so you can use $this->goBack in actionDelete()
        Yii::$app->user->returnUrl = Url::to(['unpublished']);
        $status=Story::STATUS_UNPUBLISHED;
        $searchModel = new StorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $status);

        return $this->render('stories', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'status' => $status,
            'Formatter'=>$Formatter,
        ]);
    }

    /*
    * Search pending stories
    */
    public function actionPending()
    {
        $Formatter=CommonHelpers::returnFormatter();

        //set returnUrl so you can use $this->goBack in actionDelete()
        Yii::$app->user->returnUrl = Url::to(['pending']);

        $status=Story::STATUS_PENDING;
        $searchModel = new StorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $status);

        return $this->render('stories', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'status' => $status,
            'Formatter'=>$Formatter,
        ]);
    }

    /*
    * Search published  stories
    */
    public function actionPublished()
    {
        $Formatter=CommonHelpers::returnFormatter();

        //set returnUrl so you can use $this->goBack in actionDelete()
        Yii::$app->user->returnUrl = Url::to(['published']);

        $status=Story::STATUS_PUBLISHED;
        $searchModel = new StorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $status);

        return $this->render('stories', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'status' => $status,
            'Formatter'=>$Formatter,
        ]);
    }

    /*
    * Search sponsored  stories
    */
    public function actionSponsored()
    {
        $Formatter=CommonHelpers::returnFormatter();

        //set returnUrl so you can use $this->goBack in actionDelete()
        Yii::$app->user->returnUrl = Url::to(['sponsored']);

        $status=Story::STATUS_PUBLISHED;
        $searchModel = new StorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $status, "sponsored");

        return $this->render('stories', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'status' => $status,
            'Formatter'=>$Formatter,
        ]);
    }

     /**
     * Finds the Story model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Story the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Story::find()->with(['relationUser', 'relationStoryClipkit'])->where(['id'=>$id])->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
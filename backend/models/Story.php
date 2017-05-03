<?php

namespace backend\models;

use Yii;
use yii\base\Exception;
use yii\helpers\BaseUrl;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\Point;
use backend\models\User;
use backend\models\CategoryStory;
use backend\models\Language;
use backend\components\Helpers;
use yii\caching\DbDependency;

/**
 * This is the model class for table "stories".
 *
 * @property integer $id
 * @property integer $language_id
 * @property string $title
 * @property string $seo_title
 * @property string $description
 * @property string $link
 * @property string $image
 * @property string $video
 * @property string $date_created
 * @property string $date_modified
 * @property integer $user_id
 * @property string $status
 * @property string $date_published
 * @property string $seo_url
 * @property string $alt_tag
 *
 * @property CategoryStory[] $categoryStories
 * @property Category[] $categories
 * @property CountryStory[] $countryStories
 * @property Country[] $countries
 * @property Language $language
 * @property User $user
 */
class Story extends \yii\db\ActiveRecord
{

    //used in database
    const STATUS_PENDING        = "PENDING APPROVAL"; //waiting approval from senior editor
    const STATUS_UNPUBLISHED    = "UNPUBLISHED"; //like a trash, deleted stories that for some reason we want to keep
    const STATUS_PUBLISHED      = "PUBLISHED";
    //not used in database
    const STATUS_SCHEDULE       = "SCHEDULE";

    const MAX_IMAGE_SIZE=2097152;

    //filter for dropdown list
    const FILTER_IMAGE = "image";
    const FILTER_VIDEO = "video";
    const FILTER_BOTH = "both";

    const PATH_IMAGE            = "/uploads/image/";
    const PATH_TEMP_IMAGE       = "/uploads/temp/";
    const PATH_IMAGE_THUMB      = "/uploads/image_thumb/";
    const PATH_VIDEO            = "/uploads/video/";
    //const PATH_SPONSORED            = "/uploads/sponsored/";

    //height and width of thumbnail
    const THUMB_WIDTH           = 640;
    const THUMB_HEIGHT          = 508;

    //how many characters max can go in title and summary
    const COUNT_TITLE=100;
    const COUNT_DESCRIPTION=450;

    private $categories;
    private $countries;

    public $upload_image_field;
    public $upload_video_field;

    //Story type
    const TYPE_IMAGE=0;
    const TYPE_VIDEO=1;
    //const TYPE_SPONSORED=2; !!!!not used anymore !!!!
    const TYPE_CLIPKIT=3;
    const TYPE_3RD_PARTY_VIDEO=4;

    //scenarios
    const SCENARIO_IMAGE_STORY="image-story";
    const SCENARIO_VIDEO_STORY="video-story";
    //const SCENARIO_SPONSORED_STORY="sponsored-story";  !!!!not used anymore!!!!
    const SCENARIO_CLIPKIT_STORY="clipkit-story";
    const SCENARIO_3RD_PARTY_VIDEO_STORY="3rd-party-video-story";


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'stories';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['language_id', 'user_id', 'link', 'title', 'description','seo_title', 'seo_url', 'type', 'alt_tag'], 'required'],
            [['upload_image_field'], 'required'],
            [['video', 'upload_video_field'], 'required'],
            //[['video', 'upload_video_field'], 'match', 'pattern'=>'/^[a-z0-9/./_/-]+$/i'], only alphanumberic
            [['language_id', 'user_id', 'type', 'sponsored_story'], 'integer'],
            [['link'], 'url', 'message' => 'Should be url, now it\'s just a text'],
            [['description', 'link'], 'string', 'max' => self::COUNT_DESCRIPTION],
            [['date_created', 'date_published'], 'safe'],
            [['seo_title', 'image', 'video', 'seo_url', 'alt_tag'], 'string', 'max' => 255],
            [['title'], 'string', 'max' => self::COUNT_TITLE],
        ];
    }

    public function scenarios()
    {
        $scenarios=parent::scenarios();
        $scenarios[Story::SCENARIO_IMAGE_STORY]=['language_id', 'user_id', 'link', 'title', 'description','seo_title', 'seo_url', 'type', 'upload_image_field', 'alt_tag'];
        $scenarios[Story::SCENARIO_VIDEO_STORY]=['language_id', 'user_id', 'link', 'title', 'description','seo_title', 'seo_url', 'type', 'upload_video_field', 'upload_image_field', 'alt_tag'];
        $scenarios[Story::SCENARIO_CLIPKIT_STORY]=['language_id', 'user_id', 'link', 'title', 'description','seo_title', 'seo_url', 'type'];
        $scenarios[Story::SCENARIO_3RD_PARTY_VIDEO_STORY]=['language_id', 'user_id', 'link', 'title', 'description','seo_title', 'seo_url', 'type'];
        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'language_id' => Yii::t('app', 'Language ID'),
            'title' => Yii::t('app', 'Title'),
            'seo_title' => Yii::t('app', 'Seo Title'),
            'description' => Yii::t('app', 'Description'),
            'link' => Yii::t('app', 'Link'),
            'image' => Yii::t('app', 'Image'),
            'video' => Yii::t('app', 'Video'),
            'date_created' => Yii::t('app', 'Date Created'),
            'user_id' => Yii::t('app', 'User ID'),
            'date_modified' => Yii::t('app', 'Date Modified'),
            'status' => Yii::t('app', 'Status'),
            'date_published' => Yii::t('app', 'Date Published'),
            'seo_url' => Yii::t('app', 'Seo Url'),
            'type' =>  Yii::t('app', 'Story type'), //TYPE_IMAGE, TYPE_VIDEO, TYPE_CLIPKIT...
            'alt_tag' =>  Yii::t('app', 'Alt tag'),
            'sponsored_story'=> 'Sponsored Story',
        ];
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRelationStory3rdPartyVideo()
    {
        return $this->hasOne(Story3rdPartyVideo::className(), ['story_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRelationSponsoredStories()
    {
        return $this->hasOne(SponsoredStory::className(), ['story_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRelationCategoryStories()
    {
        return $this->hasMany(CategoryStory::className(), ['story_id' => 'id']);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRelationSubCategories()
    {
        return $this->hasMany(CategoriesLevelOne::className(), ['id' => 'category_id'])->viaTable('category_stories', ['story_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRelationCountryStories()
    {
        return $this->hasMany(CountryStory::className(), ['story_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRelationCountries()
    {
        return $this->hasMany(Country::className(), ['id' => 'country_id'])->viaTable('country_stories', ['story_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id'])->one();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRelationUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRelationStoryKeywords()
    {
        return $this->hasOne(StoryKeyword::className(), ['story_id' => 'id']);
    }


    /**
    * @return \yii\db\ActiveQuery
    */
    public function getRelationStoryClipkit()
    {
        return $this->hasOne(StoryClipkit::className(), ['story_id' => 'id']);
    }

    /**
     * @inheritdoc
     * @return \backend\models\activequery\StoriesQuery the active query used by this AR class.
     */
   public static function find()
    {
        return new \backend\models\activequery\StoriesQuery(get_called_class());
    }

   /**
     * @return \yii\db\ActiveQuery
     */
    public function getRelationLanguage()
    {
        return $this->hasOne(Language::className(), ['id' => 'language_id']);
    }

    /*
    *  check if specific story is in certain category, by checking if story_id is in categori_stories so you can check checkboxes
    * you call this function from $model(Story) so it atuomatically binds story_id with loaded model
    * $categoryId - id in "categories"
    */
   /* public function hasCategory( $categoryId )
    {

        return ( $this->getRelationCategoryStories()->where(array("category_id" => $categoryId))->count() > 0 ? true : false);
    }*/

    /*
    * return directory to image or video for example: uploads/images/2015/03/12/
    * $date_created = Y-m-d H:i:s, date_crated from "stories" table for each image
    * $file - image/video name, for example: 1.jpg
    * $path_to - path to image direcotry or thumbnail directory, for example: Story::PATH_IMAGE -> /uplaods/image/
    * $root - if true return Yii::getAlias('@webroot') else Yii::getAlias('@web')
    * return
            /uploads/image/2015/01/01/img.jpg
            /uploads/image/2015/01/01/img.jpg

            D:/xampp/htdocs/b2i/backend/web/uploads/image/2015/01/01/img.jpg
            D:/xampp/htdocs/b2i/backend/web/uploads/image/2015/01/01/
    */
    public static function getFullDirectoryToImageVideo($date_created, $path_to, $file=NULL, $root)
    {
        $year=date("Y", strtotime($date_created));
        $month=date("m", strtotime($date_created));
        $day=date("d", strtotime($date_created));

        if($root==true)
            $root_tmp=Yii::getAlias('@webroot');
        else
            $root_tmp=Yii::getAlias('@web');

       $path=$root_tmp.$path_to.$year."/".$month."/".$day."/"; // ABSOLUTE_PATH(RELATIVE_PATH)/uploads/image/2015/12/07/

        if($file!=NULL)
            return $path.$file;
        else
        {
            return $path;
        }
    }
    /*
    * Create thumbnail for uploaded picture and save it to "image_thumb"
    */
    public function getImage( $width, $height, $model )
    {

        $image = null;
        if( $model->image )
        {

            $imgName    = strtolower($model->image);
            $path       = Story::getFullDirectoryToImageVideo($model->date_created, Story::PATH_IMAGE, $model->image, true);

            $thumb_name = Story::getThumbImageName($model);//640-508-some-crazy-name.jpg
            $thumbPath  =  self::PATH_IMAGE_THUMB.$thumb_name;
            $path_thumb_temp=Story::getFullDirectoryToImageVideo($model->date_created, Story::PATH_IMAGE_THUMB, $thumb_name, true);
            if ( file_exists( $path ) && !file_exists($path_thumb_temp) )
            {
                try
                {
                    //create new thumb
                    $imagine    = new Imagine();
                    $img        = $imagine->open( $path );
                    $sizes      = $img->getSize();
                    if ( $sizes->getWidth() >= $width && $sizes->getHeight() >= $height )
                    {

                        $srcWidth  = $sizes->getWidth();
                        $srcHeight = $sizes->getHeight();

                        // Calculate the scaling we need to do to fit the image inside our frame
                        $scale      = max($width/$srcWidth, $height/$srcHeight);
                        // Get the new dimensions
                        $newWidth  = ceil($scale*$srcWidth);
                        $newHeight = ceil($scale*$srcHeight);
                        $img->resize(new Box($width, $height))->save($path_thumb_temp);

                        //use Kraken to compress it
                        Helpers::krakenUploadFile($path_thumb_temp);
                        $image = BaseUrl::base( true ).$thumbPath;

                    }

                } catch (\Exception $e) {

                   // var_dump( $e );

                }

            }

            //get small image for API
            if ( file_exists($path_thumb_temp) )
            {
                $path_thumb_temp=Story::getFullDirectoryToImageVideo($model->date_created, Story::PATH_IMAGE_THUMB, $thumb_name, false);
                $image =  Helpers::backendCDN().$path_thumb_temp;

            }
            //get big image for api
            else
            {
                $path_image=Story::getFullDirectoryToImageVideo($model->date_created, Story::PATH_IMAGE, $model->image, false);
                $image =  Helpers::backendCDN().$path_image;

            }

        }

        return $image;
    }




    /*
    * upload image to "image" directory
    * $image_name -> name of image from CMS where they had to enter image name into text input
    */
    public function copyImage($imageData, $image_name)
    {
        $image = null;
        if( $imageData && ( $parts = explode(";", $imageData) ) &&  count($parts) == 5)
        {
            $path   = Yii::getAlias('@webroot'). $parts[0];
            $x      = $parts[1];
            $y      = $parts[2];
            $width  = $parts[3];//960
            $height = $parts[4];

            $ext = pathinfo($path, PATHINFO_EXTENSION);
            $image_name = $image_name.".".strtolower($ext);

            $path_to_image_dir =  Story::getFullDirectoryToImageVideo($this->date_created, Story::PATH_IMAGE, $image_name, true);

            if ( file_exists( $path ) )
            {

                try
                {
                    ini_set('memory_limit', '512M'); //some images are too big to handle by Imagine() so they need more memory
                    $imagine    = new Imagine();
                    $img        = $imagine->open( $path );
                    $sizes      = $img->getSize();
                    //$k          = 560 / $sizes->getWidth();
                    //$x          = $x * $k;
                    //$y          = $y * $k;
                    //$width      = $width * $k;
                    //$height     = $height * $k;

                    $img->crop(new Point($x, $y), new Box( $width, $height) )->save($path_to_image_dir);

                    $sizes = $this->image_resize($path_to_image_dir);


                    //use Kraken to compress it
                    Helpers::krakenUploadFile($path_to_image_dir);
               }
               catch (\Exception $e)
               {

                    // var_dump( $e );

                }

            }

        }
        return $image_name;
    }


    function image_resize($filename){
        list($width, $height) = getimagesize($filename);
        if($width<=760){
            return array('width'=>$width, 'height'=>$height);
        }
        $newWidth = 760;
        $newHeight = $height/($width/760);

// Load
        $thumb = imagecreatetruecolor($newWidth, $newHeight);
        $source = imagecreatefromjpeg($filename);

// Resize
        imagecopyresized($thumb, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

// Output
        imagejpeg($thumb, $filename, 100);
        return array('width'=>$newWidth, 'height'=>$newHeight);
    }


    /*
    * on every create and update delete old thumbnail so you can create new one
    * $model - loaded Story model
    */
    public function deleteOldImages($model)
    {return true;

        $api = new \MaxCDN("maximumhavrestll","0177f1daab95c5cac1e182035cb81a4d0562a452c","cf686ebb699177f35f1cddffeb6d2d9d");

        $path_root  =  Story::getFullDirectoryToImageVideo($this->date_created, Story::PATH_IMAGE, $model->image, true);
        if (!empty($model->image) && file_exists($path_root))
        {
            unlink($path_root);
            //clear image on MAXCDN
            $path_noroot  =  Story::getFullDirectoryToImageVideo($this->date_created, Story::PATH_IMAGE, $model->image, false);
            $params = array('file' => $path_noroot);
            $api->delete('/zones/pull.json/452303/cache', $params);
        }

        $thum_name=Story::getThumbImageName($model);
        $path_thumb_root  =  Story::getFullDirectoryToImageVideo($this->date_created, Story::PATH_IMAGE_THUMB, $thum_name, true);
        if (!empty($model->image) && file_exists($path_thumb_root))
        {
            unlink($path_thumb_root);
            //clear image on MAXCDN
            $path_thumb_noroot  =  Story::getFullDirectoryToImageVideo($this->date_created, Story::PATH_IMAGE_THUMB, $thum_name, false);
            $params = array('file' => $path_thumb_noroot);
            $api->delete('/zones/pull.json/452303/cache', $params);
        }

    }

     /*
    * return path to video
    */
    public function getVideo()
    {
        if($this->video!=NULL)
        {
            $relative_path=Story::getFullDirectoryToImageVideo($this->date_created, Story::PATH_VIDEO, $this->video, false);
            return Helpers::backendCDN().$relative_path;
        }
        else
            return NULL;
    }

    /*
    * upload video
    * $story - loaded Story model
    * $videoFile - video upload instance
    */
    public function uploadVideo($story, $videoFile, $video_name)
    {
        //if path doesn't exists create it
        //always check absolute path because mk dir works with absolute path better then relative
        $path=Story::getFullDirectoryToImageVideo($story->date_created, Story::PATH_VIDEO, null, true);
        if(!file_exists($path))
            mkdir($path,0755,true);

        $ext = pathinfo($videoFile->name, PATHINFO_EXTENSION);
        //set video name
        $videoName=$video_name.".".strtolower($ext);
        $videoFile->saveAs($path.$videoName);
        return $videoName;
    }

    /*
    * generate name for thumb image
    * $model -> loaded Story model
                name of image, for example: image-name-extra-2359.jpg
    * image name here is fixed name 640-580-someimg.jpg, because image dimensions can be changed, but old images won't be affected, all images will be fetched properly, old and new
    */
    public static function getThumbImageName($model)
    {
        //first check if is set $model->image, if it is then you can take value. Then check if $model is object, because it could be that $model->image is not set because because in database it is NULL. In other words $model->image exists but it is NULL so PHP thinks it is not set and he tries to return $model (object) as string
        if((isset($model->image) && !empty($model->image)) || is_object($model))
            return "640-508-".$model->image;
        else
            return "640-508-".$model;
    }

    /*
    * delete any old video
    * $model -> loaded Story model
    */
    public static function deleteOldVideo($model)
    {
        $videoPath=Story::getFullDirectoryToImageVideo($model->date_created, Story::PATH_VIDEO, $model->video, true);
        //before you upload video, delete old one
        if(file_exists($videoPath))
            unlink($videoPath);
    }

    /*
    * return dropdown for filtering media in stories.php: image, video or both
    */
    public static function dropDownFilterMedia()
    {
        return
        [   ''=>'',
            Story::FILTER_IMAGE=>Yii::t('app', 'Image'),
            Story::FILTER_VIDEO=>Yii::t('app', 'Video'),
            Story::FILTER_BOTH=>Yii::t('app', 'Both'),
        ];
    }

    /*
    *  check if there is some stories with error like:
        no image or video saved in database
        image/video doesn't exist on server
    */
    public static function errorStories()
    {
        $userID=Yii::$app->user->getId();
        $tableStory=Story::tableName();
        $cache=Helpers::cache();

        //--------------------------------------------------------------------------------------------------
        // ERROR #1
        //--------------------------------------------------------------------------------------------------
        //check if somewhere image or video and image is NULL, it means that it wasn't saved in database so fix it
        $end=date("Y-m-d H:i:s");
        $begin=Helpers::subDate(1, "month");
        /*$dependency = new DbDependency;
        $dependency->sql="SELECT COUNT(*), MAX(id), MAX(date_modified)
                        FROM $tableStory
                        WHERE
                        date_created BETWEEN '$begin' AND '$end' AND
                        user_id=$userID";

        $cache_key="error1_image_video_null_$userID";
        if($data=$cache->get($cache_key))
        {
            $storiesNoImgVid=$data;
        }
        else
        {*/

        $storiesNoImgVid=Story::find()
        ->where(["BETWEEN", "date_created", $begin, $end])
        ->andWhere(['user_id'=>$userID])
        ->andWhere("(type=:image AND image IS NULL) OR (type=:video AND (video IS NULL OR image IS NULL))",
        [':image'=>Story::TYPE_IMAGE, ':video'=>Story::TYPE_VIDEO])
        ->all();

        /*    $cache->set($cache_key, $storiesNoImgVid, Yii::$app->params['1_day_cache'], $dependency);
        }*/

        //if you found any error stories return whole query
        if(count($storiesNoImgVid) > 0)
            return $storiesNoImgVid;

        //--------------------------------------------------------------------------------------------------
        // ERROR #2
        //--------------------------------------------------------------------------------------------------
        //get story from today and check if thumbnail/original image/video exists on server, if they don't, warn user
       /* $dependency = new DbDependency;
        $dependency->sql="SELECT MAX(id), MAX(date_modified)
                        FROM $tableStory
                        WHERE (date_created BETWEEN '$begin' AND '$end') AND
                        user_id=$userID";

        $cache_key="error2_image_video_server_$userID";
        if($data=$cache->get($cache_key))
        {
            $storiesNoImgVid=$data;
        }
        else
        {    */
        $storiesNoImgVid=Story::find()
            ->where(['BETWEEN', 'date_created', $begin, $end])
            ->andWhere(['user_id'=>$userID])
            ->all();
        /*
            $cache->set($cache_key, $storiesNoImgVid, Yii::$app->params['1_day_cache'], $dependency);
        }*/

        $faultStories=[]; //save story id here
        foreach($storiesNoImgVid as $story)
        {
            //if there is image in database, check if it exists on server
            if(!empty($story->image))
            {
                //get image path
                $thumb_name=Story::getThumbImageName($story);
                $image=Story::getFullDirectoryToImageVideo($story->date_created, Story::PATH_IMAGE, $story->image, true);
                $image_thumb=Story::getFullDirectoryToImageVideo($story->date_created, Story::PATH_IMAGE_THUMB, $thumb_name, true);

                if(!file_exists($image) || !file_exists($image_thumb))
                {
                    $faultStories[]=$story->id;
                }
            }

            //if there is video in database, check if it exists on server
            if(!empty($story->video))
            {
                //get video path
                $video=Story::getFullDirectoryToImageVideo($story->date_created, Story::PATH_VIDEO, $story->video, true);
                if(!file_exists($video))
                {
                    $faultStories[]=$story->id;
                }
            }
        }

        //if you found stories without image/image_thumb/video on server return those stories as query
        if(!empty($faultStories))
        {
            return Story::find()->where(["IN", 'id', $faultStories])->all();
        }
    }
}

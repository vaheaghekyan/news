<?php

namespace backend\models;

use Yii;
use yii\web\IdentityInterface;
use backend\models\UserLanguage;
use yii\base\Security;
use yii\helpers\ArrayHelper;
use yii\caching\DbDependency;
use yii\db\Expression;

/**
 * This is the model class for table "users".
 *
 * @property integer $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $date
 * @property integer $status
 * @property string $role
 * @property string $auth_key
 *
 * @property Story[] $stories
 * @property Language[] $languages
 */
class User extends \yii\db\ActiveRecord  implements IdentityInterface
{

    const ROLE_SUPERADMIN   = "SuperAdmin";
    const ROLE_ADMIN        = "Admin";
    const ROLE_SENIOREDITOR = "SeniorEditor";
    const ROLE_EDITOR       = "Editor";
    const ROLE_MARKETER     = "Marketer";
    const PATH_IMAGE        = "/uploads/user/";
    const DEFAULT_PICTURE   = "/img/icons/users.png";

    public $upload_image_field;
    public $count_story_user; //field for UserSearch quey when you have to get count of how many stories specific user has

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'email', 'password'], 'required'],
            [['date'], 'safe'],
            [['status'], 'integer'],
            [['name', 'email'], 'string', 'max' => 50],
            [['password'], 'string', 'max' => 128],
            [['auth_key'], 'string', 'max' => 32],
            [['role'], 'string', 'max' => 15],

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => Yii::t('app', 'Name'),
            'email' => Yii::t('app', 'Email'),
            'password' => Yii::t('app', 'Password'),
            'date' => 'Date',
            'status' => 'Status',
            'role' => 'Role',
            'auth_key' => 'Role',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRelationStories()
    {
        return $this->hasMany(Story::className(), ['user_id' => 'id']);
    }

    public function getLanguages()
    {
        return $this->hasMany(Language::className(), ['id' => 'language_id'])->viaTable('user_languages', ['user_id' => 'id']);

    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRelationUserLanguages()
    {
        return $this->hasMany(UserLanguage::className(), ['user_id' => 'id']);
    }

    public static function findByEmail( $email )
    {
        return self::find()->where(array("email" => $email))->one();
    }

    /**
     * Validates password
     *
     * @param  string  $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return $this->password === hash("sha512", $password);
    }

    /** INCLUDE USER LOGIN VALIDATION FUNCTIONS**/
    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * @inheritdoc
     */
    /* modified */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['access_token' => $token]);
    }

    /* removed
        public static function findIdentityByAccessToken($token)
        {
            throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
        }
    */

    /**
     * Finds user by password reset token
     *
     * @param  string      $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        $expire = \Yii::$app->params['user.passwordResetTokenExpire'];
        $parts = explode('_', $token);
        $timestamp = (int) end($parts);
        if ($timestamp + $expire < time()) {
            // token expired
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

     /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $Security = new Security();
        $this->auth_key = $Security->generateRandomString();
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    public function setNewPassword( $password )
    {
        $this->password = hash("sha512", $password );
    }

    /*
    *  get authors with that specific language. For example: get all users who speaks english or currently chosen language
    */
    public static function dropDownListAuthors()
    {
        $tableUser=User::tableName();

        /*$dependency=new DbDependency;
        $dependency->sql="SELECT MAX(id) FROM $tableUser";
        $query=User::getDb()->cache(function($db) use ($tableUser)
        {
            $languageId = Language::getCurrentId();
            $userId = Yii::$app->user->getId();
            $expression = new Expression("$tableUser.`id`=$userId DESC, name ASC");

            return User::find()
                    ->joinWith(['relationUserLanguages'])
                    ->where(array("language_id" => $languageId))
                    ->orderBy($expression)
                    ->all();
        }, Yii::$app->params['14_day_cache'], $dependency);*/

        $languageId = Language::getCurrentId();
        $userId = Yii::$app->user->getId();
        $expression = new Expression("$tableUser.`id`=$userId DESC, name ASC");

        $query = User::find()
                ->joinWith(['relationUserLanguages'])
                ->where(array("language_id" => $languageId))
                ->orderBy($expression)
                ->all();

        return ArrayHelper::map($query, 'id', 'name');
    }

    public function getImage()
    {
        $path = Yii::getAlias('@webroot').self::PATH_IMAGE . $this->id . ".jpg";
        if ( file_exists($path) ) {

            return self::PATH_IMAGE . $this->id . ".jpg";

        }
        return self::DEFAULT_PICTURE;
    }

    /*
    * create list of all users and put it to dropdown list
    */
    public static function usersDropDownList()
    {
        $tableUsers=User::tableName();
        $dependency=new DbDependency;
        $dependency->sql="SELECT MAX(id) FROM $tableUsers";
        $query=Story::getDb()->cache(function($db)
        {
            return User::find()->orderBy('name ASC')->where(['status'=>1])->all();

        }, Yii::$app->params['14_day_cache'], $dependency);

        return ArrayHelper::map($query, 'id', 'name');
    }

}

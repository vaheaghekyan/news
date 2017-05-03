<?php
/**
 * Created by PhpStorm.
 * User: Oleksii
 * Date: 08.06.2015
 * Time: 20:13
 */

namespace backend\models;

use Yii;
use yii\base\Model;
use backend\models\User;
use yii\base\Security;

class ChangePasswordForm extends Model {

    public $old_password;
    public $new_password;
    public $repeat_password;

    /* @var $_user \backend\models\User */
    private $_user;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['old_password', 'new_password', 'repeat_password'], 'required'],
            [['new_password'], 'compare', 'compareAttribute' => 'repeat_password'],
            [['old_password'], 'validatePassword']

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'old_password' => Yii::t('app', 'Old password'),
            'new_password' => Yii::t('app', 'New password'),
            'repeat_password' => Yii::t('app', 'Repeat password'),

        ];
    }

    public function validatePassword()
    {
        if ( $this->old_password ) {


            if ( hash("sha512", $this->old_password) != Yii::$app->user->getIdentity()->password ) {

                $this->addError('old_password', 'Old password is not correct.');

            }

        }

    }

    public function change()
    {
        if ( $this->validate()) {

            $user = Yii::$app->user->getIdentity();
            $user->setNewPassword( $this->new_password );
            $user->save(false, array("password") );

            return true;
        }
        return false;
    }

}
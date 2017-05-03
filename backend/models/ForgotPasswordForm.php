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
use common\components\Helpers as CommonHelpers;

class ForgotPasswordForm extends Model {

    public $email;
    /* @var $_user \backend\models\User */
    private $_user;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['email'], 'required'],
            ['email', 'validateEmail']

        ];
    }

    public function validateEmail()
    {
        if ( $this->email ) {


            if ( !($this->_user = User::findByEmail( $this->email) )) {

                $this->addError('email', 'Email not found. Please try again.');

            }

        }

    }

    public function sendLink()
    {
        if ( $this->validate() && ($user = $this->getUser())) {

            $security       = new Security();
            $newPassword    = $security->generateRandomString(6);
            $user->setNewPassword( $newPassword );
            $user->generateAuthKey();
            $user->save(false, array("password", "auth_key") );

            //Send email with password
            $subject="[".\Yii::$app->params['Born2Invest']."] ".Yii::t('app', 'New password');
            $message=Yii::t("app", "Hello")." ".$user->name.".<br />".
                    Yii::t("app", "Your new password")." ".$newPassword;

            CommonHelpers::sendEmailToAnyone($subject, $message, $user->email, $user->name) ;

            return true;
        }
        return false;
    }

    public function getUser()
    {
        return $this->_user;
    }

}
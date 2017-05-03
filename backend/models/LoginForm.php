<?php

namespace backend\models;

use Yii;
use yii\base\Model;

/**
 * LoginForm is the model behind the login form.
 */
class LoginForm extends Model
{
    public $email;
    public $password;
    public $language;
    public $rememberMe = true;

    private $_user = false;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['email', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
            ['language', 'string'],
            ['language', 'validateLanguage']
        ];
    }


    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect username or password.');
            }
        }
    }

    public function validateLanguage($attribute, $params)
    {
        if (!$this->hasErrors()) {

            $user = $this->getUser();
            if ($user && $user->getLanguages()->where(['id' => $this->language])->count() == 0 ) {

                $this->addError($attribute, 'The chosen language is not available to you.');

            }

        }
    }

    /**
     * Logs in a user using the provided username and password.
     * @return boolean whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate())
        {
            $user=$this->getUser();
            //if user status is 0, user's account is deactivated
            if($user->status==0)
                throw new \yii\web\HttpException(403, Yii::t('app', 'Your account has been deactivated'));

            //remember for 365 days
            $remember=31536000;
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? $remember : $remember);
        }
        else
        {
            return false;
        }
    }

    /**
     * Finds user by [[email]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {

            $this->_user = User::findByEmail($this->email);

        }

        return $this->_user;
    }
}

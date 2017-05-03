<?php
/**
 * Created by PhpStorm.
 * User: alekseyyp
 * Date: 19.06.15
 * Time: 17:06
 */

namespace backend\models;
use Yii;
use yii\base\Model;
use backend\models\User;
use yii\base\Security;

class UserForm extends Model
{
    public $oldEmail;
    public $id;
    public $email;
    public $password;
    public $languages;
    public $name;
    public $role;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['name'], 'required', 'message' => 'Please fill in Name field.'],
            [['password'], 'required', 'message' => 'Please fill in Password field.', 'on' => 'create'],

            [['email'], 'required', 'message' => 'Please fill in Email field.'],
            [['email'], 'email', 'message' => 'Please enter email in email format.'],
            [['role'], 'required', 'message' => 'Please choose role.'],
           // [['password'], 'required', 'message' => 'Please fill in Password field.'],
            [['languages'], 'required', 'message' => 'Please choose at least one language.'],
            // rememberMe must be a boolean value
            ['password', 'validatePassword'],
            ['email', 'validateEmail'],
            [['id', 'oldEmail'], 'safe'],
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

        if ( !$this->id && empty($this->password) ) {

            $this->addError($attribute, 'Please fill in Password field.');

        }

    }

    public function validateEmail($attribute, $params)
    {
        if (!$this->hasErrors() ) {

            if ( !$this->id || ($this->email != $this->oldEmail) ) {

                if ( User::find()->where(['email' => $this->email])->exists() ) {

                    $this->addError($attribute, 'User with this email already exists in the database.');

                }

            }
        }
    }

   /*
   *  update User, his email, password, name, role, languages...
   */
    public function update()
    {
        if ($this->validate()) {

            $user = User::findOne( $this->id );
            $user->email    = $this->email;
            if ( !empty( $this->password ))
            {

                $user->password = hash("sha512", $this->password );

            }
            $user->name     = $this->name;
            $user->role     = $this->role;
            //before adding new languages on update, delte old one first
            UserLanguage::deleteAll(['user_id' => $user->id]);
            foreach ( $this->languages as $languageId)
            {

                $ul                 = new UserLanguage();
                $ul->user_id        = $user->id;
                $ul->language_id    = $languageId;
                $ul->save();

            }
            $user->save();

            return $user;
        } else {

            return false;

        }
    }

    public function create()
    {
        if ($this->validate()) {

            $user = new User();
            $user->email    = $this->email;
            $user->password = hash('sha512', $this->password );
            $user->name     = $this->name;
            $user->role     = $this->role;
            $user->date     = date("Y-m-d H:i:s");
            $user->generateAuthKey();
            $user->save();
            foreach ( $this->languages as $languageId) {

                $ul                 = new UserLanguage();
                $ul->user_id        = $user->id;
                $ul->language_id    = $languageId;
                $ul->save();

            }

            return $user;

        } else {
            return false;
        }
    }

}

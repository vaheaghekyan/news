<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "user_languages".
 *
 * @property integer $user_id
 * @property integer $language_id
 */
class UserLanguage extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_languages';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'language_id'], 'required'],
            [['user_id', 'language_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'language_id' => 'Language ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLanguage()
    {
        return $this->hasOne(Languages::className(), ['id' => 'language_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRelationUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}

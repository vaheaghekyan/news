<?php
/**
 * Created by PhpStorm.
 * User: Oleksii
 * Date: 20.05.2015
 * Time: 15:17
 */

namespace backend\components;

use backend\models\User;

class AccessRule extends \yii\filters\AccessRule
{

    /**
     * @inheritdoc
     */
    protected function matchRole($user)
    {

        if (empty($this->roles)) {
            return true;
        }
        foreach ($this->roles as $role)
        {
            if ( $role == '?' )
            {

                if ($user->getIsGuest())
                {
                    return true;
                }

            }
            elseif ( $role == "@" || $role == User::ROLE_EDITOR )
            {

                if (!$user->getIsGuest())
                {
                    return true;
                }

            }
            // Check if the user is logged in, and the roles match
            elseif ( !$user->getIsGuest() && $role == $user->identity->role )
            {

                return true;
            }

        }
        return false;
    }

}
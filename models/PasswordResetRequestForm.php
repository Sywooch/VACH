<?php

namespace app\models;

use app\models\User;
use yii\base\Model;

/**
 * Password reset request form
 */
class PasswordResetRequestForm extends Model {

    public $email;

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'exist',
                'targetClass' => '\app\models\User',
                'filter' => ['status' => User::STATUS_ACTIVE],
                'message' => 'There is no user with such email.'
            ],
        ];
    }

    public function attributeLabels() {
        return [
            'email' => \Yii::t('app', 'Email'),
        ];
    }

    /**
     * Sends an email with a link, for resetting the password.
     *
     * @return boolean whether the email was send
     */
    public function sendEmail() {
        /* @var $user User */
        $user = User::findOne([
                    'status' => User::STATUS_ACTIVE,
                    'email' => $this->email,
                    'is_coach' => 1,
        ]);

        if ($user) {
            if (!User::isPasswordResetTokenValid($user->password_reset_token)) {
                $user->generatePasswordResetToken();
            }

            if ($user->save()) {
                return \Yii::$app->mailer->compose('passwordResetToken', ['user' => $user])
                                ->setFrom(\Yii::$app->params['adminEmail'])
                                ->setTo($this->email)
                                ->setSubject(\Yii::t('app', 'Password reset for VACH'))
                                ->send();
            }
        }

        return false;
    }

}
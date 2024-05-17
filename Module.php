<?php

namespace auth;


class Module extends \yii\base\Module
{
  public $controllerNamespace = 'auth\controllers';

    /**
     * @var int
     * @desc Remember Me Time (seconds), default = 2592000 (30 days)
     */
    public $rememberMeTime = 2592000; // 30 days

    /**
     * @var array
     * @desc User model relation from other models
     * @see http://www.yiiframework.com/doc/guide/database.arr
     */
    public $relations = array();

    /**
     * Database to use
     * @var string
     */
    public $db;

    /**
     * Tables to use
     * @var array
     */
    public $tableMap = array(
        'User' => 'User',
        'UserStatus' => 'UserStatus',
        'ProfileFieldValue' => 'ProfileFieldValue',
        'ProfileField' => 'ProfileField',
        'ProfileFieldType' => 'ProfileFieldType',
    );

    /**
     * Custom Login page template
     * @var string
     */
    public $loginTemplate;

    /**
     * Custom Signup page template
     * @var string
     */
    public $signupTemplate;

    /**
     * Custom Request Password Reset Token page template
     * @var string
     */
    public $requestTemplate;

    /**
     * Custom Reset Password page template
     * @var string
     */
    public $resetTemplate;

    /**
     * Custom Profile view page template
     * @var string
     */
    public $profileTemplate;

    /**
     * Custom Profile Update page template
     * @var string
     */
    public $profileUpdateTemplate;

    /**
     * Custom User List page template
     * @var string
     */
    public $userTemplate;

    /**
     * Custom Single User page template
     * @var string
     */
    public $userViewTemplate;

    /**
     * Custom User List page template
     * @var string
     */
    public $userCreateTemplate;

    /**
     * Custom User Update page template
     * @var string
     */
    public $userUpdateTemplate;

    /**
     * Custom Link to redirect to after login
     * @var string|boolean
     */
    public $loginRedirect = false;

    /**
     * Custom Link to redirect to after successful reset or request
     * @var string|boolean
     */
    public $passwordRedirect = false;

    /**
     * Custom Link to redirect to after successful Signup
     * @var string|boolean
     */
    public $signupRedirect = false;

    /**
     * Custom Logged in layout
     * @var string
     */
    public $layoutLogged;

    /**
     * Unsuccessful Login Attempts before Captcha
     * @var int
     */
    public $attemptsBeforeCaptcha = 3;

    public $referralParam = 'ref';

  /**
   * @var int Seconds for token expiration
   */
  public $passwordResetTokenExpire = 3600;

  public $supportEmail;

  public $superAdmins = ['admin'];

    /**
     * @var boolean Use only email for signup
     */
    public $signupWithEmailOnly = false;

  public function init()
  {
    parent::init();

    \Yii::$app->getI18n()->translations['auth.*'] = [
      'class' => 'yii\i18n\PhpMessageSource',
      'basePath' => __DIR__.'/messages',
    ];
    $this->setAliases( ['@auth' => __DIR__] );
  }

}

<?php

namespace auth\controllers;

use auth\models\PasswordResetRequestForm;
use auth\models\ResetPasswordForm;
use auth\models\SignupForm;
use Yii;
use yii\base\InvalidArgumentException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use auth\models\LoginForm;

class DefaultController extends Controller
{
  /**
   * @var \auth\Module
   */
  public $module;

  protected $loginAttemptsVar = '__LoginAttemptsCount';

  public function behaviors()
  {
    return [
      'access' => [
        'class' => \yii\filters\AccessControl::class,
        'only' => ['logout', 'signup'],
        'rules' => [
          [
            'actions' => ['signup'],
            'allow' => true,
            'roles' => ['?'],
          ],
          [
            'actions' => ['logout'],
            'allow' => true,
            'roles' => ['@'],
          ],
        ],
      ],
    ];
  }

  public function actions()
  {
    return [
      'error' => [
        'class' => 'yii\web\ErrorAction',
      ],
    ];
  }

  public function actionLogin()
  {
    if ( ! Yii::$app->user->isGuest ) {
      $this->goHome();
    }

    $model = new LoginForm();

    if ( $this->module instanceof \auth\Module ) {
      $module = $this->module;
    } else {
      $module = Yii::$app->getModule( 'auth' );
    }

    if ( $model->load( $_POST ) and $model->login() ) {
      // if login is successful, reset the attempts
      $this->setLoginAttempts( 0 );
      return $this->goBack();
    }
    //if login is not successful, increase the attempts
    $this->setLoginAttempts( $this->getLoginAttempts() + 1 );

    $login = $this->module->loginTemplate;
    $login = empty( $login ) ? 'login' : $login;
    return $this->render( $login, ['model' => $model] );
  }

  protected function getLoginAttempts()
  {
    return Yii::$app->getSession()->get( $this->loginAttemptsVar, 0 );
  }

  protected function setLoginAttempts( $value )
  {
    Yii::$app->getSession()->set( $this->loginAttemptsVar, $value );
  }

  public function actionLogout()
  {
    Yii::$app->user->logout();
    return $this->goHome();
  }

  public function actionSignup()
  {
    $model = new SignupForm();
    if ( $model->load( Yii::$app->request->post() ) ) {
      if ( $model->validate() ) {
        if ( $user = $model->signup() ) {
          Yii::$app->getSession()->setFlash( 'success',
            Yii::t( 'auth.verify',
              'Thank you {name}, for registering. ' .
              'Kindly check your email to verify your account.',
              [ 'name'=>$user->username ] ) );

          if( is_bool( $this->module->pageRedirect ) ){
            if( $this->module->pageRedirect ) return $this->goHome();
            else $this->redirect( ['login'] );
          } else {
            $this->redirect( $this->module->pageRedirect );
          }
        }
      } else {
        // validation failed: $errors is an array containing error messages
        $messages = '';
        foreach ( $model->errors as $errors ) {
          $messages .= '<p>' . $errors[0] . '</p>';
          Yii::$app->getSession()->setFlash(
            'error', Yii::t( 'auth.verify', $messages )
          );
        }
      }
    }

    $signup = $this->module->signupTemplate;
    $signup = empty( $signup ) ? 'signup' : $signup;
    return $this->render( $signup, ['model' => $model] );
  }

  public function actionRequestPasswordReset()
  {
    $model = new PasswordResetRequestForm();
    if ( $model->load( Yii::$app->request->post() ) ) {
      if ( $model->validate() ) {
        if ( $model->sendEmail() ) {
          Yii::$app->getSession()->setFlash(
            'success',
            'Check your email for further instructions.'
          );

          if( is_bool( $this->module->pageRedirect ) ){
            if( $this->module->pageRedirect ) return $this->goHome();
            else $this->redirect( ['login'] );
          } else {
            $this->redirect( $this->module->pageRedirect );
          }
        } else {
          Yii::$app->getSession()->setFlash(
            'error',
            'Sorry, we are unable to reset password for email provided.'
          );
        }
      } else {
        // validation failed: $errors is an array containing error messages
        $messages = '';
        foreach ( $model->errors as $errors ) {
          $messages .= '<p>' . $errors[0] . '</p>';
          Yii::$app->getSession()->setFlash(
            'error', Yii::t( 'auth.verify', $messages )
          );
        }
      }
    }

    $request = $this->module->requestTemplate;
    $request = empty( $request ) ? 'requestPasswordResetToken' : $request;
    return $this->render( $request, ['model' => $model] );
  }

  public function actionResetPassword( $token )
  {
    try {
      $model = new ResetPasswordForm( $token );
    } catch ( InvalidArgumentException $e ) {
      throw new BadRequestHttpException( $e->getMessage() );
    }

    if (
      $model->load( Yii::$app->request->post() )
      && $model->validate()
      && $model->resetPassword()
    ) {
      Yii::$app->getSession()->setFlash(
        'success',
        'New password was saved.'
      );

      if( is_bool( $this->module->pageRedirect ) ){
        if( $this->module->pageRedirect ) return $this->goHome();
        else $this->redirect( ['login'] );
      } else {
        $this->redirect( $this->module->pageRedirect );
      }
    }

    $reset = $this->module->resetTemplate;
    $reset = empty( $reset ) ? 'resetPassword' : $reset;
    return $this->render( $reset, ['model' => $model] );
  }
}

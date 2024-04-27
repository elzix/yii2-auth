<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\captcha\Captcha;

/**
 * @var yii\web\View $this
 * @var yii\widgets\ActiveForm $form
 * @var auth\models\LoginForm $model
 */
$this->title = Yii::t( 'auth.user', 'Login' );
$this->params['breadcrumbs'][] = $this->title;
$class = 'site-login center-block float-none col-lg-3 col-md-4 col-sm-6';
?>
<div class="<?= $class ?>">
  <div class="form-signin-heading">
    <h1><?= Html::encode( $this->title ) ?></h1>
  </div>

  <?php $form = ActiveForm::begin( ['id' => 'login-form'] ); ?>

  <?= $form->field( $model, 'username' ) ?>

  <?= $form->field( $model, 'password' )->passwordInput() ?>

  <?php if ( $model->scenario == 'withCaptcha' ): ?>
    <?= $form
      ->field( $model, 'verifyCode' )
      ->widget( Captcha::class, ['captchaAction' => 'default/captcha'] ) ?>
  <?php endif; ?>

  <?= $form->field( $model, 'rememberMe' )->checkbox() ?>

  <div class="form-group">
    <div class="text-center">
      <?= Html::submitButton(
        Yii::t( 'auth.user', 'Login' ),
        ['class' => 'btn btn-primary btn-lg btn-block']
      ) ?>
    </div>
  </div>

  <?php ActiveForm::end(); ?>
  <p class="text-center">
    <?= Html::a(
      Yii::t( 'auth.user', 'Forgot password?' ),
      ['default/request-password-reset']
    ) ?>
  </p>
</div>

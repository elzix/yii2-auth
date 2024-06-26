<?php

namespace auth\controllers;

use auth\models\User;
use auth\models\UserSearch;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
{

  /**
   * @var \auth\Module
   */
  public $module;

  public function behaviors()
  {
    return [
      'verbs' => [
        'class' => VerbFilter::class,
        'actions' => [
          'delete' => ['post'],
        ],
      ],
      'access' => [
        'class' => AccessControl::class,
        'rules' => [
          [
            'allow' => true,
            'matchCallback' => function () {
                return \Yii::$app->user->getIsSuperAdmin();
              },
          ],
        ],
      ],
    ];
  }

  public function init()
  {
    $layout = $this->module->layoutLogged;
    $this->layout = empty( $layout ) ? $this->layout : $layout;
  }

  /**
   * Lists all User models.
   *
   * @return mixed
   */
  public function actionIndex()
  {
    $searchModel = new UserSearch;
    $dataProvider = $searchModel->search( $_GET );

    $index = $this->module->userTemplate;
    $index = empty( $index ) ? 'index' : $index;
    return $this->render( $index, [
      'dataProvider' => $dataProvider,
      'searchModel' => $searchModel,
    ] );
  }

  /**
   * Displays a single User model.
   *
   * @param integer $id
   * @return mixed
   */
  public function actionView( $id )
  {
    $view = $this->module->userViewTemplate;
    $view = empty( $view ) ? 'view' : $view;
    return $this->render( $view, ['model' => $this->findModel( $id )] );
  }

  /**
   * Creates a new User model.
   * If creation is successful,
   * the browser will be redirected to the 'view' page.
   *
   * @return mixed
   */
  public function actionCreate()
  {
    $model = new User;

    if ( $model->load( $_POST ) && $model->save() ) {
      return $this->redirect( ['view', 'id' => $model->id] );
    } else {
      $create = $this->module->userCreateTemplate;
      $create = empty( $create ) ? 'create' : $create;
      return $this->render( $create, ['model' => $model] );
    }
  }

  /**
   * Updates an existing User model.
   * If update is successful,
   * the browser will be redirected to the 'view' page.
   *
   * @param integer $id
   * @return mixed
   */
  public function actionUpdate( $id )
  {
    $model = $this->findModel( $id );
    $model->setScenario( 'profile' );

        if ( isset( $_POST['User']['password'] ) ) {
            $model->setPassword( $_POST['User']['password'] );
        }

    if ( $model->load( $_POST ) && $model->save() ) {
      return $this->redirect( ['view', 'id' => $model->id] );
    } else {
      $update = $this->module->userUpdateTemplate;
      $update = empty( $update ) ? 'update' : $update;
      return $this->render( $update, ['model' => $model] );
    }
  }

  /**
   * Deletes an existing User model.
   * If deletion is successful,
   * the browser will be redirected to the 'index' page.
   *
   * @param integer $id
   * @return mixed
   */
  public function actionDelete( $id )
  {
    $this->findModel( $id )->delete();
    return $this->redirect( ['index'] );
  }

  /**
   * Finds the User model based on its primary key value.
   * If the model is not found, a 404 HTTP exception will be thrown.
   *
   * @param integer $id
   * @return User the loaded model
   * @throws HttpException if the model cannot be found
   */
  protected function findModel( $id )
  {
    if ( ( $model = User::findOne( $id ) ) !== null ) {
      return $model;
    } else {
      throw new NotFoundHttpException( 'The requested page does not exist.' );
    }
  }
}

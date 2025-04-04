<?php

namespace app\controllers;

use Yii;
use app\models\SysMedPatologia;
use app\models\search\SysMedPatologiaSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * PatologiaController implements the CRUD actions for SysMedPatologia model.
 */
class PatologiaController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all SysMedPatologia models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SysMedPatologiaSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SysMedPatologia model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new SysMedPatologia model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new SysMedPatologia();

        if ($model->load(Yii::$app->request->post())) {
            
            $model->nombre = strtoupper($model->nombre);
            $model->save();
            
            if($model->save(false)):
            
            Yii::$app->getSession()->setFlash('info', [
                'type' => 'success','duration' => 1500,
                'icon' => 'glyphicons glyphicons-robot','message' => 'Los datos han sido registrados  con éxito!',
                'positonY' => 'top','positonX' => 'right']);
            
            else:
            
            Yii::$app->getSession()->setFlash('info', [
                'type' => 'danger','duration' => 1500,
                'icon' => 'glyphicons glyphicons-robot','message' => 'Ha ocurrido un error. Comuniquese con su administrador! ',
                'positonY' => 'top','positonX' => 'right']);
            endif;
            
            return $this->redirect('index');
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing SysMedPatologia model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            
            $model->nombre = strtoupper($model->nombre);
            $model->save();
            if($model->save(false)):
            
            Yii::$app->getSession()->setFlash('info', [
                'type' => 'success','duration' => 1500,
                'icon' => 'glyphicons glyphicons-robot','message' => 'Los datos han sido registrados  con éxito!',
                'positonY' => 'top','positonX' => 'right']);
            
            else:
            
            Yii::$app->getSession()->setFlash('info', [
                'type' => 'danger','duration' => 1500,
                'icon' => 'glyphicons glyphicons-robot','message' => 'Ha ocurrido un error. Comuniquese con su administrador! ',
                'positonY' => 'top','positonX' => 'right']);
            endif;
            
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing SysMedPatologia model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id);

        return $this->redirect(['index']);
    }

    /**
     * Finds the SysMedPatologia model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return SysMedPatologia the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SysMedPatologia::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}

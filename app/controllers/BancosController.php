<?php

namespace app\controllers;

use Yii;
use app\models\SysRrhhBancos;
use app\models\Search\SysRrhhSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * BancosController implements the CRUD actions for SysRrhhBancos model.
 */
class BancosController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'ghost-access'=> [
                'class' => 'webvimark\modules\UserManagement\components\GhostAccessControl',
            ],
        ];
    }

    /**
     * Lists all SysRrhhBancos models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SysRrhhSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SysRrhhBancos model.
     * @param string $id_sys_rrhh_banco
     * @param string $id_sys_empresa
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
     * Creates a new SysRrhhBancos model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new SysRrhhBancos();

        if ($model->load(Yii::$app->request->post())) {
            
            $codigo =  SysRrhhBancos::find()->select(['max(CAST(id_sys_rrhh_banco AS INT))'])->Where(['id_sys_empresa'=> '001'])->scalar();
            
            $model->id_sys_rrhh_banco = $codigo + 1;
            $model->id_sys_empresa    = '001';
            
            if($model->save(false)){
                
                Yii::$app->getSession()->setFlash('info', [
                    'type' => 'success','duration' => 1500,
                    'icon' => 'glyphicons glyphicons-robot','message' => 'El banco ha sido registrado con éxito!',
                    'positonY' => 'top','positonX' => 'right']);
            }
            else{
                
                Yii::$app->getSession()->setFlash('info', [
                    'type' => 'danger','duration' => 1500,
                    'icon' => 'glyphicons glyphicons-robot','message' => 'Ha ocurrido un error. Comuniquese con su administrador! ',
                    'positonY' => 'top','positonX' => 'right']);
            }
            return $this->redirect('index');
            
        
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing SysRrhhBancos model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id_sys_rrhh_banco
     * @param string $id_sys_empresa
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
          
            if($model->save(false)){
                
                Yii::$app->getSession()->setFlash('info', [
                    'type' => 'success','duration' => 1500,
                    'icon' => 'glyphicons glyphicons-robot','message' => 'Los datos han sido actualizados con  éxito!',
                    'positonY' => 'top','positonX' => 'right']);
            }
            else{
                
                Yii::$app->getSession()->setFlash('info', [
                    'type' => 'danger','duration' => 1500,
                    'icon' => 'glyphicons glyphicons-robot','message' => 'Ha ocurrido un error. Comuniquese con su administrador! ',
                    'positonY' => 'top','positonX' => 'right']);
                
                
            }
            return $this->redirect('index');
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing SysRrhhBancos model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id_sys_rrhh_banco
     * @param string $id_sys_empresa
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
       $model =  $this->findModel($id);
       $model->estado = 'I';
       $model->save(false);

        return $this->redirect(['index']);
    }

    /**
     * Finds the SysRrhhBancos model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id_sys_rrhh_banco
     * @param string $id_sys_empresa
     * @return SysRrhhBancos the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SysRrhhBancos::findOne(['id_sys_rrhh_banco' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}

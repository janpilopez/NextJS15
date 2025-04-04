<?php

namespace app\controllers;

use Yii;
use app\models\SysConfiguracion;
use app\models\Search\SysConfiguracionSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ConfiguracionsController implements the CRUD actions for SysConfiguracion model.
 */
class ConfiguracionsController extends Controller
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
     * Lists all SysConfiguracion models.
     * @return mixed
     */
    public function actionIndex()
    {
       /* $searchModel = new SysConfiguracionSearch();
          $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('create', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
        */
        
        $model = new SysConfiguracion();
        return $this->render('create', [
            'model' => $model,
        ]);
        
    }

    /**
     * Displays a single SysConfiguracion model.
     * @param string $id_sys_conf_cod
     * @param string $id_sys_empresa
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id_sys_conf_cod, $id_sys_empresa)
    {
        return $this->render('view', [
            'model' => $this->findModel($id_sys_conf_cod, $id_sys_empresa),
        ]);
    }

    /**
     * Creates a new SysConfiguracion model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new SysConfiguracion();

        if ($model->load(Yii::$app->request->post())) {
            
           /* $model->detalle = utf8_decode($model->detalle);
           
            if($model->save(false)){
                
                Yii::$app->getSession()->setFlash('info', [
                    'type' => 'success','duration' => 1500,
                    'icon' => 'glyphicons glyphicons-robot','message' => 'Los datos han sido registrados con éxito!',
                    'positonY' => 'top','positonX' => 'right']);
                
            }
            else{
                
                Yii::$app->getSession()->setFlash('info', [
                    'type' => 'danger','duration' => 1500,
                    'icon' => 'glyphicons glyphicons-robot','message' => 'Ha ocurrido un error. Comuniquese con su administrador! ',
                    'positonY' => 'top','positonX' => 'right']);
                
            }
            */
            return $this->redirect('index');
            
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing SysConfiguracion model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id_sys_conf_cod
     * @param string $id_sys_empresa
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id_sys_conf_cod, $id_sys_empresa)
    {
        $model = $this->findModel($id_sys_conf_cod, $id_sys_empresa);

        $model->detalle = utf8_encode($model->detalle);
       
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
     * Deletes an existing SysConfiguracion model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id_sys_conf_cod
     * @param string $id_sys_empresa
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id_sys_conf_cod, $id_sys_empresa)
    {
        $this->findModel($id_sys_conf_cod, $id_sys_empresa)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the SysConfiguracion model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id_sys_conf_cod
     * @param string $id_sys_empresa
     * @return SysConfiguracion the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id_sys_conf_cod, $id_sys_empresa)
    {
        if (($model = SysConfiguracion::findOne(['id_sys_conf_cod' => $id_sys_conf_cod, 'id_sys_empresa' => $id_sys_empresa])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}

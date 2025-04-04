<?php

namespace app\controllers;

use Exception;
use Yii;
use app\models\SysRrhhEmpleadosNovedades;
use app\models\Search\SysRrhhEmpleadosNovedadesSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\SysRrhhEmpleadosRolMov;
use app\models\SysRrhhEmpleadosRolCab;

/**
 * NovedadesController implements the CRUD actions for SysRrhhEmpleadosNovedades model.
 */
class NovedadesController extends Controller
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
     * Lists all SysRrhhEmpleadosNovedades models.
     * @return mixed
     */
    public function actionIndex()
    {
        $this->layout = '@app/views/layouts/main_emplados';
        $searchModel = new SysRrhhEmpleadosNovedadesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SysRrhhEmpleadosNovedades model.
     * @param string $id
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
     * Creates a new SysRrhhEmpleadosNovedades model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new SysRrhhEmpleadosNovedades();

        if ($model->load(Yii::$app->request->post())) {
            
            $codnovedad =  SysRrhhEmpleadosNovedades::find()->select(['max(CAST(id_sys_rrhh_empleados_novedad AS INT))'])->Where(['id_sys_empresa'=> '001'])->scalar();
            
            $model->id_sys_rrhh_empleados_novedad = $codnovedad + 1;
            $model->transaccion_usuario           = Yii::$app->user->username;
            $model->id_sys_empresa                = '001';
            
            if($model->save(false)){
                
                Yii::$app->getSession()->setFlash('info', [
                    'type' => 'success','duration' => 1500,
                    'icon' => 'glyphicons glyphicons-robot','message' => 'La Novedad ha sido registrado con éxito!',
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
     * Updates an existing SysRrhhEmpleadosNovedades model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        
        $model = $this->findModel($id);
        
        
        if($this->getCompruebaEstadoRol($model->fecha) != true):
        

                if ($model->load(Yii::$app->request->post())) {
                    
                    $model->transaccion_usuario =  Yii::$app->user->username;
                    
                    if($model->save(false)){
                        
                        Yii::$app->getSession()->setFlash('info', [
                            'type' => 'success','duration' => 1500,
                            'icon' => 'glyphicons glyphicons-robot','message' => 'La Novedad ha sido actualizada con éxito!',
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
                
        else:
       
            Yii::$app->getSession()->setFlash('info', [
            'type' => 'warning','duration' => 1500,
            'icon' => 'glyphicons glyphicons-robot','message' => 'La Novedad no puede ser actualizar, porque está dentro de un periodo procesado! ',
            'positonY' => 'top','positonX' => 'right']);
            
             return $this->redirect('index');
        
        endif;
    }

    /**
     * Deletes an existing SysRrhhEmpleadosNovedades model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        
      $model = $this->findModel($id);

  
       if($this->getCompruebaEstadoRol($model->fecha) != true):
        
        
                 $this->findModel($id)->delete();
              
                  Yii::$app->getSession()->setFlash('info', [
                  'type' => 'success','duration' => 1500,
                  'icon' => 'glyphicons glyphicons-robot','message' => 'La Novedad ha sido eliminada con éxito! ',
                  'positonY' => 'top','positonX' => 'right']);
        
        else:
        
     
            Yii::$app->getSession()->setFlash('info', [
                'type' => 'warning','duration' => 1500,
                'icon' => 'glyphicons glyphicons-robot','message' => 'La Novedad no puede ser actualizar, porque está dentro de un periodo procesado!',
                'positonY' => 'top','positonX' => 'right']);
        
        endif;
       
        
         return $this->redirect(['index']);
      
   
    }

    
    private function  getCompruebaEstadoRol($fecha){
        
       
       $db  = $_SESSION['db'];
        
        
       $rolprocesado = Yii::$app->$db->createCommand("SELECT * FROM sys_rrhh_empleados_rol_cab where  '{$fecha}' >= fecha_ini_liq and '{$fecha}' <= fecha_fin_liq and periodo = '2' and estado = 'P'")->queryOne();
     
       if($rolprocesado):
            return true ;
       endif;
       
     return false;
       
    }
    
    
    
    
    /**
     * Finds the SysRrhhEmpleadosNovedades model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return SysRrhhEmpleadosNovedades the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SysRrhhEmpleadosNovedades::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}

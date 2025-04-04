<?php

namespace app\controllers;

use Exception;
use Yii;
use app\models\Model;
use app\models\SysRrhhMareasCab;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use app\models\Search\SysRrhhMareasCabSearch;
use app\models\SysRrhhMareasDet;
use app\models\SysRrhhEmpleados;

/**
 * MareasController implements the CRUD actions for SysRrhhMareasCab model.
 */
class MareasController extends Controller
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
     * Lists all SysRrhhMareasCab models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SysRrhhMareasCabSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SysRrhhMareasCab model.
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
     * Creates a new SysRrhhMareasCab model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model    = new SysRrhhMareasCab();
        
        $modeldet = [new SysRrhhMareasDet()];
        
        $db       =  $_SESSION['db'];

        if ($model->load(Yii::$app->request->post())) {
            
        
               $modeldet = Model::createTripulantesMarea(SysRrhhMareasDet::classname());
               Model::loadMultiple($modeldet, Yii::$app->request->post());
      
               $transaction = \Yii::$app->$db->beginTransaction();
               
               try {
                   
                      $model->estado           = 'A';
                      $model->usuario_creacion =  Yii::$app->user->username;
                   
                   if ($flag = $model->save(false)) {
                       
                       //Agregar Empleados
                       foreach ($modeldet as $index => $modeldetalle) {
                           
                           
                           $md                         =  new SysRrhhMareasDet();
                           $md->id_sys_rrhh_marea_cab  = $model->id_sys_rrhh_mareas_cab;
                           $md->id_sys_rrhh_cedula     = $modeldetalle->id_sys_rrhh_cedula;
                   
                           
                           if (! ($flag = $md->save(false))) {
                               $transaction->rollBack();
                               break;
                           }
                       }
                       
                       if ($flag) {
                           $transaction->commit();
                           Yii::$app->getSession()->setFlash('info', [
                               'type' => 'success','duration' => 1500,
                               'icon' => 'glyphicons glyphicons-robot','message' => 'La marea ha sido registrada con éxito!',
                               'positonY' => 'top','positonX' => 'right']);
                           
                           return $this->redirect(['index']);
                       }
                       
                   }
                   
               }catch (Exception $e) {
                   $transaction->rollBack();
                     return ($e->getMessage());
            
               } 
           
        }

        return $this->render('create', [
            'model' => $model,
            'modeldet' => $modeldet,
            'update' => 0
        ]);
    }

    /**
     * Updates an existing SysRrhhMareasCab model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model    = $this->findModel($id);
        $modeldet = [];
        
        $db       =  $_SESSION['db'];
        
        $detalle  =  SysRrhhMareasDet::find()->where(['id_sys_rrhh_marea_cab'=> $id])->all();
        
        //Detalle Marea
        if ($detalle){
            foreach ($detalle as $data){
                $obj                          = new SysRrhhMareasDet();
                $obj->id_sys_rrhh_marea_det   = $data->id_sys_rrhh_marea_det;
                $obj->id_sys_rrhh_cedula      = $data->id_sys_rrhh_cedula;
                $obj->id_sys_rrhh_marea_cab   = $this->getNombreEmpleado($data->id_sys_rrhh_cedula);
                array_push($modeldet, $obj);
            }
        }else{
            array_push($modeldet, new SysRrhhMareasDet());
        }
        
        
        if ($model->load(Yii::$app->request->post())) {
           
            
            $oldIDs         = ArrayHelper::map($modeldet, 'id_sys_rrhh_marea_det', 'id_sys_rrhh_marea_det');
            
            $arraynucleo    = Yii::$app->request->post('SysRrhhMareasDet');
            
            if($arraynucleo):
            
               $deletedIDs   = array_diff($oldIDs, array_filter(ArrayHelper::map($modeldet, 'id_sys_rrhh_marea_det', 'id_sys_rrhh_marea_det')));
            
            else:
               
                if($model->estado == 'A'):
                
                    SysRrhhMareasDet::deleteAll(['id_sys_rrhh_marea_det' => $oldIDs]);
            
                endif;
            
            endif;
            
            if(!empty($deletedIDs)):
                
               if($model->estado == 'A'):
            
                    SysRrhhMareasDet::deleteAll(['id_sys_rrhh_marea_det' => $deletedIDs]);
            
               endif;
               
            endif;
            
            
            $modeldet = Model::createTripulantesMarea(SysRrhhMareasDet::classname());
            Model::loadMultiple($modeldet, Yii::$app->request->post());
            
            $transaction = \Yii::$app->$db->beginTransaction();
            
            try {
               
                $model->usuario_actualizacion =  Yii::$app->user->username;
                $model->fecha_actualizacion   =  date('Ymd H:i:s');
                
                if ($flag = $model->save(false)) {
                    
                    //Agregar Empleados
                    foreach ($modeldet as $index => $modeldetalle) {
                        
                        
                        if(trim($modeldetalle->id_sys_rrhh_marea_det) == '' ):
                       
                                
                                $md                         =  new SysRrhhMareasDet();
                                $md->id_sys_rrhh_marea_cab  = $model->id_sys_rrhh_mareas_cab;
                                $md->id_sys_rrhh_cedula     = $modeldetalle->id_sys_rrhh_cedula;
                                
                                
                                if (! ($flag = $md->save(false))) {
                                    $transaction->rollBack();
                                    break;
                                }
                                
                        endif;
                        
                        
                        
                    }
                    
                    if ($flag) {
                        $transaction->commit();
                        Yii::$app->getSession()->setFlash('info', [
                            'type' => 'success','duration' => 1500,
                            'icon' => 'glyphicons glyphicons-robot','message' => 'La marea ha sido actualizada con éxito!',
                            'positonY' => 'top','positonX' => 'right']);
                        
                        return $this->redirect(['index']);
                    }
                    
                }
                
            }catch (Exception $e) {
                $transaction->rollBack();
                return ($e->getMessage());
                
            }
            
            
        }

        return $this->render('update', [
            'model' => $model,
            'modeldet'=> $modeldet,
            'update'=> '1'
            
        ]);
    }

    
    public function actionListadotripulantes(){
        
        
        $datos  = SysRrhhEmpleados::find()->where(['estado' => 'A'])->andWhere(['tipo_empleado'=> 'T'])->all();         
        return $this->renderAjax('_listempleados', ['datos'=> $datos]);

    }
    
    private function getNombreEmpleado($id){
        
        $obj = SysRrhhEmpleados::find()->where(['id_sys_rrhh_cedula'=> $id])->one();
        
        if($obj):
              return $obj->nombres;
             
       endif;
        
       return 's/n';
    }
    
    
    
    /**
     * Deletes an existing SysRrhhMareasCab model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model =  $this->findModel($id);
        
        $model->anulada= 'A';

        return $this->redirect(['index']);
    }

    /**
     * Finds the SysRrhhMareasCab model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return SysRrhhMareasCab the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SysRrhhMareasCab::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}

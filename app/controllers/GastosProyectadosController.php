<?php

namespace app\controllers;

use app\models\SysRrhhEmpleadosGastos;
use app\models\SysRrhhEmpleadosGastosProyectados;
use app\models\SysRrhhEmpleadosGastosProyectadosDet;
use app\models\SysRrhhEmpleadosNucleoFamiliar;
use app\models\SysRrhhRubrosGastos;
use Yii;
use Exception;
use app\models\search\SysRrhhEmpleadosGastosProyectadosSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\Model;
use app\models\SysEmpresa;
use yii\web\UploadedFile;
use yii\helpers\ArrayHelper;

/**
 * GastosProyectadosController implements the CRUD actions for SysRrhhEmpleadosGastosProyectados model.
 */
class GastosProyectadosController extends Controller
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
     * Lists all SysRrhhEmpleadosGastosProyectados models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SysRrhhEmpleadosGastosProyectadosSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SysRrhhEmpleadosGastosProyectados model.
     * @param string $id_sys_empresa
     * @param string $id_sys_adm_area
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
     * Creates a new SysRrhhEmpleadosGastosProyectados model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new SysRrhhEmpleadosGastosProyectados();
        $modeldet  = [new SysRrhhEmpleadosGastosProyectadosDet()];

        $db = $_SESSION['db'];
        
        if ($model->load(Yii::$app->request->post())) {
            
            $empresa = SysEmpresa::find()->where(['db_name'=> $db])->one();
            $detalles = Model::createDetalleRubroGastos(SysRrhhEmpleadosGastosProyectadosDet::classname());
            Model::loadMultiple($detalles, Yii::$app->request->post());
         
            $transaction = \Yii::$app->$db->beginTransaction();
            
            try {

                $existente = SysRrhhEmpleadosGastosProyectados::find()->where(['id_sys_rrhh_cedula'=> $model->id_sys_rrhh_cedula])->andWhere(['anio'=>$model->anio])->one();

                if($existente):

                    $transaction->rollBack();
                    Yii::$app->getSession()->setFlash('info', [
                        'type' => 'danger','duration' => 1500,
                        'icon' => 'glyphicons glyphicons-robot','message' => 'Ya existe registro de este empleado en el año '.$model->anio,
                        'positonY' => 'top','positonX' => 'right']);

                else:

                    $model->file =  UploadedFile::getInstance($model, 'file');
           
                    if($model->file):
                    
                            $ruta =  "C:/tthh/gastospersonal/".$model->id_sys_rrhh_cedula.'_'.$model->anio.'.'.$model->file->extension;
                            $model->file->saveAs($ruta);
                            $model->documento = $ruta;
                    
                    endif;
                
                    $model->transaccion_usuario  = Yii::$app->user->username;
                    $model->id_sys_empresa    = '001';
                    
                    if ($flag = $model->save(false)) {
                        
                        //Agregar Detalle
                        foreach ($detalles  as $index => $detalle) {

                            $rubros = SysRrhhEmpleadosGastos::find()->where(['id_sys_rrhh_cedula'=>$model->id_sys_rrhh_cedula])->andWhere(['id_sys_rrhh_rubros_gastos'=>$detalle['id_sys_rrhh_rubros_gastos']])->one();
                            
                            if($rubros):

                                $rubros['cantidad'] = $detalle['cantidad'];

                                $rubros->save(false);
                                

                            else:

                                $newrubros = new SysRrhhEmpleadosGastos();
                                $codgasto   =  SysRrhhEmpleadosGastos::find()->select('max(id_sys_rrhh_empleados_gasto)')->scalar() + 1;
                                $newrubros->id_sys_rrhh_empleados_gasto = $codgasto;
                                $newrubros->id_sys_rrhh_cedula = $model->id_sys_rrhh_cedula;
                                $newrubros->id_sys_rrhh_rubros_gastos = $detalle['id_sys_rrhh_rubros_gastos'];
                                $newrubros->cantidad = $detalle['cantidad'];
                                $newrubros->id_sys_empresa = '001';
                                $newrubros->transaccion_usuario = Yii::$app->user->username;

                                $newrubros->save(false);

                            endif;

                            $newdetalle  =  new SysRrhhEmpleadosGastosProyectadosDet();
                            $newdetalle->id_gasto_proyectado = $model->id_gasto_proyectado;
                            $newdetalle->id_sys_rrhh_cedula =   $model->id_sys_rrhh_cedula;
                            $newdetalle->id_sys_rrhh_rubros_gastos = $detalle['id_sys_rrhh_rubros_gastos'];
                            $newdetalle->cantidad= $detalle['cantidad'];
                    
                            if(!$newdetalle->save(false)){
                                $flag = false;
                                $transaction->rollBack();
                                break;
                            }
                            
                        }
                                            
                        if ($flag) {
                        
                            $transaction->commit();
                            
                            Yii::$app->getSession()->setFlash('info', [
                                'type' => 'success','duration' => 3000,
                                'icon' => 'glyphicons glyphicons-robot','message' => 'Los gastos han sido registrado con éxito!',
                                'positonY' => 'top','positonX' => 'right']);
                        
                        }
                        
                    }

                endif;
                
            }catch (Exception $e) {
                
                $transaction->rollBack();
                return $e->getMessage();
                Yii::$app->getSession()->setFlash('info', [
                    'type' => 'danger','duration' => 1500,
                    'icon' => 'glyphicons glyphicons-robot','message' => 'Ha ocurrido un error. Comuniquese con su administrador!'.$e->getMessage(),
                    'positonY' => 'top','positonX' => 'right']);
                   
            }
                    
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
            'modeldet' => $modeldet,
            'update' => 0,
        ]);
    }

    /**
     * Updates an existing SysRrhhEmpleadosGastosProyectados model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id_sys_empresa
     * @param string $id_sys_adm_area
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $modeldet = [];

        $db    = $_SESSION['db'];

        $datos= SysRrhhEmpleadosGastosProyectadosDet::find()
        ->where(['id_gasto_proyectado'=> $id])
        ->all();
        
        if ($datos){
            foreach ($datos as $data){
                $obj                                   = new SysRrhhEmpleadosGastosProyectadosDet();
                $obj->id_gasto_proyectado_det          = $data->id_gasto_proyectado_det;
                $obj->id_sys_rrhh_rubros_gastos        = $data->id_sys_rrhh_rubros_gastos;
                $obj->cantidad                         = $data->cantidad;
                array_push($modeldet, $obj);
            }
        }else{
            array_push($modeldet, new SysRrhhEmpleadosGastosProyectadosDet());
        }

        if ($model->load(Yii::$app->request->post())) {

            $model->file =  UploadedFile::getInstance($model, 'file');
           
            if($model->file):
                    
                $ruta =  "C:/tthh/gastospersonal/".$model->id_sys_rrhh_cedula.'_'.$model->anio.'.'.$model->file->extension;
                $model->file->saveAs($ruta);
                $model->documento = $ruta;
                    
            endif;
            
                $oldIDs    = ArrayHelper::map($modeldet, 'id_gasto_proyectado_det', 'id_gasto_proyectado_det');
            
                $array  = Yii::$app->request->post('SysRrhhEmpleadosGastosProyectadosDet');
                
                if ($array){
                    
                    $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($array, 'id_gasto_proyectado_det', 'id_gasto_proyectado_det')));
                }
                
                if(!empty($deletedIDs)){
                    
                    SysRrhhEmpleadosGastosProyectadosDet::deleteAll(['id_gasto_proyectado_det' => $deletedIDs]);
                }
                
                $transaction = \Yii::$app->$db->beginTransaction();
                
                try {
                    
                    if ($flag = $model->save(false)) {
                        
            
                        if ($array){
                            
                            foreach ($array as $index => $modeldetalle) {
                            
                                if($modeldetalle['id_gasto_proyectado_det'] != ''){
                                    $md =  SysRrhhEmpleadosGastosProyectadosDet::find()->where(['id_gasto_proyectado_det'=> $modeldetalle['id_gasto_proyectado_det']])->one();
                                }
                                else{
                                    $md =  new SysRrhhEmpleadosGastosProyectadosDet();
                                }

                                if($modeldetalle['id_sys_rrhh_rubros_gastos'] != ''){

                                    $rubros = SysRrhhEmpleadosGastos::find()->where(['id_sys_rrhh_cedula'=>$model->id_sys_rrhh_cedula])->andWhere(['id_sys_rrhh_rubros_gastos'=>$modeldetalle['id_sys_rrhh_rubros_gastos']])->one();
                            
                                    if($rubros):

                                        $rubros['cantidad'] = $modeldetalle['cantidad'];

                                        $rubros->save(false);
                                        

                                    else:

                                        $newrubros = new SysRrhhEmpleadosGastos();
                                        $codgasto   =  SysRrhhEmpleadosGastos::find()->select('max(id_sys_rrhh_empleados_gasto)')->scalar() + 1;
                                        $newrubros->id_sys_rrhh_empleados_gasto = $codgasto;
                                        $newrubros->id_sys_rrhh_cedula = $model->id_sys_rrhh_cedula;
                                        $newrubros->id_sys_rrhh_rubros_gastos = $modeldetalle['id_sys_rrhh_rubros_gastos'];
                                        $newrubros->cantidad = $modeldetalle['cantidad'];
                                        $newrubros->id_sys_empresa = '001';
                                        $newrubros->transaccion_usuario = Yii::$app->user->username;

                                        $newrubros->save(false);

                                    endif;

                                    $md->id_sys_rrhh_cedula                = $model->id_sys_rrhh_cedula;
                                    $md->id_gasto_proyectado               = $model->id_gasto_proyectado;
                                    $md->id_sys_rrhh_rubros_gastos         = $modeldetalle['id_sys_rrhh_rubros_gastos'];
                                    $md->cantidad                          = $modeldetalle['cantidad'];

                                }

                                    if (! ($flag = $md->save(false))) {
                                        $transaction->rollBack();
                                        break;
                                    }
                                }  
                            
                        
                        }else{
                            
                            $flag= true;
                        }
                        
                        if ($flag) {
                            $transaction->commit();
                            Yii::$app->getSession()->setFlash('info', [
                                'type' => 'success','duration' => 1500,
                                'icon' => 'glyphicons glyphicons-robot','message' => 'Los gastos han sido actualizado con éxito!',
                                'positonY' => 'top','positonX' => 'right']);
                            
                            return $this->redirect(['index']);
                        }
                    }
                    
                }catch (Exception $e) {
                    $transaction->rollBack();
                    throw new Exception($e);
                    Yii::$app->getSession()->setFlash('info', [
                        'type' => 'danger','duration' => 1500,
                        'icon' => 'glyphicons glyphicons-robot','message' => 'Ha ocurrido un error. Comuniquese con su administrador! ',
                        'positonY' => 'top','positonX' => 'right']);
                    return $this->redirect(['index']);
                }
          
        }

        return $this->render('update', [
            'model' => $model,
            'modeldet' => $modeldet,
            'update' => 1,
        ]);
    }

    /**
     * Deletes an existing SysRrhhEmpleadosGastosProyectados model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id_sys_empresa
     * @param string $id_sys_adm_area
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

    public function actionUpdateestado(){
        
        $db          =  $_SESSION['db'];
        $datos       =  Yii::$app->request->post('datos');
        $obj         =  json_decode($datos);
      
        $codigos     =  $obj->id;

        $ids         =  explode(",",$codigos);

        $transaction = \Yii::$app->$db->beginTransaction();
                
        $flag = true;

        foreach ($ids as $data){

            $familiar = SysRrhhEmpleadosNucleoFamiliar::find()->where(['id_sys_rrhh_empleados_fam_cod'=>$data])->one();
            
            if($familiar){

                $familiar->rentas = 1;           
                
                if(!$flag = $familiar->save(false)){
                                
                    break;
                    
                }

            }else{
                echo json_encode(['data' => ['estado' => false, 'mensaje' => 'No existe familiar con ese codigo='.$data]]);
            }
        }  
        
        if($flag){
            $transaction->commit();
            echo  json_encode(['data' => [ 'estado' => false,'mensaje' => 'Los datos se ha registrado con exito!']]);
            
        }else{
            $transaction->rollBack();
            echo  json_encode(['data' => ['estado' => false, 'mensaje' => 'Ha ocurrido un error al actualizar familiar!']]);
        }
}
    public function actionEmpleadoscargas($cedula){
        
        $db =  $_SESSION['db'];
        
        $datos = [];
        
        $datos =   Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerCargasFamiliaresEmpleado] @id_sys_rrhh_cedula = '{$cedula}'")->queryAll(); 
        
        return $this->renderAjax('_listcargasempleados', [
            'datos'=>$datos
        ]);
        
    }

    public function actionDownload($id)
    {
      $model = $this->findModel($id);

      $path = $model->documento;

      if (file_exists($path)) {
        return Yii::$app->response->sendFile($path);
      }
      
    }

    /**
     * Finds the SysRrhhEmpleadosGastosProyectados model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id_sys_empresa
     * @param string $id_sys_adm_area
     * @return SysRrhhEmpleadosGastosProyectados the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SysRrhhEmpleadosGastosProyectados::findOne(['id_gasto_proyectado' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}

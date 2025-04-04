<?php

namespace app\controllers;

use Exception;
use Yii;
use app\models\SysRrhhCuadrillas;
use app\models\search\SysRrhhCuadrillasSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use app\models\Model;
use app\models\SysRrhhCuadrillasEmpleados;
use app\models\SysRrhhEmpleados;

/**
 * CuadrillasController implements the CRUD actions for SysRrhhCuadrillas model.
 */
class CuadrillasController extends Controller
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
     * Lists all SysRrhhCuadrillas models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SysRrhhCuadrillasSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SysRrhhCuadrillas model.
     * @param string $id_sys_rrhh_cuadrilla
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
     * Creates a new SysRrhhCuadrillas model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model    = new SysRrhhCuadrillas();
        $modeldet = [new SysRrhhCuadrillasEmpleados()];
        
        $db  =  $_SESSION['db'];

        if ($model->load(Yii::$app->request->post())) {
            
            $modeldet = Model::createEmpleadosCuadrillas(SysRrhhCuadrillasEmpleados::classname());
            Model::loadMultiple($modeldet, Yii::$app->request->post());
            
            
            
            
            $transaction = \Yii::$app->$db->beginTransaction();
            
            try {
                
                $codcuadrilla =  SysRrhhCuadrillas::find()->select(['max(CAST(id_sys_rrhh_cuadrilla AS INT))'])
                ->Where(['id_sys_empresa'=> '001'])->scalar();
                
                $model->id_sys_rrhh_cuadrilla         = $codcuadrilla + 1;
                $model->transaccion_usuario           = Yii::$app->user->username;
                $model->id_sys_empresa                = '001';
                $model->estado = 'A';
                
                if ($flag = $model->save(false)) {
                    
                   //Agregar Empleados       
                    foreach ($modeldet as $index => $modeldetalle) {
                            
                         
                            $md =  new SysRrhhCuadrillasEmpleados();
                            $newcodigo =  SysRrhhCuadrillasEmpleados::find()->select(['max(CAST(id_sys_rrhh_cuadrillas_empleados AS INT))'])->Where(['id_sys_empresa'=> '001'])->scalar() + 1; 
                            
                            if($modeldetalle->id_sys_rrhh_cedula == Null || empty($modeldetalle->id_sys_rrhh_cedula)){
                               
                            }else{
                                $md->id_sys_rrhh_cuadrillas_empleados  = $newcodigo;
                                $md->id_sys_rrhh_cedula                = $modeldetalle->id_sys_rrhh_cedula;
                                $md->id_sys_empresa                    = '001';
                                $md->id_sys_rrhh_cuadrilla             = $model->id_sys_rrhh_cuadrilla;
                                if (! ($flag = $md->save(false))) {
                                    $transaction->rollBack();
                                    break;
                                }
                            }

                            
                            
                        }
                    
                    if ($flag) {
                        $transaction->commit();
                        Yii::$app->getSession()->setFlash('info', [
                            'type' => 'success','duration' => 1500,
                            'icon' => 'glyphicons glyphicons-robot','message' => 'El grupo  ha sido actualizado con éxito!',
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

        return $this->render('create', [
            'model' => $model,
            'modeldet'=> $modeldet,
            'update'=> 0,
            'esupdate'=> 0,
        ]);
    }

    
    public function actionEmpleadosdepartamento($departamento){
        
        $datos = [];
        
        $datos = (new \yii\db\Query())->select(['id_sys_rrhh_cedula', 'nombres'])
        ->from('sys_rrhh_empleados')
        ->innerjoin('sys_adm_cargos', 'sys_rrhh_empleados.id_sys_adm_cargo = sys_adm_cargos.id_sys_adm_cargo and sys_rrhh_empleados.id_sys_empresa = sys_adm_cargos.id_sys_empresa')
        ->where("sys_rrhh_empleados.id_sys_empresa = '001'")
        ->andWhere("sys_rrhh_empleados.estado = 'A'")
        ->andwhere("sys_adm_cargos.id_sys_adm_departamento = {$departamento}")
        ->orderby('nombres')
        ->all(SysRrhhCuadrillas::getDb());
        
        return $this->renderAjax('_listempleados', [
            'datos'=>$datos
        ]);
        
    }
    
    /**
     * Updates an existing SysRrhhCuadrillas model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id_sys_rrhh_cuadrilla
     * @param string $id_sys_empresa
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        
        $db    = $_SESSION['db'];
       
        $modeldet = [];        
        
        $datos= SysRrhhCuadrillasEmpleados::find()
        ->joinWith(['sysRrhhCedula'])
        ->where(['sys_rrhh_cuadrillas_empleados.id_sys_rrhh_cuadrilla'=> $id])
        ->orderBy('nombres')
        ->all();
        
        //nucleo familiar
        if ($datos){
            foreach ($datos as $data){
                $obj                                   = new SysRrhhCuadrillasEmpleados();
                $emp                                   = SysRrhhEmpleados::find()->where(['id_sys_rrhh_cedula'=> $data->id_sys_rrhh_cedula])->andWhere(['id_sys_empresa'=> $data->id_sys_empresa])->one();
                $obj->id_sys_rrhh_cuadrillas_empleados = $data->id_sys_rrhh_cuadrillas_empleados;
                $obj->id_sys_rrhh_cedula               = $data->id_sys_rrhh_cedula;
                $obj->id_sys_empresa                   = $emp['nombres'];
                array_push($modeldet, $obj);
            }
        }else{
            array_push($modeldet, new SysRrhhCuadrillasEmpleados());
        }
        //fin de nucle familiar
        
        if ($model->load(Yii::$app->request->post())) {
            
            $oldIDs    = ArrayHelper::map($modeldet, 'id_sys_rrhh_cuadrillas_empleados', 'id_sys_rrhh_cuadrillas_empleados');
            
            $array  = Yii::$app->request->post('SysRrhhCuadrillasEmpleados');
            
            if ($array){
                
                $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($array, 'id_sys_rrhh_cuadrillas_empleados', 'id_sys_rrhh_cuadrillas_empleados')));
            }
            
            if(!empty($deletedIDs)){
                
                SysRrhhCuadrillasEmpleados::deleteAll(['id_sys_rrhh_cuadrillas_empleados' => $deletedIDs]);
            }
            
            $transaction = \Yii::$app->$db->beginTransaction();
            
            try {
                
                if ($flag = $model->save(false)) {
                    
                    //nucleo familiar
                    if ($array){
                        
                        foreach ($array as $index => $modeldetalle) {
                           
                            if($modeldetalle['id_sys_rrhh_cuadrillas_empleados'] != ''){
                                
                                $newcodigo = SysRrhhCuadrillasEmpleados::find()->select('id_sys_rrhh_cuadrillas_empleados')->Where(['id_sys_empresa'=> '001', 'id_sys_rrhh_cuadrillas_empleados'=> $modeldetalle['id_sys_rrhh_cuadrillas_empleados']])->scalar();
                                $md =  SysRrhhCuadrillasEmpleados::find()->where(['id_sys_empresa'=> '001', 'id_sys_rrhh_cuadrillas_empleados'=> $modeldetalle['id_sys_rrhh_cuadrillas_empleados']])->one();
                            }
                            else{
                                $md =  new SysRrhhCuadrillasEmpleados();
                                $newcodigo =  SysRrhhCuadrillasEmpleados::find()->select(['max(CAST(id_sys_rrhh_cuadrillas_empleados AS INT))'])->Where(['id_sys_empresa'=> '001'])->scalar() + 1;
                            }
                            
                            $md->id_sys_rrhh_cuadrillas_empleados  = $newcodigo;
                            $md->id_sys_rrhh_cedula                = $modeldetalle['id_sys_rrhh_cedula'];
                            $md->id_sys_rrhh_cuadrilla             = $model->id_sys_rrhh_cuadrilla;
                            $md->id_sys_empresa                    = '001';
                   
                            
                            if (! ($flag = $md->save(false))) {
                                $transaction->rollBack();
                                break;
                            }
                        }
                        
                    }else{
                        
                        $flag= true;
                    }
                    //jornadadas
                    
                    if ($flag) {
                        $transaction->commit();
                        Yii::$app->getSession()->setFlash('info', [
                            'type' => 'success','duration' => 1500,
                            'icon' => 'glyphicons glyphicons-robot','message' => 'El grupo  ha sido actualizado con éxito!',
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
            'model'   => $model,
            'modeldet' => $modeldet,
            'update'=> 1,
            'esupdate'=> 1,
        ]);
    }

    /**
     * Deletes an existing SysRrhhCuadrillas model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id_sys_rrhh_cuadrilla
     * @param string $id_sys_empresa
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
       $model =$this->findModel($id);
       
       $model->estado = 'I';
       
       $model->save(false);

        return $this->redirect(['index']);
    }

    /**
     * Finds the SysRrhhCuadrillas model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id_sys_rrhh_cuadrilla
     * @param string $id_sys_empresa
     * @return SysRrhhCuadrillas the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SysRrhhCuadrillas::findOne(['id_sys_rrhh_cuadrilla' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}

<?php

namespace app\controllers;

use Exception;
use Yii;
use app\models\Model;
use app\models\SysRrhhHextrasCab;
use app\models\search\SysRrhhHextrasCabSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use app\models\SysRrhhHextrasMov;

/**
 * HorasExtrasController implements the CRUD actions for SysRrhhHextrasCab model.
 */
class HorasExtrasController extends Controller
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
     * Lists all SysRrhhHextrasCab models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SysRrhhHextrasCabSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SysRrhhHextrasCab model.
     * @param string $id_sys_rrhh_hextras
     * @param string $id_sys_empresa
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
   
    {
        $modeldet = [];
        
        $horasmov  = SysRrhhHextrasMov::find()->where(['id_sys_rrhh_hextras'=> $id])->all();
        
        if ($horasmov){
            
            foreach ($horasmov as $data){
                
                $obj                               = new SysRrhhHextrasMov();
                $obj->secuencia                    = $data->secuencia;
                $obj->hora_inicio                  = $data->hora_inicio;
                $obj->hora_fin                     = $data->hora_fin;
                $obj->dia                          = $data->dia;
                $obj->jornada                      = $data->jornada;
                array_push($modeldet, $obj) ;
            }
            
        }else{
            
            array_push($modeldet, new SysRrhhHextrasMov());
        }
        
        return $this->render('view', [
            'model' => $this->findModel($id),
            'modeldet'=> $modeldet
        ]);
    }

    /**
     * Creates a new SysRrhhHextrasCab model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model    = new SysRrhhHextrasCab();
        $modeldet = [new SysRrhhHextrasMov()];
        

        if ($model->load(Yii::$app->request->post())) {
            
            $modeldet = Model::createHorasExtras(SysRrhhHextrasMov::classname());
            Model::loadMultiple($modeldet, Yii::$app->request->post());
            
           
            $transaction = \Yii::$app->$_SESSION['db']->beginTransaction();
            
            try {
                
            
                $model->transaccion_usuario           = Yii::$app->user->username;
                $model->id_sys_empresa                = '001';
                
                if ($flag = $model->save(false)) {
                    
                    //Agregar Empleados
                    foreach ($modeldet as $index => $modeldetalle) {
                        
                        //realizar este metodo por que el driver pdo no lo soporta
                        $nflag =  Yii::$app->$_SESSION['db']->createCommand("insert into sys_rrhh_hextras_mov  (id_sys_rrhh_hextras, dia, jornada, hora_inicio, hora_fin, id_sys_empresa) values ('{$model->id_sys_rrhh_hextras}', '{$modeldetalle['dia']}', '{$modeldetalle['jornada']}', '{$modeldetalle['hora_inicio']}', '{$modeldetalle['hora_fin']}', '001')");
                        $nflag->execute();
                        
                        if(!$nflag){
                            $flag = false;
                            $transaction->rollBack();
                            break;
                        }
                    
                    }
                    
                    if ($flag) {
                        $transaction->commit();
                        Yii::$app->getSession()->setFlash('info', [
                            'type' => 'success','duration' => 1500,
                            'icon' => 'glyphicons glyphicons-robot','message' => 'Los datos se han registrado con éxito!',
                            'positonY' => 'top','positonX' => 'right']);
                        
                        return $this->redirect(['index']);
                    }
                    
                }
                
            }catch (Exception $e) {
                $transaction->rollBack();
              //  throw new Exception($e);
                Yii::$app->getSession()->setFlash('info', [
                    'type' => 'danger','duration' => 1500,
                    'icon' => 'glyphicons glyphicons-robot','message' => 'Ha ocurrido un error. Comuniquese con su administrador! ',
                    'positonY' => 'top','positonX' => 'right']);
                return $this->redirect(['index']);
                
            }
            
            
            
          
        }

        return $this->render('create', [
            'model' => $model,
            'modeldet'=> $modeldet
        ]);
    }

    /**
     * Updates an existing SysRrhhHextrasCab model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id_sys_rrhh_hextras
     * @param string $id_sys_empresa
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model    = $this->findModel($id);
        
        $modeldet = [];
        
        $horasmov  = SysRrhhHextrasMov::find()->where(['id_sys_rrhh_hextras'=> $id])->all();
        
        if ($horasmov){
        
            foreach ($horasmov as $data){
                
                $obj                               = new SysRrhhHextrasMov();
                $obj->secuencia                    = $data->secuencia;
                $obj->hora_inicio                  = $data->hora_inicio;
                $obj->hora_fin                     = $data->hora_fin;
                $obj->dia                          = $data->dia;
                $obj->jornada                      = $data->jornada;
                array_push($modeldet, $obj) ; 
            }
            
        }else{
            
            array_push($modeldet, new SysRrhhHextrasMov());
        }
        
         if ($model->load(Yii::$app->request->post())) {
             
             $oldIDs    = ArrayHelper::map($modeldet, 'secuencia', 'secuencia'); //guarda los indices 
             
             $array     = Yii::$app->request->post('SysRrhhHextrasMov'); //recibe los datos por post
             
             if ($array){
                 
                 $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($array, 'secuencia', 'secuencia'))); //filtramos     
             }
            
             if(!empty($deletedIDs)){
                 
                 SysRrhhHextrasMov::deleteAll(['secuencia' => $deletedIDs]);
             }
             
             $transaction = \Yii::$app->$_SESSION['db']->beginTransaction();
             
             try {
                 
                 if ($flag = $model->save(false)) {
                     
                     //nucleo familiar
                     if ($array){
                         
                         foreach ($array as $index => $modeldetalle) {
                             
                             if($modeldetalle['secuencia'] != ''){
                                 //buscamos el  codigo 
                                 $newcodigo = SysRrhhHextrasMov::find()->select('secuencia')->Where(['id_sys_empresa'=> '001', 'secuencia'=> $modeldetalle['secuencia']])->scalar();
                                 $md =  SysRrhhHextrasMov::find()->where(['id_sys_empresa'=> '001', 'secuencia'=> $modeldetalle['secuencia']])->one();
                        
                                 $md->secuencia                         = $newcodigo;
                                 $md->dia                               = $modeldetalle['dia'];
                                 $md->jornada                           = $modeldetalle['jornada'];
                                 $md->hora_inicio                       = $modeldetalle['hora_inicio'];
                                 $md->hora_fin                          = $modeldetalle['hora_fin'];
                                 $md->id_sys_rrhh_hextras               = $model->id_sys_rrhh_hextras;
                                 $md->id_sys_empresa                    = '001';
                                 
                                 if (! ($flag = $md->save(false))) {
                                     $transaction->rollBack();
                                     break;
                                 }
                                 
                             }
                             else{
                                 //realizar este metodo por que el driver pdo no lo soporta 
                                 $nflag =  Yii::$app->$_SESSION['db']>createCommand("insert into sys_rrhh_hextras_mov  (id_sys_rrhh_hextras, dia, jornada, hora_inicio, hora_fin, id_sys_empresa) values ('{$model->id_sys_rrhh_hextras}', '{$modeldetalle['dia']}', '{$modeldetalle['jornada']}', '{$modeldetalle['hora_inicio']}', '{$modeldetalle['hora_fin']}', '001')");
                                 $nflag->execute();
                                 
                                 if(!$nflag){
                                     $flag = false;
                                     $transaction->rollBack();
                                     break;
                                 }
                             }
                         }
                         
                     }else{
                         
                         $flag= true;
                     }
                  
                     
                     if ($flag) {
                         $transaction->commit();
                         Yii::$app->getSession()->setFlash('info', [
                             'type' => 'success','duration' => 1500,
                             'icon' => 'glyphicons glyphicons-robot','message' => 'Los datos han sido guardados  con éxito!',
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
            'modeldet'=> $horasmov
        ]);
    }

    /**
     * Deletes an existing SysRrhhHextrasCab model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id_sys_rrhh_hextras
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
     * Finds the SysRrhhHextrasCab model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id_sys_rrhh_hextras
     * @param string $id_sys_empresa
     * @return SysRrhhHextrasCab the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SysRrhhHextrasCab::findOne(['id_sys_rrhh_hextras' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}

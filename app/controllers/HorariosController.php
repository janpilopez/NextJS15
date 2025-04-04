<?php

namespace app\controllers;

use Exception;
use Yii;
use app\models\Model;
use app\models\SysRrhhHorarioCab;
use app\models\Search\SysRrhhHorarioCabSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use app\models\SysRrhhHorarioDet;
use app\models\SysRrhhHextrasMov;

/**
 * HorariosController implements the CRUD actions for SysRrhhHorarioCab model.
 */
class HorariosController extends Controller
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
     * Lists all SysRrhhHorarioCab models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SysRrhhHorarioCabSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SysRrhhHorarioCab model.
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
     * Creates a new SysRrhhHorarioCab model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model    = new SysRrhhHorarioCab();
        $modeldet = [new SysRrhhHorarioDet()];   

        if ($model->load(Yii::$app->request->post())) {
            
            $modeldet = Model::createHorasExtras(SysRrhhHorarioDet::classname());
            Model::loadMultiple($modeldet, Yii::$app->request->post());
            
            $db = $_SESSION['db'];
            
            $transaction = \Yii::$app->$db->beginTransaction();
            
            try {
                
                
                $codigo = SysRrhhHorarioCab::find()->select(['max(CAST(id_sys_rrhh_horario_cab AS INT))'])->Where(['id_sys_empresa'=> '001'])->scalar();
                
                $model->id_sys_rrhh_horario_cab = $codigo + 1;
                $model->id_sys_empresa   = '001';
                
     
                if ($flag = $model->save(false)) {
                    
                     //agregar  detalle
                    foreach ($modeldet as $index => $modeldetalle) {
                        
                        $codigodet = SysRrhhHorarioDet::find()->select(['MAX(id_sys_rrhh_horario_det) + 1'])->Where(['id_sys_empresa'=> '001'])->scalar();
                        
                        //realizar este metodo por que el driver pdo no lo soporta
                        $nflag =  Yii::$app->$db->createCommand("insert into sys_rrhh_horario_det  (id_sys_rrhh_horario_det, dia, id_sys_rrhh_horario_cab,  id_sys_empresa) values ('".$codigodet."', '{$modeldetalle['dia']}', '{$model->id_sys_rrhh_horario_cab}', '{$model->id_sys_empresa}')");
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
        ]);
    }

    /**
     * Updates an existing SysRrhhHorarioCab model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model    = $this->findModel($id);
        
        $modeldet = [];
        
        $db       = $_SESSION['db'];
        
        $horasmov  =  SysRrhhHorarioDet::find()->where(['id_sys_rrhh_horario_cab'=> $id])->all();
        
        if ($horasmov){
            
            foreach ($horasmov as $data){
                
                $obj                               = new SysRrhhHorarioDet();
                $obj->id_sys_rrhh_horario_det      = $data->id_sys_rrhh_horario_det;
                $obj->dia                          = $data->dia;
                array_push($modeldet, $obj) ;
            }
            
        }else{
            
            array_push($modeldet, new SysRrhhHorarioDet());
        }
        
        if ($model->load(Yii::$app->request->post())) {
          
            
            $oldIDs    = ArrayHelper::map($modeldet, 'id_sys_rrhh_horario_det', 'id_sys_rrhh_horario_det'); //guarda los indices
            
            $array     = Yii::$app->request->post('SysRrhhHorarioDet'); //recibe los datos por post
            
            if ($array){
                
                $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($array, 'id_sys_rrhh_horario_det', 'id_sys_rrhh_horario_det'))); //filtramos
            }
            
            if(!empty($deletedIDs)){
                
                SysRrhhHorarioDet::deleteAll(['id_sys_rrhh_horario_det' => $deletedIDs]);
            }
            
            $transaction = \Yii::$app->$db->beginTransaction();
            
            try {
                
                if ($flag = $model->save(false)) {
                    
                    //nucleo familiar
                    if ($array){
                        
                        foreach ($array as $index => $modeldetalle) {
                            
                            if($modeldetalle['id_sys_rrhh_horario_det'] != ''){
                                //buscamos el  codigo
                                $md =  SysRrhhHorarioDet::find()->where(['id_sys_empresa'=> $model->id_sys_empresa, 'id_sys_rrhh_horario_det'=> $modeldetalle['id_sys_rrhh_horario_det']])->one();
    
                                $md->dia  = $modeldetalle['dia'];
                            
                                
                                if (! ($flag = $md->save(false))) {
                                    $transaction->rollBack();
                                    break;
                                }
                                
                            }
                            else{
                                //realizar este metodo por que el driver pdo no lo soporta
                                //realizar este metodo por que el driver pdo no lo soporta
                                $codigo =  SysRrhhHorarioDet::find()->select('max(id_sys_rrhh_horario_det) +1')->scalar();
                                
                                
                                $nflag =  Yii::$app->$db->createCommand("insert into sys_rrhh_horario_det  (id_sys_rrhh_horario_det, dia, id_sys_rrhh_horario_cab,  id_sys_empresa) values ('{$codigo}','{$modeldetalle['dia']}', '{$model->id_sys_rrhh_horario_cab}', '{$model->id_sys_empresa}')");
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
            'modeldet' => $modeldet,
        ]);
    }

    /**
     * Deletes an existing SysRrhhHorarioCab model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id);

        return $this->redirect(['index']);
    }

    /**
     * Finds the SysRrhhHorarioCab model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return SysRrhhHorarioCab the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SysRrhhHorarioCab::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}

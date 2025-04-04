<?php

namespace app\controllers;

use app\models\Model;
use app\models\SysAdmDepartamentos;
use app\models\SysCuerpoIndicador;
use app\models\SysDetalleIndicador;
use app\models\SysIndicadores;
use Yii;
use app\models\SysEncabezadoIndicador;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use app\models\search\SysCuerpoIndicadorSearch;
use Exception;
use yii\helpers\ArrayHelper;
use yii\filters\VerbFilter;

/**
 * FormIndicador implements the CRUD actions for SysIndicadores model.
 */
class FormIndicadorSistemasDetalleController extends Controller
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
     * Lists all SysIndicadores models.
     * @return mixed
     */
    public function actionIndex($id_encabezado_indicador)
    {
        $searchModel = new SysCuerpoIndicadorSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'id_encabezado_indicador' => $id_encabezado_indicador,
        ]);
    }

    /**
     * Displays a single SysIndicadores model.
     * @param string $id_sys_empresa
     * @param string $id_sys_adm_area
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id_cuerpo_indicador)
    {

        $modeldetalle = SysDetalleIndicador::find()->where(['id_cuerpo_indicador'=>$id_cuerpo_indicador])->all();

        return $this->render('view', [
            'model' => $this->findModel($id_cuerpo_indicador),
            'modeldet' => $modeldetalle,
        ]);
    }

    /**
     * Creates a new SysIndicadores model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($id_encabezado_indicador)
    {
        $model        = new SysCuerpoIndicador();
        $modeldet     = [new SysDetalleIndicador()];

        $db = $_SESSION['db'];
        
        if ($model->load(Yii::$app->request->post())) {

            $detalles = Model::createDetalleIndicadores(SysDetalleIndicador::classname());
            Model::loadMultiple($detalles, Yii::$app->request->post());

            $transaction = \Yii::$app->$db->beginTransaction();
            
            try {

                $encabezado = SysEncabezadoIndicador::find()->where(['id_encabezado_indicador'=>$id_encabezado_indicador])->one();

                $model->id_encabezado_indicador = $id_encabezado_indicador;
                $model->departamental           = $encabezado['id_sys_adm_departamento'];
                $model->tipo_indicador          = $encabezado['tipo_indicador'];
                       
                if ($flag = $model->save(false)) {

                    foreach ($detalles  as $index => $detalle) {
                            
                        $newdetalle                      =  new SysDetalleIndicador();
                        $newdetalle->id_cuerpo_indicador = $model->id_cuerpo_indicador;
                        $newdetalle->usuario             = $detalle['usuario'];
                        $newdetalle->can_negro           = $detalle['can_negro'];
                        $newdetalle->can_color           = $detalle['can_color'];
                        $newdetalle->rem_sol             = $detalle['rem_sol'];
                        $newdetalle->imp_departamento    = $model->imp_departamento;
                        $newdetalle->fecha               = $model->fecha;
                    
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
                            'icon' => 'glyphicons glyphicons-robot','message' => 'Los datos se han sido registrado con éxito!',
                            'positonY' => 'top','positonX' => 'right']);
                        
                    }
                
                }
                
                return $this->redirect(['index','id_encabezado_indicador'=>$id_encabezado_indicador]);
                
            }catch (Exception $e) {
                
                $transaction->rollBack();
                Yii::$app->getSession()->setFlash('info', [
                    'type' => 'danger','duration' => 1500,
                    'icon' => 'glyphicons glyphicons-robot','message' => 'Ha ocurrido un error. Comuniquese con su administrador!'.$e->getMessage(),
                    'positonY' => 'top','positonX' => 'right']);
                   
            }
                    
        }

        return $this->render('create', [
            'model' => $model,
            'modeldet' => $modeldet,
            'id_encabezado_indicador' => $id_encabezado_indicador,
            'update'=>0
        ]);
    }

    /**
     * Updates an existing SysIndicadores model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id_sys_empresa
     * @param string $id_sys_adm_area
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id_cuerpo_indicador)
    {
        $db    = $_SESSION['db'];
        $model = $this->findModel($id_cuerpo_indicador);
        $modeldet = [];

        $datos= SysDetalleIndicador::find()
        ->where(['id_cuerpo_indicador'=>$id_cuerpo_indicador])
        ->all();

        if ($datos){
            foreach ($datos as $data){
                $obj                                   = new SysDetalleIndicador();
                $obj->id_detalle_indicador   = $data->id_detalle_indicador;
                $obj->id_cuerpo_indicador    = $data->id_cuerpo_indicador;
                $obj->usuario                = $data->usuario;
                $obj->can_negro              = $data->can_negro;
                $obj->can_color              = $data->can_color;
                $obj->rem_sol                = $data->rem_sol;
                array_push($modeldet, $obj);
            }
        }else{
            array_push($modeldet, new SysDetalleIndicador());
        }

            if ($model->load(Yii::$app->request->post())) {

                $oldIDs    = ArrayHelper::map($modeldet, 'id_detalle_indicador', 'id_detalle_indicador');
            
                $array  = Yii::$app->request->post('SysDetalleIndicador');
                
                if ($array){
                    
                    $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($array, 'id_detalle_indicador', 'id_detalle_indicador')));
                }
                
                if(!empty($deletedIDs)){
                    
                    SysDetalleIndicador::deleteAll(['id_detalle_indicador' => $deletedIDs]);
                }
                
                $transaction = \Yii::$app->$db->beginTransaction();
                
                try {
                    
                    if ($flag = $model->save(false)) {
                        
                        //nucleo familiar
                        if ($array){
                            
                            foreach ($array as $index => $modeldetalle) {
                            
                                $md = SysDetalleIndicador::find()->where(['id_detalle_indicador'=> $modeldetalle['id_detalle_indicador']])->one();
                                if($md):
                                    $md->usuario     = $modeldetalle['usuario'];
                                    $md->can_negro   = $modeldetalle['can_negro'];
                                    $md->can_color   = $modeldetalle['can_color'];
                                    $md->rem_sol     = $modeldetalle['rem_sol'];
                                else:

                                    $md                      = new SysDetalleIndicador();
                                    $md->id_cuerpo_indicador = $model->id_cuerpo_indicador;
                                    $md->usuario             = $modeldetalle['usuario'];
                                    $md->can_negro           = $modeldetalle['can_negro'];
                                    $md->can_color           = $modeldetalle['can_color'];
                                    $md->rem_sol             = $modeldetalle['rem_sol'];
                                    $md->fecha               = $model->fecha;
                                    $md->imp_departamento    = $model->imp_departamento;
                                endif;
                                 
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
                                'icon' => 'glyphicons glyphicons-robot','message' => 'La solicitud ha sido actualizada con éxito!',
                                'positonY' => 'top','positonX' => 'right']);
                            
                            return $this->redirect(['index','id_encabezado_indicador'=>$model->id_encabezado_indicador]);
                        }
                    }
                    
                }catch (Exception $e) {
                    $transaction->rollBack();
                    throw new Exception($e);
                    Yii::$app->getSession()->setFlash('info', [
                        'type' => 'danger','duration' => 1500,
                        'icon' => 'glyphicons glyphicons-robot','message' => 'Ha ocurrido un error. Comuniquese con su administrador! ',
                        'positonY' => 'top','positonX' => 'right']);
                    return $this->redirect(['index','id_encabezado_indicador'=>$model->id_encabezado_indicador]);
                }
                
            }
        
       

        return $this->render('update', [
            'model' => $model,
            'modeldet' => $modeldet,
            'update' => 1,
        ]);
    }

    /**
     * Deletes an existing SysIndicadores model.
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

    private function getMeta($indicador){        
        $datos = [];
        $datos = SysIndicadores::find()
        ->andWhere(['id_indicador'=>$indicador])
        ->one();
   
        return $datos;
  
    }

    /**
     * Finds the SysIndicadores model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id_sys_empresa
     * @param string $id_sys_adm_area
     * @return SysCuerpoIndicador the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SysCuerpoIndicador::findOne(['id_cuerpo_indicador' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}

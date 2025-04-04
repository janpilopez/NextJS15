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
use app\models\search\SysEncabezadoIndicadorSearch;
use Exception;
use yii\helpers\ArrayHelper;
use yii\filters\VerbFilter;

/**
 * FormIndicador implements the CRUD actions for SysIndicadores model.
 */
class FormIndicadorSistemasController extends Controller
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
    public function actionIndex()
    {
        $searchModel = new SysEncabezadoIndicadorSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SysIndicadores model.
     * @param string $id_sys_empresa
     * @param string $id_sys_adm_area
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id_encabezado_indicador)
    {
        return $this->render('view', [
            'model' => $this->findModel($id_encabezado_indicador),
        ]);
    }

    /**
     * Creates a new SysIndicadores model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model        = new SysEncabezadoIndicador();

        $db = $_SESSION['db'];
        
        if ($model->load(Yii::$app->request->post())) {

            $transaction = \Yii::$app->$db->beginTransaction();
            
            try {
                
                $meta = $this->getMeta($model->tipo_indicador);
                $model->meta                     = $meta->meta;

                if ($flag = $model->save(false)) {
                                        
                    if ($flag) {
                        
                        $transaction->commit();
                            
                        Yii::$app->getSession()->setFlash('info', [
                            'type' => 'success','duration' => 3000,
                            'icon' => 'glyphicons glyphicons-robot','message' => 'Los datos se han sido registrado con éxito!',
                            'positonY' => 'top','positonX' => 'right']);
                        
                    }
                
                }
                
                return $this->redirect(['index']);
                
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
    public function actionUpdate($id_encabezado_indicador)
    {
        $model = $this->findModel($id_encabezado_indicador);
        $db    = $_SESSION['db'];

        if ($model->load(Yii::$app->request->post())) {
            
            
            $transaction = \Yii::$app->$db->beginTransaction();
                
                try {
                    
                    if ($flag = $model->save(false)) {
                            
                        }else{
                            
                            $flag= true;
                        }
                        if ($flag) {
                            $transaction->commit();
                            Yii::$app->getSession()->setFlash('info', [
                                'type' => 'success','duration' => 1500,
                                'icon' => 'glyphicons glyphicons-robot','message' => 'El indicador ha sido actualizada con éxito!',
                                'positonY' => 'top','positonX' => 'right']);
                            
                            return $this->redirect(['index']);
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
     * @return SysEncabezadoIndicador the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SysEncabezadoIndicador::findOne(['id_encabezado_indicador' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}

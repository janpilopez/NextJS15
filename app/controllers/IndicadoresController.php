<?php

namespace app\controllers;

use app\models\search\SysIndicadoresSearch;
use app\models\SysIndicadores;
use Yii;
use app\models\SysEncabezadoIndicador;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use app\models\search\SysEncabezadoIndicadorSearch;
use Exception;
use yii\filters\VerbFilter;

/**
 * IndicadoresController implements the CRUD actions for SysAdmAreas model.
 */
class IndicadoresController extends Controller
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
     * Lists all SysAdmAreas models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SysIndicadoresSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SysAdmAreas model.
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
     * Creates a new SysAdmAreas model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model        = new SysIndicadores();

        $db = $_SESSION['db'];
        
        if ($model->load(Yii::$app->request->post())) {
    
            $transaction = \Yii::$app->$db->beginTransaction();
            
            try {
                
                if ($flag = $model->save(false)) {
                                        
                    if ($flag) {
                       
                        $transaction->commit();
                        
                        Yii::$app->getSession()->setFlash('info', [
                            'type' => 'success','duration' => 3000,
                            'icon' => 'glyphicons glyphicons-robot','message' => 'Los datos se han sido registrado con éxito!',
                            'positonY' => 'top','positonX' => 'right']);
                      
                        return $this->redirect(['index']);
                    }
                    
                }
                
            }catch (Exception $e) {
                
                $transaction->rollBack();
                Yii::$app->getSession()->setFlash('info', [
                    'type' => 'danger','duration' => 1500,
                    'icon' => 'glyphicons glyphicons-robot','message' => 'Ha ocurrido un error. Comuniquese con su administrador!'.$e->getMessage(),
                    'positonY' => 'top','positonX' => 'right']);
                   
                return $this->redirect(['index']);
            }
                    
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing SysAdmAreas model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id_sys_empresa
     * @param string $id_sys_adm_area
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
     * Deletes an existing SysAdmAreas model.
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

    /**
     * Finds the SysAdmAreas model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id_sys_empresa
     * @param string $id_sys_adm_area
     * @return SysAdmAreas the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SysAdmAreas::findOne(['id_sys_adm_area' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}

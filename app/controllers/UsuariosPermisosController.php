<?php

namespace app\controllers;

use Yii;
use app\models\SysAdmUsuariosPer;
use app\models\SysAdmUsuariosPerSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * UsuariosPermisosController implements the CRUD actions for SysAdmUsuariosPer model.
 */
class UsuariosPermisosController extends Controller
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
     * Lists all SysAdmUsuariosPer models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SysAdmUsuariosPerSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SysAdmUsuariosPer model.
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
     * Creates a new SysAdmUsuariosPer model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
   /* public function actionCreate()
    {
        $model = new SysAdmUsuariosPer();

        if ($model->load(Yii::$app->request->post())) {
            
             $codpermiso =  SysAdmUsuariosPer::find()->select(['max(CAST(id_sys_adm_usuarios_dep AS INT))'])->scalar();
             $model->id_sys_adm_usuarios_dep = $codpermiso + 1;
             $model->id_sys_empresa          = '001';
             $model->save();
             return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }
    */
    
    
    public function actionRegistrar(){
        
        $model = new SysAdmUsuariosPer();
        return $this->render('_registrar',['model'=> $model]);
    }
    
    
    
    public function actionRegistrarusers(){
              
            if (Yii::$app->request->isAjax && Yii::$app->request->post()) {
                
                $obj         =  json_decode(Yii::$app->request->post('data'));
               
                $db          = $_SESSION['db'];
                
                $transaction = \Yii::$app->$db->beginTransaction();
                
                $flag = true;
                
                foreach ($obj as $data):
                    
          
                        $codpermiso =  SysAdmUsuariosPer::find()->select(['max(CAST(id_sys_adm_usuarios_per AS INT))'])->scalar();
                        $model      =  new SysAdmUsuariosPer();
                        
                        $model->id_sys_adm_usuarios_per = $codpermiso + 1;
                        $model->usuario_tipo            = $data->tipousuario;
                        $model->permiso                 = $data->permiso;
                        $model->estado                  = $data->estado;
                        
                 
                        if(!$flag = $model->save(false)){
                            
                            break;
                            
                        }
 
                    
                endforeach;
               
                
                if($flag){
                    $transaction->commit();
                    echo  json_encode(['data' => [ 'estado' => false,'mensaje' => 'Los datos se ha registrado con exito!']]);
                }else{
                    $transaction->rollBack();
                    echo  json_encode(['data' => ['estado' => false, 'mensaje' => 'Ha ocurrido un error al registrar el permiso!']]);
                }
                
            }else{
                
                echo  json_encode(['data' => ['estado' => false, 'mensaje' => 'Ha ocurrido un error!']]);
            }
       
    }
    
    
    
    /**
     * Updates an existing SysAdmUsuariosPer model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing SysAdmUsuariosPer model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id);
        return $this->redirect(['index']);
    }

    /**
     * Finds the SysAdmUsuariosPer model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return SysAdmUsuariosPer the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SysAdmUsuariosPer::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}

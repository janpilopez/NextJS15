<?php

namespace app\controllers;


use Exception;
use Yii;
use app\models\Search\SysRrhhEmpleadosUniformeSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;
use yii\helpers\ArrayHelper;
use app\models\SysRrhhEmpleadosFotoUniformes;
use app\models\SysRrhhEmpleadosUniformes;

/**
 * EmpleadosUniformeController implements the CRUD actions for SysRrhhEmpleadosUniformes model.
 */
class EmpleadosUniformeController extends Controller
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
     * Lists all SysRrhhEmpleadosUniformes models.
     * @return mixed
     */
    public function actionIndex()
    {
        $this->layout = '@app/views/layouts/main_emplados';
        
        $searchModel = new SysRrhhEmpleadosUniformeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination = ['pageSize' => 10];
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SysRrhhEmpleadosUniformes model.
     * @param string $id_sys_rrhh_cedula
     * @param string $id_sys_empresa
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id_sys_rrhh_cedula, $id_sys_empresa)
    {
        $model = $this->findModel($id_sys_rrhh_cedula, $id_sys_empresa);
        
        $db =  $_SESSION['db'];
    
        $fotos = '';

        $fotos =   Yii::$app->$db->createCommand("select foto_64,  foto, baze64 from sys_rrhh_empleados_foto_uniformes cross apply (select foto as '*' for xml path('')) T (baze64) where id_sys_rrhh_cedula = '{$model->id_sys_rrhh_cedula}'")->queryOne();

        return $this->render('view', [
            'model' => $model,
            'fotos'=> $fotos['baze64'],
        ]);
        
        
        
    }

    /**
     * Creates a new SysRrhhEmpleadosUniformes model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $this->layout = '@app/views/layouts/main_emplados';
        
        $fotos          = '';
        
        $db =  $_SESSION['db'];

        $model = new SysRrhhEmpleadosUniformes();
             
        
        if ($model->load(Yii::$app->request->post())) {
            
            try {
                            
                $existente = SysRrhhEmpleadosUniformes::find()->select('id_sys_rrhh_cedula')->where(['id_sys_rrhh_cedula'=> $model->id_sys_rrhh_cedula])->andWhere(['estado' => 1])->one();
                $ids = ArrayHelper::getValue($existente, 'id_sys_rrhh_cedula');

                $numUniforme = SysRrhhEmpleadosUniformes::find()->select('numero_uniforme')->where(['numero_uniforme'=> $model->numero_uniforme])->andWhere(['estado'=>1])->one();
                $uniforme = ArrayHelper::getValue($numUniforme, 'numero_uniforme');
        
                if( $ids != $model->id_sys_rrhh_cedula){
                    
                    if(!$uniforme){

                        $codigo = SysRrhhEmpleadosUniformes::find()->select(['max(CAST(id_sys_rrhh_empleado_uniforme AS INT))'])->Where(['id_sys_empresa'=> '001'])->scalar();
                    
                        $model->id_sys_rrhh_empleado_uniforme = $codigo + 1;
                        $model->id_sys_empresa   = '001';
                        $model->fecha_registro   = date('Ymd H:i:s');
                        $model->estado = 1;

                        Yii::$app->$db->createCommand("update sys_rrhh_empleados set numero_uniforme='$model->numero_uniforme' where id_sys_rrhh_cedula='$model->id_sys_rrhh_cedula' and id_sys_empresa='001' ")->execute();
                    
                        $model->file = UploadedFile::getInstance($model,'file');

                        if($model->file){

                            $modelfoto =  SysRrhhEmpleadosFotoUniformes::find()->where(['id_sys_empresa'=> $model->id_sys_empresa, 'id_sys_rrhh_cedula'=> $model->id_sys_rrhh_cedula])->one();
                                    
                                    
                            if ($modelfoto){
                                
                                $model->file->saveAs("C:/fotos/uniformes/".$model->id_sys_rrhh_cedula.'.'.$model->file->extension);
                                
                                $ruta =  "C:\'fotos\'uniformes\'".$model->id_sys_rrhh_cedula.'.'.$model->file->extension;
                                
                                $ruta = str_replace("'", "", $ruta);
                                
                                
                                Yii::$app->$db->createCommand("update sys_rrhh_empleados_foto_uniformes
                                        set foto = (
                                        SELECT *
                                        FROM OPENROWSET(BULK '".$ruta."', SINGLE_BLOB) test)
                                        where id_sys_rrhh_cedula = '{$model->id_sys_rrhh_cedula}'")->execute();
                                
                                
                                
                            }else{
                                
                                $model->file->saveAs("C:/fotos/uniformes/".$model->id_sys_rrhh_cedula.'.'.$model->file->extension);
                                
                                $ruta =  "C:\'fotos\'uniformes\'".$model->id_sys_rrhh_cedula.'.'.$model->file->extension;
                                
                                $ruta = str_replace("'", "", $ruta);
                                
                                Yii::$app->$db->createCommand("Insert sys_rrhh_empleados_foto_uniformes (id_sys_rrhh_cedula, foto, id_sys_empresa)
                                                            Select '{$model->id_sys_rrhh_cedula}', BulkColumn, '001'
                                                            from Openrowset (Bulk '".$ruta."', Single_Blob) as Image")->execute();
                                
                            }
                        }

                        if($model->save(false)){
                        
                            Yii::$app->getSession()->setFlash('info', [
                            'type' => 'success','duration' => 1500,
                            'icon' => 'glyphicons glyphicons-robot','message' => 'Los datos han sido registrados con éxito!',
                            'positonY' => 'top','positonX' => 'right']);
                            return $this->redirect('index');
                        }   
                        else{
                        
                            Yii::$app->getSession()->setFlash('info', [
                            'type' => 'danger','duration' => 1500,
                            'icon' => 'glyphicons glyphicons-robot','message' => 'Ha ocurrido un error. Comuniquese con su administrador! ',
                            'positonY' => 'top','positonX' => 'right']);
                            return $this->redirect('index');
                        }
                    }else{
                        Yii::$app->getSession()->setFlash('info', [
                            'type' => 'danger','duration' => 1500,
                            'icon' => 'glyphicons glyphicons-robot','message' => 'El número de uniforme ya ha sido registrado!',
                            'positonY' => 'top','positonX' => 'right']);
                            
                        return $this->redirect('index'); 
                    }
                }else{

                    Yii::$app->getSession()->setFlash('info', [
                        'type' => 'danger','duration' => 1500,
                        'icon' => 'glyphicons glyphicons-robot','message' => 'El empleado ya tiene uniforme registrado!',
                        'positonY' => 'top','positonX' => 'right']);
                        
                    return $this->redirect('index');    
            }
                   
                    
     
                
            }catch (Exception $e) {
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
            'fotos' => $fotos,
        ]);
                       
    
    }

    /**
     * Updates an existing SysRrhhEmpleadosUniformes model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id_sys_rrhh_cedula
     * @param string $id_sys_empresa
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id_sys_rrhh_cedula, $id_sys_empresa)
    {
        
        $this->layout = '@app/views/layouts/main_emplados';
        
        $model = $this->findModel($id_sys_rrhh_cedula, $id_sys_empresa);
        
        $db =  $_SESSION['db'];
    
        $fotos = '';

        $fotos =   Yii::$app->$db->createCommand("select foto_64,  foto, baze64 from sys_rrhh_empleados_foto_uniformes cross apply (select foto as '*' for xml path('')) T (baze64) where id_sys_rrhh_cedula = '{$model->id_sys_rrhh_cedula}'")->queryOne();
           
        SysRrhhEmpleadosFotoUniformes::find()->where(['id_sys_rrhh_cedula'=> $id_sys_rrhh_cedula, 'id_sys_empresa'=> $id_sys_empresa])->one();
        
        if ($model->load(Yii::$app->request->post())) {
            
            $transaction = \Yii::$app->$db->beginTransaction();
            
            try {
               
                $model->file             =  UploadedFile::getInstance($model, 'file');
                    
                $model->fecha_entrega =   $model->fecha_entrega;
                
                $model->numero_uniforme = $model->numero_uniforme;

                Yii::$app->$db->createCommand("update sys_rrhh_empleados set numero_uniforme='$model->numero_uniforme' where id_sys_rrhh_cedula='$model->id_sys_rrhh_cedula' and id_sys_empresa='001' ")->execute();

                if ($model->file){
                
                        $modelfoto =  SysRrhhEmpleadosFotoUniformes::find()->where(['id_sys_empresa'=> $model->id_sys_empresa, 'id_sys_rrhh_cedula'=> $model->id_sys_rrhh_cedula])->one();
                       
                        if ($modelfoto){
                            
                             $model->file->saveAs("C:/fotos/uniformes/".$model->id_sys_rrhh_cedula.'.'.$model->file->extension);
                        
                             $ruta =  "C:\'fotos\'uniformes\'".$model->id_sys_rrhh_cedula.'.'.$model->file->extension;
                        
                             $ruta = str_replace("'", "", $ruta);
                            
                         
                             Yii::$app->$db->createCommand("update sys_rrhh_empleados_foto_uniformes
                                    set foto = (
                                    SELECT *
                                    FROM OPENROWSET(BULK '".$ruta."', SINGLE_BLOB) test)
                                    where id_sys_rrhh_cedula = '{$model->id_sys_rrhh_cedula}'")->execute();
                             
                            
              
                        }else{
                            
                                $model->file->saveAs("C:/fotos/uniformes/".$model->id_sys_rrhh_cedula.'.'.$model->file->extension);
                                
                                $ruta =  "C:\'fotos\'uniformes\'".$model->id_sys_rrhh_cedula.'.'.$model->file->extension;
                                
                                $ruta = str_replace("'", "", $ruta);
                            
                              
                                 Yii::$app->$db->createCommand("Insert sys_rrhh_empleados_foto_uniformes (id_sys_rrhh_cedula, foto, id_sys_empresa)
                                                        Select '{$model->id_sys_rrhh_cedula}', BulkColumn, '001'
                                                         from Openrowset (Bulk '".$ruta."', Single_Blob) as Image")->execute();
                                 
                          }
                    }
                 
                
                  
                if ($flag = $model->save(false)) {
                       
                    if ($flag) {
                        $transaction->commit();
                        Yii::$app->getSession()->setFlash('info', [
                            'type' => 'success','duration' => 1500,
                            'icon' => 'glyphicons glyphicons-robot','message' => 'Los datos han sido actualizados con éxito! ',
                            'positonY' => 'top','positonX' => 'right']);
                        return $this->redirect(['index']);
                    }
                }
                
            }catch (Exception $e) {
          
                $transaction->rollBack();
               return   $e->getMessage();
                Yii::$app->getSession()->setFlash('info', [
                    'type' => 'danger','duration' => 1500,
                    'icon' => 'glyphicons glyphicons-robot','message' => 'Ha ocurrido un error. Comuniquese con su administrador! ',
                    'positonY' => 'top','positonX' => 'right']);
                return $this->redirect(['index']);
            }
         
        }else{
      
     
        return $this->render('update', [
            'model' => $model,
            'fotos'=> $fotos['baze64'],
            'update'=>1,
        ]);
        }
        
    }
    
   
    public function actionDelete($id_sys_rrhh_cedula, $id_sys_empresa)
    {
        //$this->findModel($id_sys_rrhh_cedula, $id_sys_empresa)->delete();
        $model = $this->findModel($id_sys_rrhh_cedula, $id_sys_empresa);

        $model->estado = 0;

        $model->save(false);

        return $this->redirect(['index']);
    }

    public function actionUniformesxls()
    {
    
        $datos = [];
         
        $datos = $this->getUniformesEmpleados();
         
        return $this->render('uniformexls', ['datos'=> $datos]);
        
    }

    private function getUniformesEmpleados (){
        
        $db =  $_SESSION['db'];
         
        return  Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerDatosUniformesEmpleados]")->queryAll();  
        
    }
    
    protected function findModel($id_sys_rrhh_cedula, $id_sys_empresa)
    {
        if (($model = SysRrhhEmpleadosUniformes::findOne(['id_sys_rrhh_cedula' => $id_sys_rrhh_cedula, 'id_sys_empresa' => $id_sys_empresa])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}

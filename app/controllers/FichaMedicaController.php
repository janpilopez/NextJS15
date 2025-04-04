<?php

namespace app\controllers;

use Yii;
use app\models\SysMedFichaMedica;
use app\models\search\SysMedFichaMedicaSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\SysRrhhEmpleados;

/**
 * FichaMedicaController implements the CRUD actions for SysMedFichaMedica model.
 */
class FichaMedicaController extends Controller
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
     * Lists all SysMedFichaMedica models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SysMedFichaMedicaSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SysMedFichaMedica model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        
        $db =  $_SESSION['db'];
        
        $model =  $this->findModel($id);
        $empleado = SysRrhhEmpleados::find()->where(['id_sys_rrhh_cedula'=> $model->id_sys_rrhh_cedula])->one();
        $fotos =    Yii::$app->$db->createCommand("select foto_64,  foto, baze64 from sys_rrhh_empleados_foto cross apply (select foto as '*' for xml path('')) T (baze64) where id_sys_rrhh_cedula = '{$model->id_sys_rrhh_cedula}'")->queryOne();
        
        
        return $this->render('view', [
            'model' => $model,
            'empleado'=> $empleado,
            'fotos' => $fotos
        ]);
    }

    /**
     * Creates a new SysMedFichaMedica model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($id_sys_rrhh_cedula)
    {
        
        $fichaMedica = SysMedFichaMedica::find()->where(['id_sys_rrhh_cedula'=> $id_sys_rrhh_cedula])->one();
        
        
        $contrato =  (new \yii\db\Query())
        ->select(["fecha_ingreso", "(cast(datediff(dd,fecha_ingreso,GETDATE()) / 365.25 as int)) as anios"])
        ->from("sys_rrhh_empleados_contratos")
        ->Where("id_sys_rrhh_cedula='{$id_sys_rrhh_cedula}'")
        ->andWhere("activo = 1")
        ->all(SysRrhhEmpleados::getDb());
        
        
        $nucleofamiliar =  (new \yii\db\Query())
        ->select(["nombres", "parentesco", "(cast(datediff(dd,fecha_nacimiento,GETDATE()) / 365.25 as int)) as anios", "discapacidad"])
        ->from("sys_rrhh_empleados_nucleo_familiar")
        ->Where("id_sys_rrhh_cedula='{$id_sys_rrhh_cedula}'")
        ->all(SysRrhhEmpleados::getDb());
        
        
         if(!$fichaMedica):       
                $db =  $_SESSION['db'];
                
                $model = new SysMedFichaMedica();
                $empleado = SysRrhhEmpleados::find()->where(['id_sys_rrhh_cedula'=> $id_sys_rrhh_cedula])->one();
                $fotos =    Yii::$app->$db->createCommand("select foto_64,  foto, baze64 from sys_rrhh_empleados_foto cross apply (select foto as '*' for xml path('')) T (baze64) where id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'")->queryOne();
                
             
                
                if ($model->load(Yii::$app->request->post())) {
                    
                   $numFicha = SysMedFichaMedica::find()->select(['max(numero)'])->scalar() + 1;
                   $model->id_sys_rrhh_cedula = $id_sys_rrhh_cedula;
                   $model->numero = $numFicha;
                   $model->enf_cardiovasculares = $model->enf_cardiovasculares != null ? strtoupper($model->enf_cardiovasculares) : "S/N";
                   $model->enf_neurologicos = $model->enf_neurologicos != null ? strtoupper($model->enf_neurologicos) : "S/N";
                   $model->enf_metabolicos = $model->enf_metabolicos != null ? strtoupper($model->enf_metabolicos) : "S/N";
                   $model->enf_oftalmologicos = $model->enf_oftalmologicos != null ? strtoupper($model->enf_oftalmologicos) : "S/N";
                   $model->enf_auditivas = $model->enf_auditivas != null ? strtoupper($model->enf_auditivas) : "S/N";
                   $model->infecciones_contagiosas = $model->infecciones_contagiosas != null ? strtoupper($model->infecciones_contagiosas) : "S/N";
                   $model->enf_veneras = $model->enf_veneras != null ? strtoupper($model->enf_veneras) : "S/N";
                   $model->traumatismos = $model->traumatismos != null ? strtoupper($model->traumatismos) : "S/N";
                   $model->convulsiones = $model->convulsiones != null ? strtoupper($model->convulsiones) : "S/N";
                   $model->alergias = $model->alergias != null ? strtoupper($model->alergias) : "S/N";
                   $model->cirugias = $model->cirugias != null ? strtoupper($model->cirugias) : "S/N";
                   $model->otras_patologias = $model->otras_patologias != null ? strtoupper($model->otras_patologias) : "S/N";
                   $model->papanicolau = $model->papanicolau != null ? strtoupper($model->papanicolau) : "S/N";
                   $model->mamas = $model->mamas != null ? strtoupper($model->mamas) : "S/N";
                   $model->ant_familiar_padres = $model->ant_familiar_padres != null ? strtoupper($model->ant_familiar_padres) : "S/N";
                   $model->ant_familiar_madre = $model->ant_familiar_madre != null ? strtoupper($model->ant_familiar_madre) : "S/N";
                   $model->ant_familiar_otros = $model->ant_familiar_otros != null ? strtoupper($model->ant_familiar_otros) : "S/N";
                   $model->exa_craneo = $model->exa_craneo != null ? strtoupper($model->exa_craneo) : "S/N";
                   $model->exa_ojos = $model->exa_ojos != null ? strtoupper($model->exa_ojos) : "S/N";
                   $model->exa_cabidad_oral = $model->exa_cabidad_oral != null ? strtoupper($model->exa_cabidad_oral) : "S/N";
                   $model->exa_toraz_csps = $model->exa_toraz_csps != null ? strtoupper($model->exa_toraz_csps) : "S/N";
                   $model->exa_toraz_r1c1 = $model->exa_toraz_r1c1 != null ? strtoupper($model->exa_toraz_r1c1) : "S/N";
                   $model->exa_abdomen = $model->exa_abdomen != null ? strtoupper($model->exa_abdomen) : "S/N";
                   $model->exa_cuello = $model->exa_cuello != null ? strtoupper($model->exa_cuello) : "S/N";
                   $model->exa_genital = $model->exa_genital != null ? strtoupper($model->exa_genital) : "S/N";
                   $model->exa_extremidades = $model->exa_extremidades != null ? strtoupper($model->exa_extremidades) : "S/N";
                   $model->exames_laboratorio = $model->exames_laboratorio != null ? strtoupper($model->exames_laboratorio) : "S/N";
                   $model->recomendacion = $model->recomendacion != null ? strtoupper($model->recomendacion) : "S/N";
                   $model->partos = $model->partos != null ? $model->partos : 0;
                   $model->cesarea = $model->cesarea != null ? $model->cesarea : 0;
                   $model->abortos = $model->abortos != null ? $model->abortos : 0;
                   
                   
                   if($model->save(false)){
                       
                       Yii::$app->getSession()->setFlash('info', [
                           'type' => 'success','duration' => 1500,
                           'icon' => 'glyphicons glyphicons-robot','message' => 'Los datos han sido registrados  con éxito!',
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
        
                return $this->render('create', [
                    'model' => $model,
                    'empleado' => $empleado,
                    'fotos'=> $fotos, 
                    'contrato'=> $contrato,
                    'nucleofamiliar' => $nucleofamiliar
                ]);
        else:
           return Yii::$app->response->redirect(['ficha-medica/view', 'id' => $fichaMedica->id]);
        endif;
        
    }

    /**
     * Updates an existing SysMedFichaMedica model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        
        $db =  $_SESSION['db'];
        
        $model = $this->findModel($id);
        $empleado = SysRrhhEmpleados::find()->where(['id_sys_rrhh_cedula'=> $model->id_sys_rrhh_cedula])->one();
        $fotos =    Yii::$app->$db->createCommand("select foto_64,  foto, baze64 from sys_rrhh_empleados_foto cross apply (select foto as '*' for xml path('')) T (baze64) where id_sys_rrhh_cedula = '{$model->id_sys_rrhh_cedula}'")->queryOne();
        $contrato =  (new \yii\db\Query())
        ->select(["fecha_ingreso", "(cast(datediff(dd,fecha_ingreso,GETDATE()) / 365.25 as int)) as anios"])
        ->from("sys_rrhh_empleados_contratos")
        ->Where("id_sys_rrhh_cedula='{$model->id_sys_rrhh_cedula}'")
        ->andWhere("activo = 1")
        ->all(SysRrhhEmpleados::getDb());
        
        $nucleofamiliar =  (new \yii\db\Query())
        ->select(["nombres", "parentesco", "(cast(datediff(dd,fecha_nacimiento,GETDATE()) / 365.25 as int)) as anios", "discapacidad"])
        ->from("sys_rrhh_empleados_nucleo_familiar")
        ->Where("id_sys_rrhh_cedula='{$model->id_sys_rrhh_cedula}'")
        ->all(SysRrhhEmpleados::getDb());

        if ($model->load(Yii::$app->request->post())) {
           
            $model->enf_cardiovasculares = $model->enf_cardiovasculares != null ? strtoupper($model->enf_cardiovasculares) : "S/N";
            $model->enf_neurologicos = $model->enf_neurologicos != null ? strtoupper($model->enf_neurologicos) : "S/N";
            $model->enf_metabolicos = $model->enf_metabolicos != null ? strtoupper($model->enf_metabolicos) : "S/N";
            $model->enf_oftalmologicos = $model->enf_oftalmologicos != null ? strtoupper($model->enf_oftalmologicos) : "S/N";
            $model->enf_auditivas = $model->enf_auditivas != null ? strtoupper($model->enf_auditivas) : "S/N";
            $model->infecciones_contagiosas = $model->infecciones_contagiosas != null ? strtoupper($model->infecciones_contagiosas) : "S/N";
            $model->enf_veneras = $model->enf_veneras != null ? strtoupper($model->enf_veneras) : "S/N";
            $model->traumatismos = $model->traumatismos != null ? strtoupper($model->traumatismos) : "S/N";
            $model->convulsiones = $model->convulsiones != null ? strtoupper($model->convulsiones) : "S/N";
            $model->alergias = $model->alergias != null ? strtoupper($model->alergias) : "S/N";
            $model->cirugias = $model->cirugias != null ? strtoupper($model->cirugias) : "S/N";
            $model->otras_patologias = $model->otras_patologias != null ? strtoupper($model->otras_patologias) : "S/N";
            $model->papanicolau = $model->papanicolau != null ? strtoupper($model->papanicolau) : "S/N";
            $model->mamas = $model->mamas != null ? strtoupper($model->mamas) : "S/N";
            $model->ant_familiar_padres = $model->ant_familiar_padres != null ? strtoupper($model->ant_familiar_padres) : "S/N";
            $model->ant_familiar_madre = $model->ant_familiar_madre != null ? strtoupper($model->ant_familiar_madre) : "S/N";
            $model->ant_familiar_otros = $model->ant_familiar_otros != null ? strtoupper($model->ant_familiar_otros) : "S/N";
            $model->exa_craneo = $model->exa_craneo != null ? strtoupper($model->exa_craneo) : "S/N";
            $model->exa_ojos = $model->exa_ojos != null ? strtoupper($model->exa_ojos) : "S/N";
            $model->exa_cabidad_oral = $model->exa_cabidad_oral != null ? strtoupper($model->exa_cabidad_oral) : "S/N";
            $model->exa_toraz_csps = $model->exa_toraz_csps != null ? strtoupper($model->exa_toraz_csps) : "S/N";
            $model->exa_toraz_r1c1 = $model->exa_toraz_r1c1 != null ? strtoupper($model->exa_toraz_r1c1) : "S/N";
            $model->exa_abdomen = $model->exa_abdomen != null ? strtoupper($model->exa_abdomen) : "S/N";
            $model->exa_cuello = $model->exa_cuello != null ? strtoupper($model->exa_cuello) : "S/N";
            $model->exa_genital = $model->exa_genital != null ? strtoupper($model->exa_genital) : "S/N";
            $model->exa_extremidades = $model->exa_extremidades != null ? strtoupper($model->exa_extremidades) : "S/N";
            $model->exames_laboratorio = $model->exames_laboratorio != null ? strtoupper($model->exames_laboratorio) : "S/N";
            $model->recomendacion = $model->recomendacion != null ? strtoupper($model->recomendacion) : "S/N";
            $model->partos = $model->partos != null ? $model->partos : 0;
            $model->cesarea = $model->cesarea != null ? $model->cesarea : 0;
            $model->abortos = $model->abortos != null ? $model->abortos : 0;
            $model->talla = 0;
            
            
            if($model->save(false)){
                
                Yii::$app->getSession()->setFlash('info', [
                    'type' => 'success','duration' => 1500,
                    'icon' => 'glyphicons glyphicons-robot','message' => 'Los datos han sido actualizado  con éxito!',
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
            'empleado'=> $empleado,
            'fotos'=> $fotos,
            'contrato' => $contrato,
            'nucleofamiliar' => $nucleofamiliar
        ]);
    }

    /**
     * Deletes an existing SysMedFichaMedica model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the SysMedFichaMedica model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return SysMedFichaMedica the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SysMedFichaMedica::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}

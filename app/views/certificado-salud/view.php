<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\SysRrhhEmpleados;

/* @var $this yii\web\View */
/* @var $model app\models\SysMedCertificadoSalud */

$this->title = "Certificado de Salud";
$this->params['breadcrumbs'][] = ['label' => 'Certificado Salud', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="sys-med-certificado-salud-view">

    <h1><?= Html::encode($this->title) ?></h1>


    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            //'id',
            'id_sys_rrhh_cedula',
            [
                'label'=>'Nombres',
                'attribute'=>'nombres',
                'value'=> function($model){
                 
                    $empleado = SysRrhhEmpleados::find()->where(['id_sys_rrhh_cedula'=> $model->id_sys_rrhh_cedula])->one();   
                    
                    return $empleado->nombres;
                    
                    } ,
             ],
            'fecha_emision',
            'fecha_vencimiento',
            [
                'label'=>'Entidad Emisora',
                'attribute'=>'entidad_emisora',
                'value'=> function($model){
                
                    $entidad = 'OTROS';
                    
                    if($model->entidad_emisora == 'I'):
                    
                         $entidad = 'IESS';
                    
                    elseif($model->entidad_emisora == 'M'):
                     
                         $entidad = 'MPS';
                    
                    elseif($model->entidad_emisora == 'P'):
                    
                        $entidad = 'PARTICULAR';    
                    
                    endif;
                
                
                    return $entidad;
                    
                } ,
            ],
            //'usuario_creacion',
            //'fecha_creacion',
            //'usuario_actualizacion',
            //'fecha_actualizacion',
            //'anulado',
        ],
    ]) ?>

</div>

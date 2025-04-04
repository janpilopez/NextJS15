<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */
use app\models\SysAdmAreas;
use app\models\SysAdmDepartamentos;;
use app\models\SysAdmPeriodoVacaciones;
$periodo =  SysAdmPeriodoVacaciones::find()->where(['id_sys_empresa'=> '001'])->andWhere(['id_sys_adm_periodo_vacaciones'=> $periodo])->one();

$mjs = '';

if($periodo):
   
 $mjs = $periodo->periodo;

else:

  $mjs = 'Todos los periodos';

endif

?>
<style>
 .col-xs-1, .col-sm-1, .col-md-1, .col-lg-1, .col-xs-2, .col-sm-2, .col-md-2, .col-lg-2, .col-xs-3, .col-sm-3, .col-md-3, .col-lg-3, .col-xs-4, .col-sm-4, .col-md-4, .col-lg-4, .col-xs-5, .col-sm-5, .col-md-5, .col-lg-5, .col-xs-6, .col-sm-6, .col-md-6, .col-lg-6, .col-xs-7, .col-sm-7, .col-md-7, .col-lg-7, .col-xs-8, .col-sm-8, .col-md-8, .col-lg-8, .col-xs-9, .col-sm-9, .col-md-9, .col-lg-9, .col-xs-10, .col-sm-10, .col-md-10, .col-lg-10, .col-xs-11, .col-sm-11, .col-md-11, .col-lg-11, .col-xs-12, .col-sm-12, .col-md-12, .col-lg-12 {
	border:0;
	padding:0;
	margin-left:-0.00001;
}
  th, td
    {  
    padding:5px;
    }

 </style>

   <div class= "row">
       <?= $this->render('_tableperiodovacaciones', ['datos'=> $datos]);?>
  </div>
 







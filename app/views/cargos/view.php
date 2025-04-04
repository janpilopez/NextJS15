<?php

use app\models\SysAdmDepartamentos;
use app\models\sysAdmMandos;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\SysAdmCargos */

$this->title = 'Cargos';
$this->params['breadcrumbs'][] = ['label' => 'Cargos', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Ver';
\yii\web\YiiAsset::register($this);
?>
<div class="sys-adm-cargos-view">

    <h1><?= Html::encode($this->title) ?></h1>

   <div class = 'panel panel-default'>
   <div class = 'panel-body'>
   
    <?php $form = ActiveForm::begin(); ?>
    
    <div class= 'row'>
        <div class= 'col-md-5'>
           <?= $form->field($model, 'cargo')->textInput(['maxlength' => true, 'readonly'=> TRUE]) ?>
        </div>    
        <div class= 'col-md-2'>
           <?= $form->field($model, 'reg_horas_extras')->dropDownList(['N'=> 'No', 'S'=> 'Si'],['disabled'=> TRUE]) ?>
        </div>
        <div class= 'col-md-2'>
           <?= $form->field($model, 'reg_ent_salida')->dropDownList(['N'=> 'No', 'S'=> 'Si'], ['disabled'=> TRUE]) ?>
        </div>
        <div class= 'col-md-2'>
           <?= $form->field($model, 'estado')->dropDownList(['A'=> 'Activo', 'I'=> 'Inactivo'], ['disabled'=> TRUE]) ?>
        </div>
    </div>
    <div class= 'row'>
       <div class= 'col-md-6'>
          <?= $form->field($model, 'id_sys_adm_departamento')->dropDownList(ArrayHelper::map(SysAdmDepartamentos::find()->all(), 'id_sys_adm_departamento', 'departamento'), ['prompt'=> 'seleccione..', 'disabled'=> TRUE]) ?>
       </div> 
       <div class= 'col-md-6'>
          <?= $form->field($model, 'id_sys_adm_mando')->dropDownList(ArrayHelper::map(sysAdmMandos::find()->all(),'id_sys_adm_mando', 'mando'), ['prompt'=> 'seleccione..', 'disabled'=> TRUE]) ?>
       </div>
    </div>
      <div class= 'row'>
        <div class= 'col-md-12'>
          <div class = 'panel panel-default'>  
             <div class = 'panel-body'>
                <ul class="nav nav-tabs">
                  <li class="active"><a data-toggle="tab" href="#home">Entrevistas</a></li>
                  <li><a data-toggle="tab" href="#menu1">Funciones</a></li>
                  <li><a data-toggle="tab" href="#menu2">Perfiles</a></li>
                  <li><a data-toggle="tab" href="#menu3">Tasa Salarial</a></li>
                </ul>
                <div class="tab-content">
                    <div id="home" class="tab-pane fade in active">
                    
                    </div>
                    <div id="menu1" class="tab-pane fade">
                       <p>FF</p>
                    </div>
                     <div id="menu2" class="tab-pane fade">
                     
                    </div>
                     <div id="menu3" class="tab-pane fade">
                     
                    </div>
                </div>
             </div>
          </div>
        </div>
     </div>
    <?php ActiveForm::end(); ?>
    </div>
  </div>

</div>

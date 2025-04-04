<?php

use app\models\SysAdmDepartamentos;
use app\models\SysCuerpoIndicador;
use app\models\SysDetalleIndicador;
use app\models\SysEncabezadoIndicador;
use app\models\SysIndicadores;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use yii\web\View;
use app\assets\FormIndicadorSistemasAsset;
FormIndicadorSistemasAsset::register($this);

$urlconsultas = Yii::$app->urlManager->createUrl(['form-indicador-sistemas']);
$consultas = Yii::$app->urlManager->createUrl(['consultas']);
$inlineScript = "urlconsultas = '$urlconsultas', consultas = '$consultas';";
$this->registerJs($inlineScript, View::POS_HEAD);

/* @var $this yii\web\View */
/* @var $model app\models\SysAdmAreas */

$this->title = 'Indicadores Sistemas';
$this->params['breadcrumbs'][] = ['label' => 'Indicadores Sistema', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Ver';
\yii\web\YiiAsset::register($this);
?>
<div class="sys-encabezado-indicador-view">

    <h1><?= Html::encode($this->title) ?></h1>
    
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
          [
            'attribute'=>'id_sys_adm_departamento',
            'value'=> function($model){
              
              $departamento = SysAdmDepartamentos::find()->where(['id_sys_adm_departamento'=> $model->id_sys_adm_departamento])->one();
              if($departamento):
                return $departamento->departamento;
              else: 
                return  "S/N";
              endif;
                
            }
          ],
          [
            'attribute'=>'tipo_indicador',
            'value'=> function($model){
              
              $tipo_indicador = SysIndicadores::find()->where(['id_indicador'=> $model->tipo_indicador])->one();
              if($tipo_indicador):
                return $tipo_indicador->nombre_indicador;
              else: 
                return  "S/N";
              endif;
                
            }
          ],
          'meta',
          [
            'attribute'=>'frecuencia',
            'value'=> function($model){
            
              if($model->frecuencia == 'M'):
                return 'MENSUAL';
              elseif($model->frecuencia == 'T'):
                return 'TRIMESTRAL';
              elseif($model->frecuencia == 'S'):
                return 'SEMESTRAL';
              elseif($model->frecuencia == 'A'):
                return 'ANUAL';
              else: 
                return  "S/N";
              endif;
                
            }
          ],
          'efecto_medir',
          'anio'
        ],
      ])
    ?>

  <?php 
  
  $modelcuerpo = SysCuerpoIndicador::find()->where(['id_encabezado_indicador'=>$model->id_encabezado_indicador])->one();
  ?>

  <?php $form = ActiveForm::begin(); ?>
    <div class= 'row'>
      <div class= 'col-md-2'>
        <?php echo '<label>Lugar Impresora</label>';
                   echo   Html::DropDownList('area', 'area', 
                       ArrayHelper::map(SysAdmDepartamentos::find()->orderBy(['departamento' => 'asc'])->all(), 'id_sys_adm_departamento', 'departamento'), ['class'=>'form-control input-sm', 'id'=>'imp_departamento', 'prompt' => 'Todos'])
        ?>
      </div>
      <div class= 'col-md-3'>
        <?php echo '<label>Fechas</label>';
              echo   Html::DropDownList('area', 'area', 
              ArrayHelper::map(SysCuerpoIndicador::find()->select('fecha')->all(), 'fecha', 'fecha'), ['class'=>'form-control input-sm', 'id'=>'fecha', 'prompt' => 'Todos'])
        ?>
           </div>
    </div>
    <?php ActiveForm::end(); ?>
    <br>
    
    <div id="divTabla">
        <table id="tableusuarios" class="table">
            <thead>
                <tr>
                    <th>Usuario</th>
                    <th>Cantidad B/N</th>
                    <th>Cantidad Color</th>
                    <th>Resmas Solicitadas</th>
                </tr>
            </thead>
            <tbody id="tbody">
    
            </tbody>
        </table>
    </div>
    	
    </div>
  </div>

</div>

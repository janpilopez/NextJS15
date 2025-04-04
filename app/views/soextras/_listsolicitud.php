<?php 
use Mpdf\Tag\Time;
use unclead\multipleinput\MultipleInput;
use unclead\multipleinput\MultipleInputColumn;
use kartik\datetime\DateTimePicker;
use kartik\time\TimePicker;
use unclead\multipleinput\TabularColumn;
use unclead\multipleinput\TabularInput;
use yii\bootstrap\Modal;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\date\DatePicker;
use yii\widgets\ActiveForm;
use app\models\SysAdmAreas;
use kartik\depdrop\DepDrop;
use kartik\typeahead\Typeahead;
use app\models\SysAdmUsuariosDep;
use app\models\SysRrhhEmpleados;
use app\models\SysRrhhSoextrasEmpleados;
use app\models\SysRrhhSoextras;
use app\assets\SssAsset;
use app\models\SysAdmCargos;
use app\models\SysAdmDepartamentos;
use yii\web\View;
use yii\web\JsExpression;

echo $this->render('funciones');
SssAsset::register($this);
$url = Yii::$app->urlManager->createUrl(['soextras']);

$cont = 0;

if($update != 0):

$cont =  SysRrhhSoextrasEmpleados::find()->where(['id_sys_rrhh_soextras'=> $model->id_sys_rrhh_soextras, 'id_sys_empresa'=> $model->id_sys_empresa])->count();

$iddetalle =
[
    'name' => 'id_sys_rrhh_soextras_empleados',
    'type' => TabularColumn::TYPE_HIDDEN_INPUT
];
else:

$iddetalle = [
    'name' => 'nombres',
    'type' => TabularColumn::TYPE_HIDDEN_INPUT
];
endif;


$userdeparta = SysAdmUsuariosDep::find()->where(['id_usuario'=> Yii::$app->user->id])->one();
$areas = [];

if(trim($userdeparta->area) != ''):

    $areas =  SysAdmUsuariosDep::find()->select('area')->where(['id_usuario'=> Yii::$app->user->id])->asArray()->column();

endif;

$solicitud = SysRrhhSoextras::find()->where(['id_sys_rrhh_soextras'=> $model->id_sys_rrhh_soextras])->one();

$area = SysAdmAreas::find()->where(['id_sys_adm_area'=>$solicitud->id_sys_adm_area])->one();
//$departamento = SysAdmDepartamentos::find()->where(['id_sys_adm_departamento'=>$solicitud->id_sys_adm_departamento])->one();

$horas =  SysRrhhSoextrasEmpleados::find()->where(['id_sys_rrhh_soextras'=> $model->id_sys_rrhh_soextras, 'id_sys_empresa'=> $model->id_sys_empresa])->all();

class FilterColumn {
    private $colName;
    
    function __construct($colName) {
        $this->colName = $colName;
    }
    
    function getValues($i) {
        
        return $i[$this->colName] ;
    }
  }
  class FilterData {
    private $colName;
    private $value;
    
    function __construct($colName, $value) {
        $this->colName = $colName;
        $this->value = $value;
    }
    function getFilter($i) {
        return $i[$this->colName] == $this->value;
    }
  }


?>

<div class="sys-rrhh-soextras-form">
 <?php $form = ActiveForm::begin(['id'=>'solicitud']); ?>
 <div class = 'row'>
    <table id ="table-comentario" class="table table-bordered table-condensed" style="background-color: white; font-size: 14px; width: 96%">
    <thead>
      <tr>
        <th>Comentario:</th>
        <th><?= $model->comentario ?></th>
      </tr>
    </thead>
    </table>
    <table id ="table" class="table table-bordered table-condensed" style="background-color: white; font-size: 14px; width: 96%">
    <thead>
      <tr>
        <th>Área</th>
        <th>Departamento</th>
        <th>H50</th>
        <th>H100</th>
        <th>$(50)</th>
        <th>$(100)</th>
      </tr>
    </thead>
    <tbody>
    <?php 
        
        $dataFilterIdSysAdmArea =  array_unique(array_map(array(new FilterColumn("departamento"), 'getValues'), $ingreso));  
        $con = 0;
        foreach ($dataFilterIdSysAdmArea as $index => $departamento):  
            $con+=1;
            $area = '';
            $arrayData   = array_filter($ingreso, array(new FilterData("departamento", $departamento), 'getFilter'));
            $totalh50 = 0;    
            $totalh100 = 0;
            $total50 = 0;
            $total100 = 0;
            $totalp50 = 0;
            $totalp100 = 0;
            $totaltp50 = 0;
            $totaltp100 = 0;

            foreach ($arrayData as $index => $row):
                      
              $area = $row['area'];
              $deparamento = $row['departamento'];  
            
            endforeach;
          
            foreach ($horas as $index  => $data):

                $empleado = SysRrhhEmpleados::find()->where(['id_sys_rrhh_cedula'=>$data['id_sys_rrhh_cedula']])->one();
                $cargo = SysAdmCargos::find()->where(['id_sys_adm_cargo'=>$empleado->id_sys_adm_cargo])->one();
                $departamento = SysAdmDepartamentos::find()->where(['id_sys_adm_departamento'=>$cargo->id_sys_adm_departamento])->one();
                $areaa = SysAdmAreas::find()->where(['id_sys_adm_area'=>$departamento->id_sys_adm_area])->one();
            
                if($deparamento == $departamento->departamento):
                    $totalh50  += $data['horas50'];
                    $totalh100 += $data['horas100'];
                    $totalp50  += $data['pago50'];
                    $totalp100 += $data['pago100'];
                endif;

                $total50 += $data['horas50'];
                $total100 += $data['horas100'];
                $totaltp50 += $data['pago50'];
                $totaltp100 += $data['pago100'];

            endforeach;
            
            if(DecimaltoHoras(number_format($totalh50, 2, '.', '')) != '00:00:00' && DecimaltoHoras(number_format($totalh100, 2, '.', '')) != '00:00:00' ):
        ?>
            <tr>
                <th><?= $area ?></th>
                <th><?= $deparamento ?></th>
                <th><?= DecimaltoHoras(number_format($totalh50, 2, '.', ''))?></th>
                <th><?= DecimaltoHoras(number_format($totalh100, 2, '.', ''))?></th>
                <th><?= number_format($totalp50, 2, '.', '') ?></th>
                <th><?= number_format($totalp100, 2, '.', '') ?></th>
            </tr>
        
        <?php
            elseif(DecimaltoHoras(number_format($totalh50, 2, '.', '')) != '00:00:00'):
             ?>   
             <tr>
                <th><?= $area ?></th>
                <th><?= $deparamento?></th>
                <th><?= DecimaltoHoras(number_format($totalh50, 2, '.', ''))?></th>
                <th><?= DecimaltoHoras(number_format($totalh100, 2, '.', ''))?></th>
                <th><?= number_format($totalp50, 2, '.', '') ?></th>
                <th><?= number_format($totalp100, 2, '.', '') ?></th>
            </tr>
            <?php 
            elseif(DecimaltoHoras(number_format($totalh100, 2, '.', '')) != '00:00:00'): 
            ?>  
            <tr>
                <th><?= $area ?></th>
                <th><?= $deparamento?></th>
                <th><?= DecimaltoHoras(number_format($totalh50, 2, '.', ''))?></th>
                <th><?= DecimaltoHoras(number_format($totalh100, 2, '.', ''))?></th>
                <th><?= number_format($totalp50, 2, '.', '') ?></th>
                <th><?= number_format($totalp100, 2, '.', '') ?></th>
            </tr>
            <?php 
            endif;
      endforeach;
    ?>
    </tbody>
        <tfoot>
            <tr style="background-color: #ccc">
                <th colspan="2" style="text-align: right;">Total General</th>
                <th><?= DecimaltoHoras(number_format($total50, 2, '.', ''))?></th>
                <th><?= DecimaltoHoras(number_format($total100, 2, '.', ''))?></th>
                <th>$<?= number_format($totaltp50, 2, '.', '') ?></th>
                <th>$<?= number_format($totaltp100, 2, '.', '') ?></th>
            </tr>
        </tfoot>
    </table>
  </div>
 
  <div class= 'row'>
     <div class= 'col-md-12'>
     
     <?= Html::hiddenInput('solicitudd', $cont, ['id'=> 'datossolicitud']); ?>
        <button id= 'abrir-inputs' class= ' btn btn-primary input-sm'>
            <i class = 'glyphicon glyphicon-plus'></i>
        </button>
        <?php 
         $template = '<div style="font-size:10px;"><div class="repo-language">{{nombres}}</div>' .
             '<div class="repo-description"><span class="text-muted" ><small>{{value}}</small></span></div></div>';
            echo  TabularInput::widget([
             
             'models' => $modeldet,
             'id'=> 'modeldet',
             /*'attributeOptions' => [
                 'enableAjaxValidation'      => true,
                 'enableClientValidation'    => false,
                 'validateOnChange'          => false,
                 'validateOnSubmit'          => true,
                 'validateOnBlur'            => false,
             ],*/
        
             'allowEmptyList' => true,
             'addButtonPosition' => MultipleInput::POS_HEADER,
             'addButtonOptions' => [
                 'class' => 'hidden',
                 'label' => '<i class="glyphicon glyphicon-plus"></i>',
                
             ],
             'removeButtonOptions' => [
                 'class' => 'hidden',
                 'label' => '<i class="glyphicon glyphicon-remove"></i>',
                 
               
             ],
                
             'columns'=> [
                 
                 $iddetalle,
                 
                 [
                     'name' => 'id_sys_rrhh_cedula',
                     'title' => $modeldet[0]->getAttributeLabel('id_sys_rrhh_cedula'),
                     'type'  => TabularColumn::TYPE_STATIC,
                     'enableError' => true,
                     'options' => [
                        'class'=>'input-sm'//,'style'=>'display:none'
                     ],
                    'headerOptions'=>[
                        'style'=>'width:20%',
                     ],
                 ],
                 
                 [
                     'name' => 'id_sys_empresa',
                     'title' => 'Nombres',
                     'type' => TabularColumn::TYPE_STATIC,
                     'enableError' => true,
                     'options' => [
                         
                         'class'=> 'input-sm',
                     ],
                 ], 
                 [
                    'name' => 'horas50',
                    'title' => 'H50',
                    'type'  => TabularColumn::TYPE_STATIC,
                    'enableError' => true,
                    'options' => [ 
                        'class'=> 'input-sm',
                    ],
                ],  

                [
                    'name' => 'horas100',
                    'title' => 'H100',
                    'type'  => TabularColumn::TYPE_STATIC,
                    'enableError' => true,
                    'options' => [
                        'class'=> 'input-sm', 
                    ],
                ],
                
                [
                    'name' => 'pago50',
                    'title' => '$(50)',
                    'type'  => TabularColumn::TYPE_STATIC,
                    'enableError' => true,
                    'options' => [
                        'class'=> 'input-sm', 
                    ],
                ],
                
                [
                    'name' => 'pago100',
                    'title' => '$(100)',
                    'type'  => TabularColumn::TYPE_STATIC,
                    'enableError' => true,
                    'options' => [
                        'class'=> 'input-sm', 
                    ],
                ],
                
                

             ]
         ]) 
         ?>
         
     </div>
  </div>
  <br>
  <div class = 'row'>
     <div class = 'col-md-12'>
        <div class="form-group text-center">
            <?php if($model->estado == 'P' && $userdeparta->usuario_tipo == 'G'):?>
                <?= Html::a('Desaprobar permiso', '#', [
                    'id' => 'abrir-ventana',
                    'class' => 'btn btn-danger',
                    'data-toggle' => 'modal',
                    'data-target' => '#modal',
                    'data-url' => Url::to(['createcomentario', 'id'=>$model->id_sys_rrhh_soextras]),
                    'data-pjax' => '0',
                ]); ?>
                <?= Html::a('Aprobar Permiso', ['aprobar',  'id'=>$model->id_sys_rrhh_soextras], ['class' => 'btn btn-success']) ?>
            <?php endif;?>
        </div>
     </div>
  </div>
   <?php ActiveForm::end(); ?>
</div>
<?php
$this->registerJs(
    "$(document).on('click', '#abrir-ventana', (function() {
        $.get(
            $(this).data('url'),
            function (data) {
                $('.modal-body').html(data);
                $('#modal').modal();
            }
        );
    }));"
); ?>
 
<?php
Modal::begin([
    'id' => 'modal',
    'header' => '<h4 class="modal-title">Comentario de Anulación</h4>',
    //'footer' => '<a href="#" class="btn btn-primary" data-dismiss="modal">Cerrar</a>',
]);
 
echo "<div class='well'></div>";
 
Modal::end();
?>


<?php

use app\models\SysAdmAreas;
use app\models\SysAdmCargos;
use app\models\SysAdmDepartamentos;
use app\models\SysAdmUsuariosDep;
use phpDocumentor\Reflection\Types\Null_;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use yii\grid\GridView;
use yii\jui\DatePicker;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\SysRrhhSoextrasSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Solicitud Taxis';
$this->params['breadcrumbs'][] = $this->title;
$this->render('../_alertFLOTADOR'); 

$userdeparta = SysAdmUsuariosDep::find()->where(['id_usuario'=> Yii::$app->user->id])->andwhere(['estado'=> 'A'])->one();
$areas = [];
$departamentos =[];


if($userdeparta):

    if(trim($userdeparta->area) != ''):
        $areas =  SysAdmUsuariosDep::find()->select('area')->where(['id_usuario'=> Yii::$app->user->id])->andwhere(['estado'=> 'A'])->asArray()->column();
    else:
        $areas =  SysAdmAreas::find()->select('id_sys_adm_area')->asArray()->column();
    endif;
    
    if(trim($userdeparta->departamento) != ''):
        $departamentos =  SysAdmUsuariosDep::find()->select('departamento')->where(['id_usuario'=> Yii::$app->user->id])->andwhere(['estado'=> 'A'])->asArray()->column();
    else:
        $departamentos =  SysAdmDepartamentos::find()->select('id_sys_adm_departamento')->asArray()->column();
    endif;

endif;


?>
<div class="sys-rrhh-soextras-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Registrar Solicitud', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'label'=>'Area',
                'attribute'=>'id_sys_adm_area',
                'filter'=>  ArrayHelper::map(SysAdmAreas::find()->where(['estado'=>'A'])->andWhere(['id_sys_adm_area'=> $areas])->all(), 'id_sys_adm_area', 'area'),
                'format' => 'raw',
                'value'=> function($model){
                
                    if(empty($model->sysAdmCargo->id_sys_adm_cargo)){
                        $area         = SysAdmAreas::find()->where(['id_sys_empresa' => '001'])->andWhere(['id_sys_adm_area'=> $model->sysAdmArea->id_sys_adm_area])->one();
                    }else{
                        $cargo        = SysAdmCargos::find()->where(['id_sys_empresa'=> '001'])->andWhere(['id_sys_adm_cargo'=> $model->sysAdmCargo->id_sys_adm_cargo])->one();
                        $departamento = SysAdmDepartamentos::find()->where(['id_sys_empresa'=> '001'])->andWhere(['id_sys_adm_departamento'=> $cargo->id_sys_adm_departamento])->one();
                        $area         = SysAdmAreas::find()->where(['id_sys_empresa' => '001'])->andWhere(['id_sys_adm_area'=> $departamento->id_sys_adm_area])->one();
                    }
                    
                        
                    if($area):
                        return $area->area;
                    else:
                        return "s/n";
                    endif;
                
                }
             ],
             [
                'label'=>'Departamento',
                'attribute'=>'id_sys_adm_departamento',
                'filter'=>  ArrayHelper::map(SysAdmDepartamentos::find()->where(['estado'=>'A'])->andWhere(['id_sys_adm_departamento'=> $departamentos])->orderBy('departamento')->all(), 'id_sys_adm_departamento', 'departamento'),
                'format' => 'raw',
                'value'=> function($model){
                    
                    $departamento = SysAdmDepartamentos::find()->where(['id_sys_empresa'=> '001'])->andWhere(['id_sys_adm_departamento'=> $model->id_sys_adm_departamento])->one();
                        
                    if($departamento):
                        return $departamento->departamento;
                    else:
                        return "S/D";
                    endif;
                }
                
            ],
            [
                'attribute' => 'fecha_solicitada',
                'filter' => DatePicker::widget([
                    'language' => 'es',
                    'model' => $searchModel,
                    'attribute' => 'fecha_solicitada',
                    'dateFormat' => 'yyyy-MM-dd','options'=>['class'=>'form-control']]
                    ),
                'format' => 'raw',
              
            ],
            'comentario',
            [   
                'attribute' => 'telefono',
                'value' => function($model){
                    if($model->telefono == ''){
                        return "";
                    }else{
                        return $model->telefono;
                    }
                }
            ],
            [
                'attribute'=> 'estado',
                'filter'=> ['A' => 'Aprobado','P' => 'En Proceso','N' => 'No Aprobado'],
                'value' => function ($model) {
                    if ($model->estado == 'P') {
                        return Html::a('En Proceso', [''], [
                            'class' => 'btn btn-xs btn-warning btn-block',
                        ]);
                    }elseif($model->estado == 'A'){
                        return Html::a('Aprobado',[''], [
                            'class' => 'btn btn-xs btn-success btn-block',
                        ]);
                    }elseif($model->estado == 'N'){
                        return Html::a('No Aprobado',[''], [
                            'class' => 'btn btn-xs btn-danger btn-block',
                        ]);
                    }
                },
                'format' => 'raw',
                ],

                ['class' => 'yii\grid\ActionColumn',
                'template'=>'<div class="text-center" style="display:flex"> {view}&nbsp{update}&nbsp{delete}&nbsp{aprobar}',
                'buttons'=>[
                    
                    'view' => function ($url, $model) {
                        $usuariotipo = SysAdmUsuariosDep::find()->select('usuario_tipo')->where(['id_usuario'=> Yii::$app->user->id])->one();
                        $ids = ArrayHelper::getValue($usuariotipo, 'usuario_tipo');
                        
                        if( $ids == 'N' ){
                            
                        }else{
                            return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, [
                                'title' => 'Ver',
                                
                            ]);
                        }
                    
                    },
                    'update' => function($url, $model){
                    
                        $usuariotipo = SysAdmUsuariosDep::find()->select('usuario_tipo')->where(['id_usuario'=> Yii::$app->user->id])->one();
                        $ids = ArrayHelper::getValue($usuariotipo, 'usuario_tipo');
                        
                        if( $ids == 'N' ){
                            
                        }else{
                            return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [
                                'title' => 'Actualizar',
                            ]);
                        }
                    },

                    'delete' => function($url, $model){
                    
                        $usuariotipo = SysAdmUsuariosDep::find()->select('usuario_tipo')->where(['id_usuario'=> Yii::$app->user->id])->one();
                        $ids = ArrayHelper::getValue($usuariotipo, 'usuario_tipo');
                        
                        if( $ids == 'N' ){
                            
                        }else{
                            return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
                                'title' => 'Anular',
                            ]);
                        }
                    },
                
                   'aprobar' => function($url, $model){
                    
                        $usuariotipo = SysAdmUsuariosDep::find()->select('usuario_tipo')->where(['id_usuario'=> Yii::$app->user->id])->one();
                        $ids = ArrayHelper::getValue($usuariotipo, 'usuario_tipo');
                        
                        if( $ids == 'N' ){
                            
                        }else{
                            return Html::a('<span class="glyphicon glyphicon-ok"></span>', $url, [
                                'title' => 'Aprobar Solicitud',
                                'data' => [
                                'confirm' => 'Está seguro de aprobar la solicitud?',
                                'method' => 'post',
                                ],
                            
                            ]);
                        }
                    
                    },

                    ],
                    'urlCreator'=>function($action,$data){
                        if($action=='view'){
                            
                            return ['solicitud-taxis/view','id'=>$data->id_sys_rrhh_sotaxis];
                        }
                        
                        if($action == 'update'){
                            return ['solicitud-taxis/update','id'=>$data->id_sys_rrhh_sotaxis];
                        }

                        if($action == 'delete'){
                            return ['solicitud-taxis/delete','id'=>$data->id_sys_rrhh_sotaxis];
                        }
                        
                        if($action =='aprobar'){
                            
                            return ['solicitud-taxis/aprobar','id'=> $data->id_sys_rrhh_sotaxis];
                        }

                    }
                    
                ],
        ],
    ]); ?>


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


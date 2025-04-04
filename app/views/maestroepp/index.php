<?php

use yii\helpers\Html;
use yii\grid\GridView;

$this->title = 'Listado';
$this->params['breadcrumbs'][] = $this->title;
$this->render('../_alertFLOTADOR');
?>
<div class="sys-adm-areas-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?= Html::a('Registrar EPP', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'nombre',
            'estado',
            'vida_util',
            'um',

          ['class' => 'yii\grid\ActionColumn',
          'template'=>'<div class="text-center" style="display:flex"> {view}&nbsp{update}',
          'buttons'=>[
              
              'view' => function ($url, $model) {
              return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, [
                  'title' => 'Ver Permiso',
                  
              ]);
              },
              'update' => function($url, $model){
              
              return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [
                  'title' => 'Actualizar',
                  
              ]);
              },
              
            ],
              
            'urlCreator'=>function($action,$data){
                  if($action=='view'){
                      
                      return ['maestroepp/view','id'=>$data->id_sys_ssoo_epp];
                  }
                  
                  if($action == 'update'){
                      return ['maestroepp/update','id'=>$data->id_sys_ssoo_epp];
                  }
            }

            ],
        
        ],

    ]); ?>


</div>

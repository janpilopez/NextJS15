<?php
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
$this->title = 'Certificados Médicos Registrados';
$this->render('../_alertFLOTADOR');
$meses = [1 => 'Enero',  2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre' ];

?>
<div class="site-index">
  <h1><?= Html::encode($this->title)?></h1>
   <?php $form = ActiveForm::begin(); ?>
   <div class= 'row'>
       <div class= 'col-md-2 col-md-offset-4'>
             <label >Año</label>
             <input type="number" class="form-control" value = <?= $anio?> name = 'anio'>
       </div>
       <div class = 'col-md-2'>
          <?php 
             echo '<label>Mes</label>';
             echo  Html::dropDownList('mes', 'mes', $meses,
                 ['class'=> 'form-control', 'prompt' => 'Seleccione..',
                   'options' =>[ $mes => ['selected' => true]]
                  ]);
               ?>
       </div>
   </div>
   <br>
   <div class ='row'>
   		 <div class = "form-group col-md-12 text-center">
          <button type="submit" class="btn btn-primary" id="btn-guardar">Consultar</button>
         </div>
   </div>
   <br>
   <?php ActiveForm::end(); ?>
   <?php if($datos): ?>
    <div class ="row" >
      <div class="col-md-12">
         <?=  Html::a('Exportar a Excel', ['informexls','anio'=> $anio, 'mes'=> $mes], ['class'=>'btn btn-xs btn-success pull-right', "target" => "_blank" ]);?>
      </div>
    </div>
  	<br>
     <div class="row">
      	<div class="col-md-12">
          <table  class="table table-bordered table-condensed">
                <thead>
                  <tr style="background-color: #ccc">
                    <th>No</th>
                    <th>Area</th>
                    <th>Departamento</th>
                    <th>C.c</th>
                    <th>Nombres</th>
                    <th>Fecha Inicio</th>
                    <th>Fecha Fin</th>
                    <th>Ausentismo</th>
                    <th>Diagnóstico</th>
                    <th>Entidad Emisora</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($datos as $index =>$data) :?>
                  <tr>
                    <td><?=$index + 1?></td> 
                    <td><?=$data['area']?></td>
                    <td><?=$data['departamento']?></td>
                    <td><?=$data['identificacion']?></td>
                    <td><?=$data['nombres']?></td>
                    <td><?=date('Y-m-d H:i:s', strtotime( $data['inicio']))?></td>
                    <td><?=date('Y-m-d H:i:s', strtotime( $data['fin']))?></td>
                    <td><?= $data['ausentismo']?></td>
                    <td><?= $data['diagnostico']?></td>
                    <td><?= $data['entidad']?></td>
                  </tr>
                  <?php endforeach;?>
               </tbody>
          </table>
        </div>
     </div>
   <?php endif;?>
</div>
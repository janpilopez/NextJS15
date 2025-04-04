<?php
use app\assets\PermisosEmpleadosAsset;
PermisosEmpleadosAsset::register($this);
use yii\helpers\Html;
?>
<style>
#tablemodal_filter > label{
    width:100%;
}
.table__no-margin{
    margin-bottom: 0px;
}
</style>
<script>
//funcion para sombrear la seleccion
function seleccionTr(tr){
	
	 let ant = [...document.querySelectorAll("#tablemodal > tbody > tr input[type='checkbox']")]
	 ant.map((m)=>{
		 
		    if(m.checked){
			 
		    	m.parentElement.parentElement.classList.add("seleccion")
		 	  // tr.classList.remove("seleccion")
		    }else{
			    
		    	m.parentElement.parentElement.classList.remove("seleccion")
			    }
			})
}
function marcarDescamar(input){
	
	 let inputcheck  = [...document.querySelectorAll("#tablemodal > tbody > tr input[type='checkbox']")]

     if(input.checked){

    	 inputcheck.map((m)=>{
    		 
 		        m.checked = true;
 		    	m.parentElement.parentElement.classList.add("seleccion")
 		 
 			});

    }else{
        
    	inputcheck.map((m)=>{
    		 
		        m.checked = false;
		        m.parentElement.parentElement.classList.remove("seleccion")
		 
	      });
   }

	

   return false ;	
}


//carga la tabla 
$('#tablemodal').DataTable( {
    "scrollY":        "60vh",
    "scrollX":        "100%",
    "scrollCollapse": true,
    "paging":         false,
    "ordering": false,
})

//sombrear la tabla con  doble click
$("#tablemodal > tbody > tr").bind('dblclick', function(){
	seleccionTr(this)
	//selectOrden(this.firstElementChild.innerHTML.trim())
})
//sombrear la tabla con un click
$("#tablemodal > tbody > tr").bind('click', function(){
	seleccionTr(this)
})

//cancelar 
$("#btnCancelar").on('click', (e)=>{
	e.preventDefault()
	$("#modal").modal("hide")
})

//agregar empleados 
$("#btnAgregar").on('click', (e)=>{
	
     e.preventDefault()
     //seleccionar elementos tipo checkbox
	 let ant = [...document.querySelectorAll("#tablemodal > tbody > tr input[type='checkbox']")]
	 ant.map((m)=>{
		      //verificamos si esta checkeado 
		    if(m.checked){

		       	insertarfila(m.id,m.value); //funcion para inserta empleado
		    	 m.parentElement.parentElement.classList.add("seleccion")
		    }
		    
        })
		
	$("#modal").modal("hide")
})



</script>
<div class = "row">
   <div class="col-xs-12">
        <label>Marcar/Descamarcar</label>
       <?= html::checkbox('check', false, ['id'=> 'check', 'onclick'=> 'marcarDescamar(this);']) ?>
   </div>
</div>
<br>
<div class="row">
	<div class="col-xs-12">
		<table id="tablemodal" class="table table-bordered table-condensed table__no-margin" style="width:100%; background-color: white; font-size: 11px;">
    		<thead>
                <tr>
                   <th>Cédula</th>
                   <th>Nombres</th>
                   <th>Seleccionar</th>
                </tr>
        	</thead>
        	<tbody>
        		<?php foreach($datos as $index => $dato){ ?>
        			<tr>
        				<td><?= $dato["id_sys_rrhh_cedula"] ?></td>
        				<td><?= utf8_encode($dato["nombres"]) ?></td>
        				<td style ="text-align:center;"><input  type="checkbox" id="<?= $dato['id_sys_rrhh_cedula'] ?>" value="<?= utf8_encode($dato['nombres'])?>" ></td>
        			</tr>
        		<?php } ?>
        	</tbody>
		</table>
	</div>
	<div class="col-xs-12 text-center">
		<?= Html::a("Aceptar", "", ['id'=>'btnAgregar','class'=>"btn btn-success", 'style'=>'width:45%']) ?>
		<?= Html::a("Cancelar", "", ['id'=>'btnCancelar','class'=>"btn btn-danger", 'style'=>'width:45%']) ?>
	</div>
</div>

<table  class="table table-bordered table-condensed" style="<?= $style?>">
    <thead>
      <tr style="background-color: #ccc">
        <th>No</th>
        <th>Area</th>
        <th>Departamento</th>
        <th>C.c</th>
        <th>Nombres</th>
        <th>Entidad Emisora</th>
        <th>Emisión</th>
        <th>Vencimiento</th>
      </tr>
    </thead>
    <tbody>
     <?php 
      foreach ($datos as $index => $data):     
      
      $entidad = 'OTROS';
      
      if($data['entidad_emisora'] == 'I'):
      
        $entidad = 'IESS';
      
      elseif($data['entidad_emisora'] == 'M'):
      
        $entidad = 'MPS';
      
      elseif($data['entidad_emisora'] == 'P'):
      
         $entidad = 'PARTICULAR';
      
      endif;
      
     ?>
     <tr>
     	<td><?= $index + 1?></td>
      	<td><?= $data['area']?></td>
      	<td><?= $data['departamento']?></td>
      	<td><?= $data['id_sys_rrhh_cedula']?></td>
      	<td><?= $data['nombres']?></td>
        <td><?= $entidad?></td>
        <td><?= $data['fecha_emision']?></td>
        <td><?= $data['fecha_vencimiento']?></td>
      </tr>
     <?php endforeach;?>
    </tbody>
 </table>
 <table class="table table-bordered table-condensed" style="background-color: white; font-size: 10px; width: 100%;">
        <thead>
          <tr> 
             <th>Area</th>
             <th>Departamento</th>
             <th>Cédula</th>
             <th>Nombres</th>
             <th>Fecha Inicio</th>
             <th>Fecha Fin</th>
             <th>Periodo</th>
             <th>Provicion</th>
             <th>Dias</th>
             <th>Total</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($datos as $data):
          
          $valordia = $data['valor']/$data['diasdisponibles'];
          
          ?>
            <tr> 
             <td><?= $data['area']?></td>
             <td><?= $data['departamento']?></td>
             <td><?= $data['id_sys_rrhh_cedula']?></td>
             <td><?= $data['nombres']?></td>
             <td><?= $data['fecha_inicio']?></td>
             <td><?= $data['fecha_fin']?></td>
             <td><?= $data['periodo']?></td>
             <td><?= number_format($data['valor'], 2, '.', ',') ?></td>
             <td><?= $data['diasdisponibles']?></td>
             <td><?= number_format($data['dias']* $valordia, 2, '.', ',') ?></td>
          </tr>
          <?php endforeach;?>
       </tbody>
  </table>
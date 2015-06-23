<div style="width:593px; position: relative; float: left;">
    <div class="col-xs-12">
        <div class="row">
            <h3 class="col-xs-offset-2 col-xs-7 text-center"><strong><?= "Orden Interna";?></strong></h3>
            <h3 class="text-right"><strong><?= $orden->codigoServicio; ?></strong></h3>
        </div>

        <div class="row">
            <div class="col-xs-4">
                <strong><?= "Cliente:";?></strong>
                <span class="text-capitalize"><?= $orden->responsable;?></span>
            </div>
            <div class="col-xs-2">
                <strong><?= "O. Imprenta:";?></strong>
                <?= $orden->codDependiente;?></div>
            <div class="text-right">
                <strong><?= "FECHA:";?></strong>
                <?= date("d-m-Y / H:i",strtotime($orden->fechaGenerada));?>
            </div>
        </div>

        <div class="row well well-sm" style="height:200px; border-color: #000000; background-color: #ffffff">
            <table class="table table-condensed" >
                <thead><tr>
                    <th style="border-bottom: solid; border-bottom-width: 1.5px;"><?= "Nº"; ?></th>
                    <th style="border-bottom: solid; border-bottom-width: 1.5px;"><?= "Formato"; ?></th>
                    <th style="border-bottom: solid; border-bottom-width: 1.5px;"><?= "Cant."; ?></th>
                    <th style="border-bottom: solid; border-bottom-width: 1.5px;"><?= "Colores"; ?></th>
                    <th style="border-bottom: solid; border-bottom-width: 1.5px;"><?= "Trabajo"; ?></th>
                    <th style="border-bottom: solid; border-bottom-width: 1.5px;"><?= "Pinza"; ?></th>
                    <th style="border-bottom: solid; border-bottom-width: 1.5px;"><?= "Resol."; ?></th>
                </tr></thead>

                <tbody>
                <?php foreach ($orden->ordenDetalles as $key => $producto){ ;?>
                    <tr>
                        <td style="font-size:12px; padding-top: 4px;">
                            <?= ($key+1);?>
                        </td>
                        <td style="font-size:12px; padding-top: 4px;">
                            <?= $producto->fkIdProductoStock->fkIdProducto->formato;?>
                        </td>
                        <td class="col-xs-1" style="font-size:12px; padding-top: 4px;">
                            <?= $producto->cantidad; ?>
                        </td>
                        <td style="font-size:12px; padding-top: 4px;">
                            <?= (($producto->C)?"<strong>C </strong>":"").(($producto->M)?"<strong>M </strong>":"").(($producto->Y)?"<strong>Y </strong>":"").(($producto->K)?"<strong>K </strong>":"");?>
                        </td>
                        <td style="font-size:12px; padding-top: 4px;">
                            <?= $producto->trabajo;?>
                        </td>
                        <td style="font-size:12px; padding-top: 4px;">
                            <?= $producto->pinza;?>
                        </td>
                        <td style="font-size:12px; padding-top: 4px;">
                            <?= $producto->resolucion;?>
                        </td>
                    </tr>
                <?php }?>
                </tbody>
            </table>
            <!--   </div> -->
        </div>
        <div class="col-xs-12 row">
            <div class="row">
                <div class="col-xs-12"><strong>Gerenado por: </strong> <span class="text-capitalize"><?= $orden->fkIdUserD->nombre." ".$orden->fkIdUserD->apellido?></span></div>
            </div>
            <div class="row">
                <div class="col-xs-12"><strong>Obs:</strong> <?= $orden->observaciones;?></div>
            </div>
        </div>
    </div>
</div>
<div style="width:123px; position: relative; float: right;">
    <div class="row" style="font-size: 10.5px">
        <div class="col-xs-12 row text-center"><h3><strong><?= $orden->codigoServicio;?></strong></h3></div>
        <div class="col-xs-12 row text-center" style="font-size: 8px"><?= $orden->fkIdSucursal->nombre;?></div>
        <div class="col-xs-12 row"><span class="row"><strong><?= "CLIENTE:";?></strong> <span class="col-xs-12"><?= $orden->responsable;?></span></div>
        <div class="col-xs-12 row"><span class="row"><strong><?= "I.IMP:";?></strong> <span class="col-xs-12"><?= $orden->codDependiente;?></span></div>
        <div class="col-xs-12 row"><span class="row"><strong><?= "FECHA:";?></strong> <span class="col-xs-12"><?= date("d-m-Y / H:i",strtotime($orden->fechaGenerada));?></span></span></div>
        <div class="col-xs-12 row" style="font-size: 10px"><strong>Diseñador/a:</strong> <span class="text-capitalize"><?= $orden->fkIdUserD->nombre." ".$orden->fkIdUserD->apellido;?></span></div>
        <div class="col-xs-12 row">
            <?php foreach ($orden->ordenDetalles as $producto){ ?>
                <div class="col-xs-12" style="border: 1.5px solid;">
                    <?= $producto->fkIdProductoStock->fkIdProducto->formato;?> /
                    <?= $producto->cantidad; ?> /
                    <?= (($producto->C)?"C":"").(($producto->M)?"M":"").(($producto->Y)?"Y":"").(($producto->K)?"K":"");?>
                    <br>
                    <?= $producto->trabajo;?> /
                    <?= $producto->pinza;?> /
                    <?= $producto->resolucion;?>
                </div>
            <?php }?>
        </div>
    </div>
</div>
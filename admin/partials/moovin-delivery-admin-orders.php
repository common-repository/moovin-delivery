<div class="main-content">

<span class="logo-lg">
    <br><br>
    <img src="<?php echo MOOVIN_PLUGIN_URL."/assets/images/ic_logo_moovin.png" ?>" alt="" height="55">
</span>

        <div class="page-content" style=" padding: calc(5px + 5px) calc(35px / 2) 5px calc(5px / 2)">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">

                                <h4 class="card-title">Listado de ordenes</h4>
                                <p class="card-title-desc"></p>

                                <div class="float-lg-right pb-2">
                                </div>

                                <input type="hidden" value="<?php echo get_woocommerce_currency() ?>" id="currency" name="currency">
                                <input type="hidden" value="<?php echo get_woocommerce_currency_symbol() ?>" id="currency_symbol" name="currency_symbol">

                                <div class="table-responsive">
                                    <table id="datatable-orders"
                                           class="table table-striped table-bordered dt-responsive nowrap"
                                           style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                        <thead>
                                        <tr>
                                            <th>Orden ID</th>
                                            <th>Fecha Orden</th>
                                            <th>Tipo Envio</th>
                                            <th>Monto Venta</th>
                                            <th>Monto Envio</th>
                                            <th>Nombre Cliente</th>
                                            <th>Telefono Cliente</th>
                                            <th>Email Cliente</th>
                                            <th>Punto Recolección</th>
                                            <th>Punto de Entrega</th>
                                            <th>Cantidad de articulos</th>
                                            <th>Estado</th>
                                            <th>Fecha Orden Lista</th>
                                            <th>Fecha Ult. Actualización</th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                
                                </div>
                            </div>
                        </div>
                    </div> <!-- end col -->
                </div> <!-- end row -->

            </div> <!-- container-fluid -->
        </div>
        <!-- End Page-content -->
    </div>



    <div class="modal  modal-dialog-centered fade modal-close" id="modal-dialog" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 id="title-location" class="modal-title"> </h4>
                </div>
                <div class="modal-body">
                    <div id="map_canvas"  style="width:100%; height:400px"></div>
                    <br>
                    <div class="form-group col-sm-12" style="margin-top:10px">
                        <label for="latitud">Dirección*</label>
                        <input type="text" class="form-control" id="cf_address" name="cf_address"  readonly >
                    </div>

                    <div class="form-group col-sm-12" style="margin-top:10px">
                        <label for="latitud">Notas*</label>
                        <textarea type="text"  class="form-control" id="cf_notes" name="cf_notes"  readonly > </textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="javascript:;" class="btn btn-sm btn-white" id="close-modal" >Cerrar</a>
                </div>
            </div>
        </div>
    </div>


    <div class="modal  modal-dialog-centered fade modal-close" id="modal-dialog-qr" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 id="title-location" class="modal-title">Imprimir Etiqueta </h4>
                </div>
                <div class="modal-body">
                    <center>
                    <div id="div-print-ticket">
                    <table class="table_print">
                            <tr>
                            <td class="left_col">
                            <img src="<?php echo MOOVIN_PLUGIN_URL."/assets/images/ic_logo_moovin.png" ?>" style ="width: 3.0cm;height: 0.75cm;"/>
                            <span class="date"></span>
                            <img src="" id="qr-preview" class='qr' />
                            <span class="code"></span>
                            <span class="payment"></span>
                        </td>
                        <td class="right_col">
                            <!-- <span class='t'>Solicitado por:</span><br/>
                            <span class='c s_name'>Nombre:</span><span id="s_name"></span><br/>
                            <span class='c s_phone'>Teléfono:</span><span id="s_phone"></span><br/>
                            <span class='c s_mail'>Correo:</span><span id="s_mail"></span><br/>
                            <hr/> -->
                            <span class='t package' id="r_package"></span><br>

                            <span class='t'>Entregar a:</span><br/>
                            <span class='c r_name'>Nombre:</span><span id="r_name"></span><br/>
                            <span class='c r_phone'>Teléfono:</span><span id="r_phone"></span><br/>
                            <span class='c r_address'>Dirección:</span><br/>
                            <span id="r_address"></span><br/>
                            <span class='c r_notes'>Notas:</span><span id="r_notes"></span><br/><br/>
                            <span class="c fragile">Frágil (&nbsp;&nbsp;)</span>
                        </td>
                    </tr>
                    <!-- info -->
                        <tr>
                            <td class="left_col">
                                <span class="signature">x:</span>
                            </td>
                            <td class="info_right">
                                <span class="info">2289-0377 &middot; www.moovin.me &middot; info@moovin.me</span>
                            </td>
                        </tr>
                    </table>
            
                    
                        
                    </div>
                    <br>
                    <button type="button" class="btn btn-success" id="print-qr">Imprimir</button>
                    </center>
                </div>
                <div class="modal-footer">
                    <a href="javascript:;" class="btn btn-sm btn-white close-modal" id="close-modal-qr" >Cerrar</a>
                </div>
            </div>
        </div>
    </div>


    <div class="modal modal-dialog-centered fade modal-close" id="modal-dialog-products" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 id="title-location" class="modal-title">Listado de productos</h4>
                </div>
                <div class="modal-body">
                    <table id="datatable-products"
                            class="table table-striped table-bordered dt-responsive nowrap"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                        <tr>
                            <th>SKU</th>
                            <th>Nombre Producto</th>
                            <th>Cantidad</th>
                            <th>Precio</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <a href="javascript:;" class="btn btn-sm btn-white close-modal" id="close-modal-products" >Cerrar</a>
                </div>
            </div>
        </div>
    </div>





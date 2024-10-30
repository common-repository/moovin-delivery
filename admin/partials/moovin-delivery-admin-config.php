<div class="main-content">

<span class="logo-lg">
    <br><br>
    <img src="<?php echo MOOVIN_PLUGIN_URL."/assets/images/ic_logo_moovin.png" ?>" alt="" height="55">
</span>

        <div class="page-content" style=" padding: calc(5px + 5px) calc(35px / 2) 5px calc(5px / 2)">
        <div class="row">
                            <div class="col-xl-12">
                                <div class="card">
                                    <div class="card-body">
                                     <h3 class="card-title">Ajustes</h3>
                                        <p class="card-title-desc">Esta sección es dedicada a la administración y configuración de nuestros Pluglin para entregas de paquetes Moovin</p>
                                        <!-- Nav tabs -->
                                        <ul class="nav nav-tabs nav-tabs-custom nav-justified" role="tablist">
                                             <li class="nav-item" style ="margin-bottom: 0px" id="bind-status">
                                                <a class="nav-link active" data-bs-toggle="tab" href="#status-plugin" role="tab"  >
                                                    <span class="d-block d-sm-none"><i class="far fa-user"></i></span>
                                                    <span class="d-none d-sm-block" id="bind-status-click">Estado</span>    
                                                </a>
                                            </li>
                                            <li class="nav-item"  style ="margin-bottom: 0px" id="bind-keys">
                                                <a class="nav-link " data-bs-toggle="tab" href="#keys" role="tab" >
                                                    <span class="d-block d-sm-none"><i class="fas fa-home"></i></span>
                                                    <span class="d-none d-sm-block" id="bind-keys-click">Credenciales</span>    
                                                </a>
                                            </li>
                                            <li class="nav-item"  style ="margin-bottom: 0px" id="bind-config">
                                                <a class="nav-link" data-bs-toggle="tab" href="#location" role="tab"  >
                                                    <span class="d-block d-sm-none"><i class="far fa-envelope"></i></span>
                                                    <span class="d-none d-sm-block" id="bind-config-click">Configuración</span>    
                                                </a>
                                            </li>
                                            <!-- <li class="nav-item"  style ="margin-bottom: 0px" id="bind-package">
                                                <a class="nav-link" data-bs-toggle="tab" href="#package-moovin" role="tab">
                                                    <span class="d-block d-sm-none"><i class="far fa-user"></i></span>
                                                    <span class="d-none d-sm-block">Paquetes</span>    
                                                </a>
                                            </li> -->
                                            <li class="nav-item"  style ="margin-bottom: 0px" id="bind-zone">
                                                <a class="nav-link" data-bs-toggle="tab" href="#zone" role="tab" >
                                                    <span class="d-block d-sm-none"><i class="far fa-user"></i></span>
                                                    <span class="d-none d-sm-block" id="bind-zone-click">Zona de Cobertura</span>    
                                                </a>
                                            </li>
                                        </ul>
        
                                        <!-- Tab panes -->
                                        <div class="tab-content p-3 text-muted">
                                            <div class="tab-pane active" id="status-plugin" role="tabpanel">
                                            <div  style="display: block;">
                                            
                                            <div id="notificaciones-status">                                            
                                            </div>


                                            
                                            <div class="row">
                                             <div class="col-lg-7">

                                             <div class="mb-3 row">
                                                <label class="col-md-2 col-form-label">País</label>
                                                    <div class="col-md-10">
                                                        <select class="form-select" id="sp-country">
                                                            <option value="">Seleccione el País</option>
                                                            <option value="CR">Costa Rica</option>
                                                            <option value="HN">Honduras</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="mb-3 row">
                                                <label class="col-md-2 col-form-label">Conectado a</label>
                                                    <div class="col-md-10">
                                                        <select class="form-select" id="sp-env">
                                                            <option value="">Seleccione el ambiente</option>
                                                            <option value="PROD">Producción</option>
                                                            <option value="SANDBOX">Pruebas</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                

                                                <div class="mb-3 row">
                                                <label class="col-md-2 col-form-label"> Estado: </label>
                                                    <div class="col-md-10">
                                                        <input  value="1" class="switch switch--shadow" id="status-env" name="status-env" type="checkbox">
                                                        <label for="status-env"></label>
                                                    </div>
                                                </div>


                                                <p id="msg-status"></p>

                                                <div class="row justify-content-end">
                                                    <div class="col-sm-9">
                                                        <div>
                                                            <button type="submit" class="btn btn-primary w-md" id="save-status">Guardar</button>
                                                        </div>
                                                    </div>
                                                </div>


                                            </div>
                                            
                                            
                                            <div class="col-lg-5" id="complete-install" >
                                                
                                            </div>
                                            

                                            </div>



                                             </div>
                                        </div>
                                            <div class="tab-pane " id="keys" role="tabpanel">
                                                <div class="accordion" id="accordionExample">
                                                        <div class="accordion-item">
                                                            <h2 class="accordion-header" id="headingOne">
                                                                <button class="accordion-button fw-medium" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                                                Credenciales Pruebas &emsp;<i class="fa fa-info-circle info-dialog" aria-hidden="true" data-dialog="" data-title="Credenciales Pruebas" data-body="Por favor ingresa las credenciales de pruebas de moovin"  style="color:white"></i>  

                                                                </button>
                                                            </h2>
                                                            <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                                                <div class="accordion-body">
                                                                    <div class="text-muted">

                                                                        <form>
                                                                            <div class="row mb-4">
                                                                                <label for="horizontal-firstname-input" class="col-sm-3 col-form-label">Usuario</label>
                                                                                <div class="col-sm-9">
                                                                                <input type="text" class="form-control" id="cf_username_sandbox" >
                                                                                </div>
                                                                            </div>
                                                                            <div class="row mb-4">
                                                                                <label for="horizontal-email-input" class="col-sm-3 col-form-label">Contraseña</label>
                                                                                <div class="col-sm-9">
                                                                                    <input type="email" class="form-control" id="cf_password_sandbox">
                                                                                </div>
                                                                            </div>                                                                            
                                                                           
                                                                            <div class="row justify-content-end">
                                                                                <div class="col-sm-9">
                                                                                    <div>
                                                                                        <button type="button" class="btn btn-primary w-md" id="save-sandbox">Guardar</button><br><br>
                                                                                        <div id="status-sandbox"> </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </form>


                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="accordion-item">
                                                            <h2 class="accordion-header" id="headingTwo">
                                                                <button class="accordion-button fw-medium collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                                                Credenciales Producción    &emsp;<i class="fa fa-info-circle info-dialog" aria-hidden="true" data-title="Credenciales Producción" data-body="Por favor ingresa las credenciales de producción de moovin"  style="color:white"></i>
                                                                </button>
                                            
                                                            </h2>
                                                            <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                                                                <div class="accordion-body">
                                                                    <div class="text-muted">
                                                                        <form>
                                                                            <div class="row mb-4">
                                                                                <label for="horizontal-firstname-input" class="col-sm-3 col-form-label">Usuario</label>
                                                                                <div class="col-sm-9">
                                                                                <input type="text" class="form-control" id="cf_username_prod">
                                                                                </div>
                                                                            </div>
                                                                            <div class="row mb-4">
                                                                                <label for="horizontal-email-input" class="col-sm-3 col-form-label">Contraseña</label>
                                                                                <div class="col-sm-9">
                                                                                    <input type="email" class="form-control" id="cf_password_prod">
                                                                                </div>
                                                                            </div>
                                                                           

                                                                            <div class="row justify-content-end">
                                                                                <div class="col-sm-9">
                                                                                    <div>
                                                                                        <button type="button" class="btn btn-primary w-md" id="save-prod">Guardar</button><br><br>
                                                                                        <div id="status-prod"> </div> 
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </form>

                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="accordion-item">
                                                            <h2 class="accordion-header" id="headingThree">
                                                                <button class="accordion-button fw-medium collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                                                Google Maps  &emsp;<i class="fa fa-info-circle info-dialog " aria-hidden="true" data-title="Google Maps" data-body="Por favor ingresa las credenciales para el uso de mapas de Google maps" style="color:white"></i>
                                                                </button>
                                                            </h2>
                                                            <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                                                                <div class="accordion-body">
                                                                    <div class="text-muted">
                                                                        <form>

                                                                           
                                                                            <div class="row mb-4">
                                                                                <label for="horizontal-firstname-input" class="col-sm-3 col-form-label">Google Key Api   &emsp;<a  target="_blank" href="<?php echo MOOVIN_PLUGIN_URL."assets/google_key.pdf" ?>"><i class="fa fa-info-circle  " aria-hidden="true" ></i></a></label>  
                                                                                <div class="col-sm-9">
                                                                                <input type="text" class="form-control" id="cf_google_maps" name="cf_google_maps" >
                                                                                </div>
                                                                            </div>

                                                                            <div class="row mb-4">
                                                                                <label for="horizontal-firstname-input" class="col-sm-3 col-form-label">Zoom </label>
                                                                                <div class="col-sm-9">
                                                                                <input type="number" class="form-control"  id="cf_zoom_google_maps" id="cf_zoom_google_maps">
                                                                                </div>
                                                                            </div>

                                                                            <div class="row mb-4">
                                                                                <label for="horizontal-firstname-input" class="col-sm-3 col-form-label">Detectar Ubicación de Usuario </label>
                                                   
                                                                                 <div class="col-sm-9">
                                                                                    <input  value="1" class="switch switch--shadow" type="checkbox" id="cf_google_location_map" name="cf_google_location_map" >
                                                                                    <label for="cf_google_location_map"></label>
                                                                                </div>
                                                                            </div>

                                                                            <div class="row mb-4">
                                                                                <label for="horizontal-firstname-input" class="col-sm-3 col-form-label">Estado </label>
                                                                                <div class="col-sm-9">
                                                                                    <input  value="1" class="switch switch--shadow" id="cf_google_status" name="cf_google_status" type="checkbox">
                                                                                    <label for="cf_google_status"></label>
                                                                                </div>
                                                                            </div>

                                                                            
                                                                             <div class="row justify-content-end">
                                                                                <div class="col-sm-9">
                                                                                    <div>
                                                                                        <button type="button" class="btn btn-primary w-md" id="save-google">Guardar</button>
                                                                                    </div>
                                                                                </div>
                                                                                <br>
                                                                            </div>                                                                            
                                                                        </form>
                                                                            
                                                                     </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="accordion-item">
                                                            <h2 class="accordion-header" id="headingFour">
                                                                <button class="accordion-button fw-medium collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                                                Here Maps   &emsp;<i class="fa fa-info-circle info-dialog " aria-hidden="true" data-title="Here Maps" data-body="Por favor ingresa las credenciales para el uso de mapas de Here maps" style="color:white"></i>
                                                                </button>
                                                            </h2>
                                                            <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#accordionExample">
                                                                <div class="accordion-body">
                                                                    <div class="text-muted">
                                                                        <form>

                                                                            <div class="row mb-4">
                                                                                <label for="horizontal-firstname-input" class="col-sm-3 col-form-label">Here Key Api &emsp;<a  target="_blank" href="<?php echo MOOVIN_PLUGIN_URL."assets/here_developer.pdf" ?>"><i class="fa fa-info-circle  " aria-hidden="true" ></i></a></label>
                                                                                <div class="col-sm-9">
                                                                                <input type="text" class="form-control" id="cf_key_here" name="cf_key_here">
                                                                                </div>
                                                                            </div>

                                                                            <div class="row mb-4">
                                                                                <label for="horizontal-firstname-input" class="col-sm-3 col-form-label">Zoom </label>
                                                                                <div class="col-sm-9">
                                                                                <input type="number" class="form-control" id="cf_zoom_here" name="cf_zoom_here" >
                                                                                </div>
                                                                            </div>

                                                                            <div class="row mb-4">
                                                                                <label for="horizontal-firstname-input" class="col-sm-3 col-form-label">Detectar Ubicación de Usuario </label>
                                                                                <div class="col-sm-9">
                                                                                    <input class="switch switch--shadow" id="cf_here_location_map" name="cf_here_location_map" type="checkbox">
                                                                                    <label for="cf_here_location_map"></label>
                                                                                </div>
                                                                            </div>
                                                                            <div class="row mb-4">
                                                                                <label for="horizontal-firstname-input" class="col-sm-3 col-form-label">Estado </label>
                                                                                <div class="col-sm-9">
                                                                                    <input  value="1" class="switch switch--shadow" id="cf_here_status" name="cf_here_status" type="checkbox">
                                                                                    <label for="cf_here_status"></label>
                                                                                </div>
                                                                            </div>
                                                                            

                                                                             <div class="row justify-content-end">
                                                                                <div class="col-sm-9">
                                                                                    <div>
                                                                                        <button type="button" class="btn btn-primary w-md" id="save-here">Guardar</button>
                                                                                    </div>
                                                                                </div>
                                                                                <br>
                                                                            </div>                                                                            
                                                                        </form>
                                                                            
                                                                     </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        



                                                    </div>
                                            </div>
                                             
                                            <div class="tab-pane" id="location" role="tabpanel">
                                                    <div class="accordion" id="accordionConfig">
                                                        <div class="accordion-item">
                                                            <h2 class="accordion-header" id="headingOne">
                                                                <button class="accordion-button fw-medium" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                                                Información de contacto &emsp;<i class="fa fa-info-circle info-dialog " aria-hidden="true" data-title="Contacto" data-body="Ingrese la información de contacto con la que podamos comunicarnos en caso de dudas en un paquete" style="color:white"></i>
                                                                </button>
                                                            </h2>
                                                            <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionConfig">
                                                                <div class="accordion-body">
                                                                    <div class="text-muted">

                                                                            <form>
                                                                                <div class="row mb-4">
                                                                                    <label for="horizontal-firstname-input" class="col-sm-3 col-form-label">Nombre Contacto</label>
                                                                                    <div class="col-sm-9">
                                                                                    <input type="text" class="form-control" id="cf_name_contact">
                                                                                    </div>
                                                                                </div>

                                                                                <div class="row mb-4">
                                                                                    <label for="horizontal-firstname-input" class="col-sm-3 col-form-label">Teléfono </label>
                                                                                    <div class="col-sm-9">
                                                                                    <input type="text" class="form-control" id="cf_phone_contact" >
                                                                                    </div>
                                                                                </div>

                                                                                <div class="row mb-4">
                                                                                    <label for="horizontal-firstname-input" class="col-sm-3 col-form-label">Email Contacto</label>
                                                                                    <div class="col-sm-9">
                                                                                    <input type="text" class="form-control" id="cf_email_contact">
                                                                                    </div>
                                                                                </div>

                                                                                <div class="row mb-4">
                                                                                    <label for="horizontal-firstname-input" class="col-sm-3 col-form-label">Notas </label>
                                                                                    <div class="col-sm-9">
                                                                                        <textarea  class="form-control" name="cf_note_contact" id="cf_note_contact" rows="4" cols="50"></textarea>
                                                                                    </div>
                                                                                </div>

                                                                               
                                                                                <div class="row justify-content-end">
                                                                                    <div class="col-sm-9">
                                                                                        <div>
                                                                                            <button type="button" class="btn btn-primary w-md" id="save-contact">Guardar</button>
                                                                                        </div>
                                                                                    </div>
                                                                                    <br>
                                                                                </div>                                                                            
                                                                            </form>

                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="accordion-item">
                                                            <h2 class="accordion-header" id="headingTwo">
                                                                <button class="accordion-button fw-medium collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                                                Ubicación de recolección &emsp;<i class="fa fa-info-circle info-dialog " aria-hidden="true" data-title="Recolección" data-body="Ingrese el punto donde debemos recoger los paquetes o en caso de usar fulfillment nosotros nos encargamos de todo" style="color:white"></i>
                                                                </button>
                                            
                                                            </h2>
                                                            <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionConfig">
                                                                <div class="accordion-body">
                                                                    <div class="text-muted">
                                                                        <p>Moovin se encargará de gestionar tus paquetes</p>
                                                                        <div class="row mb-4">
                                                                                <label for="horizontal-firstname-input" class="col-sm-3 col-form-label">Fulfillment </label>
                                                                                <div class="col-sm-9">
                                                                                    <input  value="1" class="switch switch--shadow" id="cf_fullfillment" name="cf_fullfillment" type="checkbox">
                                                                                    <label for="cf_fullfillment">
                                                                                    </label>
                                                                                
                                                                                </div>
                                                                            </div>
                                                                        <hr>
                                                                        <p>Por favor indica la ubicacion donde se recogeran los paquetes que vendes</p>
                                                                        <div><small><b>Nota:</b> Si el mapa no se vizualiza por favor revise el key de google y refresque la pagina</div></small>

                                                                        <form>
                                                                            <div class="row">
                                                                                <div class="col-sm-12">
                                                                                    <div id="mappicker" style="width: 100%; height: 300px;"></div>
                                                                                </div>
                                                                                <br>
                                                                                <div class="form-group col-sm-12" style="margin-top:10px">
                                                                                    <label for="latitud">Dirección*</label>
                                                                                    <input type="text" value=""  class="form-control" id="cf_address" name="cf_address"  readonly required>
                                                                                </div>

                                                                                <div class="form-group col-sm-6" style="margin-top:10px">
                                                                                    <label for="longitud">Longitud*</label>
                                                                                    <input type="text" value=""  class="form-control" id="cf_lng" name="cf_lng" readonly required>
                                                                                </div>
                                                                            
                                                                                <div class="form-group col-sm-6" style="margin-top:10px">
                                                                                    <label for="latitud">Latitud*</label>
                                                                                    <input type="text" value=""  class="form-control" id="cf_lat" name="cf_lat"  readonly required>
                                                                                </div>
                                                                            </div>

                                                                            <div class="row" style="margin-top:10px">
                                                                                <div class="col-sm-9">
                                                                                    <div>
                                                                                        <button type="button" class="btn btn-primary w-md" id="save-default-location">Guardar</button>

                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </form>




                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="accordion-item">
                                                            <h2 class="accordion-header" id="headingThree">
                                                                <button class="accordion-button fw-medium collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                                                Tamaño de paquete &emsp;<i class="fa fa-info-circle info-dialog " aria-hidden="true" data-title="Paquete" data-body="Indica un tamaño promedio de los paquetes que envías, dicha selección se enviara cuando un producto no se encuentre completamente configurado en la ficha de producto de woocommerce" style="color:white"></i>
                                                                </button>
                                                            </h2>
                                                            <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionConfig">
                                                                <div class="accordion-body">
                                                                    <div class="text-muted">
                                                                        <form>

                                                                        <p>Indica un tamaño y peso por defecto de tus paquetes , en caso de dicha información no se encuentre en el producto</p>

                                                                            <form>
                                                                                <div class="row">
                                                                                    <div class="col-lg-6" style="margin-top:15px">
                                                                                        <div>
                                                                                            <label class="form-label">Tamaño*</label>
                                                                                            <select class="form-control" id="pkg_size" name="pkg_size" style="max-width:100%;">
                                                                                                <option value="">Seleccione un tamaño de paquete</option>
                                                                                                <option value="XS">XS</option>
                                                                                                <option value="S">S</option>
                                                                                                <option value="M">M</option>
                                                                                                <option value="L">L</option>
                                                                                                <option value="XL">XL</option>
                                                                                                <option value="XXL">XXL</option>
                                                                                                <option value="XXXL">XXXL</option>
                                                                                            </select>
                                                                                        </div>
                                                                                        <br>
                                                                                        <div>
                                                                                            <label class="form-label">Peso (Kg) *</label>
                                                                                            <input class="form-control" id="pkg_weight" name="pkg_weight" type="number" min="0" step="0.01" placeholder="Peso Kg">
                                                                                        </div>
                                                                                        <br>
                                                                                        <div>
                                                                                            <button type="button" class="btn btn-primary w-md" id="save-package" >Guardar</button>
                                                                                        </div>

                                                                                    </div>
                                                                                    <div class="col-lg-6" style="margin-top:15px">
                                                                                        <table class="table table-striped mb-0" id="tbl_pckgs_size">
                                                                                            <thead>
                                                                                            <tr>
                                                                                                <th><strong><span>Acrónimo</span></strong></th>
                                                                                                <th><strong><span>Largo (</span><em><span>cm</span></em><span>)</span></strong></th>
                                                                                                <th><strong><span>Ancho (</span><em><span>cm</span></em><span>)</span></strong></th>
                                                                                                <th><strong><span>Alto (</span><em><span>cm</span></em><span>)</span></strong></th>
                                                                                                <th><strong><span>Peso máximo (</span><em><span>Kg</span></em><span>)</span></strong></th>
                                                                                            </tr>
                                                                                            </thead>
                                                                                            <tbody>
                                                                                            <tr>
                                                                                                <td><span>XS</span></td>
                                                                                                <td><span>16</span></td>
                                                                                                <td><span>15</span></td>
                                                                                                <td><span>4</span></td>
                                                                                                <td><span>0.5</span></td>
                                                                                            </tr><tr><td><span>S</span></td>
                                                                                                <td><span>24</span></td>
                                                                                                <td><span>17</span></td>
                                                                                                <td><span>9</span></td>
                                                                                                <td><span>1</span></td>
                                                                                            </tr><tr><td><span>M</span></td>
                                                                                                <td><span>32</span></td>
                                                                                                <td><span>18</span></td>
                                                                                                <td><span>22</span></td>
                                                                                                <td><span>2</span></td>
                                                                                            </tr><tr><td><span>L</span></td>
                                                                                                <td><span>39</span></td>
                                                                                                <td><span>19</span></td>
                                                                                                <td><span>22</span></td>
                                                                                                <td><span>7</span></td>
                                                                                            </tr><tr><td><span>XL</span></td>
                                                                                                <td><span>97</span></td>
                                                                                                <td><span>56</span></td>
                                                                                                <td><span>26</span></td>
                                                                                                <td><span>10</span></td>
                                                                                            </tr><tr><td><span>XXL</span></td>
                                                                                                <td><span>120</span></td>
                                                                                                <td><span>80</span></td>
                                                                                                <td><span>27</span></td>
                                                                                                <td><span>15</span></td>
                                                                                            </tr><tr><td><span>XXXL</span></td>
                                                                                                <td><span>160</span></td>
                                                                                                <td><span>100</span></td>
                                                                                                <td><span>35</span></td>
                                                                                                <td><span>20</span></td>
                                                                                            </tr></tbody></table>
                                                                                    </div>
                                                                                </div>
                                                                            </form>




                                                                        </form>
                                                                     </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="accordion-item">
                                                            <h2 class="accordion-header" id="headingFour">
                                                                <button class="accordion-button fw-medium collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                                                Tareas en punto &emsp;<i class="fa fa-info-circle info-dialog " aria-hidden="true" data-title="Tareas" data-body="Indica las tareas que quieres que realicemos cuando entregamos un paquete al cliente" style="color:white"></i>
                                                                </button>
                                                            </h2>
                                                            <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#accordionConfig">
                                                                <div class="accordion-body">
                                                                    <div class="text-muted">
                                                                        <form>
                                                                        <p>Por favor indica que debemos realizar cuando entregamos un paquete</p>


                                                                             <div class="row mb-4">
                                                                                <label for="horizontal-firstname-input" class="col-sm-3 col-form-label">Tomar foto de documento de identidad </label>
                                                                                <div class="col-sm-9">
                                                                                    <input  value="1" class="switch switch--shadow" id="cf_id_photo" name="cf_id_photo" type="checkbox">
                                                                                    <label for="cf_id_photo"></label>
                                                                                </div>
                                                                                
                                                                            </div>

                                                                            <div>
                                                                                <button type="button" class="btn btn-primary w-md" id="save-task" >Guardar</button>
                                                                            </div>

                                                                        </form>
                                                                     </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="accordion-item">
                                                            <h2 class="accordion-header" id="headingFive">
                                                                <button class="accordion-button fw-medium collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                                                                General  &emsp;<i class="fa fa-info-circle info-dialog " aria-hidden="true" data-title="General" data-body="Gestiona la configuración general en esta sección" style="color:white"></i>
                                                                </button>
                                                            </h2>
                                                            <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFive" data-bs-parent="#accordionConfig">
                                                                <div class="accordion-body">
                                                                    <div class="text-muted">
                                                                        <form>
                                                                                <p>Por favor indica que servicios podemos ofrecer</p>


                                                                                <div class="row mb-4">
                                                                                    <label for="horizontal-firstname-input" class="col-sm-3 col-form-label">Servicios Express </label>
                                                                                    <div class="col-sm-9">
                                                                                        <input  checked class="switch switch--shadow" id="cf_express_services" name="cf_express_services" type="checkbox">
                                                                                        <label for="cf_express_services"></label>
                                                                                    </div>
                                                                                </div>


                                                                                <div class="row mb-4">
                                                                                    <label for="horizontal-firstname-input" class="col-sm-3 col-form-label">Horario de servicio &emsp;<i class="fa fa-info-circle info-dialog" aria-hidden="true" data-title="General" data-body="Indica un horario de atención para ofrecer servicios express, ten presente que un servicio express requiere de personal en el punto de recolección para atender a nuestro mensajero."></i> </label>
                                                                                    <div class="col-sm-4">
                                                                                        <input   class="form-control" id="cf_hour_init" name="cf_hour_init" type="time">
                                                                                        <label for="cf_express_services"></label>
                                                                                    </div>
                                                                                    <div class="col-sm-1">
                                                                                        <center> a </center>
                                                                                    </div>
                                                                                    <div class="col-sm-4">
                                                                                        <input   class="form-control" id="cf_hour_final" name="cf_hour_final" type="time">
                                                                                        <label for="cf_express_services"></label>
                                                                                    </div>
                                                                                </div>

                                                                                <div class="row mb-4">
                                                                                    <label for="horizontal-firstname-input" class="col-sm-3 col-form-label">Nombre del servicio </label>
                                                                                    <div class="col-sm-9">
                                                                                        <input  class="form-control" id="cf_express_name" name="cf_express_name" type="text">
                                                                                        <label for="cf_express_name"></label>
                                                                                    </div>
                                                                                </div>

                                                                                <hr>

                                                                                <div class="row mb-4">
                                                                                    <label for="horizontal-firstname-input" class="col-sm-3 col-form-label">Servicios en ruta </label>
                                                                                    <div class="col-sm-9">
                                                                                        <input checked  class="switch switch--shadow" id="cf_route_services" name="cf_route_services" type="checkbox">
                                                                                        <label for="cf_route_services"></label>
                                                                                    </div>
                                                                                </div>

                                                                                <div class="row mb-4">
                                                                                    <label for="horizontal-firstname-input" class="col-sm-3 col-form-label">Solicitud de recolección automatica </label>
                                                                                    <div class="col-sm-9">
                                                                                        <input  value="1" class="switch switch--shadow" id="cf_collect_auto" name="cf_collect_auto" type="checkbox">
                                                                                        <label for="cf_collect_auto"></label>
                                                                                    </div>
                                                                                </div>

                                                                                <div class="row mb-4">
                                                                                    <label for="horizontal-firstname-input" class="col-sm-3 col-form-label">Nombre del servicio </label>
                                                                                    <div class="col-sm-9">
                                                                                        <input  class="form-control" id="cf_route_name" name="cf_route_name" type="text">
                                                                                        <label for="cf_route_name"></label>
                                                                                    </div>
                                                                                </div>

                                                                                <hr>

                                                                                <div class="row mb-4">
                                                                                    <label for="horizontal-firstname-input" class="col-sm-3 col-form-label">Metodos de pago que requieran confirmación &emsp;<i class="fa fa-info-circle info-dialog" aria-hidden="true" data-title="Confirmación de metodo de pago" data-body="Si alguno de los métodos de pago configurados en su tienda requiere de una confirmación de pago luego de que el cliente complete la orden de la tienda indíquelo aquí."></i></label>
                                                                                        
                                                                                    <div class="col-sm-9">
                                                                                        <select class="form-control payment-select2" id="cf_payment_confirm_method" name="cf_payment_confirm_method" multiple >
                                                                                        </select>   
                                                                                    </div>
                                                                                    <small> Cada compra realizada con uno de los métodos de pago seleccionados requerirá de una confirmación explicita para solicitar la recolección
                                                                                    </small>
                                                                                </div>


                                                                                <div class="row mb-4">
                                                                                    <label for="horizontal-firstname-input" class="col-sm-3 col-form-label">Metodos de pago contra entrega (Efectivo) &emsp;<i class="fa fa-info-circle info-dialog" aria-hidden="true" data-title="Metodo de pago contraentrega" data-body="Si alguno de los métodos de pago configurados en su tienda requiere de cobro contra entrega por Moovin."></i></label>
                                                                                        
                                                                                    <div class="col-sm-9">
                                                                                        <select class="form-control payment-select2" id="cf_payment_money_in_site" name="cf_payment_money_in_site" multiple >
                                                                                        </select>   
                                                                                    </div>
                                                                                    <small> Cada compra realizada con uno de los métodos de pago seleccionados enviara la solicitud de cobro contra entrega en efectivo por Moovin
                                                                                    <br> <b>Nota: Para el uso de este servicio contacte previamente a Moovin para su activación. </b>
                                                                                    </small>
                                                                                </div>


                                                                                <div class="row mb-4">
                                                                                    <label for="horizontal-firstname-input" class="col-sm-3 col-form-label">Metodos de pago contra entrega (Tarjeta) &emsp;<i class="fa fa-info-circle info-dialog" aria-hidden="true" data-title="Metodo de pago contraentrega" data-body="Si alguno de los métodos de pago configurados en su tienda requiere de cobro contra entrega por Moovin."></i></label>
                                                                                        
                                                                                    <div class="col-sm-9">
                                                                                        <select class="form-control payment-select2" id="cf_payment_card_in_site" name="cf_payment_card_in_site" multiple >
                                                                                        </select>   
                                                                                    </div>
                                                                                    <small> Cada compra realizada con uno de los métodos de pago seleccionados enviara la solicitud de cobro contra entrega en tarjeta por Moovin
                                                                                    <br> <b>Nota: Para el uso de este servicio contacte previamente a Moovin para su activación. </b>
                                                                                    </small>
                                                                                </div>




                                                                                <hr>

                                                                                <div class="row mb-4">
                                                                                    <label for="horizontal-firstname-input" class="col-sm-3 col-form-label">Monto envío gratis </label>
                                                                                    <div class="col-sm-9">
                                                                                        <input  class="form-control" id="cf_amount_promo" name="cf_amount_promo" type="number" min="0" step="1" >
                                                                                        <label for="cf_amount_promo"></label>
                                                                                    </div>
                                                                                </div>

                                                                                <div class="row mb-4">
                                                                                    <label for="horizontal-firstname-input" class="col-sm-3 col-form-label">Envío gratis Express</label>
                                                                                    <div class="col-sm-9">
                                                                                        <input value="1" class="switch switch--shadow" id="cf_status_promo" name="cf_status_promo" type="checkbox">
                                                                                        <label for="cf_status_promo"></label>
                                                                                    </div>
                                                                                    <small> Active si desea dar de regalia el envío gratis Moovin (Express) en su tienda por determinado monto de compra <br> <b>Nota: El monto de envío siempre sera cobrado por moovin. </b></small>
                                                                                </div>

                                                                                <div class="row mb-4">
                                                                                    <label for="horizontal-firstname-input" class="col-sm-3 col-form-label">Envío gratis Ruta</label>
                                                                                    <div class="col-sm-9">
                                                                                        <input value="1" class="switch switch--shadow" id="cf_status_promo_ruta" name="cf_status_promo_ruta" type="checkbox">
                                                                                        <label for="cf_status_promo_ruta"></label>
                                                                                    </div>
                                                                                    <small> Active si desea dar de regalia el envío gratis Moovin (Ruta) en su tienda por determinado monto de compra <br> <b>Nota: El monto de envío siempre sera cobrado por moovin. </b></small>
                                                                                </div>

                                                                            
                                                                                 <hr>

                                                                                 <div class="row mb-4">
                                                                                    <label for="horizontal-firstname-input" class="col-sm-3 col-form-label">Monto adicional envío </label>
                                                                                    <div class="col-sm-9">
                                                                                        <input  class="form-control" id="cf_amount_add" name="cf_amount_add" type="number" min="0" step="1" >
                                                                                        <label for="cf_amount_add"></label>
                                                                                    </div>
                                                                                    <small><b> Nota:</b> Agrege un monto adicional al costo del envio moovin, dicho monto sera sumado a la tarifa dada por moovin</small>

                                                                                </div>


                                                                                 <div class="row mb-4">
                                                                                    <label for="horizontal-firstname-input" class="col-sm-3 col-form-label">Redondear monto &emsp;<i class="fa fa-info-circle info-dialog" aria-hidden="true" data-title="General" data-body="El monto de envío será redondeado a la decena mas cercana"></i></label>
                                                                                    <div class="col-sm-9">
                                                                                        <input checked  class="switch switch--shadow" id="cf_amount_round" name="cf_amount_round" type="checkbox">
                                                                                        <label for="cf_amount_round"></label>
                                                                                    </div>
                                                                                </div>

                                                                                <div class="row mb-4">
                                                                                    <label for="horizontal-firstname-input" class="col-sm-3 col-form-label">Tipo de cambio  &emsp;<i class="fa fa-info-circle info-dialog" aria-hidden="true" data-title="General" data-body="Tipo de cambio configurado"></i></label>
                                                                                    <div class="col-sm-3">
                                                                                        <input  class="form-control" id="cf_tc_value" name="cf_tc_value" type="number" min="0" step="1" >
                                                                                        <label for="cf_tc_value"></label>
                                                                                    </div>
                                                                                    <label for="horizontal-firstname-input" class="col-sm-3 col-form-label">Automatico &emsp;<i class="fa fa-info-circle info-dialog" aria-hidden="true" data-title="General" data-body="Consultar el tipo de cambio automaticamente"></i></label>
                                                                                    <div class="col-sm-3">
                                                                                        <input checked  class="switch switch--shadow" id="cf_tc_auto" name="cf_tc_auto" type="checkbox">
                                                                                        <label for="cf_tc_auto"></label>
                                                                                    </div>
                                                                                </div>
                                                                                 <hr>

                                                                                <div class="row mb-4">
                                                                                    <label for="horizontal-firstname-input" class="col-sm-3 col-form-label">Enviar notificación cliente &emsp;<i class="fa fa-info-circle info-dialog" aria-hidden="true" data-title="General" data-body="Se enviará una notificación por correo electrónico al cliente cuando un paquete esta listo por recoger por parte de Moovin, el correo electrónico le notificará al cliente información sobre el paquete así como la pagina de seguimiento (Tracking) del paquete."></i></label>
                                                                                        
                                                                                    <div class="col-sm-9">
                                                                                        <input checked  class="switch switch--shadow" id="cf_email_notification" name="cf_email_notification" type="checkbox">
                                                                                        <label for="cf_email_notification"></label>
                                                                                    </div>

                                                                                    <small> Se requiere de la instalación y configuración del plugin llamado <b>WP Mail SMTP by WPForms</b> buscalo en el marketplace de wordpress </small>
                                                                                </div>

                                                                               <hr>

                                                                               <div class="row mb-4">
                                                                                    <label for="horizontal-firstname-input" class="col-sm-3 col-form-label">Modo Checkout Simple &emsp;<i class="fa fa-info-circle info-dialog" aria-hidden="true" data-title="General" data-body="Modo simple oculta campos no necesarios para el checkout"></i></label>
                                                                                        
                                                                                    <div class="col-sm-2">
                                                                                        <input checked  class="switch switch--shadow" id="cf_extend_mode" name="cf_extend_mode" type="checkbox">
                                                                                        <label for="cf_extend_mode"></label>
                                                                                    </div>
                                                                                    <label for="horizontal-firstname-input" class="col-sm-2 col-form-label">Modo Checkout extendido &emsp;<i class="fa fa-info-circle info-dialog" aria-hidden="true" data-title="General" data-body="Modo extendido despliega todos los campos por defecto en checkout"></i></label>

                                                                                    <small> Utilizar modo extendido en caso de que un Plug-in tercero requiera visualizar los campos por defecto en la pantalla de Checkout de la tienda </small>
                                                                               </div>


                                                                               <div class="row mb-4">
                                                                                    <label for="horizontal-firstname-input" class="col-sm-3 col-form-label">Permitir continuar venta fuera de cobertura &emsp;<i class="fa fa-info-circle info-dialog" aria-hidden="true" data-title="General" data-body="Permitir continuar la venta fuera de zona de cobertura Moovin"></i></label>
                                                                                        
                                                                                    <div class="col-sm-2">
                                                                                        <input checked  class="switch switch--shadow" id="cf_out_zone" name="cf_out_zone" type="checkbox">
                                                                                        <label for="cf_out_zone"></label>
                                                                                    </div>

                                                                               </div>


                                                                               <div class="row mb-4">
                                                                                    <label for="horizontal-firstname-input" class="col-sm-3 col-form-label">Configurar zonas de envio woocommerce </label>
                                                                                        
                                                                                    <div class="col-sm-2">
                                                                                        <input checked  class="switch switch--shadow" id="cf_woocommerce_zone" name="cf_woocommerce_zone" type="checkbox">
                                                                                        <label for="cf_woocommerce_zone"></label>
                                                                                    </div>
                                                                                    <label for="horizontal-firstname-input" class="col-sm-2 col-form-label">Automatico &emsp;<i class="fa fa-info-circle info-dialog" aria-hidden="true" data-title="General" data-body="Moovin automáticamente configura las zonas de envió Woocommerce en la tienda"></i></label>

                                                                               </div>

                                                                            <div>
                                                                                <button type="button" class="btn btn-primary w-md" id="save-general" >Guardar</button>
                                                                            </div>

                                                                        </form>
                                                                     </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                       
                                                        

                                                    </div>
                                        
                                                
                                            </div>

                                            <div class="tab-pane" id="zone" role="tabpanel">
                                                <p>Te mostramos nuestra zona de cobertura </p>
                                                    <form>
                                                        <div class="row">
                                                            <div class="col-sm-12">
                                                                <div id="mapzone" style="width: 100%; height: 300px;"></div>
                                                            </div>
                                                            </div>
                                                    </form>
                                            </div>

                                           
                                 
                                            
                                        </div>
        
                                    </div>
                                </div>
                            </div>
                       </div>
            </div> <!-- container-fluid -->
        </div>
        <!-- End Page-content -->
    </div>
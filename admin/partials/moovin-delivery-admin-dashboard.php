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

                                <h4 class="card-title">Rastreo de paquetes</h4>
                                <p class="card-title-desc"></p>

                                <div class="float-lg-right pb-2">

                                <div class="row mb-4">
                                    <label for="horizontal-firstname-input" class="col-sm-3 col-form-label">ID paquete</label>
                                    <div class="col-sm-9">
                                    <input type="text" class="form-control" id="cf_package">
                                    </div>
                                </div>
                                <div class="col-sm-9">
                                    <div>
                                        <button type="button" class="btn btn-primary w-md" id="tracking-package">Buscar</button>
                                    </div>
                                </div>

                                </div>
                                
                            </div>
                        </div>
                        
                    </div> <!-- end col -->
                    <div class="col-12" id="div-tracking">
                        <iframe id="result-tracking" frameborder="0" style="overflow:hidden;height:800px;width:100%" height="800px" width="100%">
                    </div>
                </div> <!-- end row -->

            </div> <!-- container-fluid -->
        </div>
        <!-- End Page-content -->
    </div>
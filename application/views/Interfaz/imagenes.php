<script type="text/javascript">
$(function(){

    $(document).on('submit', '.eliminarImgPost', function(event){
            var url = "<?php echo base_url();?>";
            var formdelete = $(this);
                $.ajax({
                url: $(".eliminarImgPost").attr('action')+"?"+$.now(),
                type:"POST",
                dataType: "json",
                data:formdelete.serialize(),
                processData:false,
                success:function(data){
                        if(data.res == "ok"){
                            console.log(data);
                        }
                        else
                        if(data.res == "error"){
                            console.log(data);
                        }
                    }
                });
                return false;
        });
});
</script>

<div class="container-central col-12 col-sm-12 col-md-12 col-lg-6 col-xl-6">
        <!--<div>
            <h5>Perfil</h5>
        </div>
        <?php echo form_open_multipart("eliminarImgPerfil",array("id"=>"eliminarImgPerfil","class"=>"eliminarImgPerfil"))?>
            <div class="row" style="padding:0 15px">
            <?php foreach($imgperfil as $per):?>
                <div>
                    <div class="dropdown d-flex justify-content-end">
                        <button class="btn boton-drop-elimi dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            x
                        </button>
                        <div class="dropdown-menu menu-eliminar" aria-labelledby="dropdownMenuButton">
                            <button class="dropdown-item link-drop-elimi btn-eliminar" type="submit">Eliminar Foto</button>
                        </div>
                    </div>
                    <img class="img-seccion" src="<?php echo base_url()?>assest/imagenes/perfil/<?php echo $per["fotop"]?>" alt="">
                </div>
            <?php endforeach?>
            </div>
        <?php echo form_close();?>
        <hr>-->
        <div>
            <h5>Publicaciones</h5>
        </div>
        <?php echo form_open_multipart("eliminarImgPost",array("id"=>"eliminarImgPost","class"=>"eliminarImgPost"))?>
        <?php $id_pub=$usu["id"]; $clave = $this->encryption->encrypt($id_pub);?>
        <input type="hidden" name="id_publicimgp" value="<?php echo $clave?>">
        <div class="row" style="padding:0 15px">
        <?php foreach($imgpost as $post):?>
            <div class="mr-3">
                <!--<div class="dropdown d-flex justify-content-end">
                    <button class="btn boton-drop-elimi dropdown-toggle" type="button" id="dropdownMenuButton2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        x
                    </button>
                    <div class="dropdown-menu menu-eliminar" aria-labelledby="dropdownMenuButton2">
                        <button class="dropdown-item link-drop-elimi btn-eliminarImg" type="submit">Eliminar Foto</button>
                    </div>
                </div>-->
                <img class="img-seccion" src="<?php echo base_url()?>assest/imagenes/subidas/<?php echo $post["imagen"]?>" alt="">
            </div>
        <?php endforeach?>
        </div>
        <?php echo form_close();?>
</div>
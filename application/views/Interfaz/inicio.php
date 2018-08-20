<script type="text/javascript">
$(function(){

    /* ---- Creacion de publicacion con AJAX ----*/
    $(document).on('submit', '#postAnonimo',function(event) {
      var url="<?php echo base_url()?>";
      var formElement = document.querySelector("#postAnonimo");
      var formData = new FormData(formElement);
        $.ajax({
            url: $('#postAnonimo').attr('action')+"?"+$.now(),  
            type: 'POST',
            data: formData,
            cache: false,
            processData: false,
            dataType: "json",
            contentType : false,
            success: function (data) {
              if(data.res == "error"){
                 $(".btn-post").attr("disabled", false);
                  $.notify(data.msg, {
                    className:'error',
                    globalPosition: 'top right',
                    autoHideDelay:5000
                  });
              }else if(data.res == "ok"){ 
                  for(datito in data.datos){
                            var html = '<div class="col container-post border-post">'
                                + '<div class="perfil-post">'
                                + '<img class="perfil mr-2" src="<?php echo base_url()?>assest/imagenes/login1.png" alt="">'
                                + '<span id="nombreP">'+data.datos[datito].nombre+'</span>'
                                + '</div>'
                                + '<p id="p-post" class="p-post">'+data.datos[datito].contenido+'</p>'    
                                + '<div class="row">'
                                + '<div class="col-6 container-button-post d-flex justify-content-start">'
                                + '<button type="submit" class="btn btn-secondary form-control">Me gusta</button>'
                                + '<button type="submit" class="btn btn-secondary form-control">No me gusta</button>'
                                + '<button type="submit" class="btn btn-secondary form-control">Comentar</button>'
                                + '</div>'
                                + '<div class="col-6 d-flex justify-content-end align-items-center">'
                                + '<a class="pr-2" href=""><i class="far fa-thumbs-up"></i></a>'
                                + '<span></span>'
                                + '<a class=" pr-2 pl-2" href=""><i class="far fa-thumbs-down"></i></a>'
                                + '<span></span>'
                                + '</div>'
                                + '</div>';
                            $("#publicar").html(html);
                  }
                $(".btn-post").attr("disabled", false);
                $.notify(data.msg, {
                  className:'success',
                  globalPosition: 'top right',
                  autoHideDelay:5000
                });
                $('#postAnonimo')[0].reset();
                $("#id_post").val(""); 
              }
            }
      });
      return false; 
  });

        /* -------- FUNCION PARA ME GUSTA ----------- */
        $(document).on('submit', '#meGusta', function(event){
            var url="<?php echo base_url()?>";
            var formElement = document.querySelector("#meGusta");
            var formData = new FormData(formElement);
                $.ajax({
                    url: $('#meGusta').attr('action')+"?"+$.now(),  
                    type: 'POST',
                    data: formData,
                    cache: false,
                    processData: false,
                    dataType: "json",
                    contentType : false,
                    success: function (data) {
                    if(data.res == "error"){
                        $(".btn-megusta").attr("disabled", false);
                    }else if(data.res == "ok"){
                        var htmlmg = '<div>'
                            + '<a class="pr-2 icon1" href="#"><i class="far fa-thumbs-up"></i></a>'
                            +  '<span>'+data.datos+'</span>'
                            +  '</div>'
                            +  '<div>'
                            +  '<a class=" pr-2 pl-2" href="#"><i class="far fa-thumbs-down"></i></a>'
                            +  '</div>'; 
                        $("#mg").html(htmlmg);
                        $(".btn-megusta").attr("disabled", false); 
                    }
                }
            });
            return false; 
        });
        

});
</script>

<!-- Muro -->
<div class="container-central col-12 col-sm-12 col-md-12 col-lg-6 col-xl-6">
    <?php echo form_open_multipart("postAnonimo",array("id"=>"postAnonimo","class"=>"postAnonimo"))?>
    <input type="hidden" name="id_post" id="id_post">
    <div class="col container-public p-cero">
        <input type="text" class="form-control form-control-sm mb-3" style="text-indent:15px" autofocus placeholder="ingrese un nombre" name="nombre" id="nombre" autocomplete="off">
        <textarea name="contenido" id="contenido" class="textarea-post" placeholder="Escriba lo que desee..." cols="30" rows="10"></textarea>
        <button type="submit" name="Comentar" id="Comentar" class="btn btn-primary btn-post">Publicar</button>
        <hr>
    </div> 
    <?php echo form_close();?>
    
    <div id="publicar"></div>
    <?php if(!empty($posteo)): ?>
    <?php
    foreach($posteo as $post):
    ?>
    <div class="col container-post border-post">
        <div class="perfil-post" id="post-p">
            <img class="perfil mr-2" src="<?php echo base_url()?>assest/imagenes/login1.png" alt="">
            <span id="nombreP"><?php echo $post["nombre"]?></span>
        </div>
        <p id="p-post" class="p-post"><?php echo $post["contenido"]?></p>
        <div class="row">
                <div class="col-md-10 col-lg-6 block-comment container-button-post d-flex justify-content-start">
                    <?php echo form_open_multipart("meGusta",array("id"=>"meGusta","class"=>"meGusta"))?>
                        <input type="hidden" name="id_megusta" id="id_megusta">
                        <input type="hidden" name="id_publicacion" value="<?php echo $post["id_publi"]?>" id="publicacion">
                        <button type="submit" name="publicacion" class="btn btn-secondary form-control btn-megusta">Me gusta</button>
                    <?php echo form_close();?>
                    <button type="submit" name="no_me_gusta" class="btn btn-secondary form-control">No me gusta</button>
                    <button type="submit" class="btn btn-secondary form-control">Comentar</button>
                </div>
                <div id="mg" class="block-likes col-md-2 col-lg-6 d-flex justify-content-end align-items-center">
                    <div>
                        <a class="pr-2 icon1" href="#"><i class="far fa-thumbs-up"></i></a>
                        <span><?php echo $post["mgustas"]?></span>
                    </div>
                    <div>
                        <a class=" pr-2 pl-2" href="#"><i class="far fa-thumbs-down"></i></a>
                    </div>
                </div>
        </div>
        
    </div>
    <?php
    endforeach;
    ?> 
    <?php endif; ?>
</div>

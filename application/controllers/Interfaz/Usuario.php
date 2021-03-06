<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Usuario extends CI_Controller {

    public function __construct(){
		parent::__construct();
        
        $this->load->model("usuario/usuariomodel", "usu");
        $this->load->model("anonimo/anonimomodel");
    }
    
	public function index()
	{
        if(!$this->session->userdata("procesoLogin")){
            redirect(base_url());
        }
        $id = $this->session->userdata("id");
        //$encuesta = $this->session->userdata("id_encue");
        $contenido2 = array(
            'titulo' => "Portal Usuario", 
            'contenido2' => "usuario",
            'publicaciones' => $this->usu->countPublicaciones($id),
            'countMg' => $this->usu->cantidadMg($id),
            'countNoMg' => $this->usu->cantidadNoMg($id),
            'countComentarios' => $this->usu->cantidadComentarios($id),
            'posteos_usu' => $this->usu->mostrarMuroUsuario($id),
            'fotoperfil' => $this->usu->ImagenPerfil($id),
            'encuesta' => $this->usu->mostrarEncuesta($id)
        );
        $this->load->view('plantilla/plantilla2', $contenido2);
    }

    public function MuroAnonimo(){
        $id = $this->session->userdata("id");
        $contenido3 = array(
            'titulo' => "Portal General", 
            'contenido3' => "anonimo",
            'publicaciones' => $this->usu->countPublicaciones($id),
            'countMg' => $this->usu->cantidadMg($id),
            'countNoMg' => $this->usu->cantidadNoMg($id),
            'countComentarios' => $this->usu->cantidadComentarios($id),
            'posteo'=> $this->anonimomodel->mostrarMuroAnonimo(),
            'fotoperfil' => $this->usu->ImagenPerfil($id)
        );
        $this->load->view('plantilla/plantilla3', $contenido3);
    }

    public function MuroUsuarios(){
        $id = $this->session->userdata("id");
        $contenido6 = array(
            'titulo' => "Muro Usuarios", 
            'contenido6' => "muroUsuarios",
            'publicaciones' => $this->usu->countPublicacionesAll(),
            'countMg' => $this->usu->cantidadMgAll(),
            'countNoMg' => $this->usu->cantidadNoMgAll(),
            'countComentarios' => $this->usu->cantidadComentariosAll(),
            'posteosUsu'=> $this->usu->mostrarMuroUsuarioGeneral(),
            'fotoperfil' => $this->usu->ImagenPerfil($id)
        );
        $this->load->view('plantilla/plantilla6', $contenido6);
    }

    public function seccionImgPerfil(){
        $id = $this->session->userdata("id");
        $contenido7 = array(
            'titulo' => "Imagenes", 
            'contenido7' => "imagenes",
            'publicaciones' => $this->usu->countPublicaciones($id),
            'countMg' => $this->usu->cantidadMg($id),
            'countNoMg' => $this->usu->cantidadNoMg($id),
            'countComentarios' => $this->usu->cantidadComentarios($id),
            'imgperfil'=> $this->usu->seccionImagenesPerfil($id),
            'imgpost'=> $this->usu->seccionImagenesPost($id),
            'fotoperfil' => $this->usu->ImagenPerfil($id)
        );
        $this->load->view('plantilla/plantilla7', $contenido7);
    }

    public function postUsuario(){
        if($this->input->is_ajax_request()){
            $id_post_usu=$this->security->xss_clean(strip_tags($this->input->post("id_post_usuario")));
            $usuario=$this->security->xss_clean(strip_tags($this->input->post("id_usuario")));
            $id_usu=$this->encryption->decrypt($usuario);
            $contenido_usuario=$this->security->xss_clean(strip_tags($this->input->post("contenido_usuario")));
            date_default_timezone_set("America/Santiago");

            $data_insert=array(
                "id_usuario"=>$id_usu,
                "contenido"=>$contenido_usuario,
                "fecha" => date("Y-m-d G:i:s")
            );

            if($this->form_validation->run("postUsuario") == FALSE){
                echo json_encode(array('res'=>"error", 'msg' => strip_tags(validation_errors())));exit;
            }else{
                if($id_post_usu ==""){
                    if($data=$this->usu->InsertarPostUsuario($data_insert)){

                        $config = [
                            "upload_path" => "./assets/imagenes/subidas",
                            "allowed_types" => "png|jpg|jpeg|gif"
                        ];
            
                        $this->load->library("upload", $config);
                        
                        $this->upload->do_upload('uploadimagen');
            
                        if(!$config["allowed_types"]){
                            echo json_encode(array('res'=>"error", 'msg' => "Formato de imagen no válido"));exit;
                        }
            
                        $imagen = array("upload_imagen" => $this->upload->data());
                        $data_imagen_ins=array(
                            "imagen"=>$imagen['upload_imagen']['file_name'],
                            "id_usuario"=>$id_usu,
                            "id_pub" => $data
                        );
            
                        $this->usu->insertarImagenPost($data_imagen_ins);
                        
                        $this->archivos($id_usu,$data);

                        echo json_encode(array('res'=>"ok", 'msg' => "publicacion realizada con éxito"));
                    }else{
                        echo json_encode(array('res'=>"error", 'msg' => "No se ha podido publicar"));
                    }
                }
            }

        }
    }

    public function archivos($id_usu,$data){
        $config = [
            "upload_path" => "./assets/imagenes/archivos",
            "allowed_types" => "pdf|doc|docx|xls"
        ];

        $this->load->library("upload", $config);
        
        $this->upload->do_upload('uploadarchivo');

        if(!$config["allowed_types"]){
            echo json_encode(array('res'=>"error", 'msg' => "Formato de imagen no válido"));exit;
        }

        $archivo = array("upload_archivo" => $this->upload->data());
        $data_archivo_ins=array(
            "archivo"=>$archivo['upload_archivo']['file_name'],
            "id_usuario"=>$id_usu,
            "id_pub" => $data
        );

        $this->usu->insertarArchivo($data_archivo_ins);
    }

    public function eliminarPost(){
        if($this->input->is_ajax_request()){
            $id_publi=$this->security->xss_clean(strip_tags($this->input->post("id_pu")));
            $id_pu=$this->encryption->decrypt($id_publi);
            $imagen=$this->usu->mostrarImagenPost($id_pu);

            if($data=$this->usu->eliminarPublicacion($id_pu)){
                $this->usu->eliminarImgPost($id_pu);
                unlink("./assets/imagenes/subidas/".$imagen);
                echo json_encode(array('res'=>"ok", 'datos' => $data));exit;
            }else{
                echo json_encode(array('res'=>"error", 'datos' => $data));exit;
            }

        }
    }

    public function eliminarImgPost2(){
        if($this->input->is_ajax_request()){
            $id_publi=$this->security->xss_clean(strip_tags($this->input->post("id_publicimgp")));
            $id_pu=$this->encryption->decrypt($id_publi);
            $id_usu = $this->session->userdata("id");
            $imagen=$this->usu->mostrarImagenPost2($id_usu,$id_pu);

           if($data=$this->usu->eliminarImgPost2($id_usu,$id_pu)){
            unlink("./assets/imagenes/subidas/".$imagen);
            echo json_encode(array('res'=>"ok", 'datos' => $data));exit;
            }else{
                echo json_encode(array('res'=>"error", 'datos' => $data));exit;
            }
        }
    }

    public function ComentariosUsu(){
        if($this->input->is_ajax_request()){
            $id_comment=$this->security->xss_clean(strip_tags($this->input->post("id_commentusu")));
            $id_publicacionusu=$this->security->xss_clean(strip_tags($this->input->post("id_publicacionusu")));
            $id_pusu=$this->encryption->decrypt($id_publicacionusu);
            $comentario=$this->security->xss_clean(strip_tags($this->input->post("comentariousu")));
            $id_usu = $this->session->userdata("id");

                $datos_insert = array(
                    "com_id_usu" => $id_pusu,
                    "id_usuario_com" => $id_usu,
                    "comentario_usu" => $comentario
                );

                if($this->form_validation->run("ComentariosUsu") == FALSE){
                    echo json_encode(array('res'=>"error", 'msg' => strip_tags(validation_errors())));exit;
                }else{
                    if($data=$this->usu->insertarComentario($datos_insert)){
                        $datos=$this->usu->mostrarComentarioUsuario($data,$id_pusu,$id_usu);
                        echo json_encode(array('res' => "ok", 'datos' => $datos));
                    }else{
                        echo json_encode(array('res'=>"error"));exit;
                    }
                }
            
        }
    }


    public function mostrarComPublicadosUsu(){
        $id_publicacion=$this->security->xss_clean(strip_tags($this->input->post("id_publicacionshowusu")));
        $id_pu=$this->encryption->decrypt($id_publicacion);
        $id_usu=$this->session->userdata("id");
        
        $data=$this->usu->mostrarComPublicadosUsu($id_pu);
        if($data){
            echo json_encode(array('res'=>"ok", 'datos' => $data));exit;
        }else{
            echo json_encode(array('res'=>"error", 'msg' => ERROR_MSG));exit;
        }	

}

    public function meGustaUsu(){
        if($this->input->is_ajax_request()){
            $id_publi=$this->security->xss_clean(strip_tags($this->input->post("id_publimg")));
            $id_pub=$this->encryption->decrypt($id_publi);
            $id_usu=$this->session->userdata("id");
            $ip = $this->input->ip_address();

            $datos_insert = array(
                "mg_id_usu" => $id_pub,
                "id_usuario_mg" =>  $id_usu,
                "mg_ip_usu" => $ip
            );

            $verusumg = $this->usu->verificarIpmgUsu($id_pub,$id_usu);
            $verusunmg = $this->usu->verificarIpnmgUsu($id_pub,$id_usu);
            if($verusumg == false && $verusunmg == false){
                if($this->usu->insertarMeGusta($datos_insert)){
                    $data=$this->usu->mostrarMgUsu($id_pub);
                    echo json_encode(array('res' => "ok",'datos' => $data));
                }else{
                    echo json_encode(array('res'=>"error"));exit;
                }
            }
            else{
                echo json_encode(array('res2'=>"error"));
            }   
        }
    }

    public function NomeGustaUsu(){
        if($this->input->is_ajax_request()){
            $id_public=$this->security->xss_clean(strip_tags($this->input->post("id_publinomg")));
            $id_pu=$this->encryption->decrypt($id_public);
            $id_usu=$this->session->userdata("id");
            $ip = $this->input->ip_address();

            $datos_insert = array(
                "nmg_id_usu" => $id_pu,
                "id_usuario_nmg" => $id_usu,
                "nmg_ip" => $ip
            );

            $verusunmg = $this->usu->verificarIpnmgUsu($id_pu,$id_usu);
            $verusumg = $this->usu->verificarIpmgUsu($id_pu,$id_usu);
            if($verusunmg == false && $verusumg == false){
                if($this->usu->insertarNoMeGusta($datos_insert)){
                    $data=$this->usu->mostrarNoMgUsu($id_pu);
                    echo json_encode(array('res' => "ok", 'datos' => $data));
                }else{
                    echo json_encode(array('res'=>"error"));exit;
                }
            }
            else{
                echo json_encode(array('res2'=>"error"));
            }   
            
        }
    }

    public function redirectEditarPerfil(){
        $id = $this->session->userdata("id");
        $contenido4 = array(
            'titulo' => "Edición de Perfil",
            'contenido4' => "editarUsuario",
            'publicaciones' => $this->usu->countPublicaciones($id),
            'countMg' => $this->usu->cantidadMg($id),
            'countNoMg' => $this->usu->cantidadNoMg($id),
            'countComentarios' => $this->usu->cantidadComentarios($id),
            'fotoperfil' => $this->usu->ImagenPerfil($id),
            //'usuario' => $this->session->userdata("procesoLogin");
        );
        $this->load->view("plantilla/plantilla4", $contenido4);
    }

    public function editarPerfil(){
        if($this->input->is_ajax_request()){
            $id_usuario=$this->security->xss_clean(strip_tags($this->input->post("id_usuario")));
            $nombre=$this->security->xss_clean(strip_tags($this->input->post("nombre")));
            $apellidos=$this->security->xss_clean(strip_tags($this->input->post("apellidos")));

            $config = [
                "upload_path" => "./assets/imagenes/perfil",
                "allowed_types" => "png|jpg|jpeg",
                "overwrite" => TRUE
            ];
            $this->load->library("upload", $config);

            $this->upload->do_upload('imagenGrande');

            if(!$config["allowed_types"]){
                echo json_encode(array('res'=>"error", 'msg' => "Solo se aceptan imagenes png, jpg y jpeg"));exit;
            }

            $imagen = array("imagenGrande" => $this->upload->data());

            $data_insert=array(
                "nombre"=>$nombre,
                "apellidos"=>$apellidos,
                "foto_perfil"=>$imagen['imagenGrande']['file_name']
            );

            if($this->form_validation->run("EditarPerfil") == FALSE){
                echo json_encode(array('res'=>"error", 'msg' => strip_tags(validation_errors())));exit;
            }else{
                if($this->usu->actualizarUsuario($id_usuario, $data_insert)){
                    $nuevo=$this->usu->mostrarUsuario($id_usuario);
                    echo json_encode(array('res'=>"ok", 'datos' => $nuevo));exit;
                }else{
                    echo json_encode(array('res'=>"error", 'msg' => ERROR_MSG));exit;
                }
            } 
        }
    }

    public function mostrarUsuarios(){
        $id = $this->session->userdata("id");
        $contenido5 = array(
            'titulo' => "Usuarios",
            'contenido5' => "personas",
            'publicaciones' => $this->usu->countPublicaciones($id),
            'countMg' => $this->usu->cantidadMg($id),
            'countNoMg' => $this->usu->cantidadNoMg($id),
            'countComentarios' => $this->usu->cantidadComentarios($id),
            'fotoperfil' => $this->usu->ImagenPerfil($id),
            'usuarios' => $this->usu->mostrarUsuarios()
        );
        $this->load->view("plantilla/plantilla5", $contenido5);
    }

    public function encuesta(){
        if($this->input->is_ajax_request()){
            $titulo=$this->security->xss_clean(strip_tags($this->input->post("titulo")));
            $id_usuario=$this->security->xss_clean(strip_tags($this->input->post("id_usuencuesta")));
            $id_usu=$this->encryption->decrypt($id_usuario);
            $opcion=$this->security->xss_clean($this->input->post("texto1[]"));

            $data_insert = array(
                "id_usu_encu" => $id_usu,
                "titulo" => $titulo,
            );

            if($encu=$this->usu->insertarEncuesta($data_insert)){
               
                $valorop = array();
                foreach($opcion as $op){
                    array_push($valorop, array(
                        "encu_id" => $encu,
                        "opciones" => $op
                    ));
                }

                /*$id_encu = array(
                    "id_encue" => $encu
                );

                $encuesta=$this->session->set_userdata($id_encu);
                $this->session->userdata($encuesta);*/
    
                if($this->usu->insertarOpcionesEncu($valorop)){
                    //echo json_encode(array('res' => "ok", 'datoso' => $valorop));    
                }
                else{
                    echo json_encode(array('res' => "error"));
                }
                $opc=$this->usu->mostrarOpciones($encu);
                echo json_encode(array('res' => "ok", 'datose' => $opc));

            }else{

            }
        }
    }

}

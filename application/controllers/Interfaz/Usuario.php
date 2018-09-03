<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Usuario extends CI_Controller {

    public function __construct(){
		parent::__construct();
        
        $this->load->model("Usuario/UsuarioModel", "usu");
    }
    
	public function index()
	{
        $contenido2 = array(
            'titulo' => "Portal Usuario", 
            'contenido2' => "usuario",
            'posteos_usu' => $this->usu->mostrarMuroUsuario2() 
        );
        $this->load->view('plantilla/plantilla2', $contenido2);
    }

    public function postUsuario(){
        if($this->input->is_ajax_request()){
            $usuario=$this->security->xss_clean(strip_tags($this->input->post("id_usuario")));
            $contenido_usuario=$this->security->xss_clean(strip_tags($this->input->post("contenido_usuario")));

            $config = [
                "upload_path" => "./assest/imagenes/subidas",
                "allowed_types" => "png|jpg"
            ];
            $this->load->library("upload", $config);

            $this->upload->do_upload('uploadimagen'); //método que sube archivos

            $imagen = array("upload_imagen" => $this->upload->data());
            $data_insert=array(
                "id_usuario"=>$usuario,
                "contenido"=>$contenido_usuario,
                "imagen"=>$imagen['upload_imagen']['file_name']
            );

            if($this->form_validation->run("postUsuario") == FALSE){
                echo json_encode(array('res'=>"error", 'msg' => strip_tags(validation_errors())));exit;
            }else{
                if($id_post_usuario ==""){
                    if($this->usu->InsertarPostUsuario($data_insert)){
                        echo json_encode(array('res'=>"ok", 'msg' => "publicacion realizada con éxito"));exit;
                    }else{
                        echo json_encode(array('res'=>"error", 'msg' => "No se ha podido publicar"));exit;
                    }
                }
            }

        }
    }

    public function meGustaUsu(){
        if($this->input->is_ajax_request()){
            $id_usuario=$this->security->xss_clean(strip_tags($this->input->post("id_usuariomg")));
            $ip = $this->input->ip_address();

            $datos_insert = array(
                "mg_id_usu" => $id_usuario,
                "mg_ip_usu" => $ip
            );

            if($this->usu->insertarMeGusta($datos_insert)){
                $data=$this->usu->mostrarMgUsu($id_usuario);
                echo json_encode(array('datos' => $data));
            }else{
                echo json_encode(array('res'=>"error"));exit;
            }
        }
    }

}

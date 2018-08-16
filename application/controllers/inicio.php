<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Inicio extends CI_Controller {

    public function __construct(){
		parent::__construct();
        
        $this->load->model("InicioModel");
    }
    
	public function index()
	{
        $contenido = array(
            'titulo' => "Portal", 
            'contenido' => "Inicio",
            'posteo' => $this->InicioModel->mostrarMuroAnonimo()
        );
        $this->load->view('plantilla/plantilla', $contenido);
    }

    public function login(){
        $datos = array(
            'titulo' => "Login"
        );
        $this->load->view('login', $datos);
    }

    public function registro(){
        $datos = array(
            'titulo' => "Registro"
        );
        $this->load->view('registro', $datos);
    }

    public function procesoRegistro(){
        if($this->input->is_ajax_request()){
            $id_usuario=$this->security->xss_clean(strip_tags($this->input->post("id_usuario")));
            $nombre=$this->security->xss_clean(strip_tags($this->input->post("nombre")));
            $apellidos=$this->security->xss_clean(strip_tags($this->input->post("apellidos")));
            $correo=$this->security->xss_clean(strip_tags($this->input->post("correo")));
            $pass=$this->security->xss_clean(strip_tags($this->input->post("pass")));
            $pass2=sha1($pass);

            if ($this->form_validation->run("registro") == FALSE){
                echo json_encode(array('res'=>"error", 'msg' => strip_tags(validation_errors())));exit;
            }else{	

                $data_insert=array(
                    "nombre"=>$nombre,
                    "apellidos"=>$apellidos,
                    "correo"=>$correo,
                    "contrasehna"=>$pass2
                );

                if($id_usuario==""){
                    if($this->InicioModel->insertarUsuario($data_insert)){
                        echo json_encode(array('res'=>"ok", 'msg' => OK_MSG));exit;
                    }else{
                        echo json_encode(array('res'=>"error", 'msg' => ERROR_MSG));exit;
                    }
                }    
            }
        }    
    }


    public function postAnonimo(){
        if($this->input->is_ajax_request()){
            $id_post=$this->security->xss_clean(strip_tags($this->input->post("id_post")));
            $nombre=$this->security->xss_clean(strip_tags($this->input->post("nombre")));
            $contenido=$this->security->xss_clean(strip_tags($this->input->post("contenido")));

            $data_insert=array(
                "nombre"=>$nombre,
                "contenido"=>$contenido
            );

            if($this->form_validation->run("postAnonimo") == FALSE){
                echo json_encode(array('res'=>"error", 'msg' => strip_tags(validation_errors())));exit;
            }else{
                if($id_post ==""){
                    if($this->InicioModel->insertarPostAnonimo($data_insert)){
                        echo json_encode(array('res'=>"ok", 'msg' => "publicacion realizada con éxito", 'datos' => $data_insert));exit;
                    }else{
                        echo json_encode(array('res'=>"error", 'msg' => "No se ha podido publicar"));exit;
                    }

                }
            }
        }                            
    }

    public function meGusta(){
        if($this->input->is_ajax_request()){
            $id_megusta=$this->security->xss_clean(strip_tags($this->input->post("id_megusta")));
            $id_publicacion=$this->security->xss_clean(strip_tags($this->input->post("id_publicacion")));
            $ip=$this->input->ip_address();

                $datos_insert = array(
                    "id_publicacion" => $id_publicacion,
                    "ip" => $ip
                );
            
                if($this->InicioModel->insertarMeGusta($datos_insert)){
                    $data=$this->InicioModel->countMg($id_publicacion);
                    echo json_encode(array('res'=>"ok", 'datos' => $data));exit;
                }else{
                    echo json_encode(array('res'=>"error"));exit;
                }
            
        }
    }

    public function mostrarMegusta(){
        if($this->input->is_ajax_request()){
            $id_publicacion=$this->security->xss_clean(strip_tags($this->input->post("id_publicacion")));
            $data=$this->InicioModel->mostrarMuroAnonimo($id_publicacion);
            if($data){
                echo json_encode(array('res'=>"ok", 'datos' => $data));exit;
            }else{
                echo json_encode(array('res'=>"error", 'msg' => ERROR_MSG));exit;
            }	
        }else{
            exit('No direct script access allowed');
        }
    }

    public function mostrarMuroAn(){
            $id_publicacion=$this->security->xss_clean(strip_tags($this->input->post("id_publicacion")));
            return $this->InicioModel->mostrarMuroAnonimo($id_publicacion);
    }
}

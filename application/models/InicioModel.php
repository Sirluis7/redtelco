<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class InicioModel extends CI_Model{

    public function __construct(){
		parent::__construct();
    }

    public function insertarUsuario($datos){
      if($this->db->insert('usuarios', $datos)){
				$insert_id = $this->db->insert_id();
				return $insert_id;
			}
			return FALSE;
    }
    
    public function insertarPostAnonimo($datos){
      if($this->db->insert('publicaciones_anonimos', $datos)){
        $insert_id = $this->db->insert_id();
				return $insert_id;
			}
			return FALSE;
    }


    /* DEVUELVE TODAS LAS PUBLICACIONES JUNTO SUS ME GUSTAS, NO ME GUSTAS Y COMENTARIOS */

    public function mostrarMuroAnonimo(){
      $query=$this->db->query('SELECT p.id as id_publi, p.nombre as nombre, p.contenido as contenido,
       (SELECT COUNT(*) FROM me_gusta_anonimos mg WHERE mg.mg_id_publicacion = p.id) as mgustas,
       (SELECT COUNT(*) FROM no_me_gusta_anonimos ng WHERE ng.nmg_id_publicacion = p.id) as nmgustas
       FROM publicaciones_anonimos as p ORDER BY id_publi DESC');
      return $query->result_array();
    }

    public function mostrarComPublicados($id){
      $this->db->select('com.comentario as comments');
      $this->db->from('comentarios_anonimos as com');
      $this->db->join('publicaciones_anonimos as p', 'p.id = com.com_id_publicacion', 'left');
      $this->db->where('p.id', $id);
      $res=$this->db->get();
      if($res->num_rows()>0){
        return $res->result_array();
			}
      return FALSE;
    }


    public function mostrarPostAnonimo($id){
      $this->db->select('id, nombre, contenido');
      $this->db->where('id', $id);
      $res = $this->db->get('publicaciones_anonimos');
      if($res->num_rows()>0){
        return $res->result_array();
			}
      return FALSE;
    }

    public function insertarMeGusta($datos){
      if($this->db->insert('me_gusta_anonimos',$datos)){
        $insert_id = $this->db->insert_id();
				return $insert_id;
			}
			return FALSE;
    }
    
    public function insertarNoMeGusta($datos){
      if($this->db->insert('no_me_gusta_anonimos',$datos)){
        $insert_id = $this->db->insert_id();
				return $insert_id;
			}
			return FALSE;
    }

    public function insertarComentario($datos){
      if($this->db->insert('comentarios_anonimos', $datos)){
        $insert_id = $this->db->insert_id();
				return $insert_id;
			}
			return FALSE;
    }

    /*public function mostrarComentarioAnonimo(){
      $this->db->select('contenido');
      $this->db->order_by('id','DESC');
      $res = $this->db->get('comentarios_anonimos');
      if($res->num_rows()>0){
        return $res->result_array();
			}
      return FALSE;
    }*/

    public function mostrarComentarioAnonimo($id){
      $this->db->select('comentario');
      $this->db->where('com_id_publicacion', $id);
      $res = $this->db->get('comentarios_anonimos');
      if($res->num_rows()>0){
        return $res->result_array();
			}
      return FALSE;
    }

    public function mostrarMg($id){
      $this->db->select('id_mg');
      $this->db->where('mg_id_publicacion', $id);
      $res=$this->db->count_all_results('me_gusta_anonimos');
      return $res;
      
    }

    public function mostrarNoMg($id){
      $this->db->select('id_nmg');
      $this->db->from('no_me_gusta_anonimos');
      $this->db->where('nmg_id_publicacion', $id);
      $res=$this->db->count_all_results();
      return $res;
      
    }

    /* DEVUELVE EL TOTAL DE PUBLICACIONES */
    
    public function countPublicaciones(){
      $this->db->select('id');
      $this->db->from('publicaciones_anonimos');
      $res=$this->db->count_all_results();
      return $res;
    }

    public function cantidadMg(){
      $this->db->select('id_mg');
      $this->db->from('me_gusta_anonimos');
      $res=$this->db->count_all_results();
      return $res;
    }

    public function cantidadNoMg(){
      $this->db->select('id_nmg');
      $this->db->from('no_me_gusta_anonimos');
      $res=$this->db->count_all_results();
      return $res;
    }

    public function cantidadComentarios(){
      $this->db->select('id_com');
      $this->db->from('comentarios_anonimos');
      $res=$this->db->count_all_results();
      return $res;
    }

}
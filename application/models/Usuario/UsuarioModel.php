<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class UsuarioModel extends CI_Model{

    public function __construct(){
		parent::__construct();
    }

    public function InsertarPostUsuario($datos){
      if($this->db->insert('publicaciones_usuarios', $datos)){
        $insert_id = $this->db->insert_id();
        return $insert_id;
      }
      return FALSE;
    }

    public function insertarComentario($datos){
      if($this->db->insert('comentarios_usuarios', $datos)){
        $insert_id = $this->db->insert_id();
				return $insert_id;
			}
			return FALSE;
    }

    public function insertarMeGusta($datos){
      if($this->db->insert('me_gusta_usuarios',$datos)){
        $insert_id = $this->db->insert_id();
				return $insert_id;
			}
			return FALSE;
    }
    
    public function insertarNoMeGusta($datos){
      if($this->db->insert('no_me_gusta_usuarios',$datos)){
        $insert_id = $this->db->insert_id();
				return $insert_id;
			}
			return FALSE;
    }

    public function insertarEncuesta($datos){
      if($this->db->insert('encuesta',$datos)){
        $insert_id = $this->db->insert_id();
				return $insert_id;
			}
			return FALSE;
    }

    public function insertarArchivo($datos){
      if($this->db->insert('archivos_usuarios',$datos)){
        $insert_id = $this->db->insert_id();
				return $insert_id;
			}
			return FALSE;
    }

    public function insertarImagenPost($datos){
      if($this->db->insert('imagen_pub_usu',$datos)){
        $insert_id = $this->db->insert_id();
				return $insert_id;
			}
			return FALSE;
    }

    public function insertarOpcionesEncu($datos){
      if($this->db->insert_batch('opciones_encuesta',$datos)){
        $insert_id = $this->db->insert_id();
				return $insert_id;
			}
			return FALSE;
    }

    public function eliminarPublicacion($id_pu){
      $this->db->where('id',$id_pu);
      $res=$this->db->delete('publicaciones_usuarios');
      if($res){
        return true;
      }else{
        return false;
      }
    }

    public function eliminarImgPost($id_pub){
      $this->db->where('id_pub',$id_pub);
      $res=$this->db->delete('imagen_pub_usu');
      if($res){
        return true;
      }else{
        return false;
      }
    }

    public function eliminarImgPost2($id_usu,$id_pu){
      $this->db->where('id_usuario',$id_usu);
      $this->db->where('id_pub',$id_pu);
      $res=$this->db->delete('imagen_pub_usu');
      if($res){
        return true;
      }else{
        return false;
      }
    }

    public function actualizarUsuario($id,$datos){
      $this->db->select('nombre, apellidos, foto_perfil');
      $this->db->where('id', $id);
      $this->db->update('usuarios', $datos);
      if($this->db->affected_rows()){
        return true;
      }else{
        return false;
      }
    }

    public function mostrarMuroUsuario($id){
      $query=$this->db->query("SELECT p.id as id, p.id_usuario as usuario, p.contenido as contenido,
       im.imagen as imagen, p.fecha as fecha, CONCAT(usu.nombre, ' ' ,usu.apellidos) as 'nombre', usu.foto_perfil as foto,
       (SELECT COUNT(*) FROM me_gusta_usuarios mg WHERE mg.mg_id_usu = p.id) as mgustas,
       (SELECT COUNT(*) FROM no_me_gusta_usuarios ng WHERE ng.nmg_id_usu = p.id) as nmgustas,
       (SELECT COUNT(*) FROM comentarios_usuarios cm WHERE cm.com_id_usu = p.id) as countcomment
       FROM publicaciones_usuarios as p JOIN usuarios usu ON p.id_usuario=usu.id LEFT JOIN imagen_pub_usu im ON p.id=im.id_pub WHERE p.id_usuario = $id ORDER BY id DESC");
      return $query->result_array();
    }

    public function mostrarMuroUsuarioGeneral(){
      $query=$this->db->query("SELECT p.id as id, p.id_usuario as usuario, p.contenido as contenido,
       im.imagen as imagen, p.fecha as fecha, CONCAT(usu.nombre, ' ' ,usu.apellidos) as 'nombre', usu.foto_perfil as foto,
       (SELECT COUNT(*) FROM me_gusta_usuarios mg WHERE mg.mg_id_usu = p.id) as mgustas,
       (SELECT COUNT(*) FROM no_me_gusta_usuarios ng WHERE ng.nmg_id_usu = p.id) as nmgustas,
       (SELECT COUNT(*) FROM comentarios_usuarios cm WHERE cm.com_id_usu = p.id) as countcomment
       FROM publicaciones_usuarios as p JOIN usuarios usu ON p.id_usuario=usu.id LEFT JOIN imagen_pub_usu im ON p.id=im.id_pub ORDER BY id DESC");
      return $query->result_array();
    }

    public function mostrarPostUsuario($id){
      $this->db->select('id, id_usuario, contenido, imagen');
      $this->db->where('id_usuario', $id);
      $res = $this->db->get('publicaciones_usuarios');
      if($res->num_rows()>0){
        return $res->result_array();
			}
      return FALSE;
    }

    public function mostrarComentarioUsuario($id, $idpubli,$id_usu){
      $this->db->select("com.comentario_usu as comentario, usu.foto_perfil as foto, CONCAT(nombre, ' ' , apellidos) as 'nombre'");
      $this->db->from('comentarios_usuarios as com');
      $this->db->join('publicaciones_usuarios as p', 'p.id = com.com_id_usu', 'left');
      $this->db->join('usuarios as usu', 'usu.id = com.id_usuario_com', 'left');
      $this->db->where('com.id_com_usu', $id);
      $this->db->where('com.com_id_usu', $idpubli);
      $this->db->where('com.id_usuario_com', $id_usu);
      $res = $this->db->get();
      if($res->num_rows()>0){
        return $res->result_array();
			}
      return FALSE;
    }

    public function mostrarComPublicadosUsu($id_pu){
      $this->db->select("com.comentario_usu as comments, usu.foto_perfil as foto, CONCAT(nombre, ' ' , apellidos) as 'nombre'");
      $this->db->from('comentarios_usuarios as com');
      $this->db->join('publicaciones_usuarios as p', 'p.id = com.com_id_usu', 'left');
      $this->db->join('usuarios as usu', 'usu.id = com.id_usuario_com', 'left');
      $this->db->where('p.id', $id_pu);
      $this->db->order_by('com.id_com_usu', 'ASC');
      $res=$this->db->get();
      if($res->num_rows()>0){
        return $res->result_array();
			}
      return FALSE;
    }

    public function mostrarMgUsu($id){
      $this->db->select('id_mg_usu');
      $this->db->where('mg_id_usu', $id);
      $res=$this->db->count_all_results('me_gusta_usuarios');
      return $res;
      
    }

    public function verificarIpmgUsu($idpu,$id_usu){
      $this->db->select('mg_ip_usu');
      $this->db->where('mg_id_usu', $idpu);
      $this->db->where('id_usuario_mg', $id_usu);
      $res=$this->db->get('me_gusta_usuarios');
      if($res->num_rows()>0){
        return $res->row_array();
			}
      return FALSE;
    }

    public function mostrarNoMgUsu($id){
      $this->db->select('id_nmg_usu');
      $this->db->where('nmg_id_usu', $id);
      $res=$this->db->count_all_results('no_me_gusta_usuarios');
      return $res;
      
    }

    public function verificarIpnmgUsu($idpu,$id_usu){
      $this->db->select('nmg_ip');
      $this->db->where('nmg_id_usu', $idpu);
      $this->db->where('id_usuario_nmg', $id_usu);
      $res=$this->db->get('no_me_gusta_usuarios');
      if($res->num_rows()>0){
        return $res->row_array();
			}
      return FALSE;
    }

    public function mostrarUsuario($id){
      $this->db->where('id', $id);
      $res=$this->db->get('usuarios');
      if($res->num_rows()>0){
        return $res->result_array();
			}
      return FALSE;
    }

    public function ImagenPerfil($id){
      $this->db->where('id', $id);
      $res=$this->db->get('usuarios');
      $row=$res->row_array();
      return $row["foto_perfil"];
		
    }

    public function mostrarImagenPost($id){
      $this->db->where('id_pub', $id);
      $res=$this->db->get('imagen_pub_usu');
      $row=$res->row_array();
      return $row["imagen"];
    }

    public function mostrarImagenPost2($id_usu,$id_pu){
      $this->db->where('id_usuario', $id_usu);
      $this->db->where('id_pub', $id_pu);
      $res=$this->db->get('imagen_pub_usu');
      $row=$res->row_array();
      return $row["imagen"];
    }

    public function seccionImagenesPerfil($id){
      $this->db->select('foto_perfil as fotop');
      $this->db->where('id', $id);
      $res=$this->db->get('usuarios');
      if($res->num_rows()>0){
        return $res->result_array();
			}
      return FALSE;
    }

    public function seccionImagenesPost($id_usu){
      $this->db->select('im.imagen as imagen, pu.id as id');
      $this->db->from('imagen_pub_usu as im');
      $this->db->join('publicaciones_usuarios as pu', 'pu.id = im.id_pub');
      $this->db->where('im.id_usuario', $id_usu);
      $res=$this->db->get();
      if($res->num_rows()>0){
        return $res->result_array();
			}
      return FALSE;
    }

    public function mostrarUsuarios(){
      $this->db->select("id, CONCAT(nombre, ' ' , apellidos) as 'nombre', foto_perfil as foto");
      $res=$this->db->get('usuarios');
      if($res->num_rows()>0){
        return $res->result_array();
			}
      return FALSE;
    }

    /*public function mostrarEncuesta($id){
      $this->db->select("CONCAT(usu.nombre, ' ' ,usu.apellidos) as 'nombre', usu.foto_perfil as foto, en.titulo as titulo, op.opciones as opciones");
      $this->db->from('usuarios as usu');
      $this->db->join('encuesta as en', 'en.id_usu_encu = usu.id');
      $this->db->join('opciones_encuesta as op', 'op.encu_id = en.id_encu');
      $this->db->where('usu.id', $id);
      $res=$this->db->get();
      if($res->num_rows()>0){
        return $res->result_array();
			}
      return FALSE;
    }*/
    
    /*public function mostrarOpciones($id){
      $this->db->where('encu_id', $id);
      $res=$this->db->get('opciones_encuesta');
      if($res->num_rows()>0){
        return $res->result_array();
			}
      return FALSE;
    }*/

    public function mostrarEncuesta($id){
     $this->db->select('en.titulo as titulo, op.opciones as opciones');
     $this->db->from('usuarios as usu');
     $this->db->join('encuesta as en', 'en.id_usu_encu = usu.id');
     $this->db->join('opciones_encuesta as op', 'op.encu_id = en.id_encu', 'inner');
     $this->db->where('en.id_usu_encu', $id);
     $res=$this->db->get();
      if($res->num_rows()>0){
        return $res->result_array();
			}
      return FALSE;
		
    }

    /*public function tituloEncuesta($id){
      $this->db->where('id_usu_encu', $id);
      $res=$this->db->get();
      if($res->num_rows()>0){
        return $res->result_array();
			}
      return FALSE;
    }*/

    public function countPublicaciones($id){
      $this->db->select('id');
      $this->db->where('id_usuario', $id);
      $this->db->from('publicaciones_usuarios');
      $res=$this->db->count_all_results();
      return $res;
    }

    public function cantidadComentarios($id){
      $this->db->select('id_com_usu');
      $this->db->where('id_usuario_com', $id);
      $this->db->from('comentarios_usuarios');
      $res=$this->db->count_all_results();
      return $res;
    }

    public function cantidadMg($id){
      $this->db->select('id_mg_usu');
      $this->db->where('id_usuario_mg', $id);
      $this->db->from('me_gusta_usuarios');
      $res=$this->db->count_all_results();
      return $res;
    }

    public function cantidadNoMg($id){
      $this->db->select('id_nmg_usu');
      $this->db->where('id_usuario_nmg', $id);
      $this->db->from('no_me_gusta_usuarios');
      $res=$this->db->count_all_results();
      return $res;
    }

    public function countPublicacionesAll(){
      $this->db->select('id');
      $this->db->from('publicaciones_usuarios');
      $res=$this->db->count_all_results();
      return $res;
    }

    public function cantidadComentariosAll(){
      $this->db->select('id_com_usu');
      $this->db->from('comentarios_usuarios');
      $res=$this->db->count_all_results();
      return $res;
    }

    public function cantidadMgAll(){
      $this->db->select('id_mg_usu');
      $this->db->from('me_gusta_usuarios');
      $res=$this->db->count_all_results();
      return $res;
    }

    public function cantidadNoMgAll(){
      $this->db->select('id_nmg_usu');
      $this->db->from('no_me_gusta_usuarios');
      $res=$this->db->count_all_results();
      return $res;
    }

}
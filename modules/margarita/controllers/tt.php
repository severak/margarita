<?php
class tt_controller extends frontend_controller{
  function index(){
    $data["text"]="hotovo";
    echo $this->view("timetable",$data);
  }
  
  function con(){
    $no=rpd_url_helper::value("con",0,1);
    $grid=new datagrid_library();
    $grid->db->select("station, departue")->from("tt")->where("connection",$no)->orderby("departue","asc");
    $grid->column("station","Stanice");
    $grid->column("departue","Čas");
    $grid->build();
    $data["text"]=$grid->output;
    echo $this->view("timetable",$data);
  }
  
  function s(){
    $f=new dataform_library();
    $f->field("dropdown","from","Z")->options("SELECT code,name FROM stations ORDER BY name ASC");
    $f->field("dropdown","to","Do")->options("SELECT code,name FROM stations ORDER BY name ASC");
    $f->field("input","arrival","Příjezd");
    $f->buttons('Save');
    $f->build();
    if ($f->on("show")){
      $data["text"]=$f->output;  
    }
    
    if ($f->on("success")){
      $this->db->query("SELECT DISTINCT a.station as start, a.departue as start_time, b.station as finish, b.departue as finish_time from  tt as a, tt as b
where a.station='".$_POST["from"]."' and b.station='".$_POST["to"]."' and a.connection=b.connection and a.departue<b.departue and b.departue>".$_POST["arrival"]."");
      
      $get =  $this->db->result_array();
        
      $data["text"]=var_export($get,true);
    }
    echo $this->view("timetable",$data);
  }
  
}

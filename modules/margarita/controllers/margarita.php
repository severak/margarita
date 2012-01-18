<?php

function time_out($i){
	$hh=(Int)($i/60);
	$mm=$i-($hh*60);
	return $hh.":".$mm;
}

function depart($a){
	return time_out($a["departue_time"]);
}

class margarita_controller extends frontend_controller{
	function __construct(){
		parent::__construct();
		$this->title="Jízdní řády";
	}
	
	function index(){
		$g=new datagrid_library();
		$g->label="Spoje";
		$g->source('trips');
		$g->column('{trip_short_name} ({trip_headsign})','Číslo vlaku')->url('margarita/con/{trip_id}');
		$g->build();
		$h=new datagrid_library();
		$h->source('stops');
		$h->label="Zastávky";
		$h->column('stop_name','Zastávka')->url('margarita/odjezdy/{stop_id}');
		$h->build();
		$data["text"]=$g->output." ".$h->output;
		echo $this->view("timetable",$data);
	}
	
	function con($no=1){
		$this->db->select("*")->from("trips")->where('trip_id',$no)->get();
		$info=$this->db->row_array();
		$g=new datagrid_library();
		$g->db->select("*")->from('stop_times')->join('stops','stops.stop_id=stop_times.stop_id')->orderby('stop_sequence','ASC')->where('trip_id',$no);
		$g->label=$info["trip_short_name"];
		$g->column('stop_name','');
		$g->column('departue_time','')->callback('depart');
		$g->build();
		$data["text"]=$g->output;
		
		echo $this->view("timetable",$data);
	}
	
	function odjezdy($z=1){
		$g=new datagrid_library();
		$g->db->select("*")->from('stop_times')->join('trips','stop_times.trip_id=trips.trip_id')->orderby('departue_time','ASC')->where('stop_id',$z);
		$g->column('trip_short_name','Č. vlaku');
		$g->column('departue_time','Čas odjezdu')->callback('depart');
		$g->column('trip_headsign','Směr');
		$g->build();
		$data["text"]=$g->output;
		
		echo $this->view("timetable",$data);
	}
	
	function search(){
    $form = new dataform_library();
        $form->validation->set_message('jine','Cílová stanice nesmí být stejná jako výchozí!');
        $form->field('dropdown','from','Z')->options('SELECT stop_id,stop_name FROM stops ORDER BY stop_name ASC');
        $form->field('dropdown','to','Do')->rule('callback_jine')->options('SELECT stop_id,stop_name FROM stops ORDER BY stop_name ASC');
        $form->field('tine','depart_time','Čas odjezdu');
         
        //$form->buttons(array ('save' => 'save|Next Step'));
        $form->buttons('save');
        $form->build();
        
        if ($form->on('show') OR $form->on('error'))
        {
            $output = $form->output;
        }

        if ($form->on('success'))
        {
            $m=new margarita_helper();
            $ret=$m->find_route($_POST["from"],$_POST["to"],$_POST["depart_time"]);
            if ($ret){
              $g=new datagrid_library();
              $g->source($ret);
              $g->column('trip_id','Číslo spoje')->url("margarita/con/{trip_id}");
              $g->column('stop_id','Zastávka');
              $g->column('departue_time','Odjezd');
              $g->build();
              $output=$g->output;
            }else{
              $output="Nenalezeno žádné spojení.<br>";
            }
        }
        
        $data['text']=$output.$this->anchor("margarita/search","Nové hledání");
        echo $this->view("timetable",$data);
  }
  
  function jine($v){
    return !($v==$_POST["from"]);
  }
}
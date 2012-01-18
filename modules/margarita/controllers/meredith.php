<?php
class meredith_controller extends frontend_controller{

	function __construct(){
		parent::__construct();
		$this->title="Meredith (editor)";
	}
	
	function index(){
		$data["text"]=
			$this->anchor("meredith/agencies","Společnosti")." ".
			$this->anchor("meredith/stops","Zastávky")." ".
			$this->anchor("meredith/routes","Linky")." ".
			$this->anchor("meredith/trips","Spoje")." ".
			$this->anchor("meredith/stop_times","Stanicování")." ".
			$this->anchor("margarita","Přehled")." "
		;
		echo $this->view('timetable',$data);
	}
	
	function agency(){
		$e=new dataedit_library();
		$e->back_url = $this->url('meredith/agencies');
		$e->source('agency');
		$e->field('input','agency_name','Jméno společnosti')->rule('required');
		$e->field('input','agency_timezone','Časové pásmo');
		$e->field('input','agency_url','Web');
		$e->buttons('modify','save','undo','back');
		$e->build();
		$data["text"]=$e->output;
		echo $this->view('timetable',$data);
	}
	
	function agencies(){
		$g=new datagrid_library();
		$g->source('agency');
		$g->column('agency_name','Jméno');
		$g->column('upravit','','')->url('meredith/agency/modify/{agency_id}');
		$g->column('smazat','','')->url('meredith/agency/do_delete/{agency_id}');
		$g->build();
		$data["text"]=$g->output." ".$this->anchor("meredith/agency/create/1","Přidat společnost");
		echo $this->view('timetable',$data);
	}
	
	function stop(){
		$e=new dataedit_library();
		$e->source('stops');
		$e->back_url = $this->url('meredith/stops');
		$e->field('input','stop_code','Kód zastávky');
		$e->field('input','stop_name','Název zastávky')->rule('required');
		$e->field('input','stop_lon','Zeměpisná délka');
		$e->field('input','stop_lat','Zeměpisná šířka');
		$e->field('checkbox','transfer','Přestupní');
		$e->buttons('modify','save','undo','back');
		$e->build();
		$data["text"]=$e->output;
		echo $this->view('timetable',$data);
	}
	
	function stops(){
		$g=new datagrid_library();
		$g->source('stops');
		$g->column('stop_name','Jméno');
		$g->column('upravit','','')->url('meredith/stop/modify/{stop_id}');
		$g->column('smazat','','')->url('meredith/stop/do_delete/{stop_id}');
		$g->build();
		$data["text"]=$g->output." ".$this->anchor("meredith/stop/create/1","Přidat zastávku");
		echo $this->view('timetable',$data);
	}

	function route(){
		$e=new dataedit_library();
		$e->source('routes');
		$e->back_url = $this->url('meredith/routes');
		$e->field('input','route_short_name','Kód linky')->rule('required');
		$e->field('input','route_long_name','Název linky')->rule('required');
		$e->field('dropdown','route_type','Typ linky')->options(array("tramvaj","metro","železnice","autobus","trajekt","cable-car","lanovka","pozemní lanovka"));
		$e->field('dropdown','agency_id','Společnost')->options("SELECT agency_id,agency_name FROM agency");
		$e->buttons('modify','save','undo','back');
		$e->build();
		$data["text"]=$e->output;
		echo $this->view('timetable',$data);
	}
	
	function routes(){
		$g=new datagrid_library();
		$g->source('routes');
		$g->column('route_short_name','Kód');
		$g->column('route_long_name','Název linky');
		$g->column('upravit','','')->url('meredith/route/modify/{route_id}');
		$g->column('smazat','','')->url('meredith/route/do_delete/{route_id}');
		$g->build();
		$data["text"]=$g->output." ".$this->anchor("meredith/route/create/1","Přidat linku");
		echo $this->view('timetable',$data);
	}

	function trip(){
		$e=new dataedit_library();
		$e->source('trips');
		$e->back_url = $this->url('meredith/trips');
		$e->field('input','trip_short_name','(Číslo vlaku)');
		$e->field('dropdown','route_id','Linka')->options("SELECT route_id,route_short_name FROM routes");
		$e->field('input','trip_headsign','Směrovka');
		$e->buttons('modify','save','undo','back');
		$e->build();
		$data["text"]=$e->output;
		echo $this->view('timetable',$data);
	}
	
	function trips(){
		$g=new datagrid_library();
		$g->source('trips');
		$g->column('{trip_short_name} ({trip_headsign})','Kód spoje');
		$g->column('upravit','','')->url('meredith/trip/modify/{trip_id}');
		$g->column('smazat','','')->url('meredith/trip/do_delete/{trip_id}');
		$g->build();
		$data["text"]=$g->output." ".$this->anchor("meredith/trip/create/1","Přidat spoj");
		echo $this->view('timetable',$data);
	}
	
	function stop_time(){
		$e=new dataedit_library();
		$e->source('stop_times');
		$e->back_url = $this->url('meredith/stop_times');
		$e->field('dropdown','trip_id','(Číslo vlaku)')->options("SELECT trip_id, ifnull(trip_short_name,trip_id) FROM trips");
		$e->field('tine','arrival_time','Příjezd');
		$e->field('tine','departue_time','Odjezd');
		$e->field('dropdown','stop_id','Zastávka')->options("SELECT stop_id,stop_name FROM stops");
		$e->field('input','stop_sequence','Pořadí')->rule('required');
		$e->buttons('modify','save','undo','back');
		$e->build();
		$data["text"]=$e->output;
		echo $this->view('timetable',$data);
	}
	
	function stop_times(){
		$g=new datagrid_library();
		//$g->source('stop_times');
		$g->source('SELECT * FROM stop_times JOIN stops ON stop_times.stop_id=stops.stop_id');
		$g->column('trip_id','Kód spoje');
		$g->column('stop_name','Zastávka');
		//$g->column('arrival_time','Příjezd','arrival_time');
		$g->column('departue_time','Odjezd','departue_time');
		$g->column('upravit','','')->url('meredith/stop_time/modify/{stop_times_id}');
		$g->column('smazat','','')->url('meredith/stop_time/do_delete/{stop_times_id}');
		$g->build();
		$data["text"]=$g->output." ".$this->anchor("meredith/stop_time/create/1","Přidat zastavení");
		echo $this->view('timetable',$data);
	}
	
}
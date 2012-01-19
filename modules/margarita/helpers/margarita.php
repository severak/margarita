<?php
class margarita_helper{
  public $limit=5;
  public $grandmas=0;
  
  function find_route($from,$to,$time){
    rpd::$db->select("*")->from('stop_times')->where('stop_id',$from)->where('departure_time>',$time)->orderby('departure_time','ASC')->get();
    $r=rpd::$db->result_array();
    if ($r){
      foreach ($r as $v){
        $outp=$this->grandma(array($v['trip_id']),array($v['stop_id']),array($v['departure_time']),$to);
        if ($outp){
          //echo "Babek:".$this->grandmas;
          return $outp;
          break;
        }
      }
    }else{
      return false;
    }
  }
  
  function grandma($trains,$stops,$times,$goal){
    $this->grandmas=$this->grandmas+1;
    rpd::$db->select("*")->from('stop_times')->where('stop_id',$goal)->where('trip_id',$trains[count($trains)-1])->where('departure_time>',$times[count($times)-1])->orderby('departure_time','ASC')->get();
    $r=rpd::$db->row_array();
    if ($r){
      $trains[]=$r['trip_id'];
      $stops[]=$r['stop_id'];
      $times[]=$r['departure_time'];
      $ret=array();
      for ($i=0;$i<count($trains);$i++){
        $ret[]=array('trip_id'=>$trains[$i],'stop_id'=>$this->stop($stops[$i]),'departure_time'=>$this->time_out($times[$i]));  
      }
      return $ret;
    }elseif (count($stops)<$this->limit){
      rpd::$db->select("*")->from('stop_times')->join('stops','stop_times.stop_id=stops.stop_id')->where('trip_id',$trains[count($trains)-1])->where('transfer',1)->where('stop_times.departure_time>',$times[count($times)-1])->get();
      $next_stops=rpd::$db->result_array();
      foreach ($next_stops as $ns){
        rpd::$db->select("*")->from('stop_times')->where('stop_id',$ns['stop_id'])->where('departure_time>',$times[count($times)-1])->get();
        $next_trains=rpd::$db->result_array();
          if ($next_trains){
            foreach ($next_trains as $nt) {
                $new_trains=$trains;
                $new_stops=$stops;
                $new_times=$times;
                $new_trains[]=$nt['trip_id'];
                $new_stops[]=$nt['stop_id'];
                $new_times[]=$nt['departure_time'];
                $ret=$this->grandma($new_trains,$new_stops,$new_times,$goal);
                if ($ret){
                  return $ret;
                }
           
            }
          }	
      }
      return false;
    }
    return false;
  } 
  
  function is_terminus($stop,$trip){
    rpd::$db->select('trip_id,stop_id')->from('stop_times')->orderby('stop_sequence','DESC')->where('trip_id',$trip)->get();
    $res=rpd::$db->row_array();
    return ($res['stop_id']==$stop);
  }
  
  function time_out($i){
	 $hh=(Int)($i/60);
	 $mm=$i-($hh*60);
	 return $hh.":".$mm;
  }
  
  function stop($id){
    rpd::$db->select('stop_name')->from('stops')->where('stop_id',$id)->get();
    $name=rpd::$db->row_array();
    if (is_array($name) and isset($name["stop_name"])){
      return $name["stop_name"];
    }else{
      return "no. ".$id;
    }
  }
  
}
?>
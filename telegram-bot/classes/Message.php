<?php
class Message  {

	private $connection = NULL;
	private $rs = NULL;
	private $data = null;
	private $config = null;


	public function __construct($conn=NULL){
		$this->connection = $conn;
		$this->rs =  new DBMS_res($conn);
		$this->config = new SysValues($conn);
	}


	public function send($chatid,$message,$id) {
		if ($chatid) {
			$url=$this->config->get("bot_url_send")."?chat_id=".$chatid."&text=".urlencode($message);
			$MSG = json_decode(file_get_contents($url),true);
			if ($MSG["ok"]==1) {
				$this->save_send($MSG,$id,$chatid);
				echo "Ok\n";
				return true;
				
			} else {
				echo "Message send failed\n";
				return false;
			}
		}
		
	}
	
	
	public function save_send ($MSG,$id,$userid) {
		$res=$MSG["result"];
		
		$sql ="insert into message_send (message_id,user_id,toc_ts,message) values (";
		$sql.=$id.",".$userid.",sysdate(),";
		$sql.="'".base64_encode(serialize($res))."')";     
		$this->rs->query($sql) or die(1);
		
	}
	

	
	public function show($id=null) {

		if (! $id) $sql="select id,toc_ts,content from message_get_json order by id desc limit 5";
		$this->rs->query($sql) or die;
        while ($row=$this->rs->fetchRow()) {
        	$cnt=unserialize(base64_decode($row["content"]));
        	$this->show_content($cnt);
        }
    }
    
    private function show_content ($cnt) {
    	echo "======================================================\n";
    	echo $cnt["update_id"];
    	echo "\n";
    	echo "date: ".date('d.m.Y H:i:s',intval($cnt["message"]["date"]))."\n";
    	echo "from: ".$cnt["message"]["from"]["id"]." - ".$cnt["message"]["from"]["first_name"]." - ".$cnt["message"]["from"]["last_name"];
    	echo "\n";
    	echo $cnt["message"]["text"];
    	echo "\n";
    }
	
	public function get($reset=false) {
		//$MSG=array();
		//echo $this->config->get("bot_url_get");
		if ($reset==true) $this->config->set("bot_update_id",intval(0));
		
		$MSG = json_decode(file_get_contents($this->config->get("bot_url_get")),true);
        if ($MSG["ok"]=="1") {
        	foreach ($MSG["result"] as $row) {
        		if (! $this->check_exist($row) ) {
        			echo "<br>save new message.\n";
        			$this->save_get($row);
        		} 
        	}
        }
	}
	
	private function save_get($msg) {
		$sql="insert into message_get_json (toc_ts,content) values (sysdate(),?)";
		$this->rs->prepare($sql);
		$this->rs->bindColumn(1,base64_encode(serialize($msg)),PDO::PARAM_STR);
		if (! $this->rs->execute()) {
			echo "Fehler SQL Execute";
		}
	}
	
	
	private function save_get1($msg) {
		$cols="update_id,message_id,from_id,chat_id,from_first_name,from_last_name,chat_first_name,chat_last_name,date_integer,message_text,toc_ts";
		$sql ="insert into message_get  (".$cols.") values (";
		$sql.=intval($msg["update_id"]).",";
		$sql.=intval($msg["message"]["message_id"]).",";
		$sql.=intval($msg["message"]["from"]["id"]).",";
		$sql.=intval($msg["message"]["chat"]["id"]).",";
		echo $sql;
		$sql.="'".mysql_escape_string($msg["message"]["from"]["first_name"])."',";
		$sql.="'".mysql_escape_string($msg["message"]["from"]["last_name"])."',";
		$sql.="'".mysql_escape_string($msg["message"]["chat"]["first_name"])."',";
		$sql.="'".mysql_escape_string($msg["message"]["chat"]["last_name"])."',";
		$sql.=intval($msg["message"]["date"]).",";
		$sql.="'".mysql_escape_string($msg["message"]["text"])."',";
		$sql.="sysdate())";
		
		$this->rs->query($sql) or die;
	}
	
	private function check_exist ($msg) {

		if ($msg["update_id"]>$this->config->get("bot_update_id")) {
			$this->config->set("bot_update_id",intval($msg["update_id"]));
			echo "Eintrag ".$msg["update_id"]." existiert nicht";
			return false;
		}
				
		return true;
	}
	
	
	public function set($id) {
		$sql="update message set out_ts=sysdate() where id=".intval($id);
		if ($this->rs->query($sql)) {
			return true;
		} else {
			return false;
		}
	}
}
?>
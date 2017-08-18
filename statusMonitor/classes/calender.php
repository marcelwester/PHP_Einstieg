<?php
class Calender {

	protected $htmlCode = NULL;
	protected $MONTH_NAME = array("","Januar","Februar","März","April","Mai","Juni","Juli","August","September","Oktober","November","Dezember");
	protected $DAY_NAME = array("So","Mo","Di","Mi","Do","Fr","Sa");
	protected $TIME = null; 
	private $tableClass=null; 
	
	public function __construct(){
				
	}
	
	public function addHTML ($code) {
		$this->htmlCode.=$code;
	}
	
	public function showHTML () {
		echo utf8_encode($this->htmlCode);
	}
	
	public function addTable($cl){
		$this->tableClass=$cl;
		$this->addHTML('<table width="80%" class="'.$cl.'">');
		$this->addHTML('<tbody>');
		
	}
    	
	public function endTable () {
		$this->addHTML('</tbody>');
		$this->addHTML('</table>');
	}
	
    public function addCol($x,$opt="") {
		$this->addHTML('<td class="'.$this->tableClass.'" '.$opt.'>'.$x.'</td>');
	}

	public function startRow () {
		$this->addHTML('<tr>');
	}
	
	public function endRow () {
		$this->addHTML('</tr>');
	}
	
	public function getYearMonths () {
		// return Month and indx of Month for Input Selectbox
        $ret=array();
        for ($i=1; $i<=12; $i++) {
        	$ret[$i]["name"]=$this->MONTH_NAME[$i];
        	$ret[$i]["val"]=$i;
        }
		return $ret;
	}
	
}


class Month extends Calender {
	private $day = null;
    private $month= null;
	private $year = null;
	private $first = null;
	private $last = null;
	private $calendaring = array();
	private $calendaring_indx = 0;
	
	public function __construct($y,$m,$d=0) {
		$this->year = intval($y);
		$this->month = intval($m);
		$this->first = 1;
		$this->first_day = (date("w", mktime(0,0,0,$m,1,$y)));
		$this->TIME = mktime(0,0,0,$m,$d,$y);
		$this->last = (date("t", mktime(0,0,0,$m,1,$y)));
		$this->calendaring_indx;
	}

	
	
	public function addCalEntry ($ts,$cnt) {
		$this->calendaring[$this->calendaring_indx]["ts"]=$ts;
		$this->calendaring[$this->calendaring_indx++]["cnt"]=$cnt;
		//print_r($this->calendaring);
	}
	
	
	
	private function getCalendaring($timestamp) {
		// timestamp: 00:00:00 Uhr eines Tages 
		// Dauer des Tages: 86399 sekunden
		$ret = array();
		$indx=0;
		foreach ($this->calendaring as $row) {
			if (($row["ts"]>$timestamp) and ($row["ts"]<($timestamp+86399)) )
			     $ret[$indx++] = $row;
		}
		return $ret;
	}
	
	public function format_f1 () {
		 $this->addHTML("<center>");
		 $this->addTable("cf1");
	     $this->startRow();
	     	$this->addCol('<font size="+1"><b>'.$this->MONTH_NAME[$this->month].'</font><font size="-2"><br>'.$this->year.'</b></font>','colspan="2" align="center"');
	     $this->endRow();
	     
	     for ($i=1;$i <= $this->last; $i++) {
		     $this->startRow();
                $dow=date("w",mktime(0,0,0,$this->month,$i,$this->year)); // day of week
		        $bgcolor="";
		     	if ($dow=="0" or $dow=="6") {
		     		$bgcolor=' bgcolor="#AAAAAA" ';
		     	}
		        
		     	
		     	// Tag des Monats
		     	$cnt=$i;
		     	$this->addCol($cnt,'style="width:30px;text-align:right;"'.$bgcolor);
		     	
		     	// Wochentag
		     	$cnt='<div style="height:10px;width:20px;float:left;">';
		     	$cnt.='<font size="-2">';
		     	$cnt.=$this->DAY_NAME[$dow];
		     	$cnt.='</font></div>';
		     	
		     	// Content
		     	$cnt.='<table class="calfield"><tr><td class="calfield" align="left">';
		     	   $entry=$this->getCalendaring(mktime(0,0,0,$this->month,$i,$this->year));
		     	   foreach ($entry as $row) {
		     	   	// $cnt.='<font size="-1">'.$row["ts"]." - ".$row["cnt"].'</font>';
		     	   	$cnt.='<font size="-1">'.$row["cnt"].'</font>';
		     	   }
		     	
		     	   //$cnt.='TEST '.$i;
		     	
		     	
		     	
		     	$cnt.='</td></tr></table>';
		     	$this->addCol($cnt,'align="left" '.$bgcolor);
		     	
		     $this->endRow();
	     }
		 $this->endTable();	     
	     $this->addHTML("</center>");
	}
	
}

/* 
• g - Stunde im 12-Stunden-Format (1-12 )
• G - Stunde im 24-Stunden-Format (0-23 )
• h - Stunde im 12-Stunden-Format *(01-12 )
• H - Stunde im 24-Stunden-Format *(00-23 )
• i - Minuten *(00-59)
• I - (großes i) 1 bei Sommerzeit, 0 bei Winterzeit
• j - Tag des Monats (1-31)
• l - (kleines L) ausgeschriebener Wochentag (Monday)
• L - Schaltjahr = 1, kein Schaltjahr = 0
• m - Monat *(01-12)
• n - Monat (1-12)
• M - Monatsangabe (Feb – 3stellig)
• O - Zeitunterschied gegenüber Greenwich (GMT) in Stunden (z.B.: +0100)
• r - Formatiertes Datum (z.B.: Tue, 6 Jul 2004 22:58:15 +0200)
• s - Sekunden *(00 – 59)
• S - Englische Aufzählung (th für 2(second))
• t - Anzahl der Tage des Monats (28 – 31)
• T - Zeitzoneneinstellung des Rechners (z.B. CEST)
• U - Sekunden seit Beginn der UNIX-Epoche (1.1.1970)
• w - Wochentag (0(Sonntag) bis 6(Samstag))
• W - Wochennummer des Jahres (z.B.: 28)
• Y - Jahreszahl, vierstellig (2001)
• y - Jahreszahl, zweistellig (01)
• z - Tag des Jahres (z.B. 148 (entspricht 29.05.2001))
• Z - Offset der Zeitzone gegenüber GTM (-43200 – 43200) in Minuten
*/
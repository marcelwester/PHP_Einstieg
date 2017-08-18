<?php

class Form extends validate {

	private $method = NULL;
	private $url = NULL;
	private $formid = NULL;
	private $htmlCode = NULL;
	private $javaScriptCode = NULL;
	private $formData = NULL;
	private $errorInputStyle = NULL;
	private $errorRadioStyle = NULL;
	private $cssclass="";

	private function showcal ($inp) {
		$inp=explode(".",$inp);
		$ret = '<script language="JavaScript">';
		$ret.= "new tcal ({";
		// form name
		$ret.= "'formname': '".$inp[0]."',";
		// input name
		$ret.= "'controlname': '".$inp[1]."'";
		$ret.= "});";
		$ret.= '</script>';
		return $ret;
	}



	public function __construct($url,$formid,$method,$init=""){
		$this->url = $url;
		$this->method = $method;
		$this->formid = $formid;
		$this->addHTML('<form name="'.$this->formid.'" method="'.$this->method.'" ENCTYPE="multipart/form-data" action="'.$url.'">');
		$this->addHTML('<input type="hidden" name="formsent" value="1">');
		$this->cssclass="";
		$this->errorInputStyle = ' style="border:1px solid #FF0000" ';
		$this->errorRadioStyle =' style="border:1px solid #ff0000" ';
		
		unset($_SESSION["FORMDATA"][$this->formid]);
		parent::__construct();
	}

	public function setCSSClass ($class) {
		$this->cssclass=' class="'.$class.'" ';
	}

	public function isSent () {
		if (intval($this->getStr("formsent")=="1")) 
			return true;
		else
			return false;
			
		 
	}

	private function check ($value,$type) {
		// type = text,number,date,float
		if ($type=="text") { return $this->isText($value); }
		if ($type=="number") {return $this->isNumber($value); }
		if ($type=="date") {return $this->isDate($value); }
	if ($type=="upload") {return true; }
		return true;
	}

	public function addHTML ($code) {
		$this->htmlCode.=$code;
	}

	public function addJavaScript ($code) {
		$this->javaScriptCode.=$code;
	}
	
	
	public function init ($lst) {
		// $lst =>  Liste alle im Formular verwendeter Feldernamen
		$lst = explode(",",$lst.",formsent");
		foreach ($lst as $name) {
			if ($this->method="POST") if (isset($_POST[$name])) $_SESSION["FORMDATA"][$this->formid][$name]=$_POST[$name];
			if ($this->method="GET") if (isset($_GET[$name])) $_SESSION["FORMDATA"][$this->formid][$name]=$_GET[$name];
		}
		$this->formData = $_SESSION["FORMDATA"][$this->formid];
		if (!isset($this->formData)) $this->errorInputStyle="";
	}

	public function addInput ($name,$size,$type="text",$value="",$error=false) {
		$errorStyle="";
		if (isset($this->formData[$name])) $value=$this->formData[$name];
		if (!$this->check($value,$type)) $errorStyle=$this->errorInputStyle;
		if ($error) $errorStyle=$this->errorInputStyle;

		$this->addHTML('<input '.$this->cssclass.' '.$errorStyle.' type="text" name="'.$name.'" size="'.$size.'" value="'.$value.'"/>');
	}

	public function addInputDate ($name,$size,$type="text",$value="",$error=false) {
		$errorStyle="";
		if (isset($this->formData[$name])) $value=$this->formData[$name];
		if (!$this->check($value,$type)) $errorStyle=$this->errorInputStyle;
		if ($error) $errorStyle=$this->errorInputStyle;

		$this->addHTML('<input '.$this->cssclass.$errorStyle.' type="text" id="'.$name.'" name="'.$name.'" size="'.$size.'" value="'.$value.'"/>');
		$this->addHTML('&nbsp;');
		//$this->addHTML($this->showcal($this->formid.'.'.$name));
	    $jscript='$(function() {'.
	             '$( "#'.$name.'" ).datepicker({'.
	    		 "dateFormat: 'dd.mm.yy',".
	    		 "monthNames: ['Januar','Februar','März','April','Mai','Juni','Juli','August','September','Oktober','November','Dezember'],".
	             "dayNames: ['Sonntag', 'Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag','Samstag'],".
	             "dayNamesMin: ['So', 'Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa'],".
	             "firstDay: 1".
	             '});'.
	             '});'."\n";
        $this->addJavaScript($jscript);
	}

	public function addInputAutocomplete ($name,$size,$type="text",$value="",$searchurl,$error=false) {
		$errorStyle="";
		if (isset($this->formData[$name])) $value=stripslashes($this->formData[$name]);
		if (!$this->check($value,$type)) $errorStyle=$this->errorInputStyle;
		if ($error) $errorStyle=$this->errorInputStyle;

		$this->addHTML('<input '.$this->cssclass.$errorStyle.' type="text" id="'.$name.'" name="'.$name.'" size="'.$size.'" value="'.$value.'"/>');
		$this->addHTML('&nbsp;');

		$jscript='$(function() {'.
		         '$( "#'.$name.'" ).autocomplete({'.
			     'source: "'.$searchurl.'"'.
				 '});'.
				 '});'."\n";
        $this->addJavaScript($jscript);
	}

	public function addUpload ($name,$size,$type="text",$value="",$error=false) {
		$errorStyle="";
		if (isset($this->formData[$name])) $value=$this->formData[$name];
		if (!$this->check($value,$type)) $errorStyle=$this->errorInputStyle;
		if ($error) $errorStyle=$this->errorInputStyle;
		$this->addHTML('<input '.$this->cssclass.$errorStyle.' type="file" name="'.$name.'" size="'.$size.'" value="'.$value.'"/>');
	}



	public function addCheckBox($name,$checked="",$val=1) {
		if ($checked==1 && !isset($this->formData["formsent"])) $checked="CHECKED";
		if (isset($this->formData[$name])) $checked="CHECKED";
		$this->addHTML('<input type="checkbox" name="'.$name.'" value="'.$val.'" '.$checked.'/>');
	}

	public function addRadioButton($name,$options=array(),$checked=NULL,$error=false) {
		// options (show) ==> options["indx"]["descr"]
		// value ==> options["indx"]["val"]
		$errorStyle="";
		if ($error) $errorStyle=$this->errorRadioStyle;;
		if (isset($this->formData["formsent"])) {
			$checked=$this->formData[$name];
			$found=false;
			foreach ($options as $opt) {
				if ($opt["val"] == $checked) {
					$found=true;
				}
	 		}
		 	if ($found==false) {
		 		$errorStyle=$this->errorRadioStyle;
		 		$this-> setValidateFalse();
		 		$checked=$options["0"]["val"];
		 	}
		}
		echo $checked;
		$this->addHTML('<table '.$this->cssclass.$errorStyle.' ><tr><td '.$this->cssclass.'>');
		foreach ($options as $opt) {
			if ($opt["val"] == $checked) {
				$this->addHTML($opt["descr"].'<input type="radio" name="'.$name.'" value="'.$opt["val"].'" checked/>');
			} else {
				$this->addHTML($opt["descr"].'<input type="radio" name="'.$name.'" value="'.$opt["val"].'"  />');
			}
		}
		$this->addHTML('</td></tr></table>');
	}
	 
	public function addTextArea($name,$text="",$cols=10,$rows=10) {
		// options => options["indx"]["descr"]
		if (isset($this->formData["formsent"])) $text=$this->formData[$name];
		$this->addHTML('<textarea '.$this->cssclass.' name="'.$name.'" cols="'.$cols.'" rows="'.$rows.'">'.$text.'</textarea>');
	}

	public function addSelectBox($name, $options, $field_show, $field_val, $multiple = 0, $size = 1, $std = "") {
		// options => $resultset als assoziatives Array
		// field_val => WERT
		// field_show => angezeigter Name
		// multiple=0|1
		if (isset($this->formData["formsent"])) {
			$std=$this->formData[$name];
			if (!is_array($this->formData[$name])) $std[0]=$std;
		} else {
			if (!is_array($std)) {
				$tmp=$std;
				$std=array();
				$std[0]=$tmp;
			}
		}
		 
		if ($multiple==1) $multiple="multiple";
		 
		$this->addHTML('<select name="'.$name.'[]" size="'.$size.'" '.$multiple.'>');
		foreach ($options as $opt) {
			if (in_array($opt[$field_val],$std)) {
				$this->addHTML('<option selected value="'.$opt[$field_val].'">');
			} else {
				$this->addHTML('<option value="'.$opt[$field_val].'">');
			}
			$this->addHTML($opt[$field_show]);
			$this->addHTML('</option>');
		}
		$this->addHTML('</select>');

	}



	public function getNumber($name) {
			return intval($_POST[$name]);
	}

	public function getSelectNumber($name) {
			return intval($_POST[$name]["0"]);
	}
	
	
	
	public function getStr($name) {
		return ($_POST[$name]);
	}


	public function getDateNumber($name) {
		return strtotime($_POST[$name]);
	}

	public function getDate($name) {
		return $_POST[$name];
	}

	public function getMysqlDate($name) {
		 $x=explode(".",$_POST[$name]);
		 return ($x[2]."-".$x[1]."-".$x[0]);
	}
	
	
	public function addSubmit ($value) {
		$this->addHTML('<input type="submit" value="'.$value.'"/>');
	}

	public function show () {
		$this->addHTML('</form>');
		echo $this->htmlCode;
		echo "\n\n";
		echo '<script language="JavaScript">'."\n";
		echo $this->javaScriptCode;
		echo '</script>';
		echo "\n\n";
	}

	public function getFormData () {
		return $this->formData;
	}
	
	
	public function dumpFormData() {
		echo '<br><pre>';
			print_r($this->formData);
		echo '</pre><br>';
	}
	

}

?>

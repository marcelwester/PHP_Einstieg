<?php
/*
 * Created on 15.01.2010
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 * <a href="javascript:popup_info('UEberschrift','http://www.heise.de',1024,768);">TEST1</a>
 */

 class popup {
 	private $title = "";
  	private $url = NULL;
  	private $xsize = 640;
  	private $ysize = 480;
  	private $content = NULL;
  	private $button1TEXT = NULL;
  	private $button1URL = NULL;
  	private $button2TEXT = NULL;
  	private $button2URL = NULL;

  	public function setXsize($i) {
  		$this->xsize = intval($i);
  	}	
  	
  	public function setYsize($i) {
  		$this->ysize = intval($i);
  	}	

  	public function setTitle($a) {
  		$this->title = $a;
  	}	

  	public function setContent($a) {
  		$this->content = $a;
  	}	
  	
  	public function setButton1Text($a) {
  		$this->button1TEXT = $a;
  	}	
  	
  	public function setButton1Url($a) {
  		$this->button1URL = $a;
  	}	
  	
  	public function setButton2Text($a) {
  		$this->button2TEXT = $a;
  	}	
  	
  	public function setButton2Url($a) {
  		$this->button2URL = $a;
  	}	
  
    private function table_link($text,$href,$colspan=1) {
		global $link,$link_over,$mousepointer;
	    $x='<TD class="menu" width="50%" colspan="'.$colspan.'" ALIGN="CENTER" BGCOLOR="'.$link.'" onMouseOver="this.style.backgroundColor=\''.$link_over.'\'; this.style.cursor=\''.$mousepointer.'\';" onMouseOut="this.style.backgroundColor=\''.$link.'\';" onClick="'.$href.'">';
		 	$x.=$text;
	    $x.='</TD>';
	    return $x;
    }
  	
  	
    public function start() {
		global $_SESSION;
		$x ='<table align="center" width="90%" >';
		$x.='<tr>';
		$x.='  <td bgcolor="#FFOOOO" align="center" colspan="2" class="menu">';
		$x.='    <h2>'.$this->title.'</h2>';
		$x.='  </td>';
		$x.='</tr>';
		$x.='<tr>';
		$x.='  <td align="center" colspan="2">';
		$x.=$this->content;
		$x.='  </td>';
		$x.='</tr>';
		$x.='<tr>';

		if (!$this->button1TEXT==NULL) {
			$url1="parent.parent.GB_CURRENT.hide();";
			if ($this->button1URL!=NULL) 
				$url1="parent.parent.window.open('".$this->button1URL."','_self');";
		}

		if ($this->button2TEXT!=NULL) {
			$url2="parent.parent.GB_CURRENT.hide();";
			if (!$this->button2URL==NULL) 
				$url="parent.parent.window.open('".$this->button2URL."','_self');";

			$x.=$this->table_link($this->button1TEXT,$url1,1);
			$x.=$this->table_link($this->button2TEXT,$url2,1);
		} else {
			$x.=$this->table_link($this->button1TEXT,$url1,2);
		} 

		$x.='</tr>';
		$x.='</table>';		
		
		
		$_SESSION["popupTmp"]=$x;
		
		echo '<script type="text/javascript">';
			echo "popup('".$this->title."','../../popupshow.php',".$this->xsize.",".$this->ysize.");";
		echo '</script>';	
    }
  	
 }
?>

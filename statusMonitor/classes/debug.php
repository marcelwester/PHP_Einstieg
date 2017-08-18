<?php
class debug {
	
	private $deb = null;
	
	public function __construct($d=0) {
		if ($d==0) {
			$this->deb=0;
		} else {
			$this->deb=1;
		}
	}
	
	public function pr($txt) {
		if ($this->deb==1) {
			if (is_array($txt)) {
				echo '<br><pre>',print_r($txt),'</pre>';
			} else {
				echo '<br>'.$txt;
			}
		}
	}
}
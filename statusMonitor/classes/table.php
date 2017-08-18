<?php
class Table  {

	private $htmlCode = NULL;
	private $id = NULL;
	
	public function __construct($id,$headers){
		$this->id = $id;
		$this->addHTML('<table id="'.$this->id.'" class="tablesorter">');
		$this->addHTML('<thead>');
		foreach ($headers as $head) {
			$this->addHTML("<th>".$head."</th>");	
		}
		$this->addHTML('</thead>');
		$this->addHTML('<tbody>');
	}
	
	public function addHTML ($code) {
		$this->htmlCode.=$code;
	}

	public function html() {
		return utf8_encode($this->htmlCode);
	}
	
	public function endTable () {
		$this->addHTML('</tbody>');
		$this->addHTML('</table>');
	
		$this->addHTML("\n\n");
		$this->addHTML('<script language="JavaScript">'."\n");
		$this->addHTML('$(document).ready(function()'."\n");
		$this->addHTML(' { '."\n");
		$this->addHTML('		$("#'.$this->id.'").tablesorter();'."\n");
		$this->addHTML(' }'."\n");
		$this->addHTML(');'."\n");			
		$this->addHTML('</script>'."\n");
		$this->addHTML("\n");
	}
	
	public function showTable () {
		//echo utf8_encode($this->htmlCode);
		echo $this->htmlCode;
	}

	public function addCol($x,$opt="") {
		$this->addHTML('<td '.$opt.'>'.$x.'</td>');
	}

	
	public function startRow () {
		$this->addHTML('<tr>');
	}
	
	public function endRow () {
		$this->addHTML('</tr>');
	}
}
?>
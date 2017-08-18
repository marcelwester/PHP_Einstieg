<?php
class validate {

	protected $muster_datum = '/^[0-9]{1,2}(\.|\/|\-){1}[0-9]{1,2}(\.|\/|\-){1}[0-9]{4}$/';
	protected $muster_plz = '/^[0-9]{5}$/';
	protected $muster_zahl = '/^\d+$/';
	protected $muster_geldbetrag = '/^([1-9]{1}[0-9]{0,2}((\.)?[0-9]{3})*((\.|\,)?[0-9]{0,2})?|[1-9]{1}[0-9]{0,}((\.|\,)?[0-9]{0,2})?|0((\.|\,)?[0-9]{0,2})?|((\.|\,)?[0-9]{1,2})?)$/';
	
	private $status=null;
	
    public function __construct() {
	  $this->status = true;
    }
	
	
	public function isText ($value) {
		if (strlen($value)>=1)
			return true;
		else 
			$this->status=false;
			return false;
	}
	
	public function resultValidate () {
		return $this->status;
	}
	
	public function setValidateFalse () {
		$this->status=false;
	}
	
	
	
		
	
	public function isDate ($value) {
		if (preg_match( $this->muster_datum, $value ))
		{
			$datum_elemente = explode( ".", $value );
			if (checkdate( $datum_elemente[1], $datum_elemente[0], $datum_elemente[2] ))
			{
				return true;			
			}
			else
			{
				$this->status=false;
				return false;			
			}
		}
		else
		{
			$this->status=false;
			return false;
		}
		
	}
	
	public function isNumber ($value) {
		if (preg_match( $this->muster_zahl, $value ))
		{
			return true;
		}
		else
		{
			$this->status=false;
			return false;
		}
	}
	
	public function isFloat ($value) {
		
	}
	
	
	
} 
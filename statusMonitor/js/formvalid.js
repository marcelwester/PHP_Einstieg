<script LANGUAGE="JavaScript">
<!--
	function check(ret,eingabefelder) 
	{ 
		var send = true;
		var element = null; 

		for (var i = 0; i < eingabefelder.length; i++) {
			element = document.getElementById(eingabefelder[i]); 
			if (element.value.length <= 0 && send==true) { 
				alert('Zahlenfeld fehlt !'); 
				element.focus(); 
				send=false; 
			} 
			if(element.value && send==true) {
				var myvalue = element.value;
				var num = myvalue.match(/[^0-9]/gi)
				if (num!=null) {
	 				alert('Formatfehler\n Nur Zahlen erlaubt !'); 
	 				element.focus(); 
	 				send=false; 
				}
			}
		}

		if (send==true) {
			document.form.submit()
		} 
		if (ret==0) {
			return false;
		}
	}
	
	function KeyCode(ev){
		if(ev){TastenWert = ev.which} else {TastenWert = window.event.keyCode}
		if (TastenWert == 13)
		{
			//alert('document.form.text.value');
			//document.form.submit();
			check(0,EINGABEFELDER);
		}
	}
document.onkeypress = KeyCode;
-->
</script>
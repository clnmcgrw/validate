<?php

class Validate {

	var $clean_vals;
	var $form_errors;

	/*	Initiate instances with an array of all $_POST values (required or not),
	*   and an error msg string for failure
	*
	*/
	function __construct($array, $err_msg) {

		$this->clean_vals = [];
		$this->form_errors = [];

		foreach ($array as $key=>$item) {
			if ( !is_array($item) && stripos($item,'Content-Type:') !== FALSE ) {
			   $this->form_errors['mal'] = $err_msg;
			} else {

				/* In case of set checkboxes, convert array to list */
				if ( is_array($item) ) {
					$item = implode(", ", $item);
				}

				trim(htmlspecialchars(strip_tags($item)));
				$this->clean_vals[$key] = $item;	

			}
		}
	
	}
	
	/*  Will loop the clean values and pull out empties
	*   defaults to all fields passed to the constructor
	*
	*   -takes optional subset of required fields as an array
	*   -takes optional required "on/off" fields (radios and chkboxes) as array
	*/
	public function require_fields($all = true, $bool = false) {

		$empty_fields = [];

		if ( $bool != false ) {
			foreach ($bool as $item) {
				if ( !array_key_exists($item, $this->clean_vals) ) {
					$empty_fields[] = $item;
				}
			}
		}

		$req_fields = ($all !== true ? $all : $this->clean_vals );

		foreach ($req_fields as $key=>$val) {
			if ( empty($val) ) {
				$empty_fields[] = $key;
			}
		}

		if (!empty($empty_fields)) {
		   	$this->form_errors['empty'] = $empty_fields;
		}
	
	} // end require_fields()


	/*	Used to set values in checked property to true
	*   if its corresponding chkbx/radio has been selected
	*/

	var $checked;

	public function is_checked($names) {

		foreach($names as $name) {
			
			if ( isset($this->clean_vals[$name]) ) {
			
				if ( strpos($this->clean_vals[$name], ", ") !== false ) {	
					$group_chk = explode(", ", $this->clean_vals[$name] );

					foreach ($group_chk as $chk) {
						$this->checked[$name][$chk] = true;
					}
				
				} else {
					$this->checked[$this->clean_vals[$name]] = true;
				}
			}
		
		}

	} // end is_checked()


	/* Sets up patterns to test against and returns error information
	*  to the form errors property
	*
	*/
	private function pattern_test( $key, $field, $pattern, $err_msg  ) {
		if ( !in_array($key, $this->form_errors['empty']) && !preg_match($pattern,$field) ) {
			$this->form_errors['errors'][$key] = $err_msg;
		} 
	}

	public function check_alpha($key, $err_msg) {
		$this->pattern_test($key, $this->clean_vals[$key], '/^[a-zA-Z ]*$/', $err_msg );
	}

	public function check_alphanum($key, $err_msg) {
		$this->pattern_test($key, $this->clean_vals[$key], '/^[a-zA-Z0-9]+$/', $err_msg );
	}

	public function check_url($key, $err_msg) {
		$this->pattern_test($key, $this->clean_vals[$key], '/^([a-zA-Z0-9]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,6}$/', $err_msg );
	}

	public function check_email($key, $err_msg) {
		$this->pattern_test($key, $this->clean_vals[$key], '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/', $err_msg );
	}

	public function check_phone($key, $err_msg) {
		$this->pattern_test($key, $this->clean_vals[$key], '/^\d{3}-?\d{3}-?\d{4}$/', $err_msg );
	}


	/* Easily add error class when form posts to server self
	*/
	public function empty_class($value, $class = "error") {
		if ( in_array($value, $this->form_errors['empty']) ) {
			return $class;
		}
	}

	/* Either prints JSON (Ajax), or returns an array of the form_errors 
	*  the paramater is the param the form submits to, allowing the 
	*	info to be returned conditionally
	*/
	public function error_response($param = []) {
		if ( isset($param) ) {
			return $this->form_errors;
		} else {
			echo json_encode($this->form_errors);
		}
	}


	/* Ideal for PHP Mailer - takes a condition, which if true
	*  will add the second argument (an error string) to the form_errors property
	*/
	public function try_send($condition, $err_info) {
		if ($condition) {
			$this->form_errors['mail'] = $err_info;
		} else {
			$this->form_errors['sent'] = true;
		}
	}


}
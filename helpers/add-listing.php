<?php 


class PL_Helper_Add_Listing {
	
	function property_selects() {
		echo PL_Form::item('compound_type', PL_Config::PL_API_LISTINGS('create', 'args', 'compound_type'), 'POST');
		echo PL_Form::generate_form( PL_Config::bundler('PL_API_LISTINGS', array('create', 'args'), array('property_type')), array('method'=>'POST', 'include_submit' => false, 'wrap_form' => false, 'echo_form' => false) );
	}

}
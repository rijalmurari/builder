<?php 

class PL_User {
	
	public function whoami($args = array(), $api_key = null) {
		// Assuming no API key was passed in, use the one the plugin is currently activated with...
		if ( empty($api_key) ) {
			$api_key = PL_Option_Helper::api_key();
		}
		// error_log("API Key = " . $api_key);

		$request = array_merge(array("api_key" => $api_key), PL_Validate::request($args, PL_Config::PL_API_USERS('whoami', 'args')));
		$response = PL_HTTP::send_request(trailingslashit(PL_Config::PL_API_USERS('whoami', 'request', 'url')), $request, PL_Config::PL_API_USERS('whoami', 'request', 'type'), true);
	    if ( $response ) {
			$response = PL_Validate::attributes($response, PL_Config::PL_API_USERS('whoami', 'returns'));
		}

		return $response;
	}

	public function create($args = array()) {
		$request = PL_Validate::request($args, PL_Config::PL_API_USERS('setup', 'args') );
		$request['source'] = 'wordpress';
		$response = PL_HTTP::send_request(PL_Config::PL_API_USERS('setup', 'request', 'url'), $request, PL_Config::PL_API_USERS('setup', 'request', 'type'));
		return $response;
	}

	public function update($args = array()) {
		$request = array_merge(array("api_key" => PL_Option_Helper::api_key()), PL_Validate::request($args, PL_Config::PL_API_USERS('update', 'args')));
		// pls_dump($args, $request);
		$response = PL_HTTP::send_request(PL_Config::PL_API_USERS('update', 'request', 'url'), $request, PL_Config::PL_API_USERS('update', 'request', 'type'), false);
		$response = PL_Validate::attributes($response, PL_Config::PL_API_USERS('update', 'returns'));
		return $response;
	}

	public function subscriptions($args = array()) {
		$request = array_merge(array("api_key" => PL_Option_Helper::api_key()), PL_Validate::request($args, PL_Config::PL_API_USERS('subscriptions', 'args')));
		$response = PL_HTTP::send_request(trailingslashit(PL_Config::PL_API_USERS('subscriptions', 'request', 'url')), $request, PL_Config::PL_API_USERS('subscriptions', 'request', 'type'), false);
		$response = PL_Validate::attributes($response, PL_Config::PL_API_USERS('subscriptions', 'returns'));
		return $response;
	}

	public function start_subscription_trial($args = array()) {
		$request = array_merge(array("api_key" => PL_Option_Helper::api_key()), PL_Validate::request($args, PL_Config::PL_API_USERS('start_subscriptions', 'args')));
		$response = PL_HTTP::send_request(trailingslashit(PL_Config::PL_API_USERS('subscriptions', 'request', 'url')), $request, PL_Config::PL_API_USERS('start_subscriptions', 'request', 'type'), false);
		$response = PL_Validate::attributes($response, PL_Config::PL_API_USERS('start_subscriptions', 'returns'));
		return $response;
	}
	
}
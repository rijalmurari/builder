<?php 

PL_Cache::init();
class PL_Cache {

	const TTL_LOW  = 900; // 15 minutes
	const TTL_MED = 86400; // 24 hours
	const TTL_HIGH = 172800; // 48 hours

	// public static $offset = 3;
	public $group = 'general';
	public $transient_id = false;

	function __construct ($group = 'general') {
		// self::$offset = get_option('pls_cache_offset', 0);
		$this->group = $group;
	}

	public static function init () {

		// Allow cache to be cleared by going to url like http://example.com/?clear_cache
		if(isset($_GET['clear_cache']) || isset($_POST['clear_cache'])) {
			// style-util.php calls its PLS_Style::init() immediately
			// so this can't be tied to a hook
			self::invalidate();
		}

		// This is VITAL for caching to work properly...
		error_log('Here...');
		add_action( 'w3tc_register_fragment_groups', array(__CLASS__, 'register_fragment_groups') );

		add_action( 'wp_ajax_user_empty_cache', array(__CLASS__, 'ajax_clear') );
		add_action( 'switch_theme', array(__CLASS__, 'invalidate'));
		// flush cache when posts are trashed or untrashed -pek
		add_action( 'wp_trash_post', array(__CLASS__, 'invalidate'));
		add_action( 'untrash_post', array(__CLASS__, 'invalidate'));

	}

	// Register the fragment cache groups we will use for object caching...
	public static function register_fragment_groups() {
		// error_log('In register_fragment_groups()');
		$blog_groups = array();
		$network_groups = array();

		// Blog specific group and an array of actions that will trigger a flush of the group
		foreach ( $blog_groups as $group => $actions_arr ) {
			w3tc_register_fragment_group('pl_{$group}_', $actions_arr);
		}

		//If using MultiSite Network/site wide specific group and an array of actions that will trigger a flush of the group
		foreach ( $network_groups as $group => $actions_arr ) {
			w3tc_register_fragment_group_global('{$group}_network_', $actions_arr);
		}
	}

	public function get () {

		// Just ignore caching for admins and regular folk too!
		if(is_admin() || is_admin_bar_showing() || is_user_logged_in()) {
			return false;
		}

		// Backdoor to ignore the cache completely
		if(isset($_GET['no_cache']) || isset($_POST['no_cache'])) {
			return false;
		}
	
		// Build entry key
		$func_args = func_get_args();
		$arg_hash = rawToShortMD5(MD5_85_ALPHABET, md5(http_build_query( $func_args ), true));
		$this->transient_id = $this->group . /* $this->offset . */ '_' . $arg_hash;
        
        $transient = get_transient($this->transient_id);
        if ($transient) {
        	return $transient;
        } else {
        	return false;
        }
	}

	public function save ($result, $duration = 172800, $unique_id = false) {
		// Don't save any content from logged in users
		// We were getting things like "log out" links cached
		if ($this->transient_id && !is_user_logged_in()) {
			set_transient($this->transient_id, $result, $duration);
		}


	}

	public static function items ( $group = 'general' ) {
		// global $wpdb;
		// $placester_options = $wpdb->get_results('SELECT * FROM ' . $wpdb->prefix . 'options ' ."WHERE option_name LIKE '_transient_pl_%'", ARRAY_A);		
		// if ($placester_options && is_array($placester_options)) {
		// 	return $placester_options;
		// } else {
		// 	return false;
		// }
	}

	public static function clear( $group = 'general' ) {
	    // TODO: Delete all site transients (i.e., all site fragment/object cache groups...)
	
		//manually flush a blog specific group.
		w3tc_fragmentcache_flush_group('my_plugin_');

		//manually flush a network wide group
		w3tc_fragmentcache_flush_group('my_plugin_global_', true);
	}

	public static function ajax_clear() {
		self::clear();
		echo json_encode(array('result' => true, 'message' => 'You\'ve successfully cleared your cache'));
		die();
	}

	public static function delete($option_name) {
		$option_name = str_replace('_transient_', '', $option_name);
		$result = delete_transient( $option_name );
		return $result;
	}

	// Clear ALL blog cache groups...
	public static function invalidate() {

	}

//end class
}

// Flush our cache when admins save option pages or configure widgets
add_action('init', 'PL_Options_Save_Flush');
function PL_Options_Save_Flush() {
	// Check if options are being saved
	$doing_ajax = ( defined('DOING_AJAX') && DOING_AJAX );
	$editing_widgets = ( isset($_GET['savewidgets']) || isset($_POST['savewidgets']));
	if($_SERVER['REQUEST_METHOD'] == 'POST' && is_admin() && (!$doing_ajax || $editing_widgets)) {

		// Flush the cache
		PL_Cache::invalidate();

	}
}

/* Functions for converting between notations and short MD5 generation.
 * No license (public domain) but backlink is always welcome :)
 * By Proger_XP. http://proger.i-forge.net/Short_MD5
 * Usage: rawToShortMD5(MD5_85_ALPHABET, md5($str, true))
 * (passing true as the 2nd param to md5 returns raw binary, not a hex-encoded 32-char string)
 */
define('MD5_24_ALPHABET', '0123456789abcdefghijklmnopqrstuvwxyzABCDE');
define('MD5_85_ALPHABET', '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ~!@#$%^&*()"|;:?\/\'[]<>');

function RawToShortMD5($alphabet, $raw) {
  $result = '';
  $length = strlen(DecToBase($alphabet, 2147483647));

  foreach (str_split($raw, 4) as $dword) {
    $dword = ord($dword[0]) + ord($dword[1]) * 256 + ord($dword[2]) * 65536 + ord($dword[3]) * 16777216;
    $result .= str_pad(DecToBase($alphabet, $dword), $length, $alphabet[0], STR_PAD_LEFT);
  }

  return $result;
}

function DecToBase(&$alphabet, $dword) {
  $rem = fmod($dword, strlen($alphabet));
  if ($dword < strlen($alphabet)) {
    return $alphabet[$rem];
  } else {
    return DecToBase($alphabet, ($dword - $rem) / strlen($alphabet)).$alphabet[$rem];
  }
}
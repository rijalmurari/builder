<?php 

PL_Cache::init();
class PL_Cache {

	const TTL_LOW  = 900; // 15 minutes
	const TTL_MED = 86400; // 24 hours
	const TTL_HIGH = 172800; // 48 hours

	const KEY_PREFIX = 'PL_';
	private static $logging_enabled = true; // Set to FALSE eventually...

	// public static $offset = 3;
	public $group = 'general';
	public $transient_id = false;

	function __construct ($group = 'general') {
		// self::$offset = get_option('pls_cache_offset', 0);
		$this->group = preg_replace( "/\W/", "_", strtolower( $group ) );
	}

	private static function cache_log ($msg) {
		if ( !empty($msg) && self::$logging_enabled ) {
			$msg = '[' . date("M-d-Y g:i A T") . '] ' . $msg . "\n";
			error_log($msg, 3, "/Users/iantendick/dev/wp_cache.log");
		}
	}

	private static function log_trace ($trace) {
		// Print the file, the function in that file, and the specific line where the given caching call 
		// is being made from to the cache log...
		if ( isset($trace[1]) ) {
			$file = str_replace('/Users/iantendick/Dev/wordpress/', '', @$trace[1]['file']);
			$caller = $file . ', ' . @$trace[2]['function'] . ', ' . @$trace[1]['line'];
			self::cache_log('Caller: ' . $caller);
		}
	}

	public static function init () {
		// Allow cache to be cleared by going to url like http://example.com/?clear_cache
		if(isset($_GET['clear_cache']) || isset($_POST['clear_cache'])) {
			// style-util.php calls its PLS_Style::init() immediately
			// so this can't be tied to a hook
			self::invalidate();
		}

		// This is VITAL for caching to work properly...
		add_action( 'w3tc_register_fragment_groups', array(__CLASS__, 'register_fragment_groups') );

		add_action( 'wp_ajax_user_empty_cache', array(__CLASS__, 'ajax_clear') );
		add_action( 'switch_theme', array(__CLASS__, 'invalidate'));
		// flush cache when posts are trashed or untrashed -pek
		add_action( 'wp_trash_post', array(__CLASS__, 'invalidate'));
		add_action( 'untrash_post', array(__CLASS__, 'invalidate'));

	}

	// Register the fragment cache groups we will use for object caching...
	public static function register_fragment_groups() {
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
		self::cache_log('================================');
		self::cache_log('GET cached value...');

		self::log_trace(debug_backtrace());

		// Just ignore caching for authenticated users... (this WILL change!)
		if( is_user_logged_in() ) {
			self::cache_log('Circumventing caching...');
			return false;
		}

		// Backdoor to ignore the cache completely
		if(isset($_GET['no_cache']) || isset($_POST['no_cache'])) {
			self::cache_log('Backdoor ignore caching...');
			return false;
		}
	
		// Build entry key
		self::cache_log('group: ' . $this->group);
		
		$func_args = func_get_args();
		self::cache_log('func_args: ' . serialize($func_args));
		
		$arg_hash = rawToShortMD5(MD5_85_ALPHABET, md5(http_build_query( $func_args ), true));
		self::cache_log('arg_hash: ' . $arg_hash);
		
		$this->transient_id = self::KEY_PREFIX . $this->group . /* $this->offset . */ '_' . $arg_hash;
        self::cache_log('transient_id: '. $this->transient_id);

        $transient = get_transient($this->transient_id);
        if ($transient) {
        	self::cache_log('CACHE HIT!');
        	self::cache_log('Returning: ' . $transient);
        	return $transient;
        } else {
        	self::cache_log('CACHE MISS');
        	return false;
        }
	}

	public function save ($result, $duration = self::TTL_HIGH, $unique_id = false) {
		self::cache_log('================================');
		self::cache_log('SAVE entry to cache...');
		
		self::log_trace(debug_backtrace());

		self::cache_log('Key: ' . $this->transient_id);
		// $entry = is_array($result) ? serialize($result) : $result;
		// self::cache_log('Value: ' . $entry);
		self::cache_log('TTL: ' . $duration);
		self::cache_log('Is user logged in: ' . ( is_user_logged_in() ? 'YES' : 'NO' ) );

		// Don't save any content from logged in users
		// We were getting things like "log out" links cached
		if ($this->transient_id && !is_user_logged_in()) {
			self::cache_log('Would have saved/cached entry!');
			// self::cache_log('ENTRY CACHED!');
			// set_transient($this->transient_id, $result, $duration);
		}
		else {
			self::cache_log('NOT saving/caching entry...');
		}
	}

	public static function items ( $group = 'general' ) {
		// TODO: Retrieve items based on group...this might NOT be possible, so this function make be removed
	}

	public static function clear( $group = 'general' ) {
	    error_log('Attempting to clear cache...');
	    // TODO: Delete all site transients (i.e., all site fragment/object cache groups...)
	
		//manually flush a blog specific group.
		// w3tc_fragmentcache_flush_group('my_plugin_');

		//manually flush a network wide group
		// w3tc_fragmentcache_flush_group('my_plugin_global_', true);
	}

	public static function ajax_clear() {
		self::cache_log('AJAX clear cache...');
		self::clear();
		echo json_encode(array('result' => true, 'message' => 'You\'ve successfully cleared your cache'));
		die();
	}

	public static function delete($option_name) {
		self::cache_log('================================');
		self::cache_log('Delete cache entry: ' . $option_name);
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
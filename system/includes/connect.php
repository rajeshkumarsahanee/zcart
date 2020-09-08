<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once dirname(dirname(dirname(__FILE__))) . '/config.php';

error_reporting(E_ALL);
// Connect to SQL Server
$conn = new mysqli($sql_db_host, $sql_db_user, $sql_db_pass, $sql_db_name);
// Handling Server Errors
$ServerErrors = array();
if (mysqli_connect_errno()) {
    $ServerErrors[] = "Failed to connect to MySQL: " . mysqli_connect_error();
}
if (!function_exists('curl_init')) {
    $ServerErrors[] = "PHP CURL is NOT installed on your web server !";
}
if (!extension_loaded('gd') && !function_exists('gd_info')) {
    $ServerErrors[] = "PHP GD library is NOT installed on your web server !";
}
if (!version_compare(PHP_VERSION, '5.4.0', '>=')) {
    $ServerErrors[] = "Required PHP_VERSION >= 5.4.0 , Your PHP_VERSION is : " . PHP_VERSION . "\n";
}

if (isset($ServerErrors) && !empty($ServerErrors)) {
    foreach ($ServerErrors as $Error) {
        echo "<h3>" . $Error . "</h3>";
    }
    die();
}

require dirname(dirname(__FILE__)) . '/import/PHPMailer/src/Exception.php';
require dirname(dirname(__FILE__)) . '/import/PHPMailer/src/PHPMailer.php';
require dirname(dirname(__FILE__)) . '/import/PHPMailer/src/SMTP.php';

$mail = new PHPMailer;

/*
  $utf8_ch  = mysqli_query($conn, 'SET NAMES utf8');
  $utf8_ch .= mysqli_query($conn, 'SET CHARACTER SET utf8');
  $utf8_ch .= mysqli_query($conn, 'SET COLLATION_CONNECTION="utf8_general_ci"');
 */

$baned_ips = getBanned('user');

if (in_array($_SERVER["REMOTE_ADDR"], $baned_ips)) {
    exit();
}

$sys = getConfig();

// Config Url
$sys['theme'] = isset($sys['theme']) ? $sys['theme'] : "default_theme";
$sys['theme_url'] = $site_url . '/themes/' . $sys['theme'];
$sys['site_url'] = $site_url;
$sys['site_name'] = $site_name;
$sys['upload_root'] = isset($sys['upload_root']) ? $sys['upload_root'] : "uploads";
$sys['emo'] = $emo;
$sys['site_pages'] = array();
$sys['script_version'] = '1.0.0';

$http_header = 'http://';
if (!empty($_SERVER['HTTPS'])) {
    $http_header = 'https://';
}

$sys['actual_link'] = $http_header . $_SERVER['HTTP_HOST'] . urlencode($_SERVER['REQUEST_URI']);
// Define Cache Vireble
$cache = new Cache();
if (isset($sys['config']['cacheSystem']) && $sys['config']['cacheSystem'] == 1) {
    $cache->OpenCacheDir();
}

// Language Function
if (isset($_GET['lang']) AND ! empty($_GET['lang'])) {
    $lang_name = secure(strtolower($_GET['lang']));
    $lang_path = 'system/languages/' . $lang_name . '.php';
    if (file_exists($lang_path)) {
        $_SESSION['lang'] = $lang_name;
        if (isUserLogged() === true) {
            updateUserMeta(getUserLoggedId(), "lang", $lang_name);
            if ($sys['cache_system'] == 1) {
                $cache->delete(md5(getUserLoggedId()) . '_U_Data.tmp');
            }
        }
    }
}
if (empty($_SESSION['lang'])) {
    $_SESSION['lang'] = isset($sys['lang']) ? $sys['lang'] : "english";
}
$sys['language'] = $_SESSION['lang'];
$sys['language_type'] = 'ltr';
// Add rtl languages here.
$rtl_langs = array(
    'arabic'
);
// checking if corrent language is rtl.
foreach ($rtl_langs as $lang) {
    if ($sys['language'] == strtolower($lang)) {
        $sys['language_type'] = 'rtl';
    }
}

// Icons Virables
$error_icon = '<i class="fa fa-exclamation-circle"></i> ';
$success_icon = '<i class="fa fa-check"></i> ';
// Include Language File
require dirname(dirname(__FILE__)) . '/languages/' . $sys['language'] . '.php';

$sys['marker'] = '?';
if (isset($sys['config']['seoLink']) && $sys['config']['seoLink'] == 0) {
    $sys['marker'] = '&';
}

$sys['feelingIcons'] = array(
    'happy' => 'smile',
    'loved' => 'heart-eyes',
    'sad' => 'disappointed',
    'so_sad' => 'sob',
    'angry' => 'angry',
    'confused' => 'confused',
    'smirk' => 'smirk',
    'broke' => 'broken-heart',
    'expressionless' => 'expressionless',
    'cool' => 'sunglasses',
    'funny' => 'joy',
    'tired' => 'tired-face',
    'lovely' => 'heart',
    'blessed' => 'innocent',
    'shocked' => 'scream',
    'sleepy' => 'sleeping',
    'pretty' => 'relaxed',
    'bored' => 'unamused'
);

$sys['statuses'] = array(
    'A' => "Active",
    'I' => "Inactive",
    'T' => "Deleted",
    'P' => "Pending",
    'S' => "Success",
    'C' => "Canceled",
    'PB' => "Published",
    'DF' => "Draft"
);

$sys['roles'] = array(
    "administrator" => "Administrator",
    "editor" => "Editor",
    "author" => "Author",
    "contributer" => "Contributer",
    "subscriber" => "Subscriber",
    "buyer" => "Buyer",
    "seller" => "Seller",
    "buyer,seller" => "Buyer+Seller"
);

$sys['post_types']['attachment'] = array("name" => "Attachment", "singular" => "Attachment", "plural" => "Attachments", "list" => true);
$sys['post_types']['custom_css'] = array("name" => "Custom CSS", "singular" => "Custom CSS", "plural" => "Custom CSS", "list" => false);
$sys['post_types']['nav_menu_item'] = array("name" => "Menu", "singular" => "Menu", "plural" => "Menus", "list" => false);
$sys['post_types']['post'] = array("name" => "Post", "singular" => "Post", "plural" => "Posts", "list" => true);
$sys['post_types']['page'] = array("name" => "Page", "singular" => "Page", "plural" => "Pages", "list" => true);
$sys['post_types']['revision'] = array("name" => "Revision", "singular" => "Revision", "plural" => "Revisions", "list" => false);

$sys['taxonomies']['category'] = array("name" => "Category", "singular" => "Category", "plural" => "Categories", "list" => true);
$sys['taxonomies']['tag'] = array("name" => "Tag", "singular" => "Tag", "plural" => "Tags", "list" => true);

$sys['post_formats'] = array(
    "standard" => array("name"=>"Standard", "icon" => "fa fa-pin"),
    "aside" => array("name" => "Aside", "icon" => ""),
    "chat" => array("name" => "Chat", "icon" => ""),
    "gallery" => array("name" => "Gallery", "icon" => ""),
    "link" => array("name" =>"Link", "icon" => ""),
    "Image" => array("name" => "Image", "icon" => ""),
    "quote" => array("name" => "Quote", "icon" => ""),
    "status" => array("name" => "Status", "icon" => ""),
    "audio" => array("name" => "Audio", "icon" => ""),
    "video" => array("name" => "Video", "icon" => "")
);
?>
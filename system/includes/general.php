<?php

define('DASHBOARD_SECTION', '1');

/* Constants Starts Here */

/* CMS */
define('MANAGE_POSTS_SECTION', '2');
define('MANAGE_MEDIA_SECTION', '3');
define('MANAGE_PAGES_SECTION', '4');
define('MANAGE_COMMENTS_SECTION', '5');
define('MANAGE_APPEARANCE_SECTION', '6');
define('MANAGE_USERS_SECTION', '7');
define('MANAGE_SETTINGS_SECTION', '8');

/* CATALOG */
define('MANAGE_SHOPS_SECTION', '9');
define('PRODUCT_BRANDS_SECTION', '10');
define('PRODUCT_CATEGORIES_SECTION', '11');
define('MANAGE_PRODUCTS_SECTION', '12');
define('PRODUCT_REVIEWS_SECTION', '13');
define('PRODUCT_TAGS_SECTION', '14');
define('PRODUCT_OPTIONS_SECTION', '15');
define('SELLER_OPTIONS_SECTION', '16');
define('FILTERS_SECTION', '17');
define('ATTRIBUTES_SPECIFICATIONS_SECTION', '18');

/* BUYER & SELLER */
define('MANAGE_BUYERS_SELLERS_SECTION', '19');
define('SELLER_APPROVAL_REQUESTS_SECTION', '20');
define('SELLER_APPROVAL_FORM_SECTION', '21');
define('SELLER_REQUESTS_SECTION', '22');
define('FUNDS_WITHDRAWAL_REQUESTS_SECTION', '23');

/* AFFILIATES */
define('AFFILIATE_MODULE_SECTION', '24');

/* CMS */
define('COLLECTIONS_SECTION', '25');
define('CONTENT_BLOCK_SECTION', '26');
define('LANGUAGE_LABELS_SECTION', '27');
define('SLIDES_MANAGEMENT_SECTION', '28');
define('BANNER_MANAGEMENT_SECTION', '29');
define('EMPTY_CART_ITEMS_SECTION', '30');
define('FAQ_CATEGORIES_SECTION', '31');
define('FAQs_MANAGEMENT_SECTION', '32');
define('TESTIMONIALS_SECTION', '33');
define('DISCOUNT_COUPONS_SECTION', '34');

/* ORDERS */
define('ORDERS_SECTION', '35');
define('ORDERS_RETURN_REQUESTS_SECTION', '36');
define('ORDERS_CANCELLATION_REQUESTS_SECTION', '37');

/* REPORTS */
define('REPORTS_SECTION', '38');

/* SETTINGS */
define('PORTAL_SETTINGS_SECTION', '39');
define('COUNTRY_MANAGEMENT_SECTION', '40');
define('ZONE_MANAGEMENT_SECTION', '41');
define('STATE_MANAGEMENT_SECTION', '42');
define('CURRENCY_MANAGEMENT_SECTION', '43');
define('REASONS_SECTION', '44');
define('SHIPPING_SECTION', '45');
define('COMMISSION_SETTINGS_SECTION', '46');
define('AFFILIATE_COMMISSION_SETTING_SECTION', '47');
define('PAYMENT_METHODS_SECTION', '48');
define('EMAIL_TEMPLATE_SETTINGS_SECTION', '49');
define('DATABASE_BACKUP_RESTORE_SECTION', '50');
define('SERVER_INFO_SECTION', '51');

/* SUBSCRIPTIONS */
define('SUBSCRIPTION_PAYMENT_METHODS_SECTION', '52');
define('SUBSCRIPTION_PACKAGES_SECTION', '53');
define('SUBSCRIPTION_DISCOUNT_COUPONS_SECTION', '54');
define('SUBSCRIPTION_ORDERS_SECTION', '55');

/* EXPORT/IMPORT */
define('BULK_IMPORT_EXPORT_SECTION', '56');

/* SMART RECOMMENDATIONS */
define('SMART_RECOMMENDATIONS_WEIGHTAGES_SECTION', '57');
define('SMART_RECOMMENDATIONS_PRODUCTS_SECTION', '58');
define('PRODUCTS_BROWSING_HISTORY_SECTION', '59');

/* PPC MANAGEMENT */
define('MANAGE_ADVERTISERS_SECTION', '60');
define('PPC_PAYMENT_METHODS_SECTION', '61');
define('PPC_PROMOTIONS_SECTION', '62');

/* MESSAGES */
define('MESSAGES_SECTION', '63');

//define('WITHDRAWAL_REQUESTS_SECTION','28');
//define('ORDER_CANCELLATION_SECTION','28');

/* ADMIN USERS */
define('STAFF_MEMBERS_SECTION', '64');
//define('SUPPLIER_APPROVAL_SECTION','28');
//define('SUPPLIER_APPROVAL_REQUESTS_SECTION','28');
//define('SUPPLIER_REQUESTS_SECTION','28');
define('PPC_FEE_SETTINGS_SECTION', '65');

define('ROLE_USER', 'U');
define('ROLE_ADMIN', 'A');
define('ACTIVE', 'A');
define('INACTIVE', 'I');
define('PENDING', 'P');

define("YES", "Y");
define("NO", "N");

define('SUCCESS_RESPOSE_CODE', '0');
define('ERROR_RESPOSE_CODE', '1');
define('SQL_ERROR_RESPOSE_CODE', '11');

define('YEAR', 'Y');
define('MONTH', 'M');
define('DAY', 'D');
define('YEAR_MONTH', 'YM');
define('YEAR_MONTH_DAY', 'YMD');

define("TXNID_PREFIX", "TXN");

/* Constants Ends Here */

function loadPage($page_url = '') {
    global $sys;
    $page = './themes/' . $sys['theme'] . "/" . $page_url . '.phtml';
    $page_content = '';
    ob_start();
    require($page);
    $page_content = ob_get_contents();
    ob_end_clean();
    return $page_content;
}

function url_slug($str, $options = array()) {
    // Make sure string is in UTF-8 and strip invalid UTF-8 characters
    $str = mb_convert_encoding((string) $str, 'UTF-8', mb_list_encodings());
    $defaults = array(
        'delimiter' => '-',
        'limit' => null,
        'lowercase' => true,
        'replacements' => array(),
        'transliterate' => false
    );
    // Merge options
    $options = array_merge($defaults, $options);
    $char_map = array(
        // Latin
        '??' => 'A', '??' => 'A', '??' => 'A', '??' => 'A', '??' => 'A', '??' => 'A', '??' => 'AE', '??' => 'C', '??' => 'E', '??' => 'E', '??' => 'E', '??' => 'E', '??' => 'I', '??' => 'I', '??' => 'I', '??' => 'I', '??' => 'D', '??' => 'N', '??' => 'O', '??' => 'O', '??' => 'O', '??' => 'O', '??' => 'O', '??' => 'O', '??' => 'O', '??' => 'U', '??' => 'U', '??' => 'U', '??' => 'U', '?' => 'U', '??' => 'Y', '??' => 'TH', '??' => 'ss', '?' => 'a', '?' => 'a', '?' => 'a', '?' => 'a', '?' => 'a', '?' => 'a', '?' => 'ae', '?' => 'c', '?' => 'e', '?' => 'e', '?' => 'e', '?' => 'e', '?' => 'i', '?' => 'i', '?' => 'i', '?' => 'i', '?' => 'd', '?' => 'n', '?' => 'o', '?' => 'o', '?' => 'o', '?' => 'o', '?' => 'o', '??' => 'o', '?' => 'o', '?' => 'u', '?' => 'u', '?' => 'u', '?' => 'u', '?' => 'u', '?' => 'y', '?' => 'th', '?' => 'y',
        // Latin symbols
        '?' => '(c)',
        // Greek
        '??' => 'A', '??' => 'B', '??' => 'G', '??' => 'D', '??' => 'E', '??' => 'Z', '??' => 'H', '??' => '8', '??' => 'I', '??' => 'K', '??' => 'L', '??' => 'M', '??' => 'N', '??' => '3', '??' => 'O', '?' => 'P', '?' => 'R', '?' => 'S', '?' => 'T', '?' => 'Y', '?' => 'F', '?' => 'X', '?' => 'PS', '?' => 'W', '??' => 'A', '??' => 'E', '??' => 'I', '??' => 'O', '??' => 'Y', '??' => 'H', '??' => 'W', '?' => 'I', '?' => 'Y', '?' => 'a', '?' => 'b', '?' => 'g', '?' => 'd', '?' => 'e', '?' => 'z', '?' => 'h', '?' => '8', '?' => 'i', '?' => 'k', '?' => 'l', '?' => 'm', '?' => 'n', '?' => '3', '?' => 'o', '??' => 'p', '??' => 'r', '??' => 's', '??' => 't', '??' => 'y', '??' => 'f', '??' => 'x', '??' => 'ps', '??' => 'w', '?' => 'a', '?' => 'e', '?' => 'i', '??' => 'o', '??' => 'y', '?' => 'h', '??' => 'w', '??' => 's', '??' => 'i', '?' => 'y', '??' => 'y', '??' => 'i',
        // Turkish
        '??' => 'S', '?' => 'I', '??' => 'C', '??' => 'U', '??' => 'O', '??' => 'G', '??' => 's', '?' => 'i', '?' => 'c', '?' => 'u', '?' => 'o', '??' => 'g',
        // Russian
        '??' => 'A', '??' => 'B', '??' => 'V', '??' => 'G', '??' => 'D', '??' => 'E', '??' => 'Yo', '??' => 'Zh', '??' => 'Z', '??' => 'I', '??' => 'J', '??' => 'K', '??' => 'L', '??' => 'M', '??' => 'N', '??' => 'O', '??' => 'P', '?' => 'R', '?' => 'S', '?' => 'T', '?' => 'U', '?' => 'F', '?' => 'H', '?' => 'C', '?' => 'Ch', '?' => 'Sh', '?' => 'Sh', '?' => '', '?' => 'Y', '?' => '', '?' => 'E', '?' => 'Yu', '?' => 'Ya', '?' => 'a', '?' => 'b', '?' => 'v', '?' => 'g', '?' => 'd', '?' => 'e', '??' => 'yo', '?' => 'zh', '?' => 'z', '?' => 'i', '?' => 'j', '?' => 'k', '?' => 'l', '?' => 'm', '?' => 'n', '?' => 'o', '?' => 'p', '??' => 'r', '??' => 's', '??' => 't', '??' => 'u', '??' => 'f', '??' => 'h', '??' => 'c', '??' => 'ch', '??' => 'sh', '??' => 'sh', '??' => '', '??' => 'y', '??' => '', '??' => 'e', '??' => 'yu', '??' => 'ya',
        // Ukrainian
        '??' => 'Ye', '??' => 'I', '??' => 'Yi', '??' => 'G', '??' => 'ye', '??' => 'i', '??' => 'yi', '??' => 'g',
        // Czech
        '??' => 'C', '??' => 'D', '??' => 'E', '??' => 'N', '??' => 'R', '?' => 'S', '?' => 'T', '?' => 'U', '?' => 'Z', '??' => 'c', '??' => 'd', '??' => 'e', '??' => 'n', '??' => 'r', '?' => 's', '?' => 't', '?' => 'u', '?' => 'z',
        // Polish
        '??' => 'A', '??' => 'C', '??' => 'e', '??' => 'L', '??' => 'N', '??' => 'o', '??' => 'S', '?' => 'Z', '?' => 'Z', '??' => 'a', '??' => 'c', '??' => 'e', '??' => 'l', '??' => 'n', '?' => 'o', '??' => 's', '?' => 'z', '?' => 'z',
        // Latvian
        '??' => 'A', '??' => 'C', '??' => 'E', '?' => 'G', '?' => 'i', '?' => 'k', '?' => 'L', '??' => 'N', '?' => 'S', '?' => 'u', '?' => 'Z', '??' => 'a', '??' => 'c', '??' => 'e', '?' => 'g', '?' => 'i', '?' => 'k', '?' => 'l', '??' => 'n', '?' => 's', '?' => 'u', '?' => 'z');
    // Make custom replacements
    $str = preg_replace(array_keys($options['replacements']), $options['replacements'], $str);
    // Transliterate characters to ASCII
    if ($options['transliterate']) {
        $str = str_replace(array_keys($char_map), $char_map, $str);
    }
    // Replace non-alphanumeric characters with our delimiter
    $str = preg_replace('/[^\p{L}\p{Nd}]+/u', $options['delimiter'], $str);
    // Remove duplicate delimiters
    $str = preg_replace('/(' . preg_quote($options['delimiter'], '/') . '){2,}/', '$1', $str);
    // Truncate slug to max. characters
    $str = mb_substr($str, 0, ($options['limit'] ? $options['limit'] : mb_strlen($str, 'UTF-8')), 'UTF-8');
    // Remove delimiter from ends
    $str = trim($str, $options['delimiter']);
    return $options['lowercase'] ? mb_strtolower($str, 'UTF-8') : $str;
}

function seoLink($query = '') {
    global $sys, $config;
    if (isset($sys['config']['seoLink']) && $sys['config']['seoLink'] == 1) {
        $query = preg_replace(array(
            '/^index\.php\?tab1=welcome&tab2=password_reset&user_id=([A-Za-z0-9_]+)$/i',
            '/^index\.php\?tab1=welcome&last_url=(.*)$/i',
            '/^index\.php\?tab1=([^\/]+)&query=$/i',
            '/^index\.php\?tab1=post&id=([A-Za-z0-9_]+)$/i',
            '/^index\.php\?tab1=post&id=([A-Za-z0-9_]+)&ref=([A-Za-z0-9_]+)$/i',
            '/^index\.php\?tab1=terms&page=contact-us$/i',
            '/^index\.php\?tab1=([^\/]+)&u=([A-Za-z0-9_]+)$/i',
            '/^index\.php\?tab1=timeline&u=([A-Za-z0-9_]+)&type=([A-Za-z0-9_]+)$/i',
            '/^index\.php\?tab1=messages&user=([A-Za-z0-9_]+)$/i',
            '/^index\.php\?tab1=setting&page=([A-Za-z0-9_-]+)$/i',
            '/^index\.php\?tab1=setting&user=([A-Za-z0-9_]+)&page=([A-Za-z0-9_-]+)$/i',
            '/^index\.php\?tab1=([^\/]+)&app_id=([A-Za-z0-9_]+)$/i',
            '/^index\.php\?tab1=([^\/]+)&hash=([^\/]+)$/i',
            '/^index\.php\?tab1=([^\/]+)&tab2=([^\/]+)$/i',
            '/^index\.php\?tab1=([^\/]+)&type=([^\/]+)$/i',
            '/^index\.php\?tab1=([^\/]+)&p=([^\/]+)$/i',
            '/^index\.php\?tab1=([^\/]+)&g=([^\/]+)$/i',
            '/^index\.php\?tab1=page-setting&page=([A-Za-z0-9_]+)&tab3=([A-Za-z0-9_-]+)$/i',
            '/^index\.php\?tab1=page-setting&page=([^\/]+)$/i',
            '/^index\.php\?tab1=group-setting&group=([A-Za-z0-9_]+)&tab3=([A-Za-z0-9_-]+)$/i',
            '/^index\.php\?tab1=group-setting&group=([^\/]+)$/i',
            '/^index\.php\?tab1=admincp&page=([^\/]+)$/i',
            '/^index\.php\?tab1=game&id=([^\/]+)$/i',
            '/^index\.php\?tab1=albums&user=([A-Za-z0-9_]+)$/i',
            '/^index\.php\?tab1=create-album&album=([A-Za-z0-9_]+)$/i',
            '/^index\.php\?tab1=([^\/]+)$/i'
                ), array(
            $config['site_url'] . '/password-reset/$1',
            $config['site_url'] . '/welcome/?last_url=$1',
            $config['site_url'] . '/search/$2',
            $config['site_url'] . '/post/$1',
            $config['site_url'] . '/post/$1?ref=$2',
            $config['site_url'] . '/terms/contact-us',
            $config['site_url'] . '/$2',
            $config['site_url'] . '/$1/$2',
            $config['site_url'] . '/messages/$1',
            $config['site_url'] . '/setting/$1',
            $config['site_url'] . '/setting/$1/$2',
            $config['site_url'] . '/$1/$2',
            $config['site_url'] . '/$1/$2',
            $config['site_url'] . '/$1/$2',
            $config['site_url'] . '/$1/$2',
            $config['site_url'] . '/p/$2',
            $config['site_url'] . '/g/$2',
            $config['site_url'] . '/page-setting/$1/$2',
            $config['site_url'] . '/page-setting/$1',
            $config['site_url'] . '/group-setting/$1/$2',
            $config['site_url'] . '/group-setting/$1',
            $config['site_url'] . '/admincp/$1',
            $config['site_url'] . '/game/$1',
            $config['site_url'] . '/albums/$1',
            $config['site_url'] . '/create-album/$1',
            $config['site_url'] . '/$1'
                ), $query);
    } else {
        $query = $config['site_url'] . '/' . $query;
    }
    return $query;
}

function isUserLogged($role = null) {
    if (isset($_COOKIE['user_id']) && !isset($_SESSION['user_id'])) {
        $user = getUser($_COOKIE['user_id']);
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = isset($user['metas']['role']) ? $user['metas']['role'] : "na";
        $_SESSION['display_name'] = $user['display_name'];
        $_SESSION['registered'] = date("M. Y", strtotime($user['registered']));
    }
    if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
        if ($role == null) {
            return true;
        }
        if (isset($_SESSION['role']) && trim($_SESSION['role']) == $role) {
            return true;
        }
    }
    return false;
}

function getUserLoggedId() {
    if (isset($_COOKIE['user_id'])) {
        return $_COOKIE['user_id'];
    }
    if (isset($_SESSION['user_id'])) {
        return $_SESSION['user_id'];
    }
    return 0;
}

function getUserLoggedRole() {
    if (isset($_SESSION['role'])) {
        return $_SESSION['role'];
    }
    return "visitor";
}

function redirect($url) {
    return header("Location: {$url}");
}

//function link($string) {
//    global $site_url;
//    return $site_url . '/' . $string;
//}

function sqlResult($res, $row = 0, $col = 0) {
    $numrows = mysqli_num_rows($res);
    if ($numrows && $row <= ($numrows - 1) && $row >= 0) {
        mysqli_data_seek($res, $row);
        $resrow = (is_numeric($col)) ? mysqli_fetch_row($res) : mysqli_fetch_assoc($res);
        if (isset($resrow[$col])) {
            return $resrow[$col];
        }
    }
    return false;
}

function secure($string, $censored_words = 1) {
    global $conn;
    $string = trim($string);
    $string = mysqli_real_escape_string($conn, $string);
    $string = htmlspecialchars($string, ENT_QUOTES);
    $string = str_replace('\\r\\n', '<br><br>', $string);
    $string = str_replace('\\r', '<br>', $string);
    $string = str_replace('\\n\\n', '<br><br>', $string);
    $string = str_replace('\\n', '<br>', $string);
    $string = str_replace('\\n', '<br>', $string);
    $string = stripslashes($string);
    $string = str_replace('&amp;#', '&#', $string);
    if ($censored_words == 1) {
        global $config;
        $censored_words = @explode(",", $config['censored_words']);
        foreach ($censored_words as $censored_word) {
            $censored_word = trim($censored_word);
            $string = str_replace($censored_word, '****', $string);
        }
    }
    return $string;
}

function decode($string) {
    return htmlspecialchars_decode($string);
}

function generateKey($minlength = 20, $maxlength = 20, $uselower = true, $useupper = true, $usenumbers = true, $usespecial = false) {
    $charset = '';
    if ($uselower) {
        $charset .= "abcdefghijklmnopqrstuvwxyz";
    }
    if ($useupper) {
        $charset .= "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    }
    if ($usenumbers) {
        $charset .= "123456789";
    }
    if ($usespecial) {
        $charset .= "~@#$%^*()_+-={}|][";
    }
    if ($minlength > $maxlength) {
        $length = mt_rand($maxlength, $minlength);
    } else {
        $length = mt_rand($minlength, $maxlength);
    }
    $key = '';
    for ($i = 0; $i < $length; $i++) {
        $key .= $charset[(mt_rand(0, strlen($charset) - 1))];
    }
    return $key;
}

function resizeCropImage($max_width, $max_height, $source_file, $dst_dir, $quality = 80) {
    $imgsize = @getimagesize($source_file);
    $width = $imgsize[0];
    $height = $imgsize[1];
    $mime = $imgsize['mime'];
    switch ($mime) {
        case 'image/gif':
            $image_create = "imagecreatefromgif";
            $image = "imagegif";
            break;
        case 'image/png':
            $image_create = "imagecreatefrompng";
            $image = "imagepng";
            $quality = 7;
            break;
        case 'image/jpeg':
            $image_create = "imagecreatefromjpeg";
            $image = "imagejpeg";
            $quality = 80;
            break;
        default:
            return false;
            break;
    }
    $dst_img = @imagecreatetruecolor($max_width, $max_height);
    $src_img = $image_create($source_file);
    $width_new = $height * $max_width / $max_height;
    $height_new = $width * $max_height / $max_width;
    if ($width_new > $width) {
        $h_point = (($height - $height_new) / 2);
        @imagecopyresampled($dst_img, $src_img, 0, 0, 0, $h_point, $max_width, $max_height, $width, $height_new);
    } else {
        $w_point = (($width - $width_new) / 2);
        @imagecopyresampled($dst_img, $src_img, 0, 0, $w_point, 0, $max_width, $max_height, $width_new, $height);
    }
    $image($dst_img, $dst_dir, $quality);
    if ($dst_img)
        @imagedestroy($dst_img);
    if ($src_img)
        @imagedestroy($src_img);
}

function timeElapsedString($ptime) {
    global $sys;
    $etime = time() - $ptime;
    if ($etime < 1) {
        return '0 seconds';
    }
    $a = array(
        365 * 24 * 60 * 60 => $sys['lang']['year'],
        30 * 24 * 60 * 60 => $sys['lang']['month'],
        24 * 60 * 60 => $sys['lang']['day'],
        60 * 60 => $sys['lang']['hour'],
        60 => $sys['lang']['minute'],
        1 => $sys['lang']['second']
    );
    $a_plural = array(
        $sys['lang']['year'] => $sys['lang']['years'],
        $sys['lang']['month'] => $sys['lang']['months'],
        $sys['lang']['day'] => $sys['lang']['days'],
        $sys['lang']['hour'] => $sys['lang']['hours'],
        $sys['lang']['minute'] => $sys['lang']['minutes'],
        $sys['lang']['second'] => $sys['lang']['seconds']
    );
    foreach ($a as $secs => $str) {
        $d = $etime / $secs;
        if ($d >= 1) {
            $r = round($d);
            if ($sys['language_type'] == 'rtl') {
                $time_ago = $sys['lang']['time_ago'] . ' ' . $r . ' ' . ($r > 1 ? $a_plural[$str] : $str);
            } else {
                $time_ago = $r . ' ' . ($r > 1 ? $a_plural[$str] : $str) . ' ' . $sys['lang']['time_ago'];
            }
            return $time_ago;
        }
    }
}

function folderSize($dir) {
    $count_size = 0;
    $count = 0;
    $dir_array = scandir($dir);
    foreach ($dir_array as $key => $filename) {
        if ($filename != ".." && $filename != "." && $filename != ".htaccess") {
            if (is_dir($dir . "/" . $filename)) {
                $new_foldersize = folderSize($dir . "/" . $filename);
                $count_size = $count_size + $new_foldersize;
            } else if (is_file($dir . "/" . $filename)) {
                $count_size = $count_size + filesize($dir . "/" . $filename);
                $count++;
            }
        }
    }
    return $count_size;
}

function sizeFormat($bytes) {
    $kb = 1024;
    $mb = $kb * 1024;
    $gb = $mb * 1024;
    $tb = $gb * 1024;
    if (($bytes >= 0) && ($bytes < $kb)) {
        return $bytes . ' B';
    } elseif (($bytes >= $kb) && ($bytes < $mb)) {
        return ceil($bytes / $kb) . ' KB';
    } elseif (($bytes >= $mb) && ($bytes < $gb)) {
        return ceil($bytes / $mb) . ' MB';
    } elseif (($bytes >= $gb) && ($bytes < $tb)) {
        return ceil($bytes / $gb) . ' GB';
    } elseif ($bytes >= $tb) {
        return ceil($bytes / $tb) . ' TB';
    } else {
        return $bytes . ' B';
    }
}

function clearCache() {
    $path = 'cache';
    if ($handle = opendir($path)) {
        while (false !== ($file = readdir($handle))) {
            if (strripos($file, '.tmp') !== false) {
                @unlink($path . '/' . $file);
            }
        }
    }
}

function getThemeData($file, $headers = array("name" => "Name")) {
    $file_data = file_get_contents($file);

    foreach ($headers as $field => $regex) {
        if (preg_match('/^[ \t\/*#@]*' . preg_quote($regex, '/') . ':(.*)$/mi', $file_data, $match) && $match[1]) {
            $headers[$field] = $match[1];
        } else {
            $headers[$field] = '';
        }
    }

    return $headers;
}

function getThemes() {
    global $sys;
    $themes = array();
    $dirs = glob(dirname(dirname(dirname(__FILE__))) . '/themes/*', GLOB_ONLYDIR);
    foreach ($dirs as $dir) {
        $screenshot = file_exists($dir . "/screenshot.png") ? $sys['site_url'] . "/themes/" . basename($dir) . "/screenshot.png" : "http://via.placeholder.com/400x400?text=No%20Screenshot";
        $theme = array(
            'path' => $dir,
            'folder' => basename($dir),
            'screenshot' => $screenshot,
            'name' => basename($dir)
        );
        if (file_exists($dir . "/theme-info")) {
            $theme_info = getThemeData($dir . "/theme-info", array("name" => "Name", "author" => "Author", "description" => "Description", "version" => "Version"));
            $theme['name'] = $theme_info['name'];
            $theme['author'] = $theme_info['author'];
            $theme['description'] = $theme_info['description'];
            $theme['version'] = $theme_info['version'];
        }

        $themes[] = $theme;
    }
    return $themes;
}

function isThemeOptionsExists() {
    global $sys;
    $theme_options_path = dirname(dirname(dirname(__FILE__))) . "/themes/" . $sys['theme'] . "/theme-options.phtml";
    if (file_exists($theme_options_path)) {
        return true;
    }
    return false;
}

function getThemeOptionsFilePath() {
    global $sys;
    return dirname(dirname(dirname(__FILE__))) . "/themes/" . $sys['theme'] . "/theme-options.phtml";
}

function returnBytes($val) {
    $val = trim($val);
    $last = strtolower($val[strlen($val) - 1]);
    switch ($last) {
        case 'g':
            $val *= 1024;
        case 'm':
            $val *= 1024;
        case 'k':
            $val *= 1024;
    }
    return $val;
}

function maxFileUpload() {
    //select maximum upload size
    $max_upload = ReturnBytes(ini_get('upload_max_filesize'));
    //select post limit
    $max_post = ReturnBytes(ini_get('post_max_size'));
    //select memory limit
    $memory_limit = ReturnBytes(ini_get('memory_limit'));
    // return the smallest of them, this defines the real limit
    return min($max_upload, $max_post, $memory_limit);
}

function compressImage($source_url, $destination_url, $quality) {
    $info = getimagesize($source_url);
    if ($info['mime'] == 'image/jpeg')
        $image = @imagecreatefromjpeg($source_url);
    elseif ($info['mime'] == 'image/gif')
        $image = @imagecreatefromgif($source_url);
    elseif ($info['mime'] == 'image/png')
        $image = @imagecreatefrompng($source_url);
    //save file
    @imagejpeg($image, $destination_url, $quality);
}

function getTimezones() {
    $timezones = array(
        'Pacific/Midway' => "(GMT-11:00) Midway Island",
        'US/Samoa' => "(GMT-11:00) Samoa",
        'US/Hawaii' => "(GMT-10:00) Hawaii",
        'US/Alaska' => "(GMT-09:00) Alaska",
        'US/Pacific' => "(GMT-08:00) Pacific Time (US &amp; Canada)",
        'America/Tijuana' => "(GMT-08:00) Tijuana",
        'US/Arizona' => "(GMT-07:00) Arizona",
        'US/Mountain' => "(GMT-07:00) Mountain Time (US &amp; Canada)",
        'America/Chihuahua' => "(GMT-07:00) Chihuahua",
        'America/Mazatlan' => "(GMT-07:00) Mazatlan",
        'America/Mexico_City' => "(GMT-06:00) Mexico City",
        'America/Monterrey' => "(GMT-06:00) Monterrey",
        'Canada/Saskatchewan' => "(GMT-06:00) Saskatchewan",
        'US/Central' => "(GMT-06:00) Central Time (US &amp; Canada)",
        'US/Eastern' => "(GMT-05:00) Eastern Time (US &amp; Canada)",
        'US/East-Indiana' => "(GMT-05:00) Indiana (East)",
        'America/Bogota' => "(GMT-05:00) Bogota",
        'America/Lima' => "(GMT-05:00) Lima",
        'America/Caracas' => "(GMT-04:30) Caracas",
        'Canada/Atlantic' => "(GMT-04:00) Atlantic Time (Canada)",
        'America/La_Paz' => "(GMT-04:00) La Paz",
        'America/Santiago' => "(GMT-04:00) Santiago",
        'Canada/Newfoundland' => "(GMT-03:30) Newfoundland",
        'America/Buenos_Aires' => "(GMT-03:00) Buenos Aires",
        'Greenland' => "(GMT-03:00) Greenland",
        'Atlantic/Stanley' => "(GMT-02:00) Stanley",
        'Atlantic/Azores' => "(GMT-01:00) Azores",
        'Atlantic/Cape_Verde' => "(GMT-01:00) Cape Verde Is.",
        'Africa/Casablanca' => "(GMT) Casablanca",
        'Europe/Dublin' => "(GMT) Dublin",
        'Europe/Lisbon' => "(GMT) Lisbon",
        'Europe/London' => "(GMT) London",
        'Africa/Monrovia' => "(GMT) Monrovia",
        'Europe/Amsterdam' => "(GMT+01:00) Amsterdam",
        'Europe/Belgrade' => "(GMT+01:00) Belgrade",
        'Europe/Berlin' => "(GMT+01:00) Berlin",
        'Europe/Bratislava' => "(GMT+01:00) Bratislava",
        'Europe/Brussels' => "(GMT+01:00) Brussels",
        'Europe/Budapest' => "(GMT+01:00) Budapest",
        'Europe/Copenhagen' => "(GMT+01:00) Copenhagen",
        'Europe/Ljubljana' => "(GMT+01:00) Ljubljana",
        'Europe/Madrid' => "(GMT+01:00) Madrid",
        'Europe/Paris' => "(GMT+01:00) Paris",
        'Europe/Prague' => "(GMT+01:00) Prague",
        'Europe/Rome' => "(GMT+01:00) Rome",
        'Europe/Sarajevo' => "(GMT+01:00) Sarajevo",
        'Europe/Skopje' => "(GMT+01:00) Skopje",
        'Europe/Stockholm' => "(GMT+01:00) Stockholm",
        'Europe/Vienna' => "(GMT+01:00) Vienna",
        'Europe/Warsaw' => "(GMT+01:00) Warsaw",
        'Europe/Zagreb' => "(GMT+01:00) Zagreb",
        'Europe/Athens' => "(GMT+02:00) Athens",
        'Europe/Bucharest' => "(GMT+02:00) Bucharest",
        'Africa/Cairo' => "(GMT+02:00) Cairo",
        'Africa/Harare' => "(GMT+02:00) Harare",
        'Europe/Helsinki' => "(GMT+02:00) Helsinki",
        'Europe/Istanbul' => "(GMT+02:00) Istanbul",
        'Asia/Jerusalem' => "(GMT+02:00) Jerusalem",
        'Europe/Kiev' => "(GMT+02:00) Kyiv",
        'Europe/Minsk' => "(GMT+02:00) Minsk",
        'Europe/Riga' => "(GMT+02:00) Riga",
        'Europe/Sofia' => "(GMT+02:00) Sofia",
        'Europe/Tallinn' => "(GMT+02:00) Tallinn",
        'Europe/Vilnius' => "(GMT+02:00) Vilnius",
        'Asia/Baghdad' => "(GMT+03:00) Baghdad",
        'Asia/Kuwait' => "(GMT+03:00) Kuwait",
        'Africa/Nairobi' => "(GMT+03:00) Nairobi",
        'Asia/Riyadh' => "(GMT+03:00) Riyadh",
        'Europe/Moscow' => "(GMT+03:00) Moscow",
        'Asia/Tehran' => "(GMT+03:30) Tehran",
        'Asia/Baku' => "(GMT+04:00) Baku",
        'Europe/Volgograd' => "(GMT+04:00) Volgograd",
        'Asia/Muscat' => "(GMT+04:00) Muscat",
        'Asia/Tbilisi' => "(GMT+04:00) Tbilisi",
        'Asia/Yerevan' => "(GMT+04:00) Yerevan",
        'Asia/Kabul' => "(GMT+04:30) Kabul",
        'Asia/Karachi' => "(GMT+05:00) Karachi",
        'Asia/Tashkent' => "(GMT+05:00) Tashkent",
        'Asia/Kolkata' => "(GMT+05:30) Kolkata",
        'Asia/Kathmandu' => "(GMT+05:45) Kathmandu",
        'Asia/Yekaterinburg' => "(GMT+06:00) Ekaterinburg",
        'Asia/Almaty' => "(GMT+06:00) Almaty",
        'Asia/Dhaka' => "(GMT+06:00) Dhaka",
        'Asia/Novosibirsk' => "(GMT+07:00) Novosibirsk",
        'Asia/Bangkok' => "(GMT+07:00) Bangkok",
        'Asia/Jakarta' => "(GMT+07:00) Jakarta",
        'Asia/Krasnoyarsk' => "(GMT+08:00) Krasnoyarsk",
        'Asia/Chongqing' => "(GMT+08:00) Chongqing",
        'Asia/Hong_Kong' => "(GMT+08:00) Hong Kong",
        'Asia/Kuala_Lumpur' => "(GMT+08:00) Kuala Lumpur",
        'Australia/Perth' => "(GMT+08:00) Perth",
        'Asia/Singapore' => "(GMT+08:00) Singapore",
        'Asia/Taipei' => "(GMT+08:00) Taipei",
        'Asia/Ulaanbaatar' => "(GMT+08:00) Ulaan Bataar",
        'Asia/Urumqi' => "(GMT+08:00) Urumqi",
        'Asia/Irkutsk' => "(GMT+09:00) Irkutsk",
        'Asia/Seoul' => "(GMT+09:00) Seoul",
        'Asia/Tokyo' => "(GMT+09:00) Tokyo",
        'Australia/Adelaide' => "(GMT+09:30) Adelaide",
        'Australia/Darwin' => "(GMT+09:30) Darwin",
        'Asia/Yakutsk' => "(GMT+10:00) Yakutsk",
        'Australia/Brisbane' => "(GMT+10:00) Brisbane",
        'Australia/Canberra' => "(GMT+10:00) Canberra",
        'Pacific/Guam' => "(GMT+10:00) Guam",
        'Australia/Hobart' => "(GMT+10:00) Hobart",
        'Australia/Melbourne' => "(GMT+10:00) Melbourne",
        'Pacific/Port_Moresby' => "(GMT+10:00) Port Moresby",
        'Australia/Sydney' => "(GMT+10:00) Sydney",
        'Asia/Vladivostok' => "(GMT+11:00) Vladivostok",
        'Asia/Magadan' => "(GMT+12:00) Magadan",
        'Pacific/Auckland' => "(GMT+12:00) Auckland",
        'Pacific/Fiji' => "(GMT+12:00) Fiji",
    );
    return $timezones;
}

function getEmailHeaders() {
    $headers = 'MIME-Version: 1.0' . "\r\n"
            . 'Content-type: text/html; charset=iso-8859-1' . "\r\n"
            . 'From: info@zatack.net' . "\r\n"
            . 'Reply-To: info@zatack.net' . "\r\n"
            . 'X-Mailer: PHP/' . phpversion();
    return $headers;
}

function getReferrer() {
    if (isset($_SERVER["HTTP_REFERER"])) {
        return $_SERVER["HTTP_REFERER"];
    }
    return "DIRECT";
}

function ipInfo($ip = NULL, $purpose = "location", $deep_detect = TRUE) {
    $output = NULL;
    if (filter_var($ip, FILTER_VALIDATE_IP) === FALSE) {
        $ip = $_SERVER["REMOTE_ADDR"];
        if ($deep_detect) {
            if (filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP))
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            if (filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP))
                $ip = $_SERVER['HTTP_CLIENT_IP'];
        }
    }
    $purpose = str_replace(array("name", "\n", "\t", " ", "-", "_"), NULL, strtolower(trim($purpose)));
    $support = array("country", "countrycode", "state", "region", "city", "location", "address");
    $continents = array(
        "AF" => "Africa",
        "AN" => "Antarctica",
        "AS" => "Asia",
        "EU" => "Europe",
        "OC" => "Australia (Oceania)",
        "NA" => "North America",
        "SA" => "South America"
    );
    if (filter_var($ip, FILTER_VALIDATE_IP) && in_array($purpose, $support)) {
        $ipdat = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $ip));
        if (@strlen(trim($ipdat->geoplugin_countryCode)) == 2) {
            switch ($purpose) {
                case "location":
                    $output = array(
                        "city" => @$ipdat->geoplugin_city,
                        "state" => @$ipdat->geoplugin_regionName,
                        "country" => @$ipdat->geoplugin_countryName,
                        "country_code" => @$ipdat->geoplugin_countryCode,
                        "continent" => @$continents[strtoupper($ipdat->geoplugin_continentCode)],
                        "continent_code" => @$ipdat->geoplugin_continentCode
                    );
                    break;
                case "address":
                    $address = array($ipdat->geoplugin_countryName);
                    if (@strlen($ipdat->geoplugin_regionName) >= 1)
                        $address[] = $ipdat->geoplugin_regionName;
                    if (@strlen($ipdat->geoplugin_city) >= 1)
                        $address[] = $ipdat->geoplugin_city;
                    $output = implode(", ", array_reverse($address));
                    break;
                case "city":
                    $output = @$ipdat->geoplugin_city;
                    break;
                case "state":
                    $output = @$ipdat->geoplugin_regionName;
                    break;
                case "region":
                    $output = @$ipdat->geoplugin_regionName;
                    break;
                case "country":
                    $output = @$ipdat->geoplugin_countryName;
                    break;
                case "countrycode":
                    $output = @$ipdat->geoplugin_countryCode;
                    break;
            }
        }
    }
    return $output;
}

$MONTHS['01'] = "Jan";
$MONTHS['02'] = "Feb";
$MONTHS['03'] = "Mar";
$MONTHS['04'] = "Apr";
$MONTHS['05'] = "May";
$MONTHS['06'] = "Jun";
$MONTHS['07'] = "Jul";
$MONTHS['08'] = "Aug";
$MONTHS['09'] = "Sep";
$MONTHS['10'] = "Oct";
$MONTHS['11'] = "Nov";
$MONTHS['12'] = "Dec";

$DAYSORWEEKS['D'] = "Days";
$DAYSORWEEKS['W'] = "Week";

$PRODUCT_TYPES['PHY'] = "Physical";
$PRODUCT_TYPES['DIG'] = "Digital";

$CONDITIONS['N'] = 'New';
$CONDITIONS['U'] = 'Used';
$CONDITIONS['R'] = 'Refurbished';

$PRODUCT_STATUSES['A'] = "Active";
$PRODUCT_STATUSES['I'] = "In-Active";

define('PM_AMAZON', 'amazon');
define('PM_BANK_TRANSFER', 'banktransfer');
define('PM_CASH_ON_DELIVERY', 'cashondelivery');
define('PM_CCAVENUE', 'ccavenue');
define('PM_PAYPAL', 'paypal');
define('PM_PAYTM', 'paytm');
define('PM_PAYUBIZ', 'payubizindia');
define('PM_PAYUMONEY', 'payumoneyindia');
define('PM_RAZORPAY', 'razorpay');

define('OS_PAYMENT_PENDING', '1');
define('OS_PAYMENT_CONFIRMED', '2');
define('OS_CASH_ON_DELIVERY', '3');
define('OS_APPROVED', '4');
define('OS_IN_PROGRESS', '5');
define('OS_SHIPPED', '6');
define('OS_DELIVERED', '7');
define('OS_RETURN_REQUESTED', '8');
define('OS_COMPLETED', '9');
define('OS_CANCELLED', '10');
define('OS_REFUNDED', '11');

$ORDER_STATUSES[OS_PAYMENT_PENDING] = "Payment Pending";
$ORDER_STATUSES[OS_PAYMENT_CONFIRMED] = "Payment Confirmed";
$ORDER_STATUSES[OS_CASH_ON_DELIVERY] = "Cash on Delivery";
$ORDER_STATUSES[OS_APPROVED] = "Approved";
$ORDER_STATUSES[OS_IN_PROGRESS] = "In Process";
$ORDER_STATUSES[OS_SHIPPED] = "Shipped";
$ORDER_STATUSES[OS_DELIVERED] = "Delivered";
$ORDER_STATUSES[OS_RETURN_REQUESTED] = "Return Requested";
$ORDER_STATUSES[OS_COMPLETED] = "Completed";
$ORDER_STATUSES[OS_CANCELLED] = "Cancelled";
$ORDER_STATUSES[OS_REFUNDED] = "Refunded/Completed";

$TAX_PERCENT['GST_00'] = 0.00;
$TAX_PERCENT['GST_03'] = 0.03;
$TAX_PERCENT['GST_05'] = 0.05;
$TAX_PERCENT['GST_12'] = 0.12;
$TAX_PERCENT['GST_18'] = 0.18;
$TAX_PERCENT['GST_28'] = 0.28;
$TAX_PERCENT['GST_APPAREL'] = 0.05; //0.12 if product price per piece exceed 1000

function getOptionTypes() {
    $option_types = array(
        "Select/Listbox/Dropdown",
        "Radio",
        "Checkbox",
        "Text",
        "Textarea",
        "File",
        "Date",
        "Time",
        "Date & Time"
    );
    return $option_types;
}

function hasColorVariant($seller) {
    $status = false;
    foreach ($seller['prices'] as $price) {
        if (strtoupper($price['color']) <> 'NA' && strtoupper($price['color']) <> '') {
            $status = true;
            break;
        }
    }
    return $status;
}

function hasSizeVariant($seller) {
    $status = false;
    foreach ($seller['prices'] as $price) {
        if (strtoupper($price['size']) <> 'NA' && strtoupper($price['size']) <> '') {
            $status = true;
            break;
        }
    }
    return $status;
}

function getSellingPrice($seller, $color, $size) {
    $selling_price = 0.00;
    foreach ($seller['prices'] as $price) {
        if (strtoupper($price['color']) == "NA" && strtoupper($price['size']) == "NA") {
            $selling_price = $price['selling_price'];
        }
        if (strtoupper($price['color']) == strtoupper($color) && strtoupper($price['size']) == strtoupper($size)) {
            $selling_price = $price['selling_price'];
            break;
        }
    }
    return number_format($selling_price, 2, ".", "");
}

function getDiscountedPrice($seller, $color, $size) {
    $selling_price = 0.00;
    foreach ($seller['prices'] as $price) {
        if (strtoupper($price['color']) == "NA" && strtoupper($price['size']) == "NA") {
            $selling_price = $price['selling_price'];
            $discount = $selling_price * ($price['percent_discount'] / 100);
        }
        if (strtoupper($price['color']) == strtoupper($color) && strtoupper($price['size']) == strtoupper($size)) {
            $selling_price = $price['selling_price'];
            $discount = $selling_price * ($price['percent_discount'] / 100);
            break;
        }
    }
    $discounted_price = $selling_price - $discount;
    return number_format($discounted_price, 2, ".", "");
}

function isDiscountActive($seller, $color, $size) {
    $status = false;
    foreach ($seller['prices'] as $price) {
        if (strtoupper($price['color']) == strtoupper($color) && strtoupper($price['size']) == strtoupper($size) && trim($price['active_discount']) == YES) {
            $status = true;
            break;
        }
    }
    return $status;
}

function isInStock($seller, $color, $size) {
    $status = false;
    foreach ($seller['prices'] as $price) {
        if (strtoupper($price['color']) == strtoupper($color) && strtoupper($price['size']) == strtoupper($size) && trim($price['in_stock']) == YES) {
            $status = true;
            break;
        }
    }
    return $status;
}

function calculateTotal($cart_total, $tax = 0, $shipping = 0) {
    $total = $cart_total + $tax + $shipping;
    return number_format($total, 2, ".", "");
}

function currencyRates($currencies = NULL) {
    $sys = $GLOBALS['sys'];
    $output = NULL;
    
    $endpoint = 'live';
    $access_key = isset($sys['API_CURRENCYLAYER_ACCESS_KEY']) ? $sys['API_CURRENCYLAYER_ACCESS_KEY'] : "";//this will be fetched from database
    
    if(!in_array($endpoint, array("live", "historical", "convert", "timeframe", "change", "list"))) {
        $output['success'] = false;
        $output['msg'] = "Invalid Endpoint";
        return $output;
    }
    if($access_key == '') {
        $output['success'] = false;
        $output['msg'] = "Access Key Required";
        return $output;
    }
    
    if($currencies == null || trim($currencies) == "") {
        $output = @json_decode(file_get_contents("http://api.currencylayer.com/" . $endpoint . "?access_key=" . $access_key . "&format=1"), true);
    } else {
        $output = @json_decode(file_get_contents("http://api.currencylayer.com/" . $endpoint . "?access_key=" . $access_key . "&currencies=" . $currencies . "&format=1"), true);
    }
    
    return $output;
}
?>
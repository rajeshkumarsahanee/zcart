<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

date_default_timezone_set('UTC');
session_start();
require_once('includes/cache.php');
require_once('includes/general.php');
require_once('includes/emo.php');
require_once('includes/tabels.php');
require_once('includes/matches.php');
require_once('includes/core.php');

require_once('import/simple_html_dom.inc.php');
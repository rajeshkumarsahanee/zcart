<?php
// +------------------------------------------------------------------------+
// | @author Rajesh Kumar Sahanee
// | @author_url 1: http://www.zatackcoder.com
// | @author_email: rajeshsahanee@gmail.com   
// +------------------------------------------------------------------------+

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'system/init.php';

/*
 * Visitors 
 * 
  Sys_addVisitorLog(array(
  "visitor_ip_address" => $_SERVER['REMOTE_ADDR'],
  "country_code" => Sys_ipInfo($_SERVER['REMOTE_ADDR'], "Country Code", TRUE),
  "city" => Sys_ipInfo($_SERVER['REMOTE_ADDR'], "City", TRUE),
  "visitor_source" => Sys_getReferrer(),
  "visitor_destination" => $_SERVER['REQUEST_URI'],
  "visit_date" => date("Y-m-d"),
  "visit_time" => date("H:i:s")
  ));

  echo "Total Traffic = " . Sys_getVisitorsCount() . "<br/>";
  echo "This month traffic = " . Sys_getVisitorsCount("NA", "NA", date("Y-m-1"), date("Y-m-d")) ."<br/>";
  echo "Direct Traffic = " . Sys_getVisitorsCount("NA", "NA", "NA", "NA", "DIRECT", EQUALS) . "<br/>";
  echo "Referral Traffic = " . Sys_getVisitorsCount("NA", "NA", "NA", "NA", "DIRECT", NOT_EQUALS) . "<br/>";
  echo "Search Engine Traffic = " .
  (Sys_getVisitorsCount("NA", "NA", "NA", "NA", "google", CONTAINS) + Sys_getVisitorsCount("NA", "NA", "NA", "NA", "yahoo", CONTAINS) + Sys_getVisitorsCount("NA", "NA", "NA", "NA", "bing", CONTAINS))
  . "<br/>";
  echo "StackOverflow.com traffic = " . Sys_getVisitorsCount("NA", "NA", "NA", "NA", "stackoverflow.com", CONTAINS) . "<br/>";
 */

/*
 * Wallet
  Sys_addWalletTransaction(array(
  'userid' => "1",
  'usertype' => "B",
  'credit' => "0",
  'debit' => "200",
  'description' => "test transaction credited",
  'txn_timestamp' => time(),
  'status' => "C"
  ));

  //$transactions = Sys_getWalletTransactions();
  //$transactions = Sys_getWalletTransactions(1, "2017-05-01", "2017-05-02");
  //$transactions = Sys_getWalletTransactions(1, "2017-05-01", "2017-05-02", 0, 5);
  $transactions = Sys_getWalletTransactions(1, null, null, 0, 10);
  foreach($transactions as $transaction) {
  print_r($transaction);
  echo "<br/>";
  }
 */

/*
 * Log
  Sys_addLog(array(
  'user_id' => "4",
  'user_type' => "B",
  'log' => "Added Product Id 9 to Cart",
  'log_timestamp' => time()
  ));

  //$logs = Sys_getLogs(null, null, 0, 10);
  //$logs = Sys_getLogs(2, null);
  $logs = Sys_getLogs(4, "B", 0, 10);
  foreach ($logs as $log) {
  print_r($log);
  echo "<br/>";
  }
 */

/*
 * Shop
  Sys_addShop(array(
  'owner_id' => "1",
  'name' => "Test",
  'url' => "test",
  'description' => "description",
  'logo' => "logo.jpg",
  'banner' => "banner.jpg",
  'featured' => "Y",
  'cod_enabled' => "Y",
  'contact_person_name' => "contact person",
  'phone' => "phone",
  'address1' => "address1",
  'address2' => "address2",
  'city' => "city",
  'state' => "state",
  'pincode' => "pincode",
  'country' => "country",
  'payment_policy' => "payment policies goes here",
  'delivery_policy' => "delivery policy goes here",
  'refund_policy' => "refund policy goes here",
  'additional_information' => "additional information goes here",
  'seller_information' => "seller information",
  'items_count' => 0,
  'reviews_count' => 0,
  'reports_count' => 0,
  'meta_title' => "meta title",
  'meta_keywords' => "meta keywords",
  'meta_description' => "meta description",
  'added_timestamp' => time(),
  'updated_timestamp' => time(),
  'status_message' => "Pending Approval",
  'status' => "P"
  ));
  echo $queryerrormsg."<br/>";

  Sys_updateShop(array(
  'id' => "1",
  'owner_id' => "1",
  'name' => "Test3",
  'url' => "test3",
  'description' => "description3",
  'logo' => "logo3.jpg",
  'banner' => "banner3.jpg",
  'featured' => "N",
  'cod_enabled' => "N",
  'contact_person_name' => "contact person3",
  'phone' => "phone3",
  'address1' => "address13",
  'address2' => "address23",
  'city' => "city3",
  'state' => "state3",
  'pincode' => "pincode3",
  'country' => "country3",
  'payment_policy' => "payment policies goes here3",
  'delivery_policy' => "delivery policy goes here3",
  'refund_policy' => "refund policy goes here3",
  'additional_information' => "additional information goes here3",
  'seller_information' => "seller information3",
  'items_count' => 1,
  'reviews_count' => 1,
  'reports_count' => 1,
  'meta_title' => "meta title3",
  'meta_keywords' => "meta keywords3",
  'meta_description' => "meta description3",
  'added_timestamp' => time(),
  'updated_timestamp' => time(),
  'status_message' => "Pending Approval3",
  'status' => "A"
  ));
  echo $queryerrormsg."<br/>";

  Sys_updateShopStatus(1, "Pending", "P");
  echo $queryerrormsg."<br/>";

  $shops = Sys_getShops(0, 10);
  foreach ($shops as $shop) {
  print_r($shop);
  echo "<br/><br/>";
  }

  $shops = Sys_getShopsByQuery("Test2", 0, 10);
  foreach ($shops as $shop) {
  print_r($shop);
  echo "<br/><br/>";
  }
 */

/*
 * Blog Categories
  Sys_addBCategory(array(
  'name' => "Test Sub Category ",
  'description' => "Test Category Description",
  'slug' => "Test Sub Category ",
  'main_category' => "1",
  'meta_title' => "Test Category",
  'meta_keywords' => "Test Category Meta Keywords",
  'meta_description'=> "Test Category Meta Description",
  'meta_others' => "Test Category Meta Others",
  'status' => "A"
  ));
  echo $queryerrormsg;

  Sys_updateBCategory(array(
  'id' => "1",
  'name' => "Test Category Updated",
  'description' => "Test Category Description updated",
  'slug' => "Test Category Updated",
  'main_category' => "0",
  'meta_title' => "Test Category updated",
  'meta_keywords' => "Test Category Meta Keywords updated",
  'meta_description'=> "Test Category Meta Description updated",
  'meta_others' => "Test Category Meta Others updated",
  'status' => "A"
  ));
  echo $queryerrormsg;


  $categories = Sys_getBCategories();
  $categories = Sys_getBCategories(1);
  $categories = Sys_getBCategories(null, array(), "A");
  $categories = Sys_getBCategories(null, array("blog_id" => "1"), "A");
  foreach ($categories as $category) {
  print_r($category);
  echo "<br/><br/>";
  }

  print_r(Sys_getBCategory(1));
  echo "<br/><br/>";
  print_r(Sys_getBCategory("test-category-2"));
 */

/*
 * Blog Posts
  Sys_addBlogPost(array(
  'title' => "Post Title",
  'slug' => "Post Title",
  'short_description' => "short description",
  'content' => "",
  'images' => "",
  'allow_comment' => "Y",
  'views' => "0",
  'likes' => "0",
  'meta_title' => "",
  'meta_keywords' => "",
  'meta_description' => "",
  'meta_others' => "",
  'status' => "PB",
  'posted_by' => Sys_getAdminLoggedId(),
  'posted_timestamp' => time(),
  'updated_timestamp' => time(),
  'categories' => array(1,2)
  ));
  echo $queryerrormsg;


  Sys_updateBlogPost(array(
  'id' => "1",
  'title' => "Post Title",
  'slug' => "Post Title",
  'short_description' => "short description",
  'content' => "",
  'images' => "",
  'allow_comment' => "Y",
  'views' => "0",
  'likes' => "0",
  'meta_title' => "",
  'meta_keywords' => "",
  'meta_description' => "",
  'meta_others' => "",
  'status' => "PB",
  'posted_by' => Sys_getAdminLoggedId(),
  'posted_timestamp' => time(),
  'updated_timestamp' => time(),
  'categories' => array(1,2)
  ));
  echo $queryerrormsg;

  $posts = Sys_getBlogPosts();
  $posts = Sys_getBlogPosts(array(), 0, 12);
  $posts = Sys_getBlogPosts(array("blog_id" => "1"), 0, 12, "ASC");
  foreach ($posts as $post) {
  print_r($post);
  echo "<br/><br/>";
  }

  print_r(Sys_getBlogPost(1));
  echo "<br/><br/>";
  print_r(Sys_getBlogPost("post-title"));
 */

/*
 * Blog Tags 
  Sys_addBTag(array(
  'name' => "Test Tag",
  'slug' => "Test Sub Category ",
  'term_group' => "0",
  'meta_title' => "Test Tag",
  'meta_keywords' => "Test Tag Meta Keywords",
  'meta_description'=> "Test Tag Meta Description",
  'meta_others' => "Test Tag Meta Others",
  ));
  echo $queryerrormsg;

  Sys_updateBTag(array(
  'id' => "1",
  'name' => "Test Tag Updated",
  'slug' => "Test Tag Updated",
  'term_group' => "0",
  'meta_title' => "Test Tag updated",
  'meta_keywords' => "Test Tag Meta Keywords updated",
  'meta_description'=> "Test Tag Meta Description updated",
  'meta_others' => "Test Tag Meta Others updated",
  ));
  echo $queryerrormsg;


  $tags = Sys_getBTags();
  //$tags = Sys_getBTags(array('post_id'=>"1"));
  foreach ($tags as $tag) {
  print_r($tag);
  echo "<br/><br/>";
  }

  print_r(Sys_getBTag(1));
  echo "<br/><br/>";
  print_r(Sys_getBTag("test-tag-updated"));
 */

/*
 * Blog Comments 
  Sys_addComment(array(
  'author_name' => "Rajesh Kumar",
  'author_email' => "rajesh@gmail.com",
  'comment' => "This is a test comment",
  'post_id' => "1",
  'author_ip_address' => $_SERVER['REMOTE_ADDR'],
  'comment_timestamp' => time(),
  'author_user_agent' =>  $_SERVER['HTTP_USER_AGENT'],
  'status' => "P"
  ));
  echo $queryerrormsg;


  Sys_updateComment(array(
  'id' => "1",
  'author_name' => "Rajesh Kumar",
  'author_email' => "rajeshsahanee@gmail.com",
  'comment' => "This is a test comment",
  'post_id' => "1",
  'author_ip_address' => $_SERVER['REMOTE_ADDR'],
  'author_user_agent' => $_SERVER['HTTP_USER_AGENT'],
  'status' => 'P'
  ));
  echo $queryerrormsg;

  $comments = Sys_getComments();
  $comments = Sys_getComments(array('post_id' => "1"));
  $comments = Sys_getComments(array('author_email' => "rajesh@gmail.com"));
  $comments = Sys_getComments(array('post_id' => "1", 'author_email' => "rajeshsahanee@gmail.com"));
  $comments = Sys_getComments(array(), "P");
  foreach ($comments as $comment) {
  print_r($comment);
  echo "<br/><br/>";
  }

  print_r(Sys_getComment(1));
 */

/*
 * Filters
  Sys_addFilter(array(
  "name"=> "Test",
  "display_order" => "0",
  "filter_values" => json_encode(array(array("name" => "Test", "display_order" => "0")))
  ));

  Sys_updateFilter(array(
  "id" => "1",
  "name" => "Test Updated",
  "display_order" => "0",
  "filter_values" => json_encode(array(array("name" => "Test", "display_order" => "0")))
  ));

  $filters = Sys_getFilters();
  foreach ($filters as $filter) {
  print_r($filter);
  echo "<br/><br/>";
  }
 */

/*
 * Brands
  Sys_addBrand(array(
  'name' => "Test",
  'slug' => "test",
  'description' => "description",
  'image' => "",
  'meta_title' => "meta title",
  'meta_keywords' => "meta keywords",
  'meta_description' => "meta description",
  'status' => "P"
  ));
  echo $queryerrormsg."<br/>";

  Sys_updateBrand(array(
  'id' => "1",
  'name' => "Test3",
  'slug' => "test3",
  'description' => "description3",
  'image' => "",
  'meta_title' => "meta title3",
  'meta_keywords' => "meta keywords3",
  'meta_description' => "meta description3",
  'status' => "A"
  ));
  echo $queryerrormsg."<br/>";

  $brands = Sys_getBrands(array(), 0, 10);
  foreach ($brands as $brand) {
  print_r($brand);
  echo "<br/><br/>";
  }
 */

//echo '<pre>';
//print_r($_SERVER);
//echo '</pre>';

/*
$invoice_number = "101010-00001";
$arr = array(
    "product1", 
    "product2", 
    "product3", 
    "product4", 
    "product5", 
    "product6", 
    "product7",
    "product8",
    "product9",
    "product10"
    );
$i = 1;
foreach($arr as $a) {
    echo $invoice_number . "-S" . str_pad($i++, 4, 0, STR_PAD_LEFT) . "<br/>";
}
 */
/*
echo md5("1-1"); echo '<br/>';
echo md5("1-1-<br />- <small>Color: White</small>"); echo '<br/>';
echo md5("1-1-<br />- <small>Color: White</small>"); echo '<br/>';
 */

$sql2 = "INSERT INTO " . T_ORDERS_PRODUCTS_OPTIONS . " (order_id, order_product_id, product_id, product_option_id, product_option_value_id, option_name, option_value, option_type) VALUES";
$options = array(
    array("product_option_id" => 1, "product_option_value_id" => 2, "option_name" => "tt", "option_value" => "vv", "option_type" => "radio"),
    array("product_option_id" => 1, "product_option_value_id" => 2, "option_name" => "tt", "option_value" => "vv", "option_type" => "radio"),
    array("product_option_id" => 1, "product_option_value_id" => 2, "option_name" => "tt", "option_value" => "vv", "option_type" => "radio")
);
$values = array();
$order_id = "1";
$order_product_id = "2";
$product_id = "1";
foreach ($options as $option) {
    $product_option_id = secure($option['product_option_id']);
    $product_option_value_id = secure($option['product_option_value_id']);
    $option_name = secure($option['option_name']);
    $option_value = secure($option['option_value']);
    $option_type = secure($option['option_type']);
    $values[] = "('{$order_id}', '{$order_product_id}', '{$product_id}', '{$product_option_id}', '{$product_option_value_id}', '{$option_name}', '{$option_value}', '{$option_type}')";
}
echo $sql2 . implode(",", $values);
?>

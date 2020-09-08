<?php

// +------------------------------------------------------------------------+
// | @author Rajesh Kumar Sahanee
// | @author_url: http://www.zatackcoder.com
// | @author_email: rajeshsahanee@gmail.com   
// +------------------------------------------------------------------------+

require_once('connect.php');

$queryerrormsg = "";
$uploaderrormsg = "";

/* User Functions Start */

function isValidUser($username, $password) {
    global $conn;
    $username = secure($username);
    $password = md5($password);
    $results = $conn->query("SELECT * FROM " . T_USERS . " WHERE (username = '{$username}' OR email = '$username' OR mobile = '{$username}') AND password = '{$password}'");
    return $results->num_rows > 0 ? true : false;
}

function isUserExists($username_or_email_or_mobile, $matchwith = "all") {
    global $conn;
    if (empty($username_or_email_or_mobile)) {
        return false;
    }
    $username_or_email_or_mobile = secure($username_or_email_or_mobile);
    $column = $matchwith;
    $sql = "SELECT * FROM " . T_USERS . " WHERE username = '{$username_or_email_or_mobile}' OR email = '{$username_or_email_or_mobile}' OR mobile = '{$username_or_email_or_mobile}'";
    if (in_array($column, array("username", "email", "mobile"))) {
        $sql = "SELECT * FROM " . T_USERS . " WHERE $column = '{$username_or_email_or_mobile}'";
    }
    $results = $conn->query($sql);
    return $results->num_rows > 0 ? true : false;
}

function getUsers($columns = array(), $filters = array(), $offset = 0, $limit = 10, $order_by = "id", $order = "DESC", $indexed_by = null) {
    global $conn;
    $sql = "SELECT * FROM " . T_USERS . " WHERE status != 'T'";
    if (!empty($columns) && is_array($columns)) {
        $sql = "SELECT `" . implode("`,`", $columns) . "` FROM " . T_USERS . " WHERE status != 'T'";
    }
    if (isset($filters['query'])) {
        $q = secure($filters['query']);
        $sql .= " AND (email LIKE '%{$q}%' OR username LIKE '%{$q}%' OR display_name LIKE '%{$q}%')";
    }
    if (isset($filters['role']) && !empty($filters['role'])) {
        $role = secure($filters['role']);
        $sql .= " AND id IN (SELECT user_id FROM " . T_USER_META . " WHERE meta_key = 'role' AND meta_value = '$role')";
    }
    if (isset($filters['roles']) && is_array($filters['roles'])) {
        $roles = implode("','", $filters['roles']);
        $sql .= " AND id IN (SELECT user_id FROM " . T_USER_META . " WHERE meta_key = 'role' AND meta_value IN ('$roles'))";
    }
    if (isset($filters['status'])) {
        $sql .= " AND status = '" . secure($filters['status']) . "'";
    }
    $sql .= " ORDER BY $order_by $order";
    if ($limit != -1) {
        $sql .= " LIMIT $offset, $limit";
    }
    $results = $conn->query($sql);
    $users = array();
    $i = 0;
    while ($row = $results->fetch_assoc()) {
        if (isset($filters['with_metas'])) {
            $row['metas'] = getUserMetas($row['id']);
        }
        if ($indexed_by != null && isset($row[$indexed_by])) {
            $users[$row[$indexed_by]] = $row;
        } else {
            $users[] = $row;
        }
    }
    return $users;
}

function getUser($id_or_username_or_email_or_mobile, $columns = array(), $matchwith = "all") {
    global $conn;
    $user = null;
    $id_or_username_or_email_or_mobile = secure($id_or_username_or_email_or_mobile);
    $column = $matchwith;

    $selectcolumns = "*";
    if (!empty($columns) && is_array($columns)) {
        if (!in_array("id", $columns)) {
            array_push($columns, "id");
        }
        $selectcolumns = "`" . implode("`,`", $columns) . "`";
    }
    $sql = "SELECT $selectcolumns FROM " . T_USERS . " WHERE id='{$id_or_username_or_email_or_mobile}' OR username = '{$id_or_username_or_email_or_mobile}' OR email = '{$id_or_username_or_email_or_mobile}' OR mobile = '{$id_or_username_or_email_or_mobile}'";
    if (in_array($column, array("id", "username", "email", "mobile"))) {
        $sql = "SELECT $selectcolumns FROM " . T_USERS . " WHERE $column = '{$id_or_username_or_email_or_mobile}'";
    }
    $results = $conn->query($sql);
    while ($row = $results->fetch_assoc()) {
        $user = $row;
        $user['metas'] = getUserMetas($row['id']);
    }
    return $user;
}

function addUser($user) {
    global $conn;
    $username = secure($user['username']);
    $password = md5(secure($user['password']));
    $email = secure($user['email']);
    $mobile = secure($user['mobile']);
    $url = isset($user['url']) ? secure($user['url']) : $username;
    $registered = isset($user['registered']) ? $user['registered'] : date("Y-m-d H:i:s");
    $updated = isset($user['updated']) ? $user['updated'] : date("Y-m-d H:i:s");
    $activation_key = isset($user['activation_key']) ? $user['activation_key'] : generateKey();
    $status = isset($user['status']) ? trim($user['status']) : "P";
    $display_name = isset($user['display_name']) ? secure($user['display_name']) : $username;

    if (isUserExists($username) || isUserExists($email) || isUserExists($mobile)) {
        $GLOBALS['queryerrormsg'] = 'User already exists';
        return false;
    }
    if ($conn->query("INSERT INTO " . T_USERS . " (username, password, email, mobile, url, registered, updated, activation_key, status, display_name) VALUES('{$username}', '{$password}', '{$email}', '{$mobile}', '{$url}', '{$registered}', '{$updated}', '{$activation_key}', '{$status}', '{$display_name}')")) {
        $user_id = $conn->insert_id;
        foreach ($user['metas'] as $key => $value) {
            updateUserMeta($user_id, $key, $value);
        }
        return true;
    }
    $GLOBALS['queryerrormsg'] = 'There is some problem!';
    return false;
}

function updateUser($user) {
    global $conn;
    $id = secure($user['id']);
    $username = secure($user['username']);
    $password = isset($user['password']) ? md5(secure($user['password'])) : '';
    $email = secure($user['email']);
    $mobile = secure($user['mobile']);
    $url = isset($user['url']) ? secure($user['url']) : $username;
    $updated = isset($user['updated']) ? $user['updated'] : date("Y-m-d H:i:s");
    $activation_key = isset($user['activation_key']) ? $user['activation_key'] : generateKey();
    $status = isset($user['status']) ? trim($user['status']) : "P";
    $display_name = isset($user['display_name']) ? secure($user['display_name']) : $username;

    $sql = "UPDATE " . T_USERS . " SET username = '{$username}', email = '{$email}', mobile = '{$mobile}', url = '{$url}', updated = '{$updated}', activation_key = '{$activation_key}', status = '{$status}', display_name = '{$display_name}' WHERE id = '{$id}'";
    if ($password != '') {
        $sql = "UPDATE " . T_USERS . " SET username = '{$username}', password = '{$password}', email = '{$email}', mobile = '{$mobile}', url = '{$url}', updated = '{$updated}', activation_key = '{$activation_key}', status = '{$status}', display_name = '{$display_name}' WHERE id = '{$id}'";
    }
    if ($conn->query($sql)) {
        $user_id = $id;
        foreach ($user['metas'] as $key => $value) {
            updateUserMeta($user_id, $key, $value);
        }
        return true;
    }
    $GLOBALS['queryerrormsg'] = 'There is some problem!';
    return false;
}

function deleteUser($user_id) {
    global $conn;
    $user_id = secure($user_id);
    $qstatus = $conn->query("UPDATE " . T_USERS . " SET status = 'T' WHERE id = {$user_id}");
    if ($qstatus) {
        return true;
    }
}

function isUserHavePermission($section, $userid) {
    global $conn;
    $userid = secure($userid);
    $data = "";
    $result = $conn->query("SELECT * FROM " . T_USER_META . " WHERE user_id = '{$userid}' AND meta_key = 'permissions'");
    while ($row = $result->fetch_assoc()) {
        $data = $row['meta_value'];
    }
    $sections = explode(",", $data);
    if (in_array($section, $sections)) {
        return true;
    }
    return false;
}

function getUserMetas($user_id) {
    global $conn;
    $usermeta = array();
    $user_id = secure($user_id);
    $results = $conn->query("SELECT * FROM " . T_USER_META . " WHERE user_id='{$user_id}'");
    while ($row = $results->fetch_assoc()) {
        $usermeta[$row['meta_key']] = $row['meta_value'];
    }
    return $usermeta;
}

function updateUserMeta($user_id, $meta_key, $meta_value) {
    global $conn;
    $user_id = secure($user_id);
    $meta_key = secure($meta_key);
    $meta_value = secure($meta_value);
    $results = $conn->query("SELECT * FROM " . T_USER_META . " WHERE user_id = '{$user_id}' AND meta_key = '{$meta_key}'");
    if ($results->num_rows > 0) {
        return $conn->query("UPDATE " . T_USER_META . " SET meta_value = '{$meta_value}' WHERE user_id = '{$user_id}' AND meta_key = '{$meta_key}'");
    } else {
        return $conn->query("INSERT INTO " . T_USER_META . " (user_id, meta_key, meta_value) VALUES('{$user_id}', '{$meta_key}', '{$meta_value}')");
    }
}

function getUserAddresses($columns = array(), $filters = array(), $offset = 0, $limit = 10, $order_by = "id", $order = "DESC") {
    global $conn;
    $sql = "SELECT * FROM " . T_USER_ADDRESSES . " WHERE status != 'T'";
    if (!empty($columns) && is_array($columns)) {
        $sql = "SELECT `" . implode("`,`", $columns) . "` FROM " . T_USER_ADDRESSES . " WHERE status != 'T'";
    }
    if (isset($filters['user_id']) && !empty($filters['user_id'])) {
        $user_id = secure($filters['user_id']);
        $sql .= " AND user_id = '{$user_id}'";
    }
    if (isset($filters['name']) && trim($filters['name']) <> "") {
        $name = secure($filters['name']);
        $sql .= " AND name LIKE '%{$name}%'";
    }
    if (isset($filters['email']) && trim($filters['email']) <> "") {
        $email = secure($filters['email']);
        $sql .= " AND email LIKE '%{$email}%'";
    }
    if (isset($filters['phone']) && trim($filter['phone']) <> "") {
        $phone = secure($filters['phone']);
        $sql .= " AND phone = '{$phone}'";
    }
    if (isset($filters['city']) && trim($filters['city']) <> "") {
        $city = secure($filters['city']);
        $sql .= " AND city = '{$city}'";
    }
    if (isset($filters['state']) && trim($filters['state']) <> "") {
        $state = secure($filters['state']);
        $sql .= " AND state = '{$state}'";
    }
    if (isset($filters['pincode']) && trim($filters['pincode']) <> "") {
        $pincode = secure($filters['pincode']);
        $sql .= " AND pincode = '{$pincode}'";
    }
    if (isset($filters['country']) && trim($filters['country']) <> "") {
        $country = secure($filters['country']);
        $sql .= " AND country = '{$country}'";
    }
    if (isset($filters['address_type']) && trim($filters['address_type']) <> "") {
        $address_type = secure($filters['address_type']);
        $sql .= " AND address_type = '{$address_type}'";
    }
    if (isset($filters['is_default']) && trim($filters['is_default']) <> "") {
        $is_default = secure($filters['is_default']);
        $sql .= " AND is_default = '{$is_default}'";
    }
    if (isset($filters['query'])) {
        $q = secure($filters['query']);
        $sql .= " AND (name LIKE '%{$q}%' OR email LIKE '%{$q}%' OR phone LIKE '%{$q}%' OR address1 LIKE '%{$q}%' OR address2 LIKE '%{$q}%' OR address3 LIKE '%{$q}%' OR city LIKE '%{$q}%' OR state LIKE '%{$q}%' OR pincode LIKE '%{$q}%' OR country LIKE '%{$q}%')";
    }
    if (isset($filters['status'])) {
        $sql .= " AND status = '" . secure($filters['status']) . "'";
    }
    $sql .= " ORDER BY $order_by $order";
    if ($limit != -1) {
        $sql .= " LIMIT $offset, $limit";
    }
    $results = $conn->query($sql);
    $data = array();
    $i = 0;
    while ($row = $results->fetch_assoc()) {
        $data[] = $row;
    }
    return $data;
}

function getUserAddress($id, $columns = array()) {
    global $conn;
    $data = null;
    $id = secure($id);

    $sql = "SELECT * FROM " . T_USER_ADDRESSES . " WHERE id = '{$id}'";
    if (!empty($columns) && is_array($columns)) {
        $sql = "SELECT `" . implode("`,`", $columns) . "` FROM " . T_USER_ADDRESSES . " WHERE id = '{$id}'";
    }
    $results = $conn->query($sql);
    while ($row = $results->fetch_assoc()) {
        $data = $row;
    }
    return $data;
}

function addUserAddress($address) {
    global $conn;
    $user_id = secure($address['user_id']);
    $name = secure($address['name']);
    $mobile = secure($address['mobile']);
    $phone = secure($address['phone']);
    $add = isset($address['address']) ? $address['address'] : "";
    $locality = isset($address['locality']) ? $address['locality'] : "";
    $landmark = isset($address['landmark']) ? $address['landmark'] : "";
    $city = secure($address['city']);
    $state = secure($address['state']);
    $pincode = secure($address['pincode']);
    $country = secure($address['country']);
    $address_type = secure($address['address_type']);
    $is_default = secure($address['is_default']) ? $address['is_default'] : 'N';
    $status = secure($address['status']) ? $address['status'] : 'A';

    if (count(getUserAddresses(array('id'), array('user_id' => $user_id))) <= 0) {
        $is_default = 'Y';
    }
    if ($is_default == 'Y') {
        $conn->query("UPDATE " . T_USER_ADDRESSES . " SET is_default = 'N' WHERE user_id = '{$user_id}'");
    }
    $qstatus = $conn->query("INSERT INTO " . T_USER_ADDRESSES . " (user_id, name, mobile, phone, address, locality, landmark, city, state, pincode, country, address_type, is_default, status) VALUES('{$user_id}', '{$name}', '{$mobile}', '{$phone}', '{$add}', '{$locality}', '{$landmark}', '{$city}', '{$state}', '{$pincode}', '{$country}', '{$address_type}', '{$is_default}', '{$status}')");
    $GLOBALS['queryerrormsg'] = $conn->error;
    if ($qstatus) {
        $qstatus = $conn->insert_id;
    }
    return $qstatus;
}

function updateUserAddress($address) {
    global $conn;
    $id = secure($address['id']);
    $user_id = secure($address['user_id']);
    $name = secure($address['name']);
    $mobile = secure($address['mobile']);
    $phone = secure($address['phone']);
    $add = isset($address['address']) ? $address['address'] : "";
    $locality = isset($address['locality']) ? $address['locality'] : "";
    $landmark = isset($address['landmark']) ? $address['landmark'] : "";
    $city = secure($address['city']);
    $state = secure($address['state']);
    $pincode = secure($address['pincode']);
    $country = secure($address['country']);
    $address_type = secure($address['address_type']);
    $is_default = secure($address['is_default']) ? $address['is_default'] : 'N';
    $status = secure($address['status']) ? $address['status'] : 'A';
    if ($is_default == 'Y') {
        $conn->query("UPDATE " . T_USER_ADDRESSES . " SET is_default = 'N' WHERE user_id = '{$user_id}'");
    }
    $sql = "UPDATE " . T_USER_ADDRESSES . " SET user_id = '{$user_id}', name = '{$name}', mobile = '{$mobile}', phone = '{$phone}', address = '{$add}', locality = '{$locality}', landmark = '{$landmark}', city = '{$city}', state = '{$state}', pincode = '{$pincode}', country = '{$country}', address_type = '{$address_type}', is_default = '{$is_default}', status = '{$status}' WHERE id = '{$id}'";
    if ($conn->query($sql)) {
        return true;
    }
    $GLOBALS['queryerrormsg'] = 'There is some problem!';
    return false;
}

function deleteUserAddress($id) {
    global $conn;
    $id = secure($id);
    $qstatus = $conn->query("DELETE FROM " . T_USER_ADDRESSES . " WHERE id = '{$id}'");
    $GLOBALS['queryerrormsg'] = $conn->error;
    return $qstatus;
}

function getUsersBankDetails($columns = array(), $filters = array(), $offset = 0, $limit = 10, $order_by = "id", $order = "DESC") {
    global $conn;
    $sql = "SELECT * FROM " . T_USER_BANK_DETAILS . " WHERE 1";
    if (!empty($columns) && is_array($columns)) {
        $sql = "SELECT `" . implode("`,`", $columns) . "` FROM " . T_USER_BANK_DETAILS . " WHERE 1";
    }
    if (isset($filters['user_id']) && !empty($filters['user_id'])) {
        $user_id = secure($filters['user_id']);
        $sql .= " AND user_id = '{$user_id}'";
    }
    if (isset($filters['bankname']) && trim($filter['bankname']) <> "") {
        $bankname = secure($filters['bankname']);
        $sql .= " AND bankname LIKE '%{$bankname}%'";
    }
    if (isset($filters['accountname']) && trim($filter['accountname']) <> "") {
        $accountname = secure($filters['accountname']);
        $sql .= " AND accountname LIKE '%{$accountname}%'";
    }
    if (isset($filters['accountnumber']) && trim($filter['accountnumber']) <> "") {
        $accountnumber = secure($filters['accountnumber']);
        $sql .= " AND accountnumber = '{$accountnumber}'";
    }
    if (isset($filters['ifsc']) && trim($filter['ifsc']) <> "") {
        $ifsc = secure($filters['ifsc']);
        $sql .= " AND ifsc = '{$ifsc}'";
    }
    if (isset($filters['bankaddress']) && trim($filter['bankaddress']) <> "") {
        $bankaddress = secure($filters['bankaddress']);
        $sql .= " AND bankaddress LIKE '%{$bankaddress}%'";
    }
    if (isset($filters['query'])) {
        $q = secure($filters['query']);
        $sql .= " AND (bankname LIKE '%{$q}%' OR accountname LIKE '%{$q}%' OR accountnumber LIKE '%{$q}%' OR ifsc LIKE '%{$q}%' OR bankaddress LIKE '%{$q}%')";
    }
    $sql .= " ORDER BY $order_by $order";
    if ($limit != -1) {
        $sql .= " LIMIT $offset, $limit";
    }
    $results = $conn->query($sql);
    $data = array();
    $i = 0;
    while ($row = $results->fetch_assoc()) {
        $data[] = $row;
    }
    return $data;
}

function getUserBankDetail($user_id, $columns = array()) {
    global $conn;
    $data = null;
    $user_id = secure($user_id);

    $sql = "SELECT * FROM " . T_USER_BANK_DETAILS . " WHERE user_id = '{$user_id}'";
    if (!empty($columns) && is_array($columns)) {
        $sql = "SELECT `" . implode("`,`", $columns) . "` FROM " . T_USER_BANK_DETAILS . " WHERE user_id = '{$user_id}'";
    }
    $results = $conn->query($sql);
    while ($row = $results->fetch_assoc()) {
        $data = $row;
    }
    return $data;
}

function addUpdateUserBankDetail($bank) {
    global $conn;
    $user_id = secure($bank['user_id']);
    $bankname = secure($bank['bankname']);
    $accountname = secure($bank['accountname']);
    $accountnumber = secure($bank['accountnumber']);
    $ifsc = secure($bank['ifsc']);
    $bankaddress = secure($bank['bankaddress']);

    $results = $conn->query("SELECT id FROM " . T_USER_BANK_DETAILS . " WHERE user_id = '{$user_id}'");
    if ($results->num_rows > 0) {
        $sql = "UPDATE " . T_USER_BANK_DETAILS . " SET bankname = '{$bankname}', accountname = '{$accountname}', accountnumber = '{$accountnumber}', ifsc = '{$ifsc}', bankaddress = '{$bankaddress}' WHERE user_id = '{$user_id}'";
    } else {
        $sql = "INSERT INTO " . T_USER_BANK_DETAILS . " (user_id, bankname, accountname, accountnumber, ifsc, bankaddress) VALUES('{$user_id}', '{$bankname}', '{$accountname}', '{$accountnumber}', '{$ifsc}', '{$bankaddress}')";
    }
    $qstatus = $conn->query($sql);
    $GLOBALS['queryerrormsg'] = $conn->error;
    return $qstatus;
}

function getUserTransactions($columns = array(), $filters = array(), $offset = 0, $limit = 10, $order_by = "id", $order = "DESC") {
    global $conn;
    $data = array();
    $sub_sub_sql = '';
    if (isset($filters['user_id']) && trim($filters['user_id']) <> "") {
        $user_id = secure($filters['user_id']);
        $sub_sub_sql .= "AND user_id = '{$user_id}'";
    }
    $sub_sql = '';
    if (isset($columns['balance']) || empty($columns)) {
        $sub_sql = ", COALESCE("
                . "((SELECT SUM(credit) FROM " . T_USER_TRANSACTIONS . " b WHERE b.id <= a.id $sub_sub_sql) - "
                . "(SELECT SUM(debit) FROM " . T_USER_TRANSACTIONS . " b WHERE b.id <= a.id $sub_sub_sql)), 0) AS balance";
    }

    $sql = "SELECT * $sub_sql FROM " . T_USER_TRANSACTIONS . " a WHERE 1";
    if (!empty($columns) && is_array($columns)) {
        if (($key = array_search("balance", $columns)) !== false) {
            unset($columns[$key]);
        }
        $sql = "SELECT `" . implode("`,`", $columns) . "` $sub_sql FROM " . T_USER_TRANSACTIONS . " a WHERE 1";
    }
    if (isset($filters['user_id']) && !empty($filters['user_id'])) {
        $user_id = secure($filters['user_id']);
        $sql .= " AND user_id = '{$user_id}'";
    }
    if (isset($filters['txn_datetime']) && trim($filters['txn_datetime']) <> "") {
        $txn_datetime = secure($filters['txn_datetime']);
        $sql .= " AND txn_datetime = '{$txn_datetime}'";
    }
    if (isset($filters['txn_datetime_from']) && trim($filters['txn_datetime_from']) <> "") {
        $txn_datetime_from = secure($filters['txn_datetime_from']);
        $sql .= " AND txn_datetime >= '{$txn_datetime_from}'";
    }
    if (isset($filters['txn_datetime_to']) && trim($filters['txn_datetime_to']) <> "") {
        $txn_datetime_to = secure($filters['txn_datetime_to']);
        $sql .= " AND txn_datetime <= '{$txn_datetime_to}'";
    }
    if (isset($filters['comments']) && trim($filters['comments']) <> "") {
        $comments = secure($filters['comments']);
        $sql .= " AND comments LIKE '%{$comments}%'";
    }
    if (isset($filters['order_id']) && trim($filters['order_id']) <> "") {
        $order_id = secure($filters['order_id']);
        $sql .= " AND order_id = '{$order_id}'";
    }
    if (isset($filters['withdrawal_id']) && trim($filters['withdrawal_id']) <> "") {
        $withdrawal_id = secure($filters['withdrawal_id']);
        $sql .= " AND withdrawal_id = '{$withdrawal_id}'";
    }
    if (isset($filters['query'])) {
        $q = secure($filters['query']);
        $sql .= " AND (comments LIKE '%{$q}%')";
    }
    if (isset($filters['status'])) {
        $sql .= " AND status = '" . secure($filters['status']) . "'";
    }
    $sql .= " ORDER BY $order_by $order";
    if ($limit != -1) {
        $sql .= " LIMIT $offset, $limit";
    }
    $results = $conn->query($sql);
    $GLOBALS['queryerrormsg'] = $conn->error;
    while ($row = $results->fetch_assoc()) {
        $data[] = $row;
    }
    return $data;
}

function getUserTransaction($id, $columns = array()) {
    global $conn;
    $data = null;
    $id = secure($id);

    $sql = "SELECT * FROM " . T_USER_TRANSACTIONS . " WHERE id = '{$id}'";
    if (!empty($columns) && is_array($columns)) {
        $sql = "SELECT `" . implode("`,`", $columns) . "` FROM " . T_USER_TRANSACTIONS . " WHERE id = '{$id}'";
    }
    $results = $conn->query($sql);
    $GLOBALS['queryerrormsg'] = $conn->error;
    while ($row = $results->fetch_assoc()) {
        $data = $row;
    }
    return $data;
}

function getUserBalance($user_id) {
    global $conn;
    $data = 0;
    $user_id = secure($user_id);

    $sql = "SELECT COALESCE("
            . "((SELECT SUM(credit) FROM " . T_USER_TRANSACTIONS . " b WHERE b.id <= a.id AND user_id = '{$user_id}') - "
            . "(SELECT SUM(debit) FROM " . T_USER_TRANSACTIONS . " b WHERE b.id <= a.id AND user_id = '{$user_id}')), 0) AS balance"
            . " FROM " . T_USER_TRANSACTIONS . " a WHERE user_id = '{$user_id}'";

    $results = $conn->query($sql);
    $GLOBALS['queryerrormsg'] = $conn->error;
    while ($row = $results->fetch_assoc()) {
        $data = $row['balance'];
    }
    return $data;
}

function addUserTransaction($transaction) {
    global $conn;
    $user_id = secure($transaction['user_id']);
    $txn_datetime = secure($transaction['txn_datetime']);
    $credit = secure($transaction['credit']);
    $debit = secure($transaction['debit']);
    $comments = secure($transaction['comments']);
    $order_id = secure($transaction['order_id']);
    $withdrawal_id = secure($transaction['withdrawal_id']);
    $status = secure($transaction['status']) ? $transaction['status'] : '1';

    $qstatus = $conn->query("INSERT INTO " . T_USER_TRANSACTIONS . " (user_id, txn_datetime, credit, debit, comments, order_id, withdrawal_id, status) VALUES('{$user_id}', '{$txn_datetime}', '{$credit}', '{$debit}', '{$comments}', '{$order_id}', '{$withdrawal_id}', '{$status}')");
    $GLOBALS['queryerrormsg'] = $conn->error;
    if ($qstatus) {
        $qstatus = $conn->insert_id;
    }
    return $qstatus;
}

function updateUserTransaction($transaction) {
    global $conn;
    $id = secure($transaction['id']);
    $user_id = secure($transaction['user_id']);
    $txn_datetime = secure($transaction['txn_datetime']);
    $credit = secure($transaction['credit']);
    $debit = secure($transaction['debit']);
    $comments = secure($transaction['comments']);
    $order_id = secure($transaction['order_id']);
    $withdrawal_id = secure($transaction['withdrawal_id']);
    $status = secure($transaction['status']) ? $transaction['status'] : '1';

    $sql = "UPDATE " . T_USER_TRANSACTIONS . " SET user_id = '{$user_id}', txn_datetime = '{$txn_datetime}', credit = '{$credit}', debit = '{$debit}', comments = '{$comments}', order_id = '{$order_id}', withdrawal_id = '{$withdrawal_id}', status = '{$status}' WHERE id = '{$id}'";
    $qstatus = $conn->query($sql);
    $GLOBALS['queryerrormsg'] = $conn->error;
    return $qstatus;
}

function deleteUserTransaction($id) {
    global $conn;
    $id = secure($id);
    $qstatus = $conn->query("DELETE FROM " . T_USER_TRANSACTIONS . " WHERE id = '{$id}'");
    $GLOBALS['queryerrormsg'] = $conn->error;
    return $qstatus;
}

/* User Functions End */

/* Terms Functions Start */

function getTerms($columns = array(), $filters = array(), $offset = 0, $limit = 20, $order_by = "name", $order = "ASC") {
    global $conn;
    $data = array();

    $sql = "SELECT t.*, tt.* FROM " . T_TERMS . " AS t LEFT JOIN " . T_TERM_TAXONOMY . " AS tt ON t.ID = tt.term_id WHERE 1";

    if (!empty($columns) && is_array($columns)) {
        $termscolumns = array_intersect(array("ID", "name", "slug", "term_group"), $columns);
        $taxonomycolumns = array_intersect(array("term_id", "taxonomy", "description", "parent", "count"), $columns);
        $sql = "SELECT ";
        $sql .= !empty($termscolumns) ? "t." . implode(",t.", $termscolumns) : "";
        $sql .= !empty($taxonomycolumns) ? (!empty($termscolumns) ? "," : "") . "tt." . implode(",tt.", $taxonomycolumns) : "";
        $sql .= " FROM " . T_TERMS . " AS t LEFT JOIN " . T_TERM_TAXONOMY . " AS tt ON t.ID = tt.term_id WHERE 1";
    }

    if (isset($filters['name']) && trim($filters['name']) <> "") {
        $name = secure($filters['name']);
        $sql .= " AND t.name = '{$name}'";
    }
    if (isset($filters['taxonomy']) && trim($filters['taxonomy']) <> "") {
        $taxonomy = secure($filters['taxonomy']);
        $sql .= " AND tt.taxonomy = '{$taxonomy}'";
    }
    if (isset($filters['parent']) && trim($filters['parent']) <> "") {
        $parent = secure($filters['parent']);
        $sql .= " AND tt.parent = '{$parent}'";
    }
    if (isset($filters['post_id']) && trim($filters['post_id']) <> "") {
        $post_id = secure($filters['post_id']);
        $sql .= " AND tt.id IN (SELECT term_taxonomy_id FROM " . T_TERM_RELATIONSHIPS . " WHERE post_id='{$post_id}')";
    }
    if (isset($filters['query'])) {
        $q = secure($filters['query']);
        $sql .= " AND (t.name LIKE '%{$q}%' OR t.slug LIKE '%{$q}%' OR tt.description LIKE '%{$q}%')";
    }
    $sql .= " ORDER BY $order_by $order";
    if ($limit != -1) {
        $sql .= " LIMIT $offset, $limit";
    }
    $results = $conn->query($sql);
    $GLOBALS['queryerrormsg'] = $conn->error;
    while ($row = $results->fetch_assoc()) {
        $data[] = $row;
    }
    return $data;
}

function getTerm($id_or_slug) {
    global $conn;
    $id_or_slug = secure($id_or_slug);
    $data = array();

    $results = $conn->query("SELECT t.*, tt.* FROM " . T_TERMS . " AS t LEFT JOIN " . T_TERM_TAXONOMY . " AS tt ON t.ID = tt.term_id WHERE t.ID = '{$id_or_slug}' OR t.slug = '{$id_or_slug}'");
    while ($row = $results->fetch_assoc()) {
        $data = $row;
    }

    return $data;
}

function isTermExists($term) {
    global $conn;
    $slug = url_slug(secure($term['slug']));
    $taxonomy = secure($term['taxonomy']);

    $results = $conn->query("SELECT t.*, tt.* FROM " . T_TERMS . " AS t LEFT JOIN " . T_TERM_TAXONOMY . " AS tt ON t.ID = tt.term_id WHERE t.slug = '{$slug}' AND tt.taxonomy = '{$taxonomy}'");
    if ($results->num_rows > 0) {
        return true;
    }
    return false;
}

function addTerm($term) {
    global $conn;
    $name = secure($term['name']);
    $slug = url_slug(secure($term['slug']));
    $term_group = isset($term['term_group']) ? secure($term['term_group']) : 0;
    $taxonomy = secure($term['taxonomy']);
    $description = mysqli_real_escape_string($conn, $term['description']);
    $parent = secure($term['parent']);
    $count = secure($term['count']);

    if (isTermExists($term)) {
        $GLOBALS['queryerrormsg'] = "Term already exist";
        return false;
    }
    if (!$conn->query("INSERT INTO " . T_TERMS . " (name, slug, term_group) VALUES('{$name}','{$slug}','{$term_group}')")) {
        $GLOBALS['queryerrormsg'] = $conn->error;
        return false;
    }
    $term_id = $conn->insert_id;
    $conn->query("INSERT INTO " . T_TERM_TAXONOMY . " (term_id,taxonomy,description,parent,count) VALUES('{$term_id}','{$taxonomy}','{$description}','{$parent}','{$count}')");
    if (isset($term['metas']) && is_array($term['metas'])) {
        foreach ($term['metas'] as $key => $value) {
            updateTermMeta($term_id, $key, $value);
        }
    }
    return $term_id;
}

function updateTerm($term) {
    global $conn;
    $id = secure($term['id']);
    $name = secure($term['name']);
    $slug = url_slug(secure($term['slug']));
    $term_group = isset($term['term_group']) ? secure($term['term_group']) : 0;
    $taxonomy = secure($term['taxonomy']);
    $description = mysqli_real_escape_string($conn, $term['description']);
    $parent = secure($term['parent']);
    $count = secure($term['count']);

    $results = $conn->query("SELECT * FROM " . T_TERMS . " WHERE id != '{$id}' AND (name = '{$name}' OR slug = '{$slug}')");
    if ($results->num_rows > 1) {
        $GLOBALS['queryerrormsg'] = "Term already exist";
        return false;
    }
    if (!$conn->query("UPDATE " . T_TERMS . " SET name = '{$name}', slug = '{$slug}', term_group = '{$term_group}' WHERE id = '{$id}'")) {
        $GLOBALS['queryerrormsg'] = "Please try again later";
        return false;
    }
    $conn->query("UPDATE " . T_TERM_TAXONOMY . " SET description = '{$description}', parent = '{$parent}', count = '{$count}' WHERE term_id = '{$id}')");
    foreach ($term['metas'] as $key => $value) {
        updateTermMeta($id, $key, $value);
    }
    return true;
}

function deleteTerm($id) {
    global $conn;
    $id = secure($id);
    if ($conn->query("DELETE FROM " . T_TERMS . " WHERE id='{$id}'")) {
        $conn->query("DELETE FROM " . T_TERM_RELATIONSHIPS . " WHERE term_taxonomy_id IN (SELECT id FROM " . T_TERM_TAXONOMY . " WHERE term_id='{$id}')");
        $conn->query("DELETE FROM " . T_TERM_TAXONOMY . " WHERE term_id='{$id}'");
        $conn->query("DELETE FROM " . T_TERM_META . " WHERE term_id='{$id}'");
        return true;
    }
    return false;
}

function updateTermMeta($term_id, $meta_key, $meta_value) {
    global $conn;
    $term_id = secure($term_id);
    $meta_key = secure($meta_key);
    $meta_value = secure($meta_value);
    $results = $conn->query("SELECT * FROM " . T_TERM_META . " WHERE term_id = '{$term_id}' AND meta_key = '{$meta_key}'");
    if ($results->num_rows > 0) {
        return $conn->query("UPDATE " . T_TERM_META . " SET meta_value = '{$meta_value}' WHERE term_id = '{$term_id}' AND meta_key = '{$meta_key}'");
    } else {
        return $conn->query("INSERT INTO " . T_TERM_META . " (term_id, meta_key, meta_value) VALUES('{$term_id}', '{$meta_key}', '{$meta_value}')");
    }
}

/* Terms Functions End */

/* Post Functions Start */

function addPostTerm($post_id, $term_taxonomy_id, $term_order) {
    global $conn;
    $post_id = secure($post_id);
    $term_taxonomy_id = secure($term_taxonomy_id);
    $term_order = secure($term_order);

    $results = $conn->query("SELECT * FROM " . T_TERM_RELATIONSHIPS . " WHERE post_id = '{$post_id}' AND term_taxonomy_id = '{$term_taxonomy_id}'");
    if ($results->num_rows <= 0) {
        $qstatus = $conn->query("INSERT INTO " . T_TERM_RELATIONSHIPS . " (post_id, term_taxonomy_id, term_order) VALUES('{$post_id}', '{$term_taxonomy_id}', '{$term_order}')");
        $GLOBALS['queryerrormsg'] = mysqli_error($conn);
    }
    return $qstatus;
}

function deletePostTerms($post_id) {
    global $conn;
    $post_id = secure($post_id);

    $qstatus = $conn->query("DELETE FROM " . T_TERM_RELATIONSHIPS . " WHERE post_id = '{$post_id}'");
    $GLOBALS['queryerrormsg'] = $conn->error;
    return $qstatus;
}

function getPostMetas($post_id) {
    global $conn;
    $metas = array();
    $post_id = secure($post_id);
    $results = $conn->query("SELECT * FROM " . T_POST_META . " WHERE post_id='{$post_id}'");
    while ($row = $results->fetch_assoc()) {
        $metas[$row['meta_key']] = $row['meta_value'];
    }
    return $metas;
}

function getPostMeta($post_id, $meta_key) {
    global $conn;
    $meta = array();
    $post_id = secure($post_id);
    $meta_key = secure($meta_key);
    $results = $conn->query("SELECT * FROM " . T_POST_META . " WHERE post_id = '{$post_id}' AND meta_key = '{$meta_key}'");
    while ($row = $results->fetch_assoc()) {
        $meta = $row['meta_value'];
    }
    return $meta;
}

function updatePostMeta($post_id, $meta_key, $meta_value) {
    global $conn;
    $post_id = secure($post_id);
    $meta_key = secure($meta_key);
    $meta_value = $meta_value;
    $results = $conn->query("SELECT * FROM " . T_POST_META . " WHERE post_id = '{$post_id}' AND meta_key = '{$meta_key}'");
    if ($results->num_rows > 0) {
        return $conn->query("UPDATE " . T_POST_META . " SET meta_value = '{$meta_value}' WHERE post_id = '{$post_id}' AND meta_key = '{$meta_key}'");
    } else {
        return $conn->query("INSERT INTO " . T_POST_META . " (post_id, meta_key, meta_value) VALUES('{$post_id}', '{$meta_key}', '{$meta_value}')");
    }
}

function getPosts($columns = array(), $filters = array(), $offset = 0, $limit = 12, $order_by = 'id', $order = 'DESC') {
    global $conn;
    $data = array();

    $sql = "SELECT * FROM " . T_POSTS . " WHERE 1";
    if (!empty($columns) && is_array($columns)) {
        $sql = "SELECT `" . implode("`,`", $columns) . "` FROM " . T_POSTS . " WHERE 1";
    }
    if (isset($filters['post_type'])) {
        $post_type = secure($filters['post_type']);
        $sql .= " AND post_type = '{$post_type}'";
    }
    if (isset($filters['post_title'])) {
        $post_title = secure($filters['post_title']);
        $sql .= " AND post_title = '{$post_title}'";
    }
    if (isset($filters['post_content'])) {
        $post_content = secure($filters['post_content']);
        $sql .= " AND post_content LIKE '%{$post_content}%'";
    }
    if (isset($filters['post_content_filtered'])) {
        $post_content_filtered = secure($filters['post_content_filtered']);
        $sql .= " AND post_content_filtered LIKE '%{$post_content_filtered}%'";
    }
    if (isset($filters['post_excerpt'])) {
        $post_excerpt = secure($filters['post_excerpt']);
        $sql .= " AND post_excerpt LIKE '%{$post_excerpt}%'";
    }
    if (isset($filters['post_author'])) {
        $post_author = secure($filters['post_author']);
        $sql .= " AND post_author = '{$post_author}'";
    }
    if (isset($filters['post_password'])) {
        $post_password = secure($filters['post_password']);
        $sql .= " AND post_password = '{$post_password}'";
    }
    if (isset($filters['post_name'])) {
        $post_name = secure($filters['post_name']);
        $sql .= " AND post_name = '{$post_name}'";
    }
    if (isset($filters['post_parent'])) {
        $post_parent = secure($filters['post_parent']);
        $sql .= " AND post_parent = '{$post_parent}'";
    }
    if (isset($filters['post_mime_type'])) {
        $post_mime_type = secure($filters['post_mime_type']);
        $sql .= " AND post_mime_type = '{$post_mime_type}'";
    }
    if (isset($filters['post_mime_type_like'])) {
        $post_mime_type = secure($filters['post_mime_type_like']);
        $sql .= " AND post_mime_type LIKE '{$post_mime_type}%'";
    }
    if (isset($filters['post_date'])) {
        $post_date = secure($filters['post_date']);
        $sql .= " AND post_date LIKE '%{$post_date}%'";
    }
    if (isset($filters['ping_status'])) {
        $ping_status = secure($filters['ping_status']);
        $sql .= " AND ping_status = '{$ping_status}'";
    }
    if (isset($filters['comment_status'])) {
        $comment_status = secure($filters['comment_status']);
        $sql .= " AND comment_status = '{$comment_status}'";
    }
    if (isset($filters['post_status'])) {
        $post_status = secure($filters['post_status']);
        $sql .= " AND post_status = '{$post_status}'";
    }
    if (isset($filters['statuses']) && is_array($filters['statuses'])) {
        $statuses = implode("','", $filters['statuses']);
        $sql .= " AND post_status IN ('{$statuses}')";
    }
    if (isset($filters['term_taxonomy_id'])) {
        $term_taxonomy_id = secure($filters['term_taxonomy_id']);
        $sql .= " AND id IN (SELECT post_id FROM " . T_TERM_RELATIONSHIPS . " WHERE term_taxonomy_id = '{$term_taxonomy_id}')";
    }
    if (isset($filters['q']) && trim($filters['q']) <> "") {
        $q = secure($filters['q']);
        $sql .= " AND (post_title LIKE '%{$q}%' OR post_content_filtered LIKE '%{$q}%' OR post_excerpt LIKE '%{$q}%')";
    }
    $sql .= " ORDER BY {$order_by} {$order}";
    if ($limit != -1 && is_numeric($offset) && is_numeric($limit)) {
        $sql .= " LIMIT {$offset}, {$limit}";
    }

    $results = $conn->query($sql);
    $GLOBALS['queryerrormsg'] = $conn->error;
    while ($row = $results->fetch_assoc()) {
        if (isset($filters['with_metas'])) {
            $row['metas'] = getPostMetas($row['id']);
        }
        if (isset($filters['with_terms'])) {
            $row['terms'] = getTerms(array(), array("post_id" => $row['id']));
        }
        //$post['comments'] = getComments(array(), array("post_id" => $fetched_data['id']));
        $data[] = $row;
    }
    return $data;
}

function getPost($id_or_slug, $columns = array(), $with_metas = false, $with_terms = false, $with_comments = false) {
    global $conn;
    $data = null;
    $id_or_slug = secure($id_or_slug);
    $sql = "SELECT * FROM " . T_POSTS . " WHERE id = '{$id_or_slug}' OR post_name = '{$id_or_slug}'";
    if (!empty($columns) && is_array($columns)) {
        $sql = "SELECT `" . implode("`,`", $columns) . "` FROM " . T_POSTS . " WHERE id = '{$id_or_slug}' OR post_name = '{$id_or_slug}'";
    }
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
        if ($with_metas) {
            $row['metas'] = getPostMetas($row['id']);
        }
        if ($with_terms) {
            $row['terms'] = getTerms(array(), array("post_id" => $row['id']));
        }
        if ($with_comments) {
            $row['comments'] = getComments(array(), array("post_id" => $row['id']));
        }
        $data = $row;
    }
    return $data;
}

function addPost($post) {
    global $conn;
    $post_type = secure($post['post_type']);
    $post_title = secure($post['post_title']);
    $post_content = mysqli_real_escape_string($conn, $post['post_content']);
    $post_content_filtered = mysqli_real_escape_string($conn, $post['post_content_filtered']);
    $post_excerpt = mysqli_real_escape_string($conn, $post['post_excerpt']);
    $post_author = secure($post['post_author']);
    $post_password = secure($post['post_password']);
    $post_name = $tmp_post_name = isset($post['post_name']) && trim($post['post_name']) <> "" ? url_slug(secure($post['post_name'])) : getNextIncrement(T_POSTS);
    $post_parent = secure($post['post_parent']);
    $post_mime_type = secure($post['post_mime_type']);
    $to_ping = secure($post['to_ping']);
    $pinged = secure($post['pinged']);
    $guid = secure($post['guid']);
    $menu_order = secure($post['menu_order']);
    $comment_count = secure($post['comment_count']);
    $post_date = isset($post['post_date']) ? secure($post['post_date']) : date("Y-m-d H:i:s");
    $post_modified = isset($post['post_modified']) ? secure($post['post_modified']) : date("Y-m-d H:i:s");
    $ping_status = secure($post['ping_status']);
    $comment_status = secure($post['comment_status']);
    $post_status = secure($post['post_status']);

    $checksql = "SELECT id FROM " . T_POSTS . " WHERE post_type = '{$post_type}' AND post_name = '{$post_name}'";
    if ($post_type == "page" || $post_type == "post") {
        $checksql = "SELECT id FROM " . T_POSTS . " WHERE post_type IN ('page', 'post') AND post_name = '{$post_name}'";
    }
    $i = 1;
    $results = $conn->query($checksql);
    while ($results->num_rows > 0) {
        $post_name = $tmp_post_name . "-" . $i++;
        $checksql = "SELECT id FROM " . T_POSTS . " WHERE post_type = '{$post_type}' AND post_name = '{$post_name}'";
        if ($post_type == "page" || $post_type == "post") {
            $checksql = "SELECT id FROM " . T_POSTS . " WHERE post_type IN ('page', 'post') AND post_name = '{$post_name}'";
        }
        $results = $conn->query($checksql);
    }

    if (!$conn->query("INSERT INTO " . T_POSTS . " (post_type, post_title, post_content, post_content_filtered, post_excerpt, post_author, post_password, post_name, post_parent, post_mime_type, to_ping, pinged, guid, menu_order, comment_count, post_date, post_modified, ping_status, comment_status, post_status) VALUES('{$post_type}', '{$post_title}', '{$post_content}', '{$post_content_filtered}', '{$post_excerpt}', '{$post_author}', '{$post_password}', '{$post_name}', '{$post_parent}', '{$post_mime_type}', '{$to_ping}', '{$pinged}', '{$guid}', '{$menu_order}', '{$comment_count}', '{$post_date}', '{$post_modified}', '{$ping_status}', '{$comment_status}', '{$post_status}')")) {
        $GLOBALS['queryerrormsg'] = $conn->error;
        return false;
    }
    $post_id = $conn->insert_id;
    if (isset($post['terms']) && is_array($post['terms'])) {
        foreach ($post['terms'] as $term_taxonomy_id) {
            addPostTerm($post_id, $term_taxonomy_id, 0);
        }
    }
    if (isset($post['metas']) && is_array($post['metas'])) {
        foreach ($post['metas'] as $key => $value) {
            updatePostMeta($post_id, $key, $value);
        }
    }
    return $post_id;
}

function updatePost($post) {
    global $conn;
    $id = secure($post['id']);
    $post_type = secure($post['post_type']);
    $post_title = secure($post['post_title']);
    $post_content = mysqli_real_escape_string($conn, $post['post_content']);
    $post_content_filtered = mysqli_real_escape_string($conn, $post['post_content_filtered']);
    $post_excerpt = mysqli_real_escape_string($conn, $post['post_excerpt']);
    $post_author = secure($post['post_author']);
    $post_password = secure($post['post_password']);
    $post_name = $tmp_post_name = isset($post['post_name']) && trim($post['post_name']) <> "" ? url_slug(secure($post['post_name'])) : getNextIncrement(T_POSTS);
    $post_parent = secure($post['post_parent']);
    $post_mime_type = secure($post['post_mime_type']);
    $to_ping = secure($post['to_ping']);
    $pinged = secure($post['pinged']);
    $guid = secure($post['guid']);
    $menu_order = secure($post['menu_order']);
    $comment_count = secure($post['comment_count']);
    $post_date = isset($post['post_date']) ? secure($post['post_date']) : date("Y-m-d H:i:s");
    $post_modified = isset($post['post_modified']) ? secure($post['post_modified']) : date("Y-m-d H:i:s");
    $ping_status = secure($post['ping_status']);
    $comment_status = secure($post['comment_status']);
    $post_status = secure($post['post_status']);

    $checksql = "SELECT id FROM " . T_POSTS . " WHERE post_type = '{$post_type}' AND post_name = '{$post_name}' AND id != '{$id}'";
    if ($post_type == "page" || $post_type == "post") {
        $checksql = "SELECT id FROM " . T_POSTS . " WHERE post_type IN ('page', 'post') AND post_name = '{$post_name}' AND id != '{$id}'";
    }
    $i = 1;
    $results = $conn->query($checksql);
    while ($results->num_rows > 0) {
        $post_name = $tmp_post_name . "-" . $i;
        $checksql = "SELECT id FROM " . T_POSTS . " WHERE post_type = '{$post_type}' AND post_name = '{$post_name}' AND id != '{$id}'";
        if ($post_type == "page" || $post_type == "post") {
            $checksql = "SELECT id FROM " . T_POSTS . " WHERE post_type IN ('page', 'post') AND post_name = '{$post_name}' AND id != '{$id}'";
        }
        $results = $conn->query($checksql);
    }

    if (!$conn->query("UPDATE " . T_POSTS . " SET post_type='{$post_type}', post_title='{$post_title}', post_content='{$post_content}', post_content_filtered='{$post_content_filtered}', post_excerpt='{$post_excerpt}', post_author='{$post_author}', post_password='{$post_password}', post_name = '{$post_name}', post_parent='{$post_parent}', post_mime_type='{$post_mime_type}', to_ping='{$to_ping}', pinged='{$pinged}', guid='{$guid}', menu_order='{$menu_order}', comment_count='{$comment_count}', post_date='{$post_date}', post_modified='{$post_modified}', ping_status='{$ping_status}', comment_status='{$comment_status}', post_status='{$post_status}' WHERE id='{$id}'")) {
        $GLOBALS['queryerrormsg'] = "Please try again later";
        return false;
    }
    deletePostTerms($id);
    if (isset($post['terms']) && is_array($post['terms'])) {
        foreach ($post['terms'] as $term_taxonomy_id) {
            addPostTerm($id, $term_taxonomy_id, 0);
        }
    }
    if (isset($post['metas']) && is_array($post['metas'])) {
        foreach ($post['metas'] as $key => $value) {
            updatePostMeta($id, $key, $value);
        }
    }

    return true;
}

function deletePost($id) {
    global $conn;
    $id = secure($id);
    if ($conn->query("DELETE FROM " . T_POSTS . " WHERE id='{$id}'")) {
        $conn->query("DELETE FROM " . T_TERM_RELATIONSHIPS . " WHERE post_id='{$id}'");
        $conn->query("DELETE FROM " . T_POST_META . " WHERE post_id='{$id}'");
        $conn->query("DELETE FROM " . T_COMMENTS . " WHERE post_id='{$id}'");
        $conn->query("DELETE FROM " . T_COMMENT_META . " WHERE comment_id IN (SELECT id FROM " . T_COMMENTS . " WHERE post_id='{$id}')");

        return true;
    }
    return false;
}

function getPostsDates($type = YEAR_MONTH_DAY, $post_type = null) {
    global $conn;
    $data = array();

    $condition = "1";
    if ($post_type != null) {
        $post_type = secure($post_type);
        $condition = "post_type = '{$post_type}'";
    }

    $sql = "SELECT DISTINCT DATE(post_date) AS pdate FROM " . T_POSTS . " WHERE $condition";
    if ($type == YEAR) {
        $sql = "SELECT DISTINCT EXTRACT(YEAR FROM post_date) AS pdate FROM " . T_POSTS . " WHERE $condition";
    }
    if ($type == MONTH) {
        $sql = "SELECT DISTINCT EXTRACT(MONTH FROM post_date) AS pdate FROM " . T_POSTS . " WHERE $condition";
    }
    if ($type == DAY) {
        $sql = "SELECT DISTINCT EXTRACT(DAY FROM post_date) AS pdate FROM " . T_POSTS . " WHERE $condition";
    }
    if ($type == YEAR_MONTH) {
        $sql = "SELECT DISTINCT EXTRACT(YEAR_MONTH FROM post_date) AS pdate FROM " . T_POSTS . " WHERE $condition";
    }

    $results = $conn->query($sql);
    $GLOBALS['queryerrormsg'] = $conn->error;
    while ($row = $results->fetch_assoc()) {
        $data[] = $row['pdate'];
    }
    return $data;
}

/* Post Functions End */

/* Comment Functions Start */

function getCommentMetas($comment_id) {
    global $conn;
    $metas = array();
    $comment_id = secure($comment_id);
    $results = $conn->query("SELECT * FROM " . T_COMMENT_META . " WHERE comment_id='{$comment_id}'");
    while ($row = $results->fetch_assoc()) {
        $metas[$row['meta_key']] = $row['meta_value'];
    }
    return $metas;
}

function updateCommentMeta($comment_id, $meta_key, $meta_value) {
    global $conn;
    $comment_id = secure($comment_id);
    $meta_key = secure($meta_key);
    $meta_value = secure($meta_value);
    $results = $conn->query("SELECT * FROM " . T_COMMENT_META . " WHERE comment_id = '{$comment_id}' AND meta_key = '{$meta_key}'");
    if ($results->num_rows > 0) {
        return $conn->query("UPDATE " . T_COMMENT_META . " SET meta_value = '{$meta_value}' WHERE comment_id = '{$comment_id}' AND meta_key = '{$meta_key}'");
    } else {
        return $conn->query("INSERT INTO " . T_COMMENT_META . " (comment_id, meta_key, meta_value) VALUES('{$comment_id}', '{$meta_key}', '{$meta_value}')");
    }
}

function getComments($columns = array(), $filters = array(), $offset = 0, $limit = 12, $order_by = 'id', $order = 'DESC') {
    global $conn;
    $data = array();

    $sql = "SELECT * FROM " . T_COMMENTS . " WHERE 1";
    if (!empty($columns) && is_array($columns)) {
        $sql = "SELECT `" . implode("`,`", $columns) . "` FROM " . T_COMMENTS . " WHERE 1";
    }
    if (isset($filters['post_id']) && !empty($filters['post_id'])) {
        $post_id = secure($filters['post_id']);
        $sql .= " AND post_id='{$post_id}'";
    }
    if (isset($filters['author_name']) && !empty($filters['approved'])) {
        $author_name = secure($filters['author_name']);
        $sql .= " AND author_name = '{$author_name}'";
    }
    if (isset($filters['author_email']) && !empty($filters['approved'])) {
        $author_email = secure($filters['author_email']);
        $sql .= " AND author_email = '{$author_email}'";
    }
    if (isset($filters['author_ip']) && !empty($filters['approved'])) {
        $author_ip = secure($filters['author_ip']);
        $sql .= " AND author_ip = '{$author_ip}'";
    }
    if (isset($filters['karma']) && !empty($filters['karma'])) {
        $karma = secure($filters['karma']);
        $sql .= " AND karma = '{$karma}'";
    }
    if (isset($filters['status']) && !empty($filters['status'])) {
        $status = secure($filters['status']);
        $sql .= " AND status = '{$status}'";
    }
    if (isset($filters['statuses']) && is_array($filters['statuses'])) {
        $statuses = implode("','", $filters['statuses']);
        $sql .= " AND status IN ('{$statuses}')";
    }
    if (isset($filters['type']) && $filters['type'] != null) {//null compared because empty value also checked
        $type = secure($filters['type']);
        $sql .= " AND type = '{$type}'";
    }
    if (isset($filters['user_id']) && !empty($filters['user_id'])) {
        $user_id = secure($filters['user_id']);
        $sql .= " AND user_id = '{$user_id}'";
    }
    if (isset($filters['q']) && trim($filters['q']) <> "") {
        $q = secure($filters['q']);
        $sql .= " AND (author_name LIKE '%{$q}%' OR author_email LIKE '%{$q}%' OR author_url LIKE '%{$q}%' OR author_ip LIKE '%{$q}%' OR content LIKE '%{$q}%')";
    }
    $sql .= " ORDER BY {$order_by} {$order}";
    if ($limit != -1 && is_numeric($offset) && is_numeric($limit)) {
        $sql .= " LIMIT {$offset}, {$limit}";
    }

    $results = $conn->query($sql);
    $GLOBALS['queryerrormsg'] = $conn->error;
    while ($row = $results->fetch_assoc()) {
        if (isset($filters['with_metas'])) {
            $row['metas'] = getCommentMetas($row['id']);
        }
        $data[$row['id']] = $row;
    }
    return $data;
}

function getComment($id, $columns = array(), $with_metas = false) {
    global $conn;
    $id = secure($id);
    $data = array();
    $sql = "SELECT * FROM " . T_COMMENTS . " WHERE id='{$id}'";
    if (!empty($columns) && is_array($columns)) {
        $sql = "SELECT `" . implode("`,`", $columns) . "` FROM " . T_COMMENTS . " WHERE id='{$id}'";
    }
    $results = $conn->query($sql);
    while ($row = $results->fetch_assoc()) {
        if ($with_metas) {
            $row['metas'] = getCommentMetas($row['id']);
        }
        $data = $row;
    }
    return $data;
}

function addComment($comment) {
    global $conn;
    $post_id = secure($comment['post_id']);
    $author_name = secure($comment['author_name']);
    $author_email = secure($comment['author_email']);
    $author_url = secure($comment['author_url']);
    $author_ip = secure($comment['author_ip']);
    $comment_datetime = isset($comment['comment_datetime']) ? secure($comment['comment_datetime']) : date("Y-m-d H:i:s");
    $content = mysqli_real_escape_string($conn, $comment['content']);
    $karma = isset($comment['karma']) ? secure($comment['karma']) : "0";
    $status = secure($comment['status']);
    $agent = secure($comment['agent']);
    $type = isset($comment['type']) ? secure($comment['type']) : "";
    $parent = isset($comment['parent']) ? secure($comment['parent']) : "0";
    $user_id = isset($comment['user_id']) ? secure($comment['user_id']) : "0";

    if (!$conn->query("INSERT INTO " . T_COMMENTS . " (post_id, author_name, author_email, author_url, author_ip, comment_datetime, content, karma, status, agent, type, parent, user_id) VALUES('{$post_id}','{$author_name}','{$author_email}','{$author_url}','{$author_ip}', '{$comment_datetime}', '{$content}','{$karma}','{$status}','{$agent}','{$type}','{$parent}','{$user_id}')")) {
        $GLOBALS['queryerrormsg'] = $conn->error;
        return false;
    }
    $comment_id = $conn->insert_id;
    if (isset($comment['metas']) && is_array($comment['metas'])) {
        foreach ($comment['metas'] as $key => $value) {
            updateCommentMeta($comment_id, $key, $value);
        }
    }
    return $comment_id;
}

function updateComment($comment) {
    global $conn;
    $id = secure($comment['id']);
    $post_id = secure($comment['post_id']);
    $author_name = secure($comment['author_name']);
    $author_email = secure($comment['author_email']);
    $author_url = secure($comment['author_url']);
    $author_ip = secure($comment['author_ip']);
    $comment_datetime = isset($comment['comment_datetime']) ? secure($comment['comment_datetime']) : date("Y-m-d H:i:s");
    $content = mysqli_real_escape_string($conn, $comment['content']);
    $karma = isset($comment['karma']) ? secure($comment['karma']) : "0";
    $status = secure($comment['status']);
    $agent = secure($comment['agent']);
    $type = isset($comment['type']) ? secure($comment['type']) : "";
    $parent = isset($comment['parent']) ? secure($comment['parent']) : "0";
    $user_id = isset($comment['user_id']) ? secure($comment['user_id']) : "0";

    if (!$conn->query("UPDATE " . T_COMMENTS . " SET post_id = '{$post_id}', author_name = '{$author_name}', author_email = '{$author_email}', author_url = '{$author_url}', author_ip = '{$author_ip}', comment_datetime = '{$comment_datetime}', content = '{$content}', karma = '{$karma}', status = '{$status}', agent = '{$agent}', type = '{$type}', parent = '{$parent}', user_id = '{$user_id}' WHERE id = '{$id}'")) {
        $GLOBALS['queryerrormsg'] = "Please try again later";
        return false;
    }
    if (isset($comment['metas']) && is_array($comment['metas'])) {
        foreach ($comment['metas'] as $key => $value) {
            updateCommentMeta($id, $key, $value);
        }
    }
    return true;
}

function deleteComment($id) {
    global $conn;
    $id = secure($id);
    if ($conn->query("DELETE FROM " . T_COMMENTS . " WHERE id='{$id}'")) {
        $conn->query("DELETE FROM " . T_COMMENT_META . " WHERE comment_id='{$id}'");
        return true;
    }
    return false;
}

/* Comment Functions End */

/* Category Functions Start */

function getCategoryFilters($category_id) {
    global $conn;
    $category_id = secure($category_id);
    $data = array();
    $result = $conn->query("SELECT * FROM " . T_CATEGORY_FILTERS . " WHERE category_id = '{$category_id}'");
    while ($fetched_data = $result->fetch_assoc()) {
        $data[] = $fetched_data;
    }
    return $data;
}

function getMaxPrice($filters) {
    global $conn;
    $data = 0;
    $sql = "SELECT MAX(price) AS price FROM " . T_PRODUCT_SHOP_PRICE . " WHERE 1";
    if (isset($filters['category_id']) && trim($filters['category_id']) <> "") {
        $category_id = secure($filters['category_id']);
        $sql .= " AND product_id IN (SELECT product_id FROM " . T_PRODUCT_CATEGORY . " WHERE category_id = '$category_id')";
    }
    if (isset($filters['q']) && trim($filters['q']) <> "") {
        $q = secure($filters['q']);
        $sql .= " AND product_id IN (SELECT product_id FROM " . T_PRODUCT_CATEGORY . " WHERE category_id IN (SELECT id FROM " . T_CATEGORY . " WHERE name LIKE '%{$q}%' OR description LIKE '%{$q}%' OR slug LIKE '%{$q}%' OR meta_title LIKE '%{$q}%' OR meta_keywords LIKE '%{$q}%' OR meta_description LIKE '%{$q}%'))";
        $sql .= " OR product_id IN (SELECT id FROM " . T_PRODUCTS . " WHERE sku LIKE '%{$q}%' OR name LIKE '%{$q}%' OR slug LIKE '%{$q}%' OR meta_title LIKE '%{$q}%' OR meta_keywords LIKE '%{$q}%' OR meta_description LIKE '%{$q}%')";
        $sql .= " OR product_id IN (SELECT product_id FROM " . T_PRODUCT_TAGS . " WHERE tag_id IN (SELECT id FROM " . T_TAGS . " WHERE name LIKE '%{$q}%' OR slug LIKE '%{$q}%' OR meta_title LIKE '%{$q}%' OR meta_keywords LIKE '%{$q}%' OR meta_description LIKE '%{$q}%'))";
    }
    //echo $sql;
    $results = $conn->query($sql);
    $GLOBALS['queryerrormsg'] = $conn->error;
    while ($row = $results->fetch_assoc()) {
        $data = $row['price'];
    }
    return $data;
}

function getCategories($columns = array(), $filters = array(), $offset = 0, $limit = 12, $order_by = 'display_order', $order = 'DESC') {
    global $conn;
    $data = array();

    $sql = "SELECT * FROM " . T_CATEGORY . " WHERE status != 'T'";
    if (!empty($columns) && is_array($columns)) {
        $sql = "SELECT `" . implode("`,`", $columns) . "` FROM " . T_CATEGORY . " WHERE status != 'T'";
    }
    if (isset($filters['name']) && trim($filters['name']) <> "") {
        $name = secure($filters['name']);
        $sql .= " AND name = '{$name}'";
    }
    if (isset($filters['main_category'])) {
        $main_category = secure($filters['main_category']);
        $sql .= " AND main_category = '{$main_category}'";
    }
    if (isset($filters['product_id']) && trim($filters['product_id']) <> "") {
        $product_id = secure($filters['product_id']);
        $sql .= " AND id IN (SELECT category_id FROM " . T_PRODUCT_CATEGORY . " WHERE product_id='{$product_id}')";
    }
    if (isset($filters['status']) && trim($filters['status']) <> "") {
        $status = secure($filters['status']);
        $sql .= " AND status = '{$status}'";
    }
    if (isset($filters['q']) && trim($filters['q']) <> "") {
        $q = secure($filters['q']);
        $sql .= " AND (name LIKE '%{$q}%' OR description LIKE '%{$q}%' OR slug LIKE '%{$q}%' OR meta_title LIKE '%{$q}%' OR meta_keywords LIKE '%{$q}%' OR meta_description LIKE '%{$q}%')";
    }
    $sql .= " ORDER BY {$order_by} {$order}";
    if ($limit != -1 && is_numeric($offset) && is_numeric($limit)) {
        $sql .= " LIMIT {$offset}, {$limit}";
    }
    $results = $conn->query($sql);
    while ($row = $results->fetch_assoc()) {
        $data[$row['id']] = $row;
    }
    return $data;
}

function getCategory($id_or_slug, $columns = array(), $with_filters = false) {
    global $conn;
    $id_or_slug = secure($id_or_slug);
    $data = array();
    $sql = "SELECT * FROM " . T_CATEGORY . " WHERE id = '{$id_or_slug}' OR slug = '{$id_or_slug}'";
    if (!empty($columns) && is_array($columns)) {
        $sql = "SELECT `" . implode("`,`", $columns) . "` FROM " . T_CATEGORY . " WHERE id = '{$id_or_slug}' OR slug = '{$id_or_slug}'";
    }
    $result = $conn->query($sql);
    while ($fetched_data = $result->fetch_assoc()) {
        $data = $fetched_data;
        if ($with_filters) {
            $data['filters'] = getCategoryFilters($fetched_data['id']);
        }
    }

    return $data;
}

function addCategory($category) {
    global $conn;
    $name = secure($category['name']);
    $description = secure($category['description']);
    $slug = url_slug(secure($category['slug']));
    $maincategory = secure($category['main_category']);
    $image = secure($category['image']);
    $display_order = secure($category['display_order']);
    $meta_title = secure($category['meta_title']);
    $meta_keywords = secure($category['meta_keywords']);
    $meta_description = secure($category['meta_description']);
    $status = secure($category['status']);
    $filters = $category['filters'];

    $results = $conn->query("SELECT * FROM " . T_CATEGORY . " WHERE name = '{$name}' OR slug = '{$slug}'");
    if ($results->num_rows > 0) {
        $GLOBALS['queryerrormsg'] = "Category name or slug already exist";
        return false;
    }
    if (!$conn->query("INSERT INTO " . T_CATEGORY . " (name, description, slug, main_category, image, display_order, meta_title, meta_keywords, meta_description, status) VALUES('{$name}','{$description}','{$slug}','{$maincategory}','{$image}','{$display_order}','{$meta_title}', '{$meta_keywords}', '{$meta_description}','{$status}')")) {
        $GLOBALS['queryerrormsg'] = $conn->error;
        return false;
    }
    $id = $conn->insert_id;
    if ($id && is_array($filters)) {
        foreach ($filters as $filter_id) {
            $filter_id = secure($filter_id);
            $conn->query("INSERT INTO " . T_CATEGORY_FILTERS . " (category_id, filter_id) VALUES('{$id}','{$filter_id}')");
        }
    }
    return true;
}

function updateCategory($category) {
    global $conn;
    $id = secure($category['id']);
    $name = secure($category['name']);
    $description = secure($category['description']);
    $slug = url_slug(secure($category['slug']));
    $maincategory = secure($category['main_category']);
    $display_order = secure($category['display_order']);
    $image = secure($category['image']);
    $meta_title = secure($category['meta_title']);
    $meta_keywords = secure($category['meta_keywords']);
    $meta_description = secure($category['meta_description']);
    $status = secure($category['status']);
    $filters = $category['filters'];

    $results = $conn->query("SELECT * FROM " . T_CATEGORY . " WHERE name='{$name}' OR slug = '{$slug}'");
    if ($results->num_rows > 1) {
        $GLOBALS['queryerrormsg'] = "Category name or slug already exist";
        return false;
    }
    $updatequery = "UPDATE " . T_CATEGORY . " SET name = '{$name}', description = '{$description}', slug = '{$slug}', main_category = '{$maincategory}', display_order = '{$display_order}', image = '{$image}', meta_title = '{$meta_title}', meta_keywords = '{$meta_keywords}', meta_description = '{$meta_description}', status = '{$status}' WHERE id = '{$id}'";
    if (!$conn->query($updatequery)) {
        $GLOBALS['queryerrormsg'] = "Please try again later";
        return false;
    }
    if (is_array($filters)) {
        $conn->query("DELETE FROM " . T_CATEGORY_FILTERS . " WHERE category_id = '{$id}'");
        foreach ($filters as $filter_id) {
            $filter_id = secure($filter_id);
            $conn->query("INSERT INTO " . T_CATEGORY_FILTERS . " (category_id, filter_id) VALUES('{$id}','{$filter_id}')");
        }
    }

    return true;
}

function deleteCategory($id) {
    global $conn;
    $fid = secure($id);
    return $conn->query("UPDATE " . T_CATEGORY . " SET status = 'T' WHERE id='{$fid}'");
}

/* to remove */

function uploadCategoryImage($fileElement) {
    if (empty($_FILES[$fileElement]['name'])) {
        $GLOBALS['uploaderrormsg'] = "File not selected";
        return false;
    }
    $allowed = 'jpg,png,jpeg,gif';
    $extension_allowed = explode(',', $allowed);
    $tmp = explode(".", $_FILES[$fileElement]["name"]);
    $file_extension = strtolower(end($tmp));
    if (!in_array($file_extension, $extension_allowed)) {
        $GLOBALS['uploaderrormsg'] = "File type not allowed";
        return false;
    }
    $dir = "uploads/categoryimages";
    $filename = $dir . '/category_' . generateKey() . '_' . date('d') . '_' . md5(time()) . '.' . $file_extension;
    if (move_uploaded_file($_FILES[$fileElement]["tmp_name"], $filename)) {
        return $filename;
    }
    $GLOBALS['uploaderrormsg'] = "Could not move file";
    return false;
}

/* Category Functions End */

/* Tags Functions Start */

function getTags($columns = array(), $filters = array(), $offset = 0, $limit = 12, $order_by = 'id', $order = 'DESC') {
    global $conn;
    $data = array();
    $sql = "SELECT * FROM " . T_TAGS . " WHERE status != 'T'";
    if (!empty($columns) && is_array($columns)) {
        $sql = "SELECT `" . implode("`,`", $columns) . "` FROM " . T_TAGS . " WHERE status != 'T'";
    }
    if (isset($filters['name']) && trim($filters['name']) <> "") {
        $name = secure($filters['name']);
        $sql .= " AND name LIKE '%{$name}%'";
    }
    if (isset($filters['status']) && trim($filters['status']) <> "") {
        $status = secure($filters['status']);
        $sql .= " AND status = '{$status}'";
    }
    if (isset($filters['product_id'])) {
        $product_id = secure($filters['product_id']);
        $sql .= " AND id IN (SELECT tag_id FROM " . T_PRODUCT_TAGS . " WHERE product_id = '{$product_id}')";
    }
    if (isset($filters['q']) && trim($filters['q']) <> "") {
        $q = secure($filters['q']);
        $sql .= " AND (name LIKE '%{$q}%' OR slug LIKE '%{$q}%' OR meta_title LIKE '%{$q}%' OR meta_keywords LIKE '%{$q}%' OR meta_description LIKE '%{$q}%')";
    }
    $sql .= " ORDER BY {$order_by} {$order}";
    if ($limit != -1 && is_numeric($offset) && is_numeric($limit)) {
        $sql .= " LIMIT {$offset}, {$limit}";
    }
    $results = $conn->query($sql);
    while ($row = $results->fetch_assoc()) {
        $data[$row['id']] = $row;
    }
    return $data;
}

function getTag($id, $columns = array()) {
    global $conn;
    $id = secure($id);
    $data = array();
    $sql = "SELECT * FROM " . T_TAGS . " WHERE id = '{$id}'";
    if (!empty($columns) && is_array($columns)) {
        $sql = "SELECT `" . implode("`,`", $columns) . "` FROM " . T_TAGS . " WHERE id = '{$id}'";
    }
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
        $data = $row;
    }
    return $data;
}

function addTag($tag) {
    global $conn;
    $name = secure($tag['name']);
    $slug = url_slug($tag['slug']);
    $meta_title = secure($tag['meta_title']);
    $meta_keywords = secure($tag['meta_keywords']);
    $meta_description = secure($tag['meta_description']);
    $status = secure($tag['status']);

    $result = $conn->query("SELECT * FROM " . T_TAGS . " WHERE name='{$name}'");
    if ($result->num_rows > 0) {
        $GLOBALS['queryerrormsg'] = "Tag name already exist";
        return false;
    }

    if ($conn->query("INSERT INTO " . T_TAGS . " (name, slug, meta_title, meta_keywords, meta_description, status) VALUE ('{$name}','{$slug}','{$meta_title}','{$meta_keywords}','{$meta_description}','{$status}')")) {
        return true;
    }
    $GLOBALS['queryerrormsg'] = "Please try again later | " . $conn->error;
    return false;
}

function updateTag($tag) {
    global $conn;
    $id = secure($tag['id']);
    $name = secure($tag['name']);
    $slug = url_slug($tag['slug']);
    $meta_title = secure($tag['meta_title']);
    $meta_keywords = secure($tag['meta_keywords']);
    $meta_description = secure($tag['meta_description']);
    $status = secure($tag['status']);

    if ($conn->query("UPDATE " . T_TAGS . " SET name = '{$name}', slug = '{$slug}', meta_title = '{$meta_title}', meta_keywords = '{$meta_keywords}', meta_description = '{$meta_description}', status = '{$status}' WHERE id = '{$id}'")) {
        return true;
    }
    $GLOBALS['queryerrormsg'] = "Please try again later | " . mysqli_error($conn);
    return false;
}

function deleteTag($id) {
    global $conn;
    $id = secure($id);
    if ($conn->query("DELETE FROM " . T_TAGS . " WHERE id='{$id}'")) {
        return $conn->query("DELETE FROM " . T_PRODUCT_TAGS . " WHERE tag_id='{$id}'");
    }
    return false;
}

/* Tags Functions End */

/* Product Options Functions Start */

function getOptions($filter = array(), $offset = 0, $limit = -1, $order = "ASC") {
    global $conn;
    $data = array();
    $query = "SELECT * FROM " . T_OPTIONS . " AS optn WHERE status = 'A'";
    //$query = "SELECT attr.id, attr.attribute_group_id, attr.name, attr.type, attr.terms, attr.display_order, attr.status FROM " . T_ATTRIBUTE . " AS attr WHERE 1";
//    if(isset($filter['product_id'])) {
//        $product_id = secure($filter['product_id']);
//        $query = "SELECT attr.id, attr.attribute_group_id, attr.name, attr.type, attr.terms, attr.display_order, attr.status, pattr.value FROM " . T_ATTRIBUTE . " AS attr LEFT JOIN " . T_PRODUCT_ATTRIBUTE . " AS pattr ON attr.id = pattr.attribute_id WHERE pattr.product_id = '{$product_id}'";
//    }

    $query .= " ORDER BY optn.display_order $order";
    $result = $conn->query($query);
    while ($fetched_data = $result->fetch_assoc()) {
        $result2 = $conn->query("SELECT * FROM " . T_OPTION_VALUES . " WHERE option_id = '" . $fetched_data['id'] . "'");
        $fetched_data['values'] = array();
        while ($fetched_data2 = mysqli_fetch_assoc($result2)) {
            $fetched_data['values'][] = $fetched_data2;
        }
        $data[$fetched_data['id']] = $fetched_data;
    }
    return $data;
}

function getOption($id) {
    global $conn;
    $id = secure($id);
    $data = null;
    $result = $conn->query("SELECT * FROM " . T_OPTIONS . " WHERE id = '{$id}'");
    while ($fetched_data = $result->fetch_assoc()) {
        $data = $fetched_data;
        $result = $conn->query("SELECT * FROM " . T_OPTION_VALUES . " WHERE option_id = '{$id}'");
        $data['values'] = array();
        while ($fetched_data = $result->fetch_assoc()) {
            $data['values'][] = $fetched_data;
        }
    }

    return $data;
}

function addOption($option) {
    global $conn;
    $type = secure($option['type']);
    $name = secure($option['name']);
    $display_order = secure($option['display_order']);
    $status = secure($option['status']);

    $result = $conn->query("SELECT * FROM " . T_OPTIONS . " WHERE name='{$name}'");
    if (mysqli_num_rows($result) > 0) {
        $GLOBALS['queryerrormsg'] = "Option name already exist";
        return false;
    }

    if ($conn->query("INSERT INTO " . T_OPTIONS . " (type, name, display_order, status) VALUE ('{$type}','{$name}','{$display_order}','{$status}')")) {
        $id = mysqli_insert_id($conn);
        foreach ($option['values'] as $v) {
            $v_value = secure($v['value']);
            $v_display_order = secure($v['display_order']);
            $conn->query("INSERT INTO " . T_OPTION_VALUES . " (option_id, option_value, display_order) VALUE ('{$id}','{$v_value}','{$v_display_order}')");
        }
        return true;
    }
    $GLOBALS['queryerrormsg'] = "Please try again later | " . mysqli_error($conn);
    return false;
}

function updateOption($option) {
    global $conn;
    $id = secure($option['id']);
    $type = secure($option['type']);
    $name = secure($option['name']);
    $display_order = secure($option['display_order']);
    $status = secure($option['status']);

    if ($conn->query("UPDATE " . T_OPTIONS . " SET type = '{$type}', name = '{$name}', display_order = '{$display_order}', status = '{$status}' WHERE id = '{$id}'")) {
        //if attribute deleted this will make that deleted
        $conn->query("DELETE FROM " . T_OPTION_VALUES . " WHERE option_id = '{$id}'");
        foreach ($option['values'] as $v) {
            $v_id = secure($v['id']);
            $v_value = secure($v['value']);
            $v_display_order = secure($v['display_order']);
            $conn->query("INSERT INTO " . T_OPTION_VALUES . " (option_id, option_value, display_order) VALUE ('{$id}','{$v_value}','{$v_display_order}')");
        }
        return true;
    }
}

/* Product Options Functions End */

/* Filters Functions Start */

function getFilters($group_only = true, $filters = array(), $offset = 0, $limit = -1, $order_by = 'g.display_order', $order = 'ASC') {
    global $conn;
    $data = array();
    $sql = "SELECT g.name AS group_name, g.display_order AS group_display_order, g.status AS group_status, f.* FROM " . T_FILTER_GROUP . " AS g LEFT JOIN " . T_FILTERS . " AS f ON g.id = f.filter_group_id WHERE g.status = 'A' AND f.status = 'A'";
    if ($group_only) {
        $sql = "SELECT g.id AS group_id, g.name AS group_name, g.display_order AS group_display_order, g.status AS group_status FROM " . T_FILTER_GROUP . " AS g WHERE g.status = 'A'";
    }
    if (isset($filters['group_name']) && trim($filters['group_name']) <> "") {
        $group_name = secure($filters['group_name']);
        $sql .= " AND g.name LIKE '%{$group_name}%'";
    }
    if (!$group_only && isset($filters['name']) && trim($filters['name']) <> "") {
        $name = secure($filters['name']);
        $sql .= " AND f.name LIKE '%{$name}%'";
    }
    if (isset($filters['group_status']) && trim($filters['group_status']) <> "") {
        $group_status = secure($filters['group_status']);
        $sql .= " AND g.status = '{$group_status}'";
    }
    if (!$group_only && isset($filters['status']) && trim($filters['status']) <> "") {
        $status = secure($filters['status']);
        $sql .= " AND f.status = '{$status}'";
    }
    if (!$group_only && isset($filters['product_id'])) {
        $product_id = secure($filters['product_id']);
        $sql .= " AND f.id IN (SELECT filter_id FROM " . T_PRODUCT_FILTERS . " WHERE product_id = '{$product_id}')";
    }
    if (!$group_only && isset($filters['category_id'])) {
        $category_id = secure($filters['category_id']);
        $sql .= " AND f.id IN (SELECT filter_id FROM " . T_CATEGORY_FILTERS . " WHERE category_id = '{$category_id}')";
    }
    if (!$group_only && isset($filters['q']) && trim($filters['q']) <> "") {
        $q = secure($filters['q']);
        $sql .= " AND (g.name LIKE '%{$q}%' OR f.name LIKE '%{$q}%')";
        if (isset($filters['qtype']) && trim($filters['qtype']) == 'global') {
            $sql .= " OR f.id IN (SELECT filter_id FROM " . T_PRODUCT_FILTERS . " WHERE product_id IN (SELECT product_id FROM " . T_PRODUCT_CATEGORY . " WHERE category_id IN (SELECT id FROM " . T_CATEGORY . " WHERE name LIKE '%{$q}%' OR description LIKE '%{$q}%' OR slug LIKE '%{$q}%' OR meta_title LIKE '%{$q}%' OR meta_keywords LIKE '%{$q}%' OR meta_description LIKE '%{$q}%')))";
            $sql .= " OR f.id IN (SELECT filter_id FROM " . T_PRODUCT_FILTERS . " WHERE product_id IN (SELECT id FROM " . T_PRODUCTS . " WHERE sku LIKE '%{$q}%' OR name LIKE '%{$q}%' OR slug LIKE '%{$q}%' OR meta_title LIKE '%{$q}%' OR meta_keywords LIKE '%{$q}%' OR meta_description LIKE '%{$q}%'))";
            $sql .= " OR f.id IN (SELECT filter_id FROM " . T_PRODUCT_FILTERS . " WHERE product_id IN (SELECT product_id FROM " . T_PRODUCT_TAGS . " WHERE tag_id IN (SELECT id FROM " . T_TAGS . " WHERE name LIKE '%{$q}%' OR slug LIKE '%{$q}%' OR meta_title LIKE '%{$q}%' OR meta_keywords LIKE '%{$q}%' OR meta_description LIKE '%{$q}%')))";
        }
    }
    $sql .= " ORDER BY {$order_by} {$order}";
    if ($limit != -1 && is_numeric($offset) && is_numeric($limit)) {
        $sql .= " LIMIT {$offset}, {$limit}";
    }
    //echo $sql;
    $results = $conn->query($sql);
    $GLOBALS['queryerrormsg'] = $conn->error;
    while ($row = $results->fetch_assoc()) {
        $data[] = $row;
    }
    return $data;
}

function getFilter($id, $columns = array(), $group_only = false) {
    global $conn;
    $id = secure($id);
    $data = null;
    $sql = "SELECT * FROM " . T_FILTER_GROUP . " WHERE id = '{$id}'";
    if (!empty($columns) && is_array($columns)) {
        $sql = "SELECT `" . implode("`,`", $columns) . "` FROM " . T_FILTER_GROUP . " WHERE id = '{$id}'";
    }
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
        if (!$group_only) {
            $result = $conn->query("SELECT * FROM " . T_FILTERS . " WHERE filter_group_id = '{$id}' AND status != 'T'");
            $row['filters'] = array();
            while ($row2 = $result->fetch_assoc()) {
                $row['filters'][] = $row2;
            }
        }
        $data = $row;
    }

    return $data;
}

function addFilter($filter) {
    global $conn;
    $name = secure($filter['name']);
    $display_order = secure($filter['display_order']);
    $status = secure($filter['status']);

    $results = $conn->query("SELECT * FROM " . T_FILTER_GROUP . " WHERE name='{$name}'");
    if ($results->num_rows > 0) {
        $GLOBALS['queryerrormsg'] = "Filter name already exist";
        return false;
    }

    if ($conn->query("INSERT INTO " . T_FILTER_GROUP . " (name, display_order, status) VALUE ('{$name}','{$display_order}','$status')")) {
        $fg_id = $conn->insert_id;
        foreach ($filter['filters'] as $f) {
            $f_name = secure($f['name']);
            $f_display_order = secure($f['display_order']);
            $f_status = isset($f['status']) ? secure($f['status']) : $status;
            $conn->query("INSERT INTO " . T_FILTERS . " (filter_group_id, name, display_order, status) VALUE ('{$fg_id}','{$f_name}','{$f_display_order}','{$f_status}')");
        }
        return true;
    }
    $GLOBALS['queryerrormsg'] = "Please try again later";
    return false;
}

function updateFilter($filter) {
    global $conn;
    $id = secure($filter['id']);
    $name = secure($filter['name']);
    $display_order = secure($filter['display_order']);
    $status = secure($filter['status']);

    if ($conn->query("UPDATE " . T_FILTER_GROUP . " SET name = '{$name}', display_order = '{$display_order}', status = '{$status}' WHERE id = '{$id}'")) {
        //if filters deleted this will make that deleted
        $conn->query("UPDATE " . T_FILTERS . " SET status = 'T' WHERE filter_group_id = '{$id}'");
        foreach ($filter['filters'] as $f) {
            $f_id = secure($f['id']);
            $f_name = secure($f['name']);
            $f_display_order = secure($f['display_order']);
            $f_status = secure($f['status']);

            if (!empty($f_id)) {
                //update existing attribute and make its status undeleted/active which was updated by previous query
                $conn->query("UPDATE " . T_FILTERS . " SET name = '{$f_name}', display_order = '{$f_display_order}',  status = '{$f_status}' WHERE id = '{$f_id}'");
            } else {
                //if id empty then new attribute added
                $conn->query("INSERT INTO " . T_FILTERS . " (filter_group_id, name, display_order, status) VALUE ('{$id}','{$f_name}','{$f_display_order}','{$f_status}')");
            }
        }
        return true;
    }
}

/* Filters Functions End */

/* Attribute Functions Start */

function getAttributes($filter = array(), $offset = 0, $limit = -1, $order = "ASC") {
    global $conn;
    $data = array();
    $query = "SELECT * FROM " . T_ATTRIBUTE_GROUP . " AS attr_grp WHERE status != 'T'";
    //$query = "SELECT attr.id, attr.attribute_group_id, attr.name, attr.type, attr.terms, attr.display_order, attr.status FROM " . T_ATTRIBUTE . " AS attr WHERE 1";
//    if(isset($filter['product_id'])) {
//        $product_id = secure($filter['product_id']);
//        $query = "SELECT attr.id, attr.attribute_group_id, attr.name, attr.type, attr.terms, attr.display_order, attr.status, pattr.value FROM " . T_ATTRIBUTE . " AS attr LEFT JOIN " . T_PRODUCT_ATTRIBUTE . " AS pattr ON attr.id = pattr.attribute_id WHERE pattr.product_id = '{$product_id}'";
//    }

    $query .= " ORDER BY attr_grp.display_order $order";
    $result = $conn->query($query);
    while ($fetched_data = $result->fetch_assoc()) {
        $data[$fetched_data['id']] = $fetched_data;
    }
    return $data;
}

//function getAttributesByCategoryID($catid) {
//    global $conn;
//    $data = array();
//    $cresult = $conn->query("SELECT attributes FROM " . T_CATEGORY . " WHERE id = '$catid'");
//    $crow = mysqli_fetch_assoc($cresult);    
//    if (trim($crow['attributes']) <> '') {
//        $result = $conn->query("SELECT * FROM " . T_ATTRIBUTE . " WHERE id IN (" . $crow['attributes'] . ")");
//        while ($fetched_data = $result->fetch_assoc()) {
//            $attribute = $fetched_data;
//            $attribute['termsR'] = explode(",", $fetched_data['terms']);
//            $data[] = $attribute;
//        }
//    }
//    return $data;
//}

function getAttribute($id) {
    global $conn;
    $id = secure($id);
    $attribute = null;
    $result = $conn->query("SELECT * FROM " . T_ATTRIBUTE_GROUP . " WHERE id = '{$id}'");
    while ($fetched_data = $result->fetch_assoc()) {
        $attribute = $fetched_data;
        $result = $conn->query("SELECT * FROM " . T_ATTRIBUTE . " WHERE attribute_group_id = '{$id}' AND status = 'A'");
        $attribute['attributes'] = array();
        while ($fetched_data = $result->fetch_assoc()) {
            $attribute['attributes'][] = $fetched_data;
        }
    }

    return $attribute;
}

//function getFilterableAttributesByCatId($catid) {
//    global $conn;
//    $fcatid = secure($catid);
//    $data = array();        
//    $cresult = $conn->query("SELECT * FROM " . T_CATEGORY . " WHERE id='{$fcatid}'");    
//    $crow = mysqli_fetch_assoc($cresult);    
//    if (!empty(trim($crow['attributes']))) {
//        $result = $conn->query("SELECT * FROM " . T_ATTRIBUTE . " WHERE filterable='1' AND id IN (" . $crow['attributes'] . ")");
//        while ($fetched_data = $result->fetch_assoc()) {
//            $attribute = $fetched_data;
//            $result2 = $conn->query("SELECT DISTINCT value FROM " . T_PRODUCT_ATTRIBUTE . " WHERE value !='' AND attribute_id='" . $fetched_data['id'] . "' AND product_id IN (SELECT id FROM " . T_PRODUCTS . " WHERE category_id='$fcatid')");
//            $terms = array();
//            while ($fetchd_data2 = mysqli_fetch_assoc($result2)) {
//                $terms[] = $fetchd_data2['value'];
//            }
//            $attribute['termsR'] = $terms;
//            $data[] = $attribute;
//        }
//    }
//    return $data;
//}

function addAttribute($attribute) {
    global $conn;
    $name = secure($attribute['name']);
    $display_order = secure($attribute['display_order']);
    $status = secure($attribute['status']);

    $result = $conn->query("SELECT * FROM " . T_ATTRIBUTE_GROUP . " WHERE name='{$name}'");
    if (mysqli_num_rows($result) > 0) {
        $GLOBALS['queryerrormsg'] = "Attribute name already exist";
        return false;
    }

    if ($conn->query("INSERT INTO " . T_ATTRIBUTE_GROUP . " (name, display_order, status) VALUE ('{$name}','{$display_order}','$status')")) {
        $ag_id = mysqli_insert_id($conn);
        foreach ($attribute['attributes'] as $a) {
            $a_name = secure($a['name']);
            $a_type = "Text";
            $a_terms = "";
            $a_display_order = secure($a['display_order']);
            $a_status = secure($a['status']);
            $conn->query("INSERT INTO " . T_ATTRIBUTE . " (attribute_group_id, name, type, terms, display_order, status) VALUE ('{$ag_id}','{$a_name}','{$a_type}','{$a_terms}','{$a_display_order}','{$a_status}')");
        }
        return true;
    }
    $GLOBALS['queryerrormsg'] = "Please try again later";
    return false;
}

function updateAttribute($attribute) {
    global $conn;
    $id = secure($attribute['id']);
    $name = secure($attribute['name']);
    $display_order = secure($attribute['display_order']);
    $status = secure($attribute['status']);

    if ($conn->query("UPDATE " . T_ATTRIBUTE_GROUP . " SET name = '{$name}', display_order = '{$display_order}', status = '{$status}' WHERE id = '{$id}'")) {
        //if attribute deleted this will make that deleted
        $conn->query("UPDATE " . T_ATTRIBUTE . " SET status = 'T' WHERE attribute_group_id = '{$id}'");
        foreach ($attribute['attributes'] as $a) {
            $a_id = secure($a['id']);
            $a_name = secure($a['name']);
            $a_type = "Text";
            $a_terms = "";
            $a_display_order = secure($a['display_order']);
            $a_status = secure($a['status']);

            if (!empty($a_id)) {
                //update existing attribute and make its status undeleted/active which was updated by previous query
                $conn->query("UPDATE " . T_ATTRIBUTE . " SET name = '{$a_name}', type = '{$a_type}', terms = '{$a_terms}', display_order = '{$a_display_order}',  status = '{$a_status}' WHERE id = '{$a_id}'");
            } else {
                //if id empty then new attribute added
                $conn->query("INSERT INTO " . T_ATTRIBUTE . " (attribute_group_id, name, type, terms, display_order, status) VALUE ('{$id}','{$a_name}','{$a_type}','{$a_terms}','{$a_display_order}','{$a_status}')");
            }
        }
        return true;
    }
}

function deleteAttribute($id) {
    global $conn;
    $fid = secure($id);
    return $conn->query("DELETE FROM " . T_ATTRIBUTE . " WHERE id='{$fid}'");
}

function deleteAttributeTerm($id, $term) {
    global $conn;
    $fid = secure($id);
    $fterm = secure($term);
    $result = $conn->query("SELECT * FROM " . T_ATTRIBUTE . " WHERE id='{$fid}'");
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_array($result);
        $terms = explode(",", trim($row['terms']));
        $uterm = "";
        foreach ($terms as $term1) {
            if (trim($fterm) != trim($term1)) {
                $uterm .= $term1 . ",";
            }
        }
        return $conn->query("UPDATE " . T_ATTRIBUTE . " SET terms = '" . trim($uterm, ",") . "' WHERE id='{$fid}'");
    } else {
        $GLOBALS['queryerrormsg'] = "Attribute does not exists";
        return false;
    }
}

/* Attribute Functions End */

/* Countries Functions Start */

function getCountries($columns = array(), $filters = array(), $offset = 0, $limit = -1, $order_by = 'name', $order = 'ASC') {
    global $conn;
    $data = array();
    $sql = "SELECT * FROM " . T_COUNTRIES . " WHERE status != 'T'";
    if (!empty($columns) && is_array($columns)) {
        $sql = "SELECT `" . implode("`,`", $columns) . "` FROM " . T_COUNTRIES . " WHERE status != 'T'";
    }
    if (isset($filters['code']) && trim($filters['code']) <> "") {
        $code = secure($filters['code']);
        $sql .= " AND code = '{$code}'";
    }
    if (isset($filters['name']) && trim($filters['name']) <> "") {
        $name = secure($filters['name']);
        $sql .= " AND name = '{$name}'";
    }
    if (isset($filters['status']) && trim($filters['status']) <> "") {
        $status = secure($filters['status']);
        $sql .= " AND status = '{$status}'";
    }
    if (isset($filters['q']) && trim($filters['q']) <> "") {
        $q = secure($filters['q']);
        $sql .= " AND (code LIKE '%{$q}%' OR name LIKE '%{$q}%')";
    }
    $sql .= " ORDER BY {$order_by} {$order}";
    if ($limit != -1 && is_numeric($offset) && is_numeric($limit)) {
        $sql .= " LIMIT {$offset}, {$limit}";
    }
    $results = $conn->query($sql);
    $GLOBALS['queryerrormsg'] = $conn->error;
    while ($row = $results->fetch_assoc()) {
        $data[] = $row;
    }
    return $data;
}

function getCountry($id_or_code, $columns = array()) {
    global $conn;
    $id_or_code = secure($id_or_code);
    $data = null;
    $sql = "SELECT * FROM " . T_COUNTRIES . " WHERE id = '{$id_or_code}' OR code = '{$id_or_code}'";
    if (!empty($columns) && is_array($columns)) {
        $sql = "SELECT `" . implode("`,`", $columns) . "` FROM " . T_COUNTRIES . " WHERE id = '{$id_or_code}' OR code = '{$id_or_code}'";
    }
    $results = $conn->query($sql);
    $GLOBALS['queryerrormsg'] = $conn->error;
    while ($row = $results->fetch_assoc()) {
        $data = $row;
    }
    return $data;
}

function addCountry($country) {
    global $conn;
    $code = secure($country['code']);
    $name = secure($country['name']);
    $status = isset($country['status']) ? secure($country['status']) : 'A';

    $results = $conn->query("SELECT * FROM " . T_COUNTRIES . " WHERE code = '{$code}' AND name = '{$name}'");
    if ($results->num_rows > 0) {
        $GLOBALS['queryerrormsg'] = "Country already exist";
        return false;
    }

    $qstatus = $conn->query("INSERT INTO " . T_COUNTRIES . " (code, name, status) VALUE ('{$code}','{$name}','{$status}')");
    $GLOBALS['queryerrormsg'] = $conn->error;
    return $qstatus;
}

function updateCountry($country) {
    global $conn;
    $id = secure($country['id']);
    $code = secure($country['code']);
    $name = secure($country['name']);
    $status = isset($country['status']) ? secure($country['status']) : 'A';

    $qstatus = $conn->query("UPDATE " . T_COUNTRIES . " SET code = '{$code}', name = '{$name}', status = '{$status}' WHERE id = '{$id}'");
    $GLOBALS['queryerrormsg'] = $conn->error;
    return $qstatus;
}

function deleteCountry($id) {
    global $conn;
    $id = secure($id);
    $qstatus = $conn->query("DELETE FROM " . T_COUNTRIES . " WHERE id = '{$id}'");
    $GLOBALS['queryerrormsg'] = $conn->error;
    return $qstatus;
}

/* Countries Functions End */

/* Zones Functions Start */

function getZones($columns = array(), $filters = array(), $offset = 0, $limit = -1, $order_by = 'name', $order = 'ASC') {
    global $conn;
    $data = array();
    $sql = "SELECT * FROM " . T_ZONES . " WHERE status != 'T'";
    if (!empty($columns) && is_array($columns)) {
        $sql = "SELECT `" . implode("`,`", $columns) . "` FROM " . T_ZONES . " WHERE status != 'T'";
    }
    if (isset($filters['name']) && trim($filters['name']) <> "") {
        $name = secure($filters['name']);
        $sql .= " AND name = '{$name}'";
    }
    if (isset($filters['status']) && trim($filters['status']) <> "") {
        $status = secure($filters['status']);
        $sql .= " AND status = '{$status}'";
    }
    if (isset($filters['q']) && trim($filters['q']) <> "") {
        $q = secure($filters['q']);
        $sql .= " AND (name LIKE '%{$q}%' OR description LIKE '%{$q}%')";
    }
    $sql .= " ORDER BY {$order_by} {$order}";
    if ($limit != -1 && is_numeric($offset) && is_numeric($limit)) {
        $sql .= " LIMIT {$offset}, {$limit}";
    }
    $results = $conn->query($sql);
    $GLOBALS['queryerrormsg'] = $conn->error;
    while ($row = $results->fetch_assoc()) {
        $data[] = $row;
    }
    return $data;
}

function getZone($id, $columns = array()) {
    global $conn;
    $id = secure($id);
    $data = null;
    $sql = "SELECT * FROM " . T_ZONES . " WHERE id = '{$id}'";
    if (!empty($columns) && is_array($columns)) {
        $sql = "SELECT `" . implode("`,`", $columns) . "` FROM " . T_ZONES . " WHERE id = '{$id}'";
    }
    $results = $conn->query($sql);
    $GLOBALS['queryerrormsg'] = $conn->error;
    while ($row = $results->fetch_assoc()) {
        $data = $row;
    }
    return $data;
}

function addZone($zone) {
    global $conn;
    $name = secure($zone['name']);
    $description = secure($zone['description']);
    $status = isset($zone['status']) ? secure($zone['status']) : 'A';

    $results = $conn->query("SELECT * FROM " . T_ZONES . " WHERE name = '{$name}' AND description = '{$description}'");
    if ($results->num_rows > 0) {
        $GLOBALS['queryerrormsg'] = "Zone already exist";
        return false;
    }

    $qstatus = $conn->query("INSERT INTO " . T_ZONES . " (name, description, status) VALUE ('{$name}','{$description}','{$status}')");
    $GLOBALS['queryerrormsg'] = $conn->error;
    return $qstatus;
}

function updateZone($zone) {
    global $conn;
    $id = secure($zone['id']);
    $name = secure($zone['name']);
    $description = secure($zone['description']);
    $status = isset($zone['status']) ? secure($zone['status']) : 'A';

    $qstatus = $conn->query("UPDATE " . T_ZONES . " SET name = '{$name}', description = '{$description}', status = '{$status}' WHERE id = '{$id}'");
    $GLOBALS['queryerrormsg'] = $conn->error;
    return $qstatus;
}

function deleteZone($id) {
    global $conn;
    $id = secure($id);
    $qstatus = $conn->query("DELETE FROM " . T_ZONES . " WHERE id = '{$id}'");
    $GLOBALS['queryerrormsg'] = $conn->error;
    return $qstatus;
}

/* Zones Functions End */

/* States Functions Start */

function getStates($columns = array(), $filters = array(), $offset = 0, $limit = -1, $order_by = 'name', $order = 'ASC') {
    global $conn;
    $data = array();
    $sql = "SELECT * FROM " . T_STATES . " WHERE status != 'T'";
    if (!empty($columns) && is_array($columns)) {
        $sql = "SELECT `" . implode("`,`", $columns) . "` FROM " . T_STATES . " WHERE status != 'T'";
    }
    if (isset($filters['country_id']) && trim($filters['country_id']) <> "") {
        $country_id = secure($filters['country_id']);
        $sql .= " AND country_id = '{$country_id}'";
    }
    if (isset($filters['zone_id']) && trim($filters['zone_id']) <> "") {
        $zone_id = secure($filters['zone_id']);
        $sql .= " AND zone_id = '{$zone_id}'";
    }
    if (isset($filters['code']) && trim($filters['code']) <> "") {
        $code = secure($filters['code']);
        $sql .= " AND code = '{$code}'";
    }
    if (isset($filters['name']) && trim($filters['name']) <> "") {
        $name = secure($filters['name']);
        $sql .= " AND name = '{$name}'";
    }
    if (isset($filters['status']) && trim($filters['status']) <> "") {
        $status = secure($filters['status']);
        $sql .= " AND status = '{$status}'";
    }
    if (isset($filters['q']) && trim($filters['q']) <> "") {
        $q = secure($filters['q']);
        $sql .= " AND (code LIKE '%{$q}%' OR name LIKE '%{$q}%')";
    }
    $sql .= " ORDER BY {$order_by} {$order}";
    if ($limit != -1 && is_numeric($offset) && is_numeric($limit)) {
        $sql .= " LIMIT {$offset}, {$limit}";
    }
    $results = $conn->query($sql);
    $GLOBALS['queryerrormsg'] = $conn->error;
    while ($row = $results->fetch_assoc()) {
        $data[] = $row;
    }
    return $data;
}

function getState($id, $columns = array()) {
    global $conn;
    $id = secure($id);
    $data = null;
    $sql = "SELECT * FROM " . T_STATES . " WHERE id = '{$id}'";
    if (!empty($columns) && is_array($columns)) {
        $sql = "SELECT `" . implode("`,`", $columns) . "` FROM " . T_STATES . " WHERE id = '{$id}'";
    }
    $results = $conn->query($sql);
    $GLOBALS['queryerrormsg'] = $conn->error;
    while ($row = $results->fetch_assoc()) {
        $data = $row;
    }
    return $data;
}

function addState($state) {
    global $conn;
    $country_id = secure($state['country_id']);
    $zone_id = secure($state['zone_id']);
    $code = secure($state['code']);
    $name = secure($state['name']);
    $status = isset($state['status']) ? secure($state['status']) : 'A';

    $results = $conn->query("SELECT * FROM " . T_STATES . " WHERE code = '{$code}' AND name = '{$name}'");
    if ($results->num_rows > 0) {
        $GLOBALS['queryerrormsg'] = "State already exist";
        return false;
    }

    $qstatus = $conn->query("INSERT INTO " . T_STATES . " (country_id, zone_id, code, name, status) VALUE ('{$country_id}','{$zone_id}','{$code}','{$name}','{$status}')");
    $GLOBALS['queryerrormsg'] = $conn->error;
    return $qstatus;
}

function updateState($state) {
    global $conn;
    $id = secure($state['id']);
    $country_id = secure($state['country_id']);
    $zone_id = secure($state['zone_id']);
    $code = secure($state['code']);
    $name = secure($state['name']);
    $status = isset($state['status']) ? secure($state['status']) : 'A';

    $qstatus = $conn->query("UPDATE " . T_STATES . " SET country_id = '$country_id', zone_id = '$zone_id',  code = '$code', name = '{$name}', status = '{$status}' WHERE id = '{$id}'");
    $GLOBALS['queryerrormsg'] = $conn->error;
    return $qstatus;
}

function deleteState($id) {
    global $conn;
    $id = secure($id);
    $qstatus = $conn->query("DELETE FROM " . T_STATES . " WHERE id = '{$id}'");
    $GLOBALS['queryerrormsg'] = $conn->error;
    return $qstatus;
}

/* States Functions End */

/* Currency Functions Start */

function getCurrencies($columns = array(), $filters = array(), $offset = 0, $limit = -1, $order_by = 'title', $order = 'ASC') {
    global $conn;
    $data = array();
    $sql = "SELECT * FROM " . T_CURRENCIES . " WHERE 1";
    if (!empty($columns) && is_array($columns)) {
        $sql = "SELECT `" . implode("`,`", $columns) . "` FROM " . T_CURRENCIES . " WHERE 1";
    }
    if (isset($filters['title']) && trim($filters['title']) <> "") {
        $title = secure($filters['title']);
        $sql .= " AND title LIKE '%{$title}%'";
    }
    if (isset($filters['code']) && trim($filters['code']) <> "") {
        $code = secure($filters['code']);
        $sql .= " AND code = '{$code}'";
    }
    if (isset($filters['countries']) && trim($filters['countries']) <> "") {
        $countries = secure($filters['countries']);
        $sql .= " AND countries LIKE '%{$countries}%'";
    }
    if (isset($filters['q']) && trim($filters['q']) <> "") {
        $q = secure($filters['q']);
        $sql .= " AND (title LIKE '%{$q}%' OR code LIKE '%{$q}%')";
    }
    $sql .= " ORDER BY {$order_by} {$order}";
    if ($limit != -1 && is_numeric($offset) && is_numeric($limit)) {
        $sql .= " LIMIT {$offset}, {$limit}";
    }
    $results = $conn->query($sql);
    $GLOBALS['queryerrormsg'] = $conn->error;
    while ($row = $results->fetch_assoc()) {
        $data[] = $row;
    }
    return $data;
}

function getCurrency($id_or_code, $columns = array()) {
    global $conn;
    $id_or_code = secure($id_or_code);
    $data = null;
    $sql = "SELECT * FROM " . T_CURRENCIES . " WHERE id = '{$id_or_code}' OR code = '{$id_or_code}'";
    if (!empty($columns) && is_array($columns)) {
        $sql = "SELECT `" . implode("`,`", $columns) . "` FROM " . T_CURRENCIES . " WHERE id = '{$id_or_code}' OR code = '{$id_or_code}'";
    }
    $results = $conn->query($sql);
    $GLOBALS['queryerrormsg'] = $conn->error;
    while ($row = $results->fetch_assoc()) {
        $data = $row;
    }
    return $data;
}

function addCurrency($currency) {
    global $conn;
    $title = secure($currency['title']);
    $code = secure($currency['code']);
    $decimal_places = secure($currency['decimal_places']);
    $decimal_separator = secure($currency['decimal_separator']);
    $thousand_separator = secure($currency['thousand_separator']);
    $symbol_left = secure($currency['symbol_left']);
    $symbol_right = secure($currency['symbol_right']);
    $countries = secure($currency['countries']);
    $rate_usd_base = secure($currency['rate_usd_base']);
    $rate_dc_base = secure($currency['rate_dc_base']);
    $rate_last_updated = isset($currency['rate_last_updated']) ? secure($currency['rate_last_updated']) : date("Y-m-d H:i:s");
    $rate_last_updated_by = isset($currency['rate_last_updated_by']) ? secure($currency['rate_last_updated_by']) : getUserLoggedId();

    $results = $conn->query("SELECT * FROM " . T_CURRENCIES . " WHERE code = '{$code}'");
    if ($results->num_rows > 0) {
        $GLOBALS['queryerrormsg'] = "Currency code already exist";
        return false;
    }

    $qstatus = $conn->query("INSERT INTO " . T_CURRENCIES . " (title, code, decimal_places, decimal_separator, thousand_separator, symbol_left, symbol_right, countries, rate_usd_base, rate_dc_base, rate_last_updated, rate_last_updated_by) VALUE ('{$title}','{$code}','{$decimal_places}','{$decimal_separator}','{$thousand_separator}', '{$symbol_left}', '{$symbol_right}', '{$countries}', '{$rate_usd_base}', '{$rate_dc_base}', '{$rate_last_updated}', '{$rate_last_updated_by}')");
    $GLOBALS['queryerrormsg'] = $conn->error;
    return $qstatus;
}

function updateCurrency($currency) {
    global $conn;
    $id = secure($currency['id']);
    $title = secure($currency['title']);
    $code = secure($currency['code']);
    $decimal_places = secure($currency['decimal_places']);
    $decimal_separator = secure($currency['decimal_separator']);
    $thousand_separator = secure($currency['thousand_separator']);
    $symbol_left = secure($currency['symbol_left']);
    $symbol_right = secure($currency['symbol_right']);
    $countries = secure($currency['countries']);
    $rate_usd_base = secure($currency['rate_usd_base']);
    $rate_dc_base = secure($currency['rate_dc_base']);
    $rate_last_updated = isset($currency['rate_last_updated']) ? secure($currency['rate_last_updated']) : date("Y-m-d H:i:s");
    $rate_last_updated_by = isset($currency['rate_last_updated_by']) ? secure($currency['rate_last_updated_by']) : getUserLoggedId();

    $results = $conn->query("SELECT * FROM " . T_CURRENCIES . " WHERE code = '{$code}' AND id != '{$id}'");
    if ($results->num_rows > 0) {
        $GLOBALS['queryerrormsg'] = "Currency code already exist";
        return false;
    }
    $qstatus = $conn->query("UPDATE " . T_CURRENCIES . " SET title = '{$title}', code = '{$code}',  decimal_places = '{$decimal_places}', decimal_separator = '{$decimal_separator}', thousand_separator = '{$thousand_separator}', symbol_left = '{$symbol_left}', symbol_right = '{$symbol_right}', countries = '{$countries}', rate_usd_base = '{$rate_usd_base}', rate_dc_base = '{$rate_dc_base}', rate_last_updated = '{$rate_last_updated}', rate_last_updated_by = '{$rate_last_updated_by}' WHERE id = '{$id}'");
    $GLOBALS['queryerrormsg'] = $conn->error;
    return $qstatus;
}

function deleteCurrency($id) {
    global $conn;
    $id = secure($id);
    $qstatus = $conn->query("DELETE FROM " . T_CURRENCIES . " WHERE id = '{$id}'");
    $GLOBALS['queryerrormsg'] = $conn->error;
    return $qstatus;
}

/* Currency Functions End */

/* Reasons Functions Start */

function getReasons($columns = array(), $filters = array(), $offset = 0, $limit = -1, $order_by = 'id', $order = 'DESC') {
    global $conn;
    $data = array();
    $sql = "SELECT * FROM " . T_REASONS . " WHERE 1";
    if (!empty($columns) && is_array($columns)) {
        $sql = "SELECT `" . implode("`,`", $columns) . "` FROM " . T_REASONS . " WHERE 1";
    }
    if (isset($filters['reason_type']) && trim($filters['reason_type']) <> "") {
        $reason_type = secure($filters['reason_type']);
        $sql .= " AND reason_type = '{$reason_type}'";
    }
    if (isset($filters['title']) && trim($filters['title']) <> "") {
        $title = secure($filters['title']);
        $sql .= " AND title LIKE '%{$title}%'";
    }
    if (isset($filters['description']) && trim($filters['description']) <> "") {
        $description = secure($filters['description']);
        $sql .= " AND description LIKE '%{$description}%'";
    }
    if (isset($filters['q']) && trim($filters['q']) <> "") {
        $q = secure($filters['q']);
        $sql .= " AND (title LIKE '%{$q}%' OR description LIKE '%{$q}%')";
    }
    $sql .= " ORDER BY {$order_by} {$order}";
    if ($limit != -1 && is_numeric($offset) && is_numeric($limit)) {
        $sql .= " LIMIT {$offset}, {$limit}";
    }
    $results = $conn->query($sql);
    $GLOBALS['queryerrormsg'] = $conn->error;
    while ($row = $results->fetch_assoc()) {
        $data[$row['id']] = $row;
    }
    return $data;
}

function getReason($id, $columns = array()) {
    global $conn;
    $id = secure($id);
    $data = null;
    $sql = "SELECT * FROM " . T_REASONS . " WHERE id = '{$id}'";
    if (!empty($columns) && is_array($columns)) {
        $sql = "SELECT `" . implode("`,`", $columns) . "` FROM " . T_REASONS . " WHERE id = '{$id}'";
    }
    $results = $conn->query($sql);
    $GLOBALS['queryerrormsg'] = $conn->error;
    while ($row = $results->fetch_assoc()) {
        $data = $row;
    }
    return $data;
}

function addReason($reason) {
    global $conn;
    $reason_type = secure($reason['reason_type']);
    $title = secure($reason['title']);
    $description = secure($reason['description']);

    $results = $conn->query("SELECT * FROM " . T_REASONS . " WHERE reason_type = '{$reason_type}' AND title = '{$title}'");
    if ($results->num_rows > 0) {
        $GLOBALS['queryerrormsg'] = "Reason already exist";
        return false;
    }

    $qstatus = $conn->query("INSERT INTO " . T_REASONS . " (reason_type, title, description) VALUE ('{$reason_type}','{$title}','{$description}')");
    $GLOBALS['queryerrormsg'] = $conn->error;
    return $qstatus;
}

function updateReason($reason) {
    global $conn;
    $id = secure($reason['id']);
    $reason_type = secure($reason['reason_type']);
    $title = secure($reason['title']);
    $description = secure($reason['description']);

    $qstatus = $conn->query("UPDATE " . T_REASONS . " SET reason_type = '{$reason_type}', title = '{$title}', description = '{$description}' WHERE id = '{$id}'");
    $GLOBALS['queryerrormsg'] = $conn->error;
    return $qstatus;
}

function deleteReason($id) {
    global $conn;
    $id = secure($id);
    $qstatus = $conn->query("DELETE FROM " . T_REASONS . " WHERE id = '{$id}'");
    $GLOBALS['queryerrormsg'] = $conn->error;
    return $qstatus;
}

/* Reasons Functions End */

/* Shipping Companies Functions Start */

function getShippingCompanies($columns = array(), $filters = array(), $offset = 0, $limit = -1, $order_by = 'name', $order = 'DESC') {
    global $conn;
    $data = array();
    $sql = "SELECT * FROM " . T_SHIPPING_COMPANIES . " WHERE 1";
    if (!empty($columns) && is_array($columns)) {
        $sql = "SELECT `" . implode("`,`", $columns) . "` FROM " . T_SHIPPING_COMPANIES . " WHERE 1";
    }
    if (isset($filters['name']) && trim($filters['name']) <> "") {
        $name = secure($filters['name']);
        $sql .= " AND name LIKE '%{$name}%'";
    }
    if (isset($filters['website']) && trim($filters['website']) <> "") {
        $website = secure($filters['website']);
        $sql .= " AND website LIKE '%{$website}%'";
    }
    if (isset($filters['status']) && trim($filters['status']) <> "") {
        $status = secure($filters['status']);
        $sql .= " AND status = '{$status}'";
    }
    if (isset($filters['q']) && trim($filters['q']) <> "") {
        $q = secure($filters['q']);
        $sql .= " AND (name LIKE '%{$q}%' OR website LIKE '%{$q}%' OR comments LIKE '%{$q}%')";
    }
    $sql .= " ORDER BY {$order_by} {$order}";
    if ($limit != -1 && is_numeric($offset) && is_numeric($limit)) {
        $sql .= " LIMIT {$offset}, {$limit}";
    }
    $results = $conn->query($sql);
    $GLOBALS['queryerrormsg'] = $conn->error;
    while ($row = $results->fetch_assoc()) {
        $data[] = $row;
    }
    return $data;
}

function getShippingCompany($id, $columns = array()) {
    global $conn;
    $id = secure($id);
    $data = null;
    $sql = "SELECT * FROM " . T_SHIPPING_COMPANIES . " WHERE id = '{$id}'";
    if (!empty($columns) && is_array($columns)) {
        $sql = "SELECT `" . implode("`,`", $columns) . "` FROM " . T_SHIPPING_COMPANIES . " WHERE id = '{$id}'";
    }
    $results = $conn->query($sql);
    $GLOBALS['queryerrormsg'] = $conn->error;
    while ($row = $results->fetch_assoc()) {
        $data = $row;
    }
    return $data;
}

function addShippingCompany($shippingCompany) {
    global $conn;
    $name = secure($shippingCompany['name']);
    $website = secure($shippingCompany['website']);
    $comments = secure($shippingCompany['comments']);
    $status = secure($shippingCompany['status']);

    $results = $conn->query("SELECT * FROM " . T_SHIPPING_COMPANIES . " WHERE name='{$name}'");
    if ($results->num_rows > 0) {
        $GLOBALS['queryerrormsg'] = "Shipping Company already exist";
        return false;
    }

    $qstatus = $conn->query("INSERT INTO " . T_SHIPPING_COMPANIES . " (name, website, comments, status) VALUE ('{$name}','{$website}','{$comments}','{$status}')");
    $GLOBALS['queryerrormsg'] = $conn->error;
    return $qstatus;
}

function updateShippingCompany($shippingCompany) {
    global $conn;
    $id = secure($shippingCompany['id']);
    $name = secure($shippingCompany['name']);
    $website = secure($shippingCompany['website']);
    $comments = secure($shippingCompany['comments']);
    $status = secure($shippingCompany['status']);

    $qstatus = $conn->query("UPDATE " . T_SHIPPING_COMPANIES . " SET name = '{$name}', website = '{$website}', comments = '{$comments}', status = '{$status}' WHERE id = '{$id}'");
    $GLOBALS['queryerrormsg'] = $conn->error;
    return $qstatus;
}

/* Shipping Companies Functions End */

/* Shipping Duration Functions Start */

function getShippingDurations($columns = array(), $filters = array(), $offset = 0, $limit = -1, $order_by = 'label', $order = 'DESC') {
    global $conn;
    $data = array();
    $sql = "SELECT * FROM " . T_SHIPPING_DURATIONS . " WHERE 1";
    if (!empty($columns) && is_array($columns)) {
        $sql = "SELECT `" . implode("`,`", $columns) . "` FROM " . T_SHIPPING_DURATIONS . " WHERE 1";
    }
    if (isset($filters['label']) && trim($filters['label']) <> "") {
        $label = secure($filters['label']);
        $sql .= " AND label LIKE '%{$label}%'";
    }
    if (isset($filters['q']) && trim($filters['q']) <> "") {
        $q = secure($filters['q']);
        $sql .= " AND (label LIKE '%{$q}%' OR duration_from LIKE '%{$q}%' OR duration_to LIKE '%{$q}%')";
    }
    $sql .= " ORDER BY {$order_by} {$order}";
    if ($limit != -1 && is_numeric($offset) && is_numeric($limit)) {
        $sql .= " LIMIT {$offset}, {$limit}";
    }
    $results = $conn->query($sql);
    $GLOBALS['queryerrormsg'] = $conn->error;
    while ($row = $results->fetch_assoc()) {
        $data[$row['id']] = $row;
    }
    return $data;
}

function getShippingDuration($id, $columns = array()) {
    global $conn;
    $id = secure($id);
    $data = null;
    $sql = "SELECT * FROM " . T_SHIPPING_DURATIONS . " WHERE id = '{$id}'";
    if (!empty($columns) && is_array($columns)) {
        $sql = "SELECT `" . implode("`,`", $columns) . "` FROM " . T_SHIPPING_DURATIONS . " WHERE id = '{$id}'";
    }
    $results = $conn->query($sql);
    $GLOBALS['queryerrormsg'] = $conn->error;
    while ($row = $results->fetch_assoc()) {
        $data = $row;
    }
    return $data;
}

function addShippingDuration($shippingDuration) {
    global $conn;
    $label = secure($shippingDuration['label']);
    $duration_from = secure($shippingDuration['duration_from']);
    $duration_to = secure($shippingDuration['duration_to']);
    $days_or_week = secure($shippingDuration['days_or_week']);

    $results = $conn->query("SELECT * FROM " . T_SHIPPING_DURATIONS . " WHERE label='{$label}'");
    if ($results->num_rows > 0) {
        $GLOBALS['queryerrormsg'] = "Shipping Duration already exist";
        return false;
    }

    $qstatus = $conn->query("INSERT INTO " . T_SHIPPING_DURATIONS . " (label, duration_from, duration_to, days_or_week) VALUE ('{$label}', '{$duration_from}', '{$duration_to}', '{$days_or_week}')");
    $GLOBALS['queryerrormsg'] = $conn->error;
    return $qstatus;
}

function updateShippingDuration($shippingDuration) {
    global $conn;
    $id = secure($shippingDuration['id']);
    $label = secure($shippingDuration['label']);
    $duration_from = secure($shippingDuration['duration_from']);
    $duration_to = secure($shippingDuration['duration_to']);
    $days_or_week = secure($shippingDuration['days_or_week']);

    $qstatus = $conn->query("UPDATE " . T_SHIPPING_DURATIONS . " SET label = '{$label}', duration_from = '{$duration_from}', duration_to = '{$duration_to}', days_or_week = '{$days_or_week}' WHERE id = '{$id}'");
    $GLOBALS['queryerrormsg'] = $conn->error;
    return $qstatus;
}

function deleteShippingDuration($id) {
    global $conn;
    $id = secure($id);

    $qstatus = $conn->query("DELETE FROM " . T_SHIPPING_DURATIONS . " WHERE id = '{$id}'");
    $GLOBALS['queryerrormsg'] = $conn->error;
    return $qstatus;
}

/* Shipping Duration Functions End */

/* Commission Functions Start */

function getCommissions($columns = array(), $filters = array(), $offset = 0, $limit = -1, $order_by = 'id', $order = 'ASC') {
    global $conn;
    $data = array();
    $sql = "SELECT cs.*, p.name AS product_name, s.name AS shop_name, c.name AS category_name FROM " . T_COMMISSION_SETTINGS . " cs"
            . " LEFT JOIN " . T_PRODUCTS . " p ON cs.product_id = p.id"
            . " LEFT JOIN " . T_SHOPS . " s ON cs.shop_id = s.id"
            . " LEFT JOIN " . T_CATEGORY . " c ON cs.category_id = c.id WHERE 1";
    $sub_sql = "";
    if (!empty($columns) && is_array($columns) && in_array("product_name", $columns)) {
        $sub_sql .= ",p.name AS product_name";
        if (($key = array_search("product_name", $columns)) !== false) {
            unset($columns[$key]);
        }
    }
    if (!empty($columns) && is_array($columns) && in_array("shop_name", $columns)) {
        $sub_sql .= ",s.name AS shop_name";
        if (($key = array_search("shop_name", $columns)) !== false) {
            unset($columns[$key]);
        }
    }
    if (!empty($columns) && is_array($columns) && in_array("category_name", $columns)) {
        $sub_sql .= ",c.name AS category_name";
        if (($key = array_search("category_name", $columns)) !== false) {
            unset($columns[$key]);
        }
    }
    if (!empty($columns) && is_array($columns)) {
        $sql = "SELECT cs." . implode(",cs.", $columns) . "$sub_sql FROM " . T_COMMISSION_SETTINGS . " cs "
                . "LEFT JOIN " . T_PRODUCTS . " p ON cs.product_id = p.id "
                . "LEFT JOIN " . T_SHOPS . " s ON cs.shop_id = s.id "
                . "LEFT JOIN " . T_CATEGORY . " c ON cs.category_id = c.id WHERE 1";
    }

    if (isset($filters['product_id']) && trim($filters['product_id']) <> "") {
        $product_id = secure($filters['product_id']);
        $sql .= " AND cs.product_id = '{$product_id}'";
    }
    if (isset($filters['shop_id']) && trim($filters['shop_id']) <> "") {
        $shop_id = secure($filters['shop_id']);
        $sql .= " AND cs.shop_id = '{$shop_id}'";
    }
    if (isset($filters['category_id']) && trim($filters['category_id']) <> "") {
        $category_id = secure($filters['category_id']);
        $sql .= " AND cs.category_id = '{$category_id}'";
    }
    if (isset($filters['fees']) && trim($filters['fees']) <> "") {
        $fees = secure($filters['fees']);
        $sql .= " AND cs.fees = '{$fees}'";
    }
    if (isset($filters['is_mandatory']) && trim($filters['is_mandatory']) <> "") {
        $is_mandatory = secure($filters['is_mandatory']);
        $sql .= " AND cs.is_mandatory = '{$is_mandatory}'";
    }
    if (isset($filters['status']) && trim($filters['status']) <> "") {
        $status = secure($filters['status']);
        $sql .= " AND cs.status = '{$status}'";
    }
    $sql .= " ORDER BY {$order_by} {$order}";
    if ($limit != -1 && is_numeric($offset) && is_numeric($limit)) {
        $sql .= " LIMIT {$offset}, {$limit}";
    }
    $results = $conn->query($sql);
    $GLOBALS['queryerrormsg'] = $conn->error;
    while ($row = $results->fetch_assoc()) {
        $data[] = $row;
    }
    return $data;
}

function getCommission($id, $columns = array()) {
    global $conn;
    $id = secure($id);
    $data = null;
    $sql = "SELECT cs.*, p.name AS product_name, s.name AS shop_name, c.name AS category_name FROM " . T_COMMISSION_SETTINGS . " cs"
            . " LEFT JOIN " . T_PRODUCTS . " p ON cs.product_id = p.id"
            . " LEFT JOIN " . T_SHOPS . " s ON cs.shop_id = s.id"
            . " LEFT JOIN " . T_CATEGORY . " c ON cs.category_id = c.id WHERE id = '{$id}'";
    $sub_sql = "";
    if (!empty($columns) && is_array($columns) && in_array("product_name", $columns)) {
        $sub_sql .= ",p.name AS product_name";
        if (($key = array_search("product_name", $columns)) !== false) {
            unset($columns[$key]);
        }
    }
    if (!empty($columns) && is_array($columns) && in_array("shop_name", $columns)) {
        $sub_sql .= ",s.name AS shop_name";
        if (($key = array_search("shop_name", $columns)) !== false) {
            unset($columns[$key]);
        }
    }
    if (!empty($columns) && is_array($columns) && in_array("category_name", $columns)) {
        $sub_sql .= ",c.name AS category_name";
        if (($key = array_search("category_name", $columns)) !== false) {
            unset($columns[$key]);
        }
    }
    if (!empty($columns) && is_array($columns)) {
        $sql = "SELECT cs." . implode(",cs.", $columns) . "$sub_sql FROM " . T_COMMISSION_SETTINGS . " cs "
                . "LEFT JOIN " . T_PRODUCTS . " p ON cs.product_id = p.id "
                . "LEFT JOIN " . T_SHOPS . " s ON cs.shop_id = s.id "
                . "LEFT JOIN " . T_CATEGORY . " c ON cs.category_id = c.id WHERE 1";
    }
    $results = $conn->query($sql);
    $GLOBALS['queryerrormsg'] = $conn->error;
    while ($row = $results->fetch_assoc()) {
        $data = $row;
    }
    return $data;
}

function addCommission($commission) {
    global $conn;
    $product_id = secure($commission['product_id']);
    $shop_id = secure($commission['shop_id']);
    $category_id = secure($commission['category_id']);
    $fees = secure($commission['fees']);
    $is_mandatory = isset($commission['is_mandatory']) ? secure($commission['is_mandatory']) : "0";
    $status = isset($commission['status']) ? secure($commission['status']) : "A";

    $results = $conn->query("SELECT * FROM " . T_COMMISSION_SETTINGS . " WHERE product_id = '{$product_id}' AND shop_id = '{$shop_id}' AND category_id = '{$category_id}'");
    if ($results->num_rows > 0) {
        $GLOBALS['queryerrormsg'] = "Commission settings already exist";
        return false;
    }

    $qstatus = $conn->query("INSERT INTO " . T_COMMISSION_SETTINGS . " (product_id, shop_id, category_id, fees, is_mandatory, status) VALUE ('{$product_id}','{$shop_id}','{$category_id}','{$fees}','{$is_mandatory}','{$status}')");
    $GLOBALS['queryerrormsg'] = $conn->error;
    return $qstatus;
}

function updateCommission($commission) {
    global $conn;
    $id = secure($commission['id']);
    $product_id = secure($commission['product_id']);
    $shop_id = secure($commission['shop_id']);
    $category_id = secure($commission['category_id']);
    $fees = secure($commission['fees']);
    $is_mandatory = isset($commission['is_mandatory']) ? secure($commission['is_mandatory']) : "0";
    $status = isset($commission['status']) ? secure($commission['status']) : "A";

    $qstatus = $conn->query("UPDATE " . T_COMMISSION_SETTINGS . " SET product_id = '{$product_id}', shop_id = '{$shop_id}', category_id = '{$category_id}', fees = '{$fees}', is_mandatory = '{$is_mandatory}', status = '{$status}' WHERE id = '{$id}'");
    $GLOBALS['queryerrormsg'] = $conn->error;
    return $qstatus;
}

function deleteCommission($id) {
    global $conn;
    $id = secure($id);
    $qstatus = $conn->query("UPDATE " . T_COMMISSION_SETTINGS . " SET status = 'T' WHERE id = '{$id}'");
    $GLOBALS['queryerrormsg'] = $conn->error;
    return $qstatus;
}

/* Commission Functions End */

/* Payment Method Functions Start */

function getPaymentMethods($columns = array(), $filters = array(), $offset = 0, $limit = -1, $order_by = 'display_order', $order = 'ASC') {
    global $conn;
    $data = array();
    $sql = "SELECT * FROM " . T_PAYMENT_METHODS . " WHERE 1";
    if (!empty($columns) && is_array($columns)) {
        $sql = "SELECT `" . implode("`,`", $columns) . "` FROM " . T_PAYMENT_METHODS . " WHERE 1";
    }
    if (isset($filters['name']) && trim($filters['name']) <> "") {
        $name = secure($filters['name']);
        $sql .= " AND name = '{$name}'";
    }
    if (isset($filters['code']) && trim($filters['code']) <> "") {
        $code = secure($filters['code']);
        $sql .= " AND code = '{$code}'";
    }
    if (isset($filters['status']) && trim($filters['status']) <> "") {
        $status = secure($filters['status']);
        $sql .= " AND status = '{$status}'";
    }
    if (isset($filters['q']) && trim($filters['q']) <> "") {
        $q = secure($filters['q']);
        $sql .= " AND (name LIKE '%{$q}%' OR details LIKE '%{$q}%' OR code LIKE '%{$q}%')";
    }
    $sql .= " ORDER BY {$order_by} {$order}";
    if ($limit != -1 && is_numeric($offset) && is_numeric($limit)) {
        $sql .= " LIMIT {$offset}, {$limit}";
    }
    $results = $conn->query($sql);
    $GLOBALS['queryerrormsg'] = $conn->error;
    while ($row = $results->fetch_assoc()) {
        $data[] = $row;
    }
    return $data;
}

function getPaymentMethod($id_or_code, $columns = array()) {
    global $conn;
    $id_or_code = secure($id_or_code);
    $data = null;
    $sql = "SELECT * FROM " . T_PAYMENT_METHODS . " WHERE id = '{$id_or_code}' OR code = '{$id_or_code}'";
    if (!empty($columns) && is_array($columns)) {
        $sql = "SELECT `" . implode("`,`", $columns) . "` FROM " . T_PAYMENT_METHODS . " WHERE id = '{$id_or_code}' OR code = '{$id_or_code}'";
    }
    $results = $conn->query($sql);
    $GLOBALS['queryerrormsg'] = $conn->error;
    while ($row = $results->fetch_assoc()) {
        $data = $row;
    }
    return $data;
}

function addPaymentMethod($method) {
    global $conn;
    $name = secure($method['name']);
    $details = secure($method['details']);
    $icon = secure($method['icon']);
    $code = secure($method['code']);
    $fields = mysqli_real_escape_string($conn, $method['fields']);
    $display_order = secure($method['display_order']);
    $status = isset($method['status']) ? $method['status'] : 'A';

    $results = $conn->query("SELECT * FROM " . T_PAYMENT_METHODS . " WHERE code = '{$code}' OR name = '{$name}'");
    if ($results->num_rows > 0) {
        $GLOBALS['queryerrormsg'] = "Payment Methods already exist";
        return false;
    }

    $qstatus = $conn->query("INSERT INTO " . T_PAYMENT_METHODS . " (name, details, icon, code, fields, display_order, status) VALUE ('{$name}','{$details}','{$icon}','{$code}','{$fields}','{$display_order}','{$status}')");
    $GLOBALS['queryerrormsg'] = $conn->error;
    return $qstatus;
}

function updatePaymentMethod($method) {
    global $conn;
    $id = secure($method['id']);
    $name = secure($method['name']);
    $details = secure($method['details']);
    $icon = secure($method['icon']);
    $code = secure($method['code']);
    $fields = mysqli_real_escape_string($conn, $method['fields']);
    $display_order = secure($method['display_order']);
    $status = isset($method['status']) ? $method['status'] : 'A';

    $results = $conn->query("SELECT * FROM " . T_PAYMENT_METHODS . " WHERE code = '{$code}' AND id != '{$id}'");
    if ($results->num_rows > 0) {
        $GLOBALS['queryerrormsg'] = "Payment Method already exist";
        return false;
    }
    $qstatus = $conn->query("UPDATE " . T_PAYMENT_METHODS . " SET name = '{$name}', details = '{$details}', icon = '{$icon}', code = '{$code}', fields = '{$fields}', display_order = '{$display_order}', status = '{$status}' WHERE id = '{$id}'");
    $GLOBALS['queryerrormsg'] = $conn->error;
    return $qstatus;
}

function deletePaymentMethod($id) {
    global $conn;
    $id = secure($id);
    $qstatus = $conn->query("UPDATE " . T_PAYMENT_METHODS . " SET status = 'I' WHERE id = '{$id}'");
    $GLOBALS['queryerrormsg'] = $conn->error;
    return $qstatus;
}

/* Payment Method Functions End */

/* Email Template Functions Start */

function getEmailTemplates($columns = array(), $filters = array(), $offset = 0, $limit = -1, $order_by = 'id', $order = 'DESC') {
    global $conn;
    $data = array();
    $sql = "SELECT * FROM " . T_EMAIL_TEMPLATES . " WHERE 1";
    if (!empty($columns) && is_array($columns)) {
        $sql = "SELECT `" . implode("`,`", $columns) . "` FROM " . T_EMAIL_TEMPLATES . " WHERE 1";
    }
    if (isset($filters['code']) && trim($filters['code']) <> "") {
        $code = secure($filters['code']);
        $sql .= " AND code = '{$code}'";
    }
    if (isset($filters['name']) && trim($filters['name']) <> "") {
        $name = secure($filters['name']);
        $sql .= " AND name = '{$name}'";
    }
    if (isset($filters['subject']) && trim($filters['subject']) <> "") {
        $subject = secure($filters['subject']);
        $sql .= " AND subject LIKE '%{$subject}%'";
    }
    if (isset($filters['q']) && trim($filters['q']) <> "") {
        $q = secure($filters['q']);
        $sql .= " AND (code LIKE '%{$q}%' OR name LIKE '%{$q}%' OR subject LIKE '%{$q}%' OR body LIKE '%{$q}%' OR replacements LIKE '%{$q}%')";
    }
    $sql .= " ORDER BY {$order_by} {$order}";
    if ($limit != -1 && is_numeric($offset) && is_numeric($limit)) {
        $sql .= " LIMIT {$offset}, {$limit}";
    }
    $results = $conn->query($sql);
    $GLOBALS['queryerrormsg'] = $conn->error;
    while ($row = $results->fetch_assoc()) {
        $data[] = $row;
    }
    return $data;
}

function getEmailTemplate($id_or_code, $columns = array()) {
    global $conn;
    $id_or_code = secure($id_or_code);
    $data = null;
    $sql = "SELECT * FROM " . T_EMAIL_TEMPLATES . " WHERE id = '{$id_or_code}' OR code = '{$id_or_code}'";
    if (!empty($columns) && is_array($columns)) {
        $sql = "SELECT `" . implode("`,`", $columns) . "` FROM " . T_EMAIL_TEMPLATES . " WHERE id = '{$id_or_code}' OR code = '{$id_or_code}'";
    }
    $results = $conn->query($sql);
    $GLOBALS['queryerrormsg'] = $conn->error;
    while ($row = $results->fetch_assoc()) {
        $data = $row;
    }
    return $data;
}

function addEmailTemplate($template) {
    global $conn;
    $code = secure($template['code']);
    $name = secure($template['name']);
    $subject = secure($template['subject']);
    $body = mysqli_real_escape_string($conn, $template['body']);
    $replacements = mysqli_real_escape_string($conn, $template['replacements']);
    $status = isset($template['status']) ? $template['status'] : 'A';

    $results = $conn->query("SELECT * FROM " . T_EMAIL_TEMPLATES . " WHERE code = '{$code}' OR name = '{$name}'");
    if ($results->num_rows > 0) {
        $GLOBALS['queryerrormsg'] = "Email Template already exist";
        return false;
    }

    $qstatus = $conn->query("INSERT INTO " . T_EMAIL_TEMPLATES . " (code, name, subject, body, replacements, status) VALUE ('{$code}','{$name}','{$subject}','{$body}','{$replacements}','{$status}')");
    $GLOBALS['queryerrormsg'] = $conn->error;
    return $qstatus;
}

function updateEmailTemplate($template) {
    global $conn;
    $id = secure($template['id']);
    $code = secure($template['code']);
    $name = secure($template['name']);
    $subject = secure($template['subject']);
    $body = mysqli_real_escape_string($conn, $template['body']);
    $replacements = mysqli_real_escape_string($conn, $template['replacements']);
    $status = isset($template['status']) ? $template['status'] : 'A';

    $results = $conn->query("SELECT * FROM " . T_EMAIL_TEMPLATES . " WHERE code = '{$code}' AND id != '{$id}'");
    if ($results->num_rows > 0) {
        $GLOBALS['queryerrormsg'] = "Email Template already exist";
        return false;
    }
    $qstatus = $conn->query("UPDATE " . T_EMAIL_TEMPLATES . " SET code = '{$code}', name = '{$name}', subject = '{$subject}', body = '{$body}', replacements = '{$replacements}', status = '{$status}' WHERE id = '{$id}'");
    $GLOBALS['queryerrormsg'] = $conn->error;
    return $qstatus;
}

function deleteEmailTemplate($id) {
    global $conn;
    $id = secure($id);
    $qstatus = $conn->query("UPDATE " . T_EMAIL_TEMPLATES . " SET status = 'I' WHERE id = '{$id}'");
    $GLOBALS['queryerrormsg'] = $conn->error;
    return $qstatus;
}

/* Email Template Functions End */

/* Product Review Functions Start */

function getProductReviews($columns = array(), $filters = array(), $offset = 0, $limit = 12, $order_by = 'id', $order = 'DESC') {
    global $conn;
    $data = array();
    $sql = "SELECT * FROM " . T_PRODUCT_REVIEW . " WHERE status != 'T'";
    if (!empty($columns) && is_array($columns)) {
        $sql = "SELECT `" . implode("`,`", $columns) . "` FROM " . T_PRODUCT_REVIEW . " WHERE status != 'T'";
    }
    if (isset($filters['product_id']) && trim($filters['product_id']) <> "") {
        $product_id = secure($filters['product_id']);
        $sql .= " AND product_id = '{$product_id}'";
    }
    if (isset($filters['shop_id']) && trim($filters['shop_id']) <> "") {
        $shop_id = secure($filters['shop_id']);
        $sql .= " AND shop_id = '{$shop_id}'";
    }
    if (isset($filters['user_id']) && trim($filters['user_id']) <> "") {
        $user_id = secure($filters['user_id']);
        $sql .= " AND user_id = '{$user_id}'";
    }
    if (isset($filters['name']) && trim($filters['name']) <> "") {
        $name = secure($filters['name']);
        $sql .= " AND name = '{$name}'";
    }
    if (isset($filters['review']) && trim($filters['review']) <> "") {
        $review = secure($filters['review']);
        $sql .= " AND review LIKE '%{$review}%'";
    }
    if (isset($filters['rating']) && trim($filters['rating']) <> "") {
        $rating = secure($filters['rating']);
        $sql .= " AND rating = '{$rating}'";
    }
    if (isset($filters['ip_address']) && trim($filters['ip_address']) <> "") {
        $ip_address = secure($filters['ip_address']);
        $sql .= " AND ip_address = '{$ip_address}'";
    }
    if (isset($filters['query']) && trim($filters['query']) <> "") {
        $q = secure($filters['query']);
        $sql .= " AND (name LIKE '%{$q}%' OR review LIKE '%{$q}%')";
    }
    $sql .= " ORDER BY {$order_by} {$order}";
    if ($limit != -1 && is_numeric($offset) && is_numeric($limit)) {
        $sql .= " LIMIT {$offset}, {$limit}";
    }
    $results = $conn->query($sql);
    while ($row = $results->fetch_assoc()) {
        $data[] = $row;
    }
    return $data;
}

function getProductReview($id, $columns = array()) {
    global $conn;
    $data = null;
    $id = secure($id);

    $sql = "SELECT * FROM " . T_PRODUCT_REVIEW . " WHERE id = '{$id}'";
    if (!empty($columns) && is_array($columns)) {
        $sql = "SELECT `" . implode("`,`", $columns) . "` FROM " . T_PRODUCT_REVIEW . " WHERE id = '{$id}'";
    }
    $results = $conn->query($sql);
    while ($row = $results->fetch_assoc()) {
        $data = $row;
    }
    return $data;
}

function addProductReview($review) {
    global $conn;
    $product_id = secure($review['product_id']);
    $shop_id = secure($review['shop_id']);
    $name = secure($review['name']);
    $rev = secure($review['review']);
    $rating = secure($review['rating']);
    $review_timestamp = date("Y-m-d H:i:s");
    $ip_address = $review['ip_address'];
    $results = $conn->query("SELECT * FROM " . T_PRODUCT_REVIEW . " WHERE product_id = '{$product_id}' AND shop_id = '{$shop_id}' AND ip_address = '{$ip_address}'");
    if ($results->num_rows > 0) {
        $GLOBALS['queryerrormsg'] = "Review already given";
        return false;
    }
    if (!$conn->query("INSERT INTO " . T_PRODUCT_REVIEW . " (product_id, shop_id, name, review, rating, review_timestamp, ip_address, status) VALUES('{$product_id}', '{$shop_id}', '{$name}', '{$rev}', '{$rating}','$review_timestamp','$ip_address','P')")) {
        $GLOBALS['queryerrormsg'] = "Please try again later";
        return false;
    }
    return true;
}

function updateProductReview($review) {
    global $conn;
    $id = secure($review['id']);
    $product_id = secure($review['product_id']);
    $shop_id = secure($review['shop_id']);
    $name = secure($review['name']);
    $rev = secure($review['review']);
    $rating = secure($review['rating']);
    $review_timestamp = date("Y-m-d H:i:s");
    $ip_address = $review['ip_address'];
    $status = isset($review['status']) ? secure($review['status']) : 'P';

    if ($conn->query("UPDATE " . T_PRODUCT_REVIEW . " SET product_id = '{$product_id}', shop_id = '{$shop_id}', name = '{$name}', review = '{$rev}', rating = '{$rating}', ip_address = '{$ip_address}', review_timestamp = '{$review_timestamp}', status='{$status}' WHERE id = '{$id}'")) {
        return true;
    } else {
        $GLOBALS['queryerrormsg'] = "Try again later";
        return false;
    }
}

function deleteProductReview($id) {
    global $conn;
    $id = secure($id);
    if ($conn->query("UPDATE " . T_PRODUCT_REVIEW . " SET status = 'T' WHERE id='{$id}'")) {
        return true;
    }
    return false;
}

/* Product Review Functions End */

/* Product Functions Start */

function getProducts($columns = array(), $filters = array(), $offset = 0, $limit = 12, $order_by = 'display_order', $order = 'DESC') {
    global $conn;
    $data = array();
    $today = date("Y-m-d");
    //add subquery if ordery by price or price is in columns
    $sub_sub_sql = '';
    if (isset($filters['shop']) && trim($filters['shop']) <> "") {
        $shop_id = secure($filters['shop']);
        $sub_sub_sql .= "AND shop_id = '{$shop_id}'";
    }
    $sub_sql = '';
    if ($order_by == 'price' || in_array("price", $columns)) {
        $sub_sql = ", (SELECT MIN(price) FROM " . T_PRODUCT_SHOP_PRICE . " WHERE " . T_PRODUCTS . ".id = " . T_PRODUCT_SHOP_PRICE . ".product_id $sub_sub_sql"
                . " UNION (SELECT MIN(price) FROM " . T_PRODUCT_SPECIAL_DISCOUNT . " WHERE " . T_PRODUCTS . ".id = " . T_PRODUCT_SPECIAL_DISCOUNT . ".product_id $sub_sub_sql"
                . " AND start_date >= '$today' AND end_date <= '$today' AND status = 'A' ORDER BY priority ASC)"
                . " LIMIT 1) AS price";
    }

    $sql = "SELECT * $sub_sql FROM " . T_PRODUCTS . " WHERE status != 'T'";
    if (!empty($columns) && is_array($columns)) {
        if (($key = array_search("price", $columns)) !== false) {
            unset($columns[$key]);
        }
        $sql = "SELECT `" . implode("`,`", $columns) . "` $sub_sql FROM " . T_PRODUCTS . " WHERE status != 'T'";
    }
    if (isset($filters['ids']) && is_array($filters['ids'])) {
        $ids = implode("','", $filters['ids']);
        $sql .= " AND id IN ('{$ids}')";
    }
    if (isset($filters['sku']) && trim($filters['sku']) <> "") {
        $sku = secure($filters['sku']);
        $sql .= " AND sku = '{$sku}'";
    }
    if (isset($filters['name']) && trim($filters['name']) <> "") {
        $name = secure($filters['name']);
        $sql .= " AND name LIKE '%{$name}%'";
    }
    if (isset($filters['model']) && trim($filters['model']) <> "") {
        $model = secure($filters['model']);
        $sql .= " AND model = '{$model}'";
    }
    if (isset($filters['brand']) && trim($filters['brand']) <> "") {
        $brand = secure($filters['brand']);
        $sql .= " AND brand = '{$brand}'";
    }
    if (isset($filters['slug']) && trim($filters['slug']) <> "") {
        $slug = secure($filters['slug']);
        $sql .= " AND slug = '{$slug}'";
    }
    if (isset($filters['length_class']) && trim($filters['length_class']) <> "") {
        $length_class = secure($filters['length_class']);
        $sql .= " AND length_class = '{$length_class}'";
    }
    if (isset($filters['weight_class']) && trim($filters['weight_class']) <> "") {
        $weight_class = secure($filters['weight_class']);
        $sql .= " AND weight_class = '{$weight_class}'";
    }
    if (isset($filters['requires_shipping']) && trim($filters['requires_shipping']) <> "") {
        $requires_shipping = secure($filters['requires_shipping']);
        $sql .= " AND requires_shipping = '{$requires_shipping}'";
    }
    if (isset($filters['featured_product']) && trim($filters['featured_product']) <> "") {
        $featured_product = secure($filters['featured_product']);
        $sql .= " AND featured_product = '{$featured_product}'";
    }
    if (isset($filters['status']) && trim($filters['status']) <> "") {
        $status = secure($filters['status']);
        $sql .= " AND status = '{$status}'";
    }
    if (isset($filters['added_by']) && trim($filters['added_by']) <> "") {
        $added_by = secure($filters['added_by']);
        $sql .= " AND added_by = '{$added_by}'";
    }
    if (isset($filters['updated_by']) && trim($filters['updated_by']) <> "") {
        $updated_by = secure($filters['updated_by']);
        $sql .= " AND updated_by = '{$updated_by}'";
    }
    if (isset($filters['category']) && trim($filters['category']) <> "") {
        $category_id = secure($filters['category']);
        $sql .= " AND id IN (SELECT product_id FROM " . T_PRODUCT_CATEGORY . " WHERE category_id = '{$category_id}')";
    }
    if (isset($filters['categories']) && is_array($filters['categories'])) {
        $category_ids = implode("','", $filters['categories']);
        $sql .= " AND id IN (SELECT product_id FROM " . T_PRODUCT_CATEGORY . " WHERE category_id IN ('{$category_ids}'))";
    }
    if (isset($filters['shop']) && trim($filters['shop']) <> "") {
        $shop_id = secure($filters['shop']);
        $sql .= " AND id IN (SELECT product_id FROM " . T_PRODUCT_SHOP_PRICE . " WHERE shop_id = '{$shop_id}')";
    }
    if (isset($filters['price_from']) && trim($filters['price_from']) <> "" && isset($filters['price_to']) && trim($filters['price_to']) <> "") {
        $price_from = secure($filters['price_from']);
        $price_to = secure($filters['price_to']);
        $sql .= " AND id IN (SELECT product_id FROM " . T_PRODUCT_SHOP_PRICE . " WHERE price >= '$price_from' AND price <= '$price_to')";
        //$sql .= " AND id IN (SELECT product_id FROM " . T_PRODUCT_SHOP_PRICE . " WHERE (price + price_gst + commission + commission_gst) >= '$price_from' AND (price + price_gst + commission + commission_gst) <= '$price_to')";
    }
    if (isset($filters['attributes']) && is_array($filters['attributes'])) {
        $attribute_ids = array();
        $attribute_values = array();
        foreach ($filters['attributes'] as $key => $values) {
            $attribute_ids[] = $key;
            $attribute_values = array_merge($attribute_values, $values);
        }
        $sql .= " AND id IN (SELECT product_id FROM " . T_PRODUCT_ATTRIBUTE . " WHERE attribute_id IN ('" . implode("','", $attribute_ids) . "') AND value IN (" . implode("','", $attribute_values) . "))";
    }
    if (isset($filters['tag']) && trim($filters['tag']) <> "") {
        $tag_id = secure($filters['tag']);
        $sql .= " AND id IN (SELECT product_id FROM " . T_PRODUCT_TAGS . " WHERE tag_id = '{$tag_id}')";
    }
    if (isset($filters['tags']) && is_array($filters['tags'])) {
        $tag_ids = implode("','", $filters['tags']);
        $sql .= " AND id IN (SELECT product_id FROM " . T_PRODUCT_TAGS . " WHERE tag_id IN ('{$tag_ids}'))";
    }
    if (isset($filters['filter']) && trim($filters['filter']) <> "") {
        $filter_id = secure($filters['filter']);
        $sql .= " AND id IN (SELECT product_id FROM " . T_PRODUCT_FILTERS . " WHERE filter_id = '{$filter_id}')";
    }
    if (isset($filters['filters']) && is_array($filters['filters']) && !empty($filters['filters'])) {
        $filter_ids = implode("','", $filters['filters']);
        $sql .= " AND id IN (SELECT product_id FROM " . T_PRODUCT_FILTERS . " WHERE filter_id IN ('{$filter_ids}'))";
    }
    if (isset($filters['query']) && trim($filters['query']) <> "") {
        $q = secure($filters['query']);
        $sql .= " AND (sku LIKE '%{$q}%' OR name LIKE '%{$q}%' OR slug LIKE '%{$q}%' OR meta_title LIKE '%{$q}%' OR meta_keywords LIKE '%{$q}%' OR meta_description LIKE '%{$q}%')";
    }
    $sql .= " ORDER BY {$order_by} {$order}";
    if ($limit != -1 && is_numeric($offset) && is_numeric($limit)) {
        $sql .= " LIMIT {$offset}, {$limit}";
    }

    $results = $conn->query($sql);
    while ($fetched_data = $results->fetch_assoc()) {
        $product = $fetched_data;
        if (isset($filters['with_prices'])) {
            $product['prices'] = getProductPrices($product['id'], null);
        }
        if (isset($filters['with_categories'])) {
            $product['categories'] = getCategories(array(), array("product_id" => $fetched_data['id']), 0, -1);
        }
        if (isset($filters['with_attributes'])) {
            $product['attributes'] = getAttributes($fetched_data['id']);
        }
        //$product['sellers'] = getSellers($fetched_data['id']);
        $data[] = $product;
    }
    return $data;
}

function getProduct($id_slug_sku, $shop_id = null, $columns = array(), $with_prices = true, $with_categories = true, $with_shipppings = true, $with_tags = true, $with_filters = true, $with_specifications = true, $with_options = true, $with_qty_discounts = true, $with_spl_discounts = true, $with_downloads = true) {
    global $conn;
    $product = null;
    $id = secure($id_slug_sku);
    $shop_id = secure($shop_id);

    $sql = "SELECT * FROM " . T_PRODUCTS . " WHERE id = '{$id}' OR slug = '{$id}' OR sku = '{$id}'";
    if (!empty($columns) && is_array($columns)) {
        if (!in_array("id", $columns)) {
            array_push($columns, "id");
        }
        $sql = "SELECT `" . implode("`,`", $columns) . "` FROM " . T_PRODUCTS . " WHERE id = '{$id}' OR slug = '{$id}' OR sku = '{$id}'";
    }
    $result = $conn->query($sql);
    while ($fetched_data = $result->fetch_assoc()) {
        $product = $fetched_data;
        /* getting shop prices */
        if ($with_prices) {
            $product['prices'] = getProductPrices($product['id'], $shop_id);
        }
        /* gettting categories */
        if ($with_categories) {
            $product['categories'] = getCategories(array(), array("product_id" => $product['id']), 0, -1);
        }
        /* getting shipping rate */
        if ($with_shipppings) {
            $product['shippings'] = getProductShippings($product['id'], $shop_id);
        }
        /* getting tags */
        if ($with_tags) {
            $product['tags'] = getTags(array(), array("product_id" => $product['id']), 0, -1);
        }
        /* getting filters */
        if ($with_filters) {
            $product['filters'] = getFilters(false, array("product_id" => $product['id']));
        }
        /* getting attributes/specifications */
        if ($with_specifications) {
            $product['specifications'] = getProductSpecifications($product['id']);
        }
        /* getting product option */
        if ($with_options) {
            $product['product_options'] = getProductOptions($product['id']);
        }
        /* getting quantity discount */
        if ($with_qty_discounts) {
            $product['qty_discounts'] = getProductQuantityDiscounts($product['id'], $shop_id);
        }
        /* getting special discount */
        if ($with_spl_discounts) {
            $product['special_discounts'] = getProductSpecialDiscounts($product['id'], $shop_id);
        }
        /* getting downloads */
        if ($with_downloads) {
            $product['downloads'] = getProductDownloads($product['id']);
        }
    }
    return $product;
}

function getProductsCount($catid = null) {
    global $conn;
    $products = array();
    $fcatid = secure($catid);
    $i = 0;
    if ($catid != null) {
        $result = $conn->query("SELECT * FROM " . T_PRODUCTS . " WHERE category_id='{$fcatid}'");
    } else {
        $result = $conn->query("SELECT * FROM " . T_PRODUCTS);
    }
    return mysqli_num_rows($result);
}

function getProductPrices($product_id, $shop_id = null) {
    global $conn, $TAX_PERCENT;
    $product_id = secure($product_id);

    $sql = "SELECT * FROM " . T_PRODUCT_SHOP_PRICE . " WHERE product_id = '{$product_id}'";
    if ($shop_id != null) {
        $sql .= " AND shop_id = '$shop_id'";
    }
    $result = $conn->query($sql);

    $data = array();
    while ($row = $result->fetch_assoc()) {
        $tax_percent = 0.00;
        if (isset($TAX_PERCENT[$row['tax_code']]) && $row['tax_code'] == "GST_APPAREL") {
            $tax_percent = $row['price'] <= 1000 ? $TAX_PERCENT['GST_05'] : $TAX_PERCENT['GST_12'];
        } else if (isset($TAX_PERCENT[$row['tax_code']])) {
            $tax_percent = $TAX_PERCENT[$row['tax_code']];
        }
        $row['price_taxed'] = $row['price'] + $row['price'] * $tax_percent;
        $data[$row['shop_id']] = $row;
    }

    return $data;
}

function getProductPrice($product_id, $shop_id = null, $quantity = 1) {
    global $conn, $TAX_PERCENT;
    $data = array();
    $product_id = secure($product_id);
    $quantity = secure($quantity);

    $sql = "SELECT *, price AS sale_price FROM " . T_PRODUCT_SHOP_PRICE . " WHERE product_id = '{$product_id}'";
    if ($shop_id != null) {
        $sql .= " AND shop_id = '$shop_id'";
    } else {
        $sql .= " AND price = (SELECT MIN(price) FROM " . T_PRODUCT_SHOP_PRICE . " WHERE product_id = '{$product_id}')";
    }
    $sql .= " LIMIT 1";

    $results = $conn->query($sql);
    while ($row = $results->fetch_assoc()) {
        $data = $row;
        $shop_id = $data['shop_id']; //used to get commission and special discount
        $today = date("Y-m-d"); //used to get special discount

        /* checking for special discount */
        $sdresults = $conn->query("SELECT * FROM " . T_PRODUCT_SPECIAL_DISCOUNT . " WHERE product_id = '{$product_id}' AND shop_id = '{$shop_id}' AND start_date <= '$today' AND end_date >= '$today' AND status = 'A' ORDER BY priority ASC LIMIT 1");
        if ($sdresults->num_rows > 0) {
            $sdrow = $sdresults->fetch_assoc();
            $data['price'] = $sdrow['price'];
        }
        /* checking for quantity discount */
        $qdresults = $conn->query("SELECT * FROM " . T_PRODUCT_QUANTITY_DISCOUNT . " WHERE product_id = '{$product_id}' AND shop_id = '{$shop_id}' AND quantity = '{$quantity}' AND start_date <= '$today' AND end_date >= '$today' AND status = 'A' ORDER BY priority ASC LIMIT 1");
        if ($qdresults->num_rows > 0) {
            $qdrow = $qdresults->fetch_assoc();
            $data['price'] = $qdrow['price'];
        }

        $tax_percent = 0.00;
        if (isset($TAX_PERCENT[$data['tax_code']]) && $data['tax_code'] == "GST_APPAREL") {
            $tax_percent = $data['price'] <= 1000 ? $TAX_PERCENT['GST_05'] : $TAX_PERCENT['GST_12'];
        } else if (isset($TAX_PERCENT[$data['tax_code']])) {
            $tax_percent = $TAX_PERCENT[$data['tax_code']];
        }

        $data['commission_percent'] = getProductCommission($product_id, $shop_id);
        $data['discount'] = $data['sale_price'] - $data['price'];
        $data['sale_price_gst'] = $data['sale_price'] * $tax_percent;
        $data['price_gst'] = $data['price'] * $tax_percent;
        $data['sale_price_commission'] = $data['sale_price'] * ($data['commission_percent'] / 100);
        $data['sale_price_commission_gst'] = $data['sale_price_commission'] * 0.18;
        $data['commission'] = $data['price'] * ($data['commission_percent'] / 100);
        $data['commission_gst'] = $data['commission'] * 0.18;
    }

    return $data;
}

function getProductCommission($product_id, $shop_id = null) {
    global $conn;
    $product_id = secure($product_id);
    $shop_id = secure($shop_id);

    $categories_arr = array();
    $pcresults = $conn->query("SELECT category_id FROM " . T_PRODUCT_CATEGORY . " WHERE product_id = '{$product_id}'");
    while ($row = $pcresults->fetch_assoc()) {
        $categories_arr[] = $row['category_id'];
    }

    $commission_percent = 0.00;
    $category_id = 0;
    $pid_found = $vid_found = $cid_found = false;
    $cresults = $conn->query("SELECT * FROM " . T_COMMISSION_SETTINGS . " WHERE (product_id = '{$product_id}' OR shop_id = '{$shop_id}' OR category_id IN ('" . implode("','", $categories_arr) . "')) AND status = 'A' ORDER BY id DESC LIMIT 1");
    if ($cresults->num_rows > 0) {
        while ($row = $cresults->fetch_assoc()) {
            if ($row['product_id'] == $product_id) {
                $commission_percent = $row['fees'];
                $pid_found = true;
            }
            if ($row['shop_id'] == $shop_id && !$pid_found) {
                $commission_percent = $row['fees'];
                $vid_found = true;
            }
            if (in_array($row['category_id'], $categories_arr) && $row['category_id'] > $category_id && !$pid_found && !$vid_found) {
                $commission_percent = $row['fees'];
                $category_id = $row['category_id'];
                $cid_found = true;
            }
        }
    } else {
        $cresults = $conn->query("SELECT * FROM " . T_COMMISSION_SETTINGS . " WHERE product_id = '0' AND shop_id = '0' AND category_id = '0' AND status = 'A' ORDER BY id DESC LIMIT 1");
        while ($row = $cresults->fetch_assoc()) {
            $commission_percent = $row['fees'];
        }
    }


    return $commission_percent;
}

function addUpdateProductPrice($product_id, $shop_id, $price) {
    global $conn;
    $product_condition = secure($price['product_condition']);
    $p = secure($price['price']);
    $marketplace_fees = secure($price['marketplace_fees']);
    $hsn_code = secure($price['hsn_code']);
    $tax_code = secure($price['tax_code']);
    $stock = secure($price['stock']);
    $shipping_country = secure($price['shipping_country']);
    $ship_free = secure($price['ship_free']);
    $min_order_qty = secure($price['min_order_qty']);
    $substract_stock = secure($price['substract_stock']);
    $track_inventory = secure($price['track_inventory']);
    $alert_stock_level = secure($price['alert_stock_level']);
    $in_stock = secure($price['in_stock']);
    $date_available = secure($price['date_available']);
    $enable_cod = secure($price['enable_cod']);

    $results = $conn->query("SELECT * FROM " . T_PRODUCT_SHOP_PRICE . " WHERE product_id = '{$product_id}' AND shop_id = '{$shop_id}'");
    if ($results->num_rows > 0) {
        $qstatus = $conn->query("UPDATE " . T_PRODUCT_SHOP_PRICE . " SET product_condition = '{$product_condition}', price = '{$p}', marketplace_fees = '{$marketplace_fees}', hsn_code = '{$hsn_code}', tax_code = '{$tax_code}', stock = '{$stock}', shipping_country = '{$shipping_country}', ship_free = '{$ship_free}', min_order_qty = '{$min_order_qty}', substract_stock = '{$substract_stock}', track_inventory = '{$track_inventory}', alert_stock_level = '{$alert_stock_level}', in_stock = '{$in_stock}', date_available = '{$date_available}', enable_cod = '{$enable_cod}'  WHERE product_id = '{$product_id}' AND shop_id = '{$shop_id}'");
        $GLOBALS['queryerrormsg'] = $conn->error;
    } else {
        $qstatus = $conn->query("INSERT INTO " . T_PRODUCT_SHOP_PRICE . " (product_id, shop_id, product_condition, price, marketplace_fees, hsn_code, tax_code, stock, shipping_country, ship_free, min_order_qty, substract_stock, track_inventory, alert_stock_level, in_stock, date_available, enable_cod) VALUES('{$product_id}', '{$shop_id}', '{$product_condition}', '{$p}', '{$marketplace_fees}', '{$hsn_code}', '{$tax_code}', '{$stock}', '{$shipping_country}', '{$ship_free}', '{$min_order_qty}', '{$substract_stock}', '{$track_inventory}', '{$alert_stock_level}', '{$in_stock}', '{$date_available}', '{$enable_cod}')");
        $GLOBALS['queryerrormsg'] = $conn->error;
    }

    return $qstatus;
}

function addProduct($product) {
    global $conn;
    $type = isset($product['type']) ? secure($product['type']) : "PHY";
    $sku = secure($product['sku']);
    $name = secure($product['name']);
    $model = secure($product['model']);
    $brand = secure($product['brand']);
    $slug = url_slug(secure($product['slug']));
    $short_description = $product['short_description'];
    $long_description = $product['long_description'];
    $images = !empty($product['images']) ? implode(",", $product['images']) : "";
    $length_class = secure($product['length_class']);
    $length = secure($product['length']);
    $width = secure($product['width']);
    $height = secure($product['height']);
    $weight_class = secure($product['weight_class']);
    $weight = secure($product['weight']);
    $requires_shipping = isset($product['requires_shipping']) ? secure($product['requires_shipping']) : "Y";
    $youtube_video = secure($product['youtube_video']);
    $related_products = implode(",", $product['related_products']);
    $product_addons = json_encode($product['product_addons']);
    $views = secure($product['views']);
    $likes = secure($product['likes']);
    $orders = secure($product['orders']);
    $display_order = secure($product['display_order']);
    $featured_product = isset($product['featured_product']) ? "Y" : "N";
    $meta_title = secure($product['meta_title']);
    $meta_keywords = secure($product['meta_keywords']);
    $meta_description = secure($product['meta_description']);
    $status = isset($product['status']) ? secure($product['status']) : "A";
    $added_by = $updated_by = secure($product['added_by']);
    $addedtimestamp = isset($product['added_timestamp']) ? secure($product['added_timestamp']) : date("Y-m-d H:i:s");
    $updatedtimestamp = isset($product['updated_timestamp']) ? secure($product['updated_timestamp']) : date("Y-m-d H:i:s");

    $results = $conn->query("SELECT * FROM " . T_PRODUCTS . " WHERE sku = '{$sku}' OR name = '{$name}'");
    if ($results->num_rows > 0) {
        $GLOBALS['queryerrormsg'] = "Product already exist";
        return false;
    }

    if (!$conn->query("INSERT INTO " . T_PRODUCTS . " (type, sku, name, model, brand, slug, short_description, long_description, images, length_class, length, width, height, weight_class, weight, requires_shipping, youtube_video, related_products, product_addons, views, likes, orders, display_order, featured_product, meta_title, meta_keywords, meta_description, status, added_by, updated_by, added_timestamp, updated_timestamp) "
                    . "VALUES('{$type}', '{$sku}', '{$name}', '{$model}', '{$brand}', '{$slug}', '{$short_description}', '{$long_description}', '{$images}', '{$length_class}', '{$length}', '{$width}', '{$height}', '{$weight_class}', '{$weight}', '{$requires_shipping}', '{$youtube_video}', '{$related_products}', '{$product_addons}', '{$views}', '{$likes}', '{$orders}', '{$display_order}', '{$featured_product}', '{$meta_title}', '{$meta_keywords}', '{$meta_description}', '{$status}', '{$added_by}', '{$updated_by}', '{$addedtimestamp}', '{$updatedtimestamp}')")) {
        $GLOBALS['queryerrormsg'] = mysqli_error($conn);
        return false;
    }
    $pid = $conn->insert_id;
    /* Adding shop price */
    addUpdateProductPrice($pid, $product['shop_id'], $product['price']);
    /* Adding product categories */
    foreach ($product['categories'] as $category_id) {
        updateProductCategory($pid, $category_id);
    }
    /* Adding shipping rate */
    foreach ($product['shippings'] as $shipping) {
        addUpdateProductShipping($pid, $product['shop_id'], $shipping);
    }
    /* Adding tags */
    foreach ($product['tags'] as $tag_id) {
        addProductTag($pid, $tag_id);
    }
    /* Adding filters */
    foreach ($product['filters'] as $filter) {
        addProductFilter($pid, $filter);
    }
    /* Adding attributes/specifications */
    foreach ($product['specifications'] as $specification) {
        if (trim($specification['attribute_value']) == '') {
            continue;
        }
        addUpdateProductSpecification($pid, $specification);
    }
    /* Adding product option */
    foreach ($product['product_options'] as $product_option) {
        addUpdateProductOption($pid, $product_option);
    }
    /* Adding quantity discount */
    foreach ($product['qty_discounts'] as $qty_discount) {
        addUpdateProductQuantityDiscount($pid, $product['shop_id'], $qty_discount);
    }
    /* Adding special discount */
    foreach ($product['special_discounts'] as $special_discount) {
        addUpdateProductSpecialDiscount($pid, $product['shop_id'], $special_discount);
    }
    /* Adding downloads */
    foreach ($product['downloads'] as $download) {
        addUpdateProductDownload($pid, $download);
    }

    return true;
}

function updateProduct($product) {
    global $conn;
    $id = secure($product['id']);
    $type = isset($product['type']) ? secure($product['type']) : "PHY";
    $sku = secure($product['sku']);
    $name = secure($product['name']);
    $model = secure($product['model']);
    $brand = secure($product['brand']);
    $slug = url_slug(secure($product['slug']));
    $short_description = $product['short_description'];
    $long_description = $product['long_description'];
    $images = !empty($product['images']) ? implode(",", $product['images']) : "";
    $length_class = secure($product['length_class']);
    $length = secure($product['length']);
    $width = secure($product['width']);
    $height = secure($product['height']);
    $weight_class = secure($product['weight_class']);
    $weight = secure($product['weight']);
    $requires_shipping = isset($product['requires_shipping']) ? secure($product['requires_shipping']) : "Y";
    $youtube_video = secure($product['youtube_video']);
    $related_products = implode(",", $product['related_products']);
    $product_addons = json_encode($product['product_addons']);
    $views = secure($product['views']);
    $likes = secure($product['likes']);
    $orders = secure($product['orders']);
    $display_order = secure($product['display_order']);
    $featured_product = isset($product['featured_product']) ? "Y" : "N";
    $meta_title = secure($product['meta_title']);
    $meta_keywords = secure($product['meta_keywords']);
    $meta_description = secure($product['meta_description']);
    $status = isset($product['status']) ? secure($product['status']) : "A";
    $updated_by = isset($product['updated_by']) ? secure($product['updated_by']) : getUserLoggedId();
    //$addedtimestamp = isset($product['added_timestamp']) ? secure($product['added_timestamp']) : date("Y-m-d H:i:s");
    $updatedtimestamp = isset($product['updated_timestamp']) ? secure($product['updated_timestamp']) : date("Y-m-d H:i:s");

    $results = $conn->query("SELECT * FROM " . T_PRODUCTS . " WHERE id != '{$id}' AND (sku = '{$sku}' OR name = '{$name}' OR slug = '{$slug}')");
    if ($results->num_rows > 0) {
        $GLOBALS['queryerrormsg'] = "Product already exist";
        return false;
    }

    $sql = "UPDATE " . T_PRODUCTS . " SET type='{$type}', sku='{$sku}', name='{$name}', model='{$model}', "
            . "brand='{$brand}', slug='{$slug}', short_description='{$short_description}', "
            . "long_description='{$long_description}', images='{$images}', length_class='{$length_class}', "
            . "length='{$length}', width='{$width}', height='{$height}', weight_class='{$weight_class}', "
            . "weight='{$weight}', requires_shipping='{$requires_shipping}', youtube_video='{$youtube_video}', "
            . "related_products='{$related_products}', product_addons='{$product_addons}', views='{$views}', "
            . "likes='{$likes}', orders='{$orders}', display_order='{$display_order}', "
            . "featured_product='{$featured_product}', meta_title = '{$meta_title}', "
            . "meta_keywords = '{$meta_keywords}', meta_description = '{$meta_description}', status='{$status}', "
            . "updated_by='{$updated_by}', updated_timestamp='{$updatedtimestamp}' WHERE id='{$id}'";

    if (!$conn->query($sql)) {
        $GLOBALS['queryerrormsg'] = "Please try again later";
        return false;
    }

    /* Updating shop price */
    addUpdateProductPrice($id, $product['shop_id'], $product['price']);
    /* Updating product categories */
    deleteProductCategories($id);
    foreach ($product['categories'] as $category_id) {
        updateProductCategory($id, $category_id);
    }
    /* Updating shipping rate */
    deleteProductShipping($id, $product['shop_id']);
    foreach ($product['shippings'] as $shipping) {
        addUpdateProductShipping($id, $product['shop_id'], $shipping);
    }
    /* Updating tags */
    deleteProductTag($id);
    foreach ($product['tags'] as $tag_id) {
        addProductTag($id, $tag_id);
    }
    /* Updating filters */
    deleteProductFilter($id);
    foreach ($product['filters'] as $filter) {
        addProductFilter($id, $filter);
    }
    /* Updating attributes/specifications */
    deleteProductSpecification($id);
    foreach ($product['specifications'] as $specification) {
        if (trim($specification['attribute_value']) == '') {
            continue;
        }
        addUpdateProductSpecification($id, $specification);
    }
    /* Updating product option */
    deleteProductOption($id);
    foreach ($product['product_options'] as $product_option) {
        addUpdateProductOption($id, $product_option);
    }
    /* Updating quantity discount */
    deleteProductQuantityDiscount($id, $product['shop_id']);
    foreach ($product['qty_discounts'] as $qty_discount) {
        addUpdateProductQuantityDiscount($id, $product['shop_id'], $qty_discount);
    }
    /* Updating special discount */
    deleteProductSpecialDiscount($id, $product['shop_id']);
    foreach ($product['special_discounts'] as $special_discount) {
        addUpdateProductSpecialDiscount($id, $product['shop_id'], $special_discount);
    }
    /* Updating downloads */
    deleteProductDownload($id);
    foreach ($product['downloads'] as $download) {
        addUpdateProductDownload($id, $download);
    }

    return true;
}

function addProductCSVariant($product) {
    global $conn;
    $pid = secure($product['id']);
    $seller = $product['seller'];
    $sid = $seller['id'];
    $color = secure($seller['color']);
    $size = secure($seller['size']);
    $imagesarr = preg_split("/[\r\n,;]+/", $seller['images'], -1, PREG_SPLIT_NO_EMPTY);
    $images = implode(",", $imagesarr);
    $price = secure($seller['price']);
    $shipping = secure($seller['shipping']);
    $marketplace_fees = secure($seller['marketplace_fees']);
    $tax = secure($seller['tax']);
    $selling_price = secure($seller['selling_price']);
    $in_stock = secure($seller['in_stock']);
    $percent_discount = secure($seller['percent_discount']);
    $active_discount = secure($seller['active_discount']);

    $result = $conn->query("SELECT * FROM " . T_PRODUCT_SELLER_PRICE . " WHERE product_id = '{$pid}' AND seller_id = '{$sid}' AND color = '{$color}' AND size = '{$size}'");
    if (mysqli_num_rows($result) > 0) {
        $GLOBALS['queryerrormsg'] = "Product variant already exist";
        return false;
    }
    updateProductSeller($pid, $sid, $color, $size, $images, $price, $shipping, $marketplace_fees, $tax, $selling_price, $in_stock, $percent_discount, $active_discount);

    return true;
}

function deleteProduct($id) {
    global $conn;
    $sid = secure($id);
    if ($conn->query("UPDATE " . T_PRODUCTS . " SET status = 'T' WHERE id='{$sid}'")) {
        //$conn->query("DELETE FROM " . T_PRODUCT_ATTRIBUTE . " WHERE product_id='{$sid}'");
        //$conn->query("DELETE FROM " . T_PRODUCT_SHOP_PRICE . " WHERE product_id='{$sid}'");
        //$conn->query("DELETE FROM " . T_PRODUCT_AFFILIATE_PRICE . " WHERE product_id='{$sid}'");
        return true;
    }
    return false;
}

/*
  function updateProductAttribute($product_id, $attribute_id, $value) {
  global $conn;
  $fproduct_id = secure($product_id);
  $fattribute_id = secure($attribute_id);
  $fvalue = secure($value);
  $result = $conn->query("SELECT * FROM ".T_PRODUCT_ATTRIBUTE." WHERE product_id='{$fproduct_id}' AND attribute_id='{$fattribute_id}'");
  if (mysqli_num_rows($result) > 0) {
  $conn->query("UPDATE ".T_PRODUCT_ATTRIBUTE." SET value='{$fvalue}' WHERE product_id='{$fproduct_id}' AND attribute_id='{$fattribute_id}'");
  } else {
  $conn->query("INSERT INTO ".T_PRODUCT_ATTRIBUTE." (product_id, attribute_id, value) VALUES('{$fproduct_id}','{$fattribute_id}','{$fvalue}')");
  }
  }
 */

function deleteProductCategories($product_id) {
    global $conn;
    $product_id = secure($product_id);

    $result = $conn->query("DELETE FROM " . T_PRODUCT_CATEGORY . " WHERE product_id='{$product_id}'");
    $GLOBALS['queryerrormsg'] = mysqli_error($conn);
    return $result;
}

function updateProductCategory($product_id, $category_id) {
    global $conn;
    $product_id = secure($product_id);
    $category_id = secure($category_id);

    $result = $conn->query("SELECT * FROM " . T_PRODUCT_CATEGORY . " WHERE product_id='{$product_id}' AND category_id='{$category_id}'");
    if (mysqli_num_rows($result) <= 0) {
        $result = $conn->query("INSERT INTO " . T_PRODUCT_CATEGORY . " (product_id, category_id) VALUES('{$product_id}','{$category_id}')");
        $GLOBALS['queryerrormsg'] = mysqli_error($conn);
    }
    return $result;
}

/*
  function updateProductSeller($product_id, $seller_id, $color, $size, $images, $price, $shipping, $marketplace_fees, $tax, $selling_price, $in_stock = 'Y', $percent_discount, $active_discount) {
  global $conn;
  $fproduct_id = secure($product_id);
  $fseller_id = secure($seller_id);
  $fcolor = secure($color);
  $fsize = secure($size);
  $imagesarr = preg_split("/[\r\n,;]+/", $images, -1, PREG_SPLIT_NO_EMPTY);
  $images = implode(",", $imagesarr);
  $fprice = secure($price);
  $fshipping = secure($shipping);
  $fmarketplace_fees = secure($marketplace_fees);
  $ftax = secure($tax);
  $fselling_price = secure($selling_price);
  $fin_stock = secure($in_stock);
  $percent_discount = secure($percent_discount);
  $active_discount = secure($active_discount);

  $result = $conn->query("SELECT * FROM " . T_PRODUCT_SELLER_PRICE . " WHERE product_id='{$fproduct_id}' AND seller_id='{$fseller_id}' AND color='{$fcolor}' AND size='{$fsize}'");
  if (mysqli_num_rows($result) > 0) {
  $conn->query("UPDATE ".T_PRODUCT_SELLER_PRICE." SET images='{$images}', price='{$fprice}', shipping='{$fshipping}', marketplace_fees='{$fmarketplace_fees}', tax='{$tax}', selling_price='{$fselling_price}', in_stock='{$fin_stock}', percent_discount='{$percent_discount}', active_discount='{$active_discount}' WHERE product_id='{$fproduct_id}' AND seller_id='{$fseller_id}' AND color='{$fcolor}' AND size='{$size}'");
  } else {
  $conn->query("INSERT INTO ".T_PRODUCT_SELLER_PRICE." (product_id, seller_id, color, size, images, price, shipping, marketplace_fees, tax, selling_price, in_stock, percent_discount, active_discount) VALUES('{$fproduct_id}','{$fseller_id}','{$fcolor}','{$fsize}','{$images}','{$fprice}','{$fshipping}','{$fmarketplace_fees}','{$ftax}','{$fselling_price}','{$fin_stock}','{$percent_discount}','{$active_discount}')");
  }
  $GLOBALS['queryerrormsg'] = mysqli_error($conn);
  }
 */

function addProductFilter($product_id, $filter_id) {
    global $conn;
    $product_id = secure($product_id);
    $filter_id = secure($filter_id);

    $results = $conn->query("SELECT * FROM " . T_PRODUCT_FILTERS . " WHERE product_id = '{$product_id}' AND filter_id = '{$filter_id}'");
    if ($results->num_rows > 0) {
        return true;
    }

    if (!$conn->query("INSERT INTO " . T_PRODUCT_FILTERS . " (product_id, filter_id) VALUES('{$product_id}', '{$filter_id}')")) {
        $GLOBALS['queryerrormsg'] = $conn->error;
        return false;
    }

    return true;
}

function deleteProductFilter($product_id, $filter_id = null) {
    global $conn;
    $product_id = secure($product_id);

    $sql = "DELETE FROM " . T_PRODUCT_FILTERS . " WHERE product_id = '{$product_id}'";
    if (isset($filter_id)) {
        $filter_id = secure($filter_id);
        $sql .= "AND filter_id = '{$filter_id}'";
    }
    $status = $conn->query($sql);
    $GLOBALS['queryerrormsg'] = $conn->error;
    return $status;
}

/* Product Functions End */

function updateProductAffiliate($product_id, $seller_id, $url, $price) {
    global $conn;
    $fproduct_id = secure($product_id);
    $fseller_id = secure($seller_id);
    $furl = secure($url);
    $fprice = secure($price);
    $result = $conn->query("SELECT * FROM " . T_PRODUCT_AFFILIATE_PRICE . " WHERE product_id='{$fproduct_id}' AND seller_id='{$fseller_id}'");
    if (mysqli_num_rows($result) > 0) {
        $conn->query("UPDATE " . T_PRODUCT_AFFILIATE_PRICE . " SET url='{$furl}', price='{$fprice}' WHERE product_id='{$fproduct_id}' AND seller_id='{$fseller_id}'");
    } else {
        $conn->query("INSERT INTO " . T_PRODUCT_AFFILIATE_PRICE . " (product_id, seller_id, url, price) VALUES('{$fproduct_id}','{$fseller_id}','{$furl}','{$fprice}')");
    }
}

function isProductHasColorSizeVariant($product_id, $seller_id) {
    global $conn;
    $product_id = secure($product_id);
    $seller_id = secure($seller_id);
    $result = $conn->query("SELECT * FROM " . T_PRODUCT_SELLER_PRICE . " WHERE product_id='{$product_id}' AND seller_id='{$seller_id}'");
    if ($result->num_rows > 1) {
        return true;
    }
    return false;
}

function addProductTag($product_id, $tag_id) {
    global $conn;
    $product_id = secure($product_id);
    $tag_id = secure($tag_id);

    $results = $conn->query("SELECT * FROM " . T_PRODUCT_TAGS . " WHERE product_id = '{$product_id}' AND tag_id = '{$tag_id}'");
    if ($results->num_rows > 0) {
        return true;
    }

    if (!$conn->query("INSERT INTO " . T_PRODUCT_TAGS . " (product_id, tag_id) VALUES('{$product_id}', '{$tag_id}')")) {
        $GLOBALS['queryerrormsg'] = $conn->error;
        return false;
    }

    return true;
}

function deleteProductTag($product_id, $tag_id = null) {
    global $conn;
    $product_id = secure($product_id);
    $tag_id = secure($tag_id);

    $sql = "DELETE FROM " . T_PRODUCT_TAGS . " WHERE product_id = '{$product_id}'";
    if ($tag_id != null) {
        $sql .= "AND tag_id = '{$tag_id}'";
    }
    $status = $conn->query($sql);
    $GLOBALS['queryerrormsg'] = $conn->error;
    return $status;
}

function getProductShippings($product_id, $shop_id, $country = null) {
    global $conn;
    $product_id = secure($product_id);
    $shop_id = secure($shop_id);
    $$country = secure($country);
    $sql = "SELECT * FROM " . T_PRODUCT_SHIPPING_RATES . " WHERE product_id = '{$product_id}'";
    if ($shop_id != null) {
        $sql .= " AND shop_id = '$shop_id'";
    }
    if ($country != null) {
        $sql .= " AND country = '$country'";
    }
    $result = $conn->query($sql);

    $data = array();
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    return $data;
}

function getProductShipping($product_id, $shop_id, $country) {
    global $conn;
    $product_id = secure($product_id);
    $shop_id = secure($shop_id);
    $country = secure($country);

    $sql = "SELECT * FROM " . T_PRODUCT_SHIPPING_RATES . " WHERE product_id = '{$product_id}' AND shop_id = '$shop_id' AND country = '$country'";
    $result = $conn->query($sql);

    $data = array();
    while ($row = $result->fetch_assoc()) {
        $data = $row;
    }
    return $data;
}

function addUpdateProductShipping($product_id, $shop_id, $shipping) {
    global $conn;
    $country = secure($shipping['country']);
    $company = secure($shipping['company']);
    $duration_id = secure($shipping['duration_id']);
    $charges = secure($shipping['charges']);
    $additional_charges = secure($shipping['additional_charges']);

    $results = $conn->query("SELECT * FROM " . T_PRODUCT_SHIPPING_RATES . " WHERE product_id = '{$product_id}' AND shop_id = '{$shop_id}' AND country = '{$country}' AND company = '{$company}'");
    if ($results->num_rows > 0) {
        $qstatus = $conn->query("UPDATE " . T_PRODUCT_SHIPPING_RATES . " SET duration_id = '{$duration_id}', charges = '{$charges}', additional_charges='{$additional_charges}' WHERE product_id = '{$product_id}' AND shop_id = '{$shop_id}' AND country = '{$country}' AND company = '{$company}'");
        $GLOBALS['queryerrormsg'] = $conn->error;
    } else {
        $qstatus = $conn->query("INSERT INTO " . T_PRODUCT_SHIPPING_RATES . " (product_id, shop_id, country, company, duration_id, charges, additional_charges) VALUES('{$product_id}', '{$shop_id}', '{$country}', '{$company}', '{$duration_id}', '{$charges}', '{$additional_charges}')");
        $GLOBALS['queryerrormsg'] = $conn->error;
    }
    return $qstatus;
}

function deleteProductShipping($product_id, $shop_id) {
    global $conn;
    $product_id = secure($product_id);
    $shop_id = secure($shop_id);

    $qstatus = $conn->query("DELETE FROM " . T_PRODUCT_SHIPPING_RATES . " WHERE product_id='{$product_id}' AND shop_id='{$shop_id}'");
    $GLOBALS['queryerrormsg'] = $conn->error;
    return $qstatus;
}

/* Product Specifications Functions Start */

function getProductSpecifications($product_id) {
    global $conn;
    $data = array();
    $product_id = secure($product_id);

    $result = $conn->query("SELECT a.*, pa.* FROM " . T_PRODUCT_ATTRIBUTE . " AS pa LEFT JOIN " . T_ATTRIBUTE . " AS a ON a.id = pa.attribute_id WHERE product_id = '{$product_id}'");
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    return $data;
}

function addUpdateProductSpecification($product_id, $specification) {
    global $conn;
    $product_id = secure($product_id);
    $attribute_id = secure($specification['attribute_id']);
    $attribute_value = secure($specification['attribute_value']);

    $results = $conn->query("SELECT * FROM " . T_PRODUCT_ATTRIBUTE . " WHERE product_id = '{$product_id}' AND attribute_id = '{$attribute_id}'");
    if ($results->num_rows > 0) {
        $qstatus = $conn->query("UPDATE " . T_PRODUCT_ATTRIBUTE . " SET attribute_value = '{$attribute_value}' WHERE product_id = '{$product_id}' AND attribute_id = '{$attribute_id}'");
        $GLOBALS['queryerrormsg'] = $conn->error;
    } else {
        $qstatus = $conn->query("INSERT INTO " . T_PRODUCT_ATTRIBUTE . " (product_id, attribute_id, attribute_value) VALUES('{$product_id}', '{$attribute_id}', '{$attribute_value}')");
        $GLOBALS['queryerrormsg'] = $conn->error;
    }

    return $qstatus;
}

function deleteProductSpecification($product_id, $attribute_id = null) {
    global $conn;
    $product_id = secure($product_id);

    $sql = "DELETE FROM " . T_PRODUCT_ATTRIBUTE . " WHERE product_id = '{$product_id}'";
    if (isset($attribute_id)) {
        $attribute_id = secure($attribute_id);
        $sql .= "AND attribute_id = '{$attribute_id}'";
    }
    $status = $conn->query($sql);
    $GLOBALS['queryerrormsg'] = mysqli_error($conn);
    return $status;
}

/* Product Specifications Functions End */

/* Product Options Functions Start */

function getProductOptions($product_id) {
    global $conn;
    $data = array();
    $product_id = secure($product_id);

    $sql = "SELECT * FROM " . T_PRODUCT_OPTIONS . " WHERE product_id = '{$product_id}'";
    $results = $conn->query($sql);

    while ($row = $results->fetch_assoc()) {
        $option_id = $row['option_id'];
        $result2 = $conn->query("SELECT * FROM " . T_PRODUCT_OPTION_VALUES . " WHERE product_id = '{$product_id}' AND option_id = '{$option_id}'");
        $values = array();
        while ($row2 = $result2->fetch_assoc()) {
            $values[] = $row2;
        }
        $row['values'] = $values;
        $data[] = $row;
    }
    return $data;
}

function addUpdateProductOption($product_id, $option) {
    global $conn;
    $product_id = secure($product_id);
    $id = isset($option['id']) ? secure($option['id']) : 'NULL';
    $option_id = secure($option['option_id']);
    $required = secure($option['required']);
    $option_value = isset($option['option_value']) && !is_array($option['option_value']) ? $option['option_value'] : "";

    $qstatus = $conn->query("INSERT INTO " . T_PRODUCT_OPTIONS . " (id, product_id, option_id, option_value, required) VALUES({$id}, '{$product_id}', '{$option_id}', '{$option_value}', '{$required}')");
    $GLOBALS['queryerrormsg'] = $conn->error;

    if ($qstatus && isset($option['option_value']) && is_array($option['option_value'])) {
        $id = $conn->insert_id;

        $conn->query("DELETE FROM " . T_PRODUCT_OPTION_VALUES . " WHERE product_option_id = '{$id}'");
        foreach ($option['option_value'] as $ov) {
            $option_v_id = isset($ov['id']) ? secure($ov['id']) : 'NULL';
            $option_v = $ov['option_value'];
            $qty = $ov['quantity'];
            $subtract = $ov['subtract'];
            $price_prefix = $ov['price_prefix'];
            $price = $ov['price'];
            $weight_prefix = $ov['weight_prefix'];
            $weight = $ov['weight'];
            $conn->query("INSERT INTO " . T_PRODUCT_OPTION_VALUES . " (id, product_option_id, product_id, option_id, option_value, quantity, subtract, price, price_prefix, weight, weight_prefix) VALUES({$option_v_id}, '{$id}', '{$product_id}', '{$option_id}', '{$option_v}', '{$qty}', '{$subtract}', '{$price}', '{$price_prefix}', '{$weight}', '{$weight_prefix}')");
        }
    }
    return $qstatus;
}

function deleteProductOption($product_id, $option = array()) {
    global $conn;
    $product_id = secure($product_id);

    $sql1 = "DELETE FROM " . T_PRODUCT_OPTION_VALUES . " WHERE product_id = '{$product_id}'";
    $sql2 = "DELETE FROM " . T_PRODUCT_OPTIONS . " WHERE product_id = '{$product_id}'";
    if (isset($option['option_id'])) {
        $option_id = secure($option['option_id']);
        $sql1 .= "AND option_id = '{$option_id}'";
        $sql2 .= "AND option_id = '{$option_id}'";
    }

    $qstatus = false;
    if ($conn->query($sql1) && $conn->query($sql2)) {
        $qstatus = true;
    }
    $GLOBALS['queryerrormsg'] = $conn->error;
    return $qstatus;
}

/* Product Options Functions End */

/* Product Discounts Functions Start */

function getProductQuantityDiscounts($product_id, $shop_id = null) {
    global $conn;
    $product_id = secure($product_id);

    $sql = "SELECT * FROM " . T_PRODUCT_QUANTITY_DISCOUNT . " WHERE product_id = '{$product_id}'";
    if ($shop_id != null) {
        $sql .= " AND shop_id = '{$shop_id}'";
    }
    $result = $conn->query($sql);

    $data = array();
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    return $data;
}

function addUpdateProductQuantityDiscount($product_id, $shop_id, $qty_discount) {
    global $conn;
    $product_id = secure($product_id);
    $shop_id = secure($shop_id);
    $quantity = secure($qty_discount['quantity']);
    $priority = secure($qty_discount['priority']);
    $price = secure($qty_discount['price']);
    $start_date = secure($qty_discount['start_date']);
    $end_date = secure($qty_discount['end_date']);

    $results = $conn->query("SELECT * FROM " . T_PRODUCT_QUANTITY_DISCOUNT . " WHERE product_id = '{$product_id}' AND shop_id = '{$shop_id}' AND quantity = '{$quantity}'");
    if ($results->num_rows > 0) {
        $qstatus = $conn->query("UPDATE " . T_PRODUCT_QUANTITY_DISCOUNT . " SET priority = '{$priority}', price = '{$price}', start_date = '{$start_date}', end_date = '{$end_date}' WHERE product_id = '{$product_id}' AND shop_id = '{$shop_id}' AND quantity = '{$quantity}'");
        $GLOBALS['queryerrormsg'] = $conn->error;
    } else {
        $qstatus = $conn->query("INSERT INTO " . T_PRODUCT_QUANTITY_DISCOUNT . " (product_id, shop_id, quantity, priority, price, start_date, end_date) VALUES('{$product_id}', '{$shop_id}', '{$quantity}', '{$priority}', '{$price}', '{$start_date}', '{$end_date}')");
        $GLOBALS['queryerrormsg'] = $conn->error;
    }

    return $qstatus;
}

function deleteProductQuantityDiscount($product_id, $shop_id, $qty_discount = array()) {
    global $conn;
    $product_id = secure($product_id);
    $shop_id = secure($shop_id);

    $sql = "DELETE FROM " . T_PRODUCT_QUANTITY_DISCOUNT . " WHERE product_id = '{$product_id}' AND shop_id = '{$shop_id}'";
    if (isset($qty_discount['quantity'])) {
        $quantity = secure($qty_discount['quantity']);
        $sql .= "AND quantity = '{$quantity}'";
    }

    $qstatus = $conn->query($sql);
    $GLOBALS['queryerrormsg'] = mysqli_error($conn);

    return $qstatus;
}

function getProductSpecialDiscounts($product_id, $shop_id = null) {
    global $conn;
    $product_id = secure($product_id);

    $sql = "SELECT * FROM " . T_PRODUCT_SPECIAL_DISCOUNT . " WHERE product_id = '{$product_id}'";
    if ($shop_id != null) {
        $sql .= " AND shop_id = '{$shop_id}'";
    }
    $result = $conn->query($sql);

    $data = array();
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    return $data;
}

function addUpdateProductSpecialDiscount($product_id, $shop_id, $special_discount) {
    global $conn;
    $product_id = secure($product_id);
    $shop_id = secure($shop_id);
    $priority = secure($special_discount['priority']);
    $price = secure($special_discount['price']);
    $start_date = secure($special_discount['start_date']);
    $end_date = secure($special_discount['end_date']);

    $result = $conn->query("SELECT * FROM " . T_PRODUCT_SPECIAL_DISCOUNT . " WHERE product_id = '{$product_id}' AND shop_id = '{$shop_id}' AND start_date = '{$start_date}' AND end_date = '{$end_date}'");
    if (mysqli_num_rows($result) > 0) {
        $qstatus = $conn->query("UPDATE " . T_PRODUCT_SPECIAL_DISCOUNT . " SET priority = '{$priority}', price = '{$price}', WHERE product_id = '{$product_id}' AND shop_id = '{$shop_id}' AND start_date = '{$start_date}' AND end_date = '{$end_date}'");
        $GLOBALS['queryerrormsg'] = mysqli_error($conn);
    } else {
        $qstatus = $conn->query("INSERT INTO " . T_PRODUCT_SPECIAL_DISCOUNT . " (product_id, shop_id, priority, price, start_date, end_date) VALUES('{$product_id}', '{$shop_id}', '{$priority}', '{$price}', '{$start_date}', '{$end_date}')");
        $GLOBALS['queryerrormsg'] = mysqli_error($conn);
    }

    return $qstatus;
}

function deleteProductSpecialDiscount($product_id, $shop_id, $special_discount = array()) {
    global $conn;
    $product_id = secure($product_id);
    $shop_id = secure($shop_id);

    $sql = "DELETE FROM " . T_PRODUCT_SPECIAL_DISCOUNT . " WHERE product_id = '{$product_id}' AND shop_id = '{$shop_id}'";
    if (isset($special_discount['start_date'])) {
        $start_date = secure($special_discount['start_date']);
        $sql .= "AND start_date = '{$start_date}'";
    }
    if (isset($special_discount['end_date'])) {
        $end_date = secure($special_discount['end_date']);
        $sql .= "AND end_date = '{$end_date}'";
    }

    $qstatus = $conn->query($sql);
    $GLOBALS['queryerrormsg'] = mysqli_error($conn);

    return $qstatus;
}

/* Product Discounts Functions End */

/* Product Downloads Functions Start */

function getProductDownloads($product_id) {
    global $conn;
    $product_id = secure($product_id);

    $sql = "SELECT * FROM " . T_PRODUCT_DOWNLOADS . " WHERE product_id = '{$product_id}'";

    $result = $conn->query($sql);

    $data = array();
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    return $data;
}

function addUpdateProductDownload($product_id, $download) {
    global $conn;
    $product_id = secure($product_id);
    $download_name = secure($download['name']);
    $file_path = secure($download['file_path']);
    $max_downloads_time = secure($download['max_downloads_time']);
    $validity_days = secure($download['validity_days']);

    $results = $conn->query("SELECT * FROM " . T_PRODUCT_DOWNLOADS . " WHERE product_id = '{$product_id}' AND download_name = '{$download_name}'");
    if ($results->num_rows > 0) {
        $qstatus = $conn->query("UPDATE " . T_PRODUCT_DOWNLOADS . " SET file_path = '{$file_path}', max_downloads_time = '{$max_downloads_time}', validity_days = '{$validity_days}' WHERE product_id = '{$product_id}' AND download_name = '{$download_name}',");
        $GLOBALS['queryerrormsg'] = $conn->error;
    } else {
        $qstatus = $conn->query("INSERT INTO " . T_PRODUCT_DOWNLOADS . " (product_id, download_name, file_path, max_downloads_time, validity_days) VALUES('{$product_id}', '{$download_name}', '{$file_path}', '{$max_downloads_time}', '{$validity_days}')");
        $GLOBALS['queryerrormsg'] = $conn->error;
    }

    return $qstatus;
}

function deleteProductDownload($product_id, $filters = array()) {
    global $conn;
    $product_id = secure($product_id);

    $sql = "DELETE FROM " . T_PRODUCT_DOWNLOADS . " WHERE product_id = '{$product_id}'";
    if (isset($filters['download_name'])) {
        $download_name = secure($filters['download_name']);
        $sql .= " AND download_name = '{$download_name}'";
    }

    $qstatus = $conn->query($sql);
    $GLOBALS['queryerrormsg'] = mysqli_error($conn);

    return $qstatus;
}

function uploadProductDownloads($fileElement) {
    if (empty($_FILES[$fileElement]['name'])) {
        $GLOBALS['uploaderrormsg'] = "File not selected";
        return false;
    }
    $allowed = 'jpg,png,jpeg,gif,pdf,doc,docx';
    $extension_allowed = explode(',', $allowed);
    $tmp = explode(".", $_FILES[$fileElement]["name"]);
    $file_extension = strtolower(end($tmp));
    if (!in_array($file_extension, $extension_allowed)) {
        $GLOBALS['uploaderrormsg'] = "File type not allowed";
        return false;
    }
    $dir = "uploads/productdownloads";
    $filename = $dir . '/download_' . generateKey() . '_' . date('d') . '_' . md5(time()) . '.' . $file_extension;
    if (move_uploaded_file($_FILES[$fileElement]["tmp_name"], $filename)) {
        return $filename;
    }
    $GLOBALS['uploaderrormsg'] = "Could not move file";
    return false;
}

/* Product Downloads Functions End */

function addAffiliate($affiliate) {
    global $conn;
    $fsellername = secure($affiliate['name']);
    $faffiliate_id = secure($affiliate['affiliate_id']);
    $ftracking_id = secure($affiliate['tracking_id']);
    $fsellerstatus = secure($affiliate['status']);

    $inserquery = "INSERT INTO " . T_AFFILIATE . " (name, affiliate_id, tracking_id, status) VALUES('{$fsellername}', '{$faffiliate_id}', '{$ftracking_id}', '{$fsellerstatus}')";
    if (!$conn->query($inserquery)) {
        $GLOBALS['queryerrormsg'] = "Please try again later";
        return false;
    }
    return true;
}

function updateAffiliate($affiliate) {
    global $conn;
    $fsellerid = secure($affiliate['id']);
    $fsellername = secure($affiliate['name']);
    $faffiliate_id = secure($affiliate['affiliate_id']);
    $ftracking_id = secure($affiliate['tracking_id']);
    $fsellerstatus = secure($affiliate['status']);

    $updatequery = "UPDATE " . T_AFFILIATE . " SET name = '{$fsellername}', affiliate_id = '{$faffiliate_id}', tracking_id = '{$ftracking_id}', status = '{$fsellerstatus}' WHERE id = '{$fsellerid}'";
    if (!$conn->query($updatequery)) {
        $GLOBALS['queryerrormsg'] = "Please try again later";
        return false;
    }
    return true;
}

function deleteAffiliate($id) {
    global $conn;
    $fid = secure($id);
    return $conn->query("DELETE FROM " . T_AFFILIATE . " WHERE id='{$fid}'");
}

function getAffiliates($pid = null) {
    global $conn;
    $data = array();
    if ($pid != null) {
        $pid = secure($pid);
        $result = $conn->query("SELECT seller.id, seller.name, seller.affiliate_id, seller.tracking_id, seller.status, pprice.url, pprice.price FROM " . T_AFFILIATE . " As seller LEFT JOIN " . T_PRODUCT_AFFILIATE_PRICE . " As pprice ON seller.id = pprice.seller_id WHERE pprice.product_id = '{$pid}' AND seller.status='1'");
    } else {
        $result = $conn->query("SELECT * FROM " . T_AFFILIATE);
    }
    while ($fetched_data = $result->fetch_assoc()) {
        $data[$fetched_data['id']] = $fetched_data;
    }

    return $data;
}

function isListerExists($username) {
    global $conn;
    $username = secure($username);
    $result = $conn->query("SELECT * FROM " . T_LISTER . " WHERE username='{$username}' OR email='{$username}'");
    if (mysqli_num_rows($result) > 0) {
        return true;
    }
    return false;
}

function addLister($lister) {
    global $conn;
    $fname = secure($lister['name']);
    $femail = secure($lister['email']);
    $fusername = secure($lister['username']);
    $fpassword = trim($lister['password']);
    $fmobile = secure($lister['mobile']);
    $fphone = secure($lister['phone']);
    $faddress = secure($lister['address']);
    $fcity = secure($lister['city']);
    $fstate = secure($lister['state']);
    $fpincode = secure($lister['pincode']);
    $fcountry = secure($lister['country']);
    $fwebsite = secure($lister['website']);
    $fstatus = secure($lister['status']);
    $lastlogin = $createdtime = $updatedtime = time();

    if (isListerExists($fusername)) {
        $GLOBALS['queryerrormsg'] = "Lister already exists";
        return false;
    }
    $insertquery = "INSERT INTO " . T_LISTER . " (name, email, username, password, mobile, phone, address, city, state, pincode, country, website, status, last_login_timestamp, created_timestamp, updated_timestamp) VALUES('{$fname}', '{$femail}', '{$fusername}', '{$fpassword}','{$fmobile}','{$fphone}','{$faddress}','{$fcity}','{$fstate}','{$fpincode}','{$fcountry}','{$fwebsite}','{$fstatus}','{$lastlogin}','{$createdtime}','{$updatedtime}')";
    if (!$conn->query($insertquery)) {
        $GLOBALS['queryerrormsg'] = mysqli_error($conn);
        return false;
    }
    return mysqli_insert_id($conn);
}

function updateLister($lister) {
    global $conn;
    $flisterid = secure($lister['id']);
    $fname = secure($lister['name']);
    $femail = secure($lister['email']);
    $fusername = secure($lister['username']);
    $fpassword = trim($lister['password']);
    $fmobile = secure($lister['mobile']);
    $fphone = secure($lister['phone']);
    $faddress = secure($lister['address']);
    $fcity = secure($lister['city']);
    $fstate = secure($lister['state']);
    $fpincode = secure($lister['pincode']);
    $fcountry = secure($lister['country']);
    $fwebsite = secure($lister['website']);
    $fstatus = secure($lister['status']);
    $updatetime = time();

    $updatequery = "UPDATE " . T_LISTER . " SET name = '{$fname}', email = '{$femail}', username = '{$fusername}', password = '{$fpassword}', mobile = '{$fmobile}', phone = '{$fphone}', address = '{$faddress}', city = '{$fcity}', state = '{$fstate}', pincode = '{$fpincode}', country = '{$fcountry}', website = '{$fwebsite}', status = '{$fstatus}', updated_timestamp = '{$updatetime}' WHERE id = '{$flisterid}'";
    if (!$conn->query($updatequery)) {
        $GLOBALS['queryerrormsg'] = "Please try again later";
        echo mysqli_error($conn);
        return false;
    }
    return true;
}

function deleteLister($id) {
    global $conn;
    $sid = secure($id);
    return $conn->query("UPDATE " . T_SELLER . " SET status = 'T' WHERE id='{$sid}'");
}

function getListers($pid = null, $status = null) {
    global $conn;
    $data = array();
    if ($pid != null) {
        $pid = secure($pid);
        $lresult = $conn->query("SELECT * FROM " . T_LISTER);
        while ($fetched_data = mysqli_fetch_assoc($lresult)) {
            $lpresult = $conn->query("SELECT * FROM " . T_PRODUCT_LISTER_PRICE . " WHERE product_id = '{$pid}' AND lister_id = '{$fetched_data['id']}' AND status = 'A'");
            if (mysqli_num_rows($lpresult) > 0) {
                while ($fetched_data2 = mysqli_fetch_assoc($lpresult)) {
                    $fetched_data['product_id'] = $fetched_data2['product_id'];
                    $fetched_data['url'] = $fetched_data2['url'];
                    $fetched_data['price'] = $fetched_data2['price'];
                    $fetched_data['listed_timestamp'] = $fetched_data2['listed_timestamp'];
                }
                $data[$fetched_data['id']] = $fetched_data;
            }
        }
        return $data;
        //$result = $conn->query("SELECT seller.id, seller.name, seller.email, seller.username, seller.password, seller.mobile, seller.phone, seller.address, seller.city, seller.state, seller.country, seller.status, seller.last_login_timestamp, seller.created_timestamp, seller.updated_timestamp, pprice.color, pprice.size, pprice.price, pprice.shipping, pprice.marketplace_fees, pprice.tax, pprice.selling_price, pprice.in_stock FROM " . T_SELLER . " As seller LEFT JOIN " . T_PRODUCT_SELLER_PRICE . " As pprice ON seller.id = pprice.seller_id WHERE pprice.product_id = '{$pid}' AND seller.status='".ACTIVE."'");
    } else {
        $result = $conn->query("SELECT * FROM " . T_LISTER);
    }

    $GLOBALS['queryerrormsg'] = mysqli_error($conn);
    while ($fetched_data = $result->fetch_assoc()) {
        $data[$fetched_data['id']] = $fetched_data;
    }

    return $data;
}

function getLister($lid) {
    global $conn;
    $data = null;
    $id = secure($lid);
    $result = $conn->query("SELECT * FROM " . T_LISTER . " WHERE id='{$id}'");

    while ($fetched_data = $result->fetch_assoc()) {
        $data = $fetched_data;
    }

    return $data;
}

function getListerId($username, $password) {
    global $conn;
    $data = null;
    $username = secure($username);
    $password = secure($password);
    $result = $conn->query("SELECT * FROM " . T_LISTER . " WHERE username='{$username}' AND password = '{$password}'");

    while ($fetched_data = $result->fetch_assoc()) {
        $data = $fetched_data['id'];
    }

    return $data;
}

function addListerPrice($productid, $listerid, $producturl, $price, $status) {
    global $conn;
    $productid = secure($productid);
    $listerid = secure($listerid);
    $producturl = secure($producturl);
    $price = secure($price);
    $listedtimestamp = time();

    $query = "INSERT INTO " . T_PRODUCT_LISTER_PRICE . " (product_id, lister_id, url, price, listed_timestamp, status) VALUES('{$productid}', '{$listerid}', '{$producturl}', '{$price}','{$listedtimestamp}','{$status}')";
    $searchresult = $conn->query("SELECT * FROM " . T_PRODUCT_LISTER_PRICE . " WHERE product_id = '{$productid}' AND lister_id = '{$listerid}'");
    if (mysqli_num_rows($searchresult) > 0) {
        $query = "UPDATE " . T_PRODUCT_LISTER_PRICE . " SET url = '{$producturl}', price = '{$price}' WHERE product_id = '{$productid}' AND lister_id = '{$listerid}'";
    }
    if (!$conn->query($query)) {
        $GLOBALS['queryerrormsg'] = mysqli_error($conn);
        return false;
    }
    return true;
}

function getListerPrice($listerid, $productid) {
    global $conn;
    $productid = secure($productid);
    $listerid = secure($listerid);

    $data = null;
    $result = $conn->query("SELECT * FROM " . T_PRODUCT_LISTER_PRICE . " WHERE product_id = '{$productid}' AND lister_id = '{$listerid}'");
    $GLOBALS['queryerrormsg'] = mysqli_error($conn);
    while ($fetched_data = $result->fetch_assoc()) {
        $data = $fetched_data;
    }

    return $data;
}

function incrementProductLikes($pid) {
    global $conn;
    $fpid = secure($pid);
    $presult = $conn->query("SELECT likes FROM " . T_PRODUCTS . " WHERE id='{$fpid}'");
    $row = mysqli_fetch_assoc($presult);
    $likes = $row['likes'] + 1;
    return $conn->query("UPDATE " . T_PRODUCTS . " SET likes='{$likes}' WHERE id='{$fpid}'");
}

function incrementProductViews($slug) {
    global $conn;
    $fslug = secure($slug);
    $presult = $conn->query("SELECT id, views FROM " . T_PRODUCTS . " WHERE slug='{$fslug}'");
    $row = mysqli_fetch_assoc($presult);
    $views = $row['views'] + 1;
    $pvresult = $conn->query("SELECT * FROM " . T_PRODUCT_VIEWS . " WHERE product_id='{$row['id']}' AND v_date = '" . date("Y-m-d") . "'");
    if (mysqli_num_rows($pvresult) > 0) {
        $pvrow = mysqli_fetch_assoc($pvresult);
        $vc = $pvrow['views'] + 1;
        $conn->query("UPDATE " . T_PRODUCT_VIEWS . " SET views = '{$vc}' WHERE id='{$pvrow['id']}'");
    } else {
        $conn->query("INSERT INTO " . T_PRODUCT_VIEWS . " (product_id, v_date, views) VALUES('{$row['id']}', '" . date("Y-m-d") . "', '1')");
    }
    return $conn->query("UPDATE " . T_PRODUCTS . " SET views='{$views}' WHERE slug='{$fslug}'");
}

function getProductViews($from, $to) {
    global $conn;
    $ffrom = secure($from);
    $fto = secure($to);
    $pvresult = $conn->query("SELECT * FROM " . T_PRODUCT_VIEWS . " WHERE v_date >= '{$ffrom}' AND v_date <= '{$fto}'");

    $pvarray = array();
    while ($row = mysqli_fetch_assoc($pvresult)) {
        $pvarray[] = $row;
    }
    return $pvarray;
}

function getProductCSVSample($catid) {
    global $conn;

    $pvresult = $conn->query("SELECT * FROM " . T_PRODUCTS . " WHERE v_date >= '{$ffrom}' AND v_date <= '{$fto}'");

    $pvarray = array();
    while ($row = mysqli_fetch_assoc($pvresult)) {
        $pvarray[] = $row;
    }
    return $pvarray;
}

/* Cart Functions Start */

function getCart($user_id, $columns = array()) {
    global $conn;
    $user_id = secure($user_id);
    $data = array(
        'user_id' => $user_id,
        'cart_details' => array('items' => array(), 'cart_total' => 0, 'tax_total' => 0, 'grand_total' => 0),
        'ip_address' => $_SERVER['REMOTE_ADDR'],
        'user_agent' => $_SERVER['HTTP_USER_AGENT'],
        'mail_datetime' => "0000-00-00 00:00:00",
        'mail_sent' => "N");
    $sql = "SELECT * FROM " . T_CART . " WHERE user_id = '{$user_id}'";
    if (!empty($columns) && is_array($columns)) {
        $sql = "SELECT `" . implode("`,`", $columns) . "` FROM " . T_CART . " WHERE user_id = '{$user_id}'";
    }
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
        $row['cart_details'] = unserialize($row['cart_details']);
        $data = $row;
    }
    return $data;
}

function updateCart($cart) {
    global $conn;
    $user_id = secure($cart['user_id']);
    $cart_details = serialize($cart['cart_details']);
    $ip_address = isset($cart['ip_address']) ? secure($cart['ip_address']) : $_SERVER['REMOTE_ADDR'];
    $user_agent = isset($cart['user_agent']) ? secure($cart['user_agent']) : $_SERVER['HTTP_USER_AGENT'];
    $mail_datetime = isset($cart['mail_datetime']) ? secure($cart['mail_datetime']) : date("Y-m-d H:i:s");
    $mail_sent = isset($cart['mail_sent']) ? secure($cart['mail_sent']) : "N";

    $results = $conn->query("SELECT * FROM " . T_CART . " WHERE user_id = '{$user_id}'");
    if ($results->num_rows > 0) {
        return $conn->query("UPDATE " . T_CART . " SET cart_details = '{$cart_details}', ip_address = '{$ip_address}', user_agent = '{$user_agent}', mail_datetime = '{$mail_datetime}', mail_sent = '{$mail_sent}' WHERE user_id = '{$user_id}'");
    } else {
        return $conn->query("INSERT INTO " . T_CART . " (user_id, cart_details, ip_address, user_agent, mail_datetime, mail_sent) VALUES('{$user_id}', '{$cart_details}', '{$ip_address}', '{$user_agent}', '{$mail_datetime}', '{$mail_sent}')");
    }
}

function syncCart($session_id, $user_id) {
    $cartold = getCart($session_id);
    $cartnew = getCart($user_id);
    $ipinfo = ipInfo($_SERVER['REMOTE_ADDR']);
    $country = getCountry($ipinfo['country_code'], array('id'));
    foreach ($cartold['cart_details']['items'] as $key => $item) {
        if (isset($cartnew['cart_details']['items'][$key])) {
            $tmpitem = $cartnew['cart_details']['items'][$key];
            $quantity = $tmpitem['quantity'] + $item['quantity'];
            $shipping = getProductShipping($tmpitem['product_id'], $tmpitem['shop_id'], $country['id']);
            if (empty($shipping)) {
                $shipping = getProductShipping($tmpitem['product_id'], $tmpitem['shop_id'], "0");
            }
            $shipping_charges = empty($shipping) ? 0 : ($shipping['charges'] + $shipping['additional_charges'] * ($quantity - 1));

            $tmpitem['quantity'] = $quantity;
            $tmpitem['shipping_charges'] = $shipping_charges;
            $tmpitem['total'] = $tmpitem['quantity'] * $tmpitem['amount'] + $shipping_charges;
            $cartnew['cart_details']['items'][$key] = $tmpitem;
        } else {
            $cartnew['cart_details']['items'][$key] = $item;
        }
    }
    updateCart($cartnew);
    /* Clearing Old Cart */
    $cartold['cart_details'] = array('items' => array(), 'cart_total' => 0, 'tax_total' => 0, 'shipping_total' => 0, 'grand_total' => 0);
    updateCart($cartold);
}

/* Cart Functions End */

/* Order Functions Start */

function isReferenceNumberExists($reference_number) {
    global $conn;
    $reference_number = secure($reference_number);
    $results = $conn->query("SELECT * FROM " . T_ORDERS . " WHERE reference_number = '{$reference_number}'");
    if ($reference_number == "" || $results->num_rows > 0) {
        return true;
    }
    return false;
}

function getReferenceNumber() {
    return generateKey(5, 5, false, true, true) . "-" . generateKey(5, 5, false, true, true) . "-" . generateKey(5, 5, false, true, true);
}

function isInvoiceNumberExists($invoice_number) {
    global $conn;
    $invoice_number = secure($invoice_number);
    $results = $conn->query("SELECT * FROM " . T_ORDERS . " WHERE invoice_number = '{$invoice_number}'");
    if ($invoice_number == "" || $results->num_rows > 0) {
        return true;
    }
    return false;
}

function getInvoiceNumber() {
    global $conn;
    $invoice_number = "";
    $results = $conn->query("SELECT LPAD(MID(COALESCE(MAX(invoice_number), 0), 8, 7) + 1, 7, '0') AS invoice_number FROM " . T_ORDERS);
    while ($row = $results->fetch_assoc()) {
        $invoice_number = $row['invoice_number'];
    }
    return date("ymd-" . $invoice_number);
}

function getOrders($columns = array(), $filters = array(), $offset = 0, $limit = 12, $order_by = 'added_timestamp', $order = 'DESC') {
    global $conn;
    $data = array();
    $sql = "SELECT * FROM " . T_ORDERS . " WHERE 1";
    if (!empty($columns) && is_array($columns)) {
        $sql = "SELECT `" . implode("`,`", $columns) . "` FROM " . T_ORDERS . " WHERE 1";
    }
    if (isset($filters['reference_number']) && trim($filters['reference_number']) <> "") {
        $reference_number = secure($filters['reference_number']);
        $sql .= " AND reference_number = '{$reference_number}'";
    }
    if (isset($filters['invoice_number']) && trim($filters['invoice_number']) <> "") {
        $invoice_number = secure($filters['invoice_number']);
        $sql .= " AND invoice_number = '{$invoice_number }'";
    }
    if (isset($filters['user_id']) && trim($filters['user_id']) <> "") {
        $user_id = secure($filters['user_id']);
        $sql .= " AND user_id = '{$user_id}'";
    }
    if (isset($filters['name']) && trim($filters['name']) <> "") {
        $name = secure($filters['name']);
        $sql .= " AND name = '{$name}'";
    }
    if (isset($filters['phone']) && trim($filters['phone']) <> "") {
        $phone = secure($filters['phone']);
        $sql .= " AND phone = '{$phone}'";
    }
    if (isset($filters['email']) && trim($filters['email']) <> "") {
        $email = secure($filters['email']);
        $sql .= " AND email = '{$email}'";
    }
    if (isset($filters['product_id']) && trim($filters['product_id']) <> "") {
        $product_id = secure($filters['product_id']);
        $sql .= " AND id IN (SELECT order_id FROM " . T_ORDERS_PRODUCTS . " WHERE product_id = '{$product_id}')";
    }
    if (isset($filters['shop_id']) && trim($filters['shop_id']) <> "") {
        $shop_id = secure($filters['shop_id']);
        $sql .= " AND id IN (SELECT order_id FROM " . T_ORDERS_PRODUCTS . " WHERE shop_id = '{$shop_id}')";
    }
    if (isset($filters['billing_name']) && trim($filters['billing_name']) <> "") {
        $billing_name = secure($filters['billing_name']);
        $sql .= " AND billing_name = '{$billing_name}'";
    }
    if (isset($filters['billing_mobile']) && trim($filters['billing_mobile']) <> "") {
        $billing_mobile = secure($filters['billing_mobile']);
        $sql .= " AND billing_mobile = '{$billing_mobile}'";
    }
    if (isset($filters['billing_phone']) && trim($filters['billing_phone']) <> "") {
        $billing_phone = secure($filters['billing_phone']);
        $sql .= " AND billing_phone = '{$billing_phone}'";
    }
    if (isset($filters['billing_email']) && trim($filters['billing_email']) <> "") {
        $billing_email = secure($filters['billing_email']);
        $sql .= " AND billing_email = '{$billing_email}'";
    }
    if (isset($filters['billing_address']) && trim($filters['billing_address']) <> "") {
        $billing_address = secure($filters['billing_address']);
        $sql .= " AND billing_address LIKE '%{$billing_address}%'";
    }
    if (isset($filters['billing_locality']) && trim($filters['billing_locality']) <> "") {
        $billing_locality = secure($filters['billing_locality']);
        $sql .= " AND billing_locality = '{$billing_locality}'";
    }
    if (isset($filters['billing_landmark']) && trim($filters['billing_landmark']) <> "") {
        $billing_landmark = secure($filters['billing_landmark']);
        $sql .= " AND billing_landmark = '{$billing_landmark}'";
    }
    if (isset($filters['billing_city']) && trim($filters['billing_city']) <> "") {
        $billing_city = secure($filters['billing_city']);
        $sql .= " AND billing_city = '{$billing_city}'";
    }
    if (isset($filters['billing_state']) && trim($filters['billing_state']) <> "") {
        $billing_state = secure($filters['billing_state']);
        $sql .= " AND billing_state = '{$billing_state}'";
    }
    if (isset($filters['billing_pincode']) && trim($filters['billing_pincode']) <> "") {
        $billing_pincode = secure($filters['billing_pincode']);
        $sql .= " AND billing_pincode = '{$billing_pincode}'";
    }
    if (isset($filters['billing_country']) && trim($filters['billing_country']) <> "") {
        $billing_country = secure($filters['billing_country']);
        $sql .= " AND billing_country = '{$billing_country}'";
    }
    if (isset($filters['billing_country_id']) && trim($filters['billing_country_id']) <> "") {
        $billing_country_id = secure($filters['billing_country_id']);
        $sql .= " AND billing_country_id = '{$billing_country_id}'";
    }
    if (isset($filters['billing_address_type']) && trim($filters['billing_address_type']) <> "") {
        $billing_address_type = secure($filters['billing_address_type']);
        $sql .= " AND billing_address_type = '{$billing_address_type}'";
    }
    if (isset($filters['shipping_name']) && trim($filters['shipping_name']) <> "") {
        $shipping_name = secure($filters['shipping_name']);
        $sql .= " AND shipping_name = '{$shipping_name}'";
    }
    if (isset($filters['shipping_mobile']) && trim($filters['shipping_mobile']) <> "") {
        $shipping_mobile = secure($filters['shipping_mobile']);
        $sql .= " AND shipping_mobile = '{$shipping_mobile}'";
    }
    if (isset($filters['shipping_phone']) && trim($filters['shipping_phone']) <> "") {
        $shipping_phone = secure($filters['shipping_phone']);
        $sql .= " AND shipping_phone = '{$shipping_phone}'";
    }
    if (isset($filters['shipping_email']) && trim($filters['shipping_email']) <> "") {
        $shipping_email = secure($filters['shipping_email']);
        $sql .= " AND shipping_email = '{$shipping_email}'";
    }
    if (isset($filters['shipping_address']) && trim($filters['shipping_address']) <> "") {
        $shipping_address = secure($filters['shipping_address']);
        $sql .= " AND shipping_address = '{$shipping_address}'";
    }
    if (isset($filters['shipping_locality']) && trim($filters['shipping_locality']) <> "") {
        $shipping_locality = secure($filters['shipping_locality']);
        $sql .= " AND shipping_locality = '{$shipping_locality}'";
    }
    if (isset($filters['shipping_landmark']) && trim($filters['shipping_landmark']) <> "") {
        $shipping_landmark = secure($filters['shipping_landmark']);
        $sql .= " AND shipping_landmark = '{$shipping_landmark}'";
    }
    if (isset($filters['shipping_city']) && trim($filters['shipping_city']) <> "") {
        $shipping_city = secure($filters['shipping_city']);
        $sql .= " AND shipping_city = '{$shipping_city}'";
    }
    if (isset($filters['shipping_state']) && trim($filters['shipping_state']) <> "") {
        $shipping_state = secure($filters['shipping_state']);
        $sql .= " AND shipping_state = '{$shipping_state}'";
    }
    if (isset($filters['shipping_pincode']) && trim($filters['shipping_pincode']) <> "") {
        $shipping_pincode = secure($filters['shipping_pincode']);
        $sql .= " AND shipping_pincode = '{$shipping_pincode}'";
    }
    if (isset($filters['shipping_country']) && trim($filters['shipping_country']) <> "") {
        $shipping_country = secure($filters['shipping_country']);
        $sql .= " AND shipping_country = '{$shipping_country}'";
    }
    if (isset($filters['shipping_country_id']) && trim($filters['shipping_country_id']) <> "") {
        $shipping_country_id = secure($filters['shipping_country_id']);
        $sql .= " AND shipping_country_id = '{$shipping_country_id}'";
    }
    if (isset($filters['shipping_address_type']) && trim($filters['shipping_address_type']) <> "") {
        $shipping_address_type = secure($filters['shipping_address_type']);
        $sql .= " AND shipping_address_type = '{$shipping_address_type}'";
    }
    if (isset($filters['shipping_method']) && trim($filters['shipping_method']) <> "") {
        $shipping_method = secure($filters['shipping_method']);
        $sql .= " AND shipping_method = '{$shipping_method}'";
    }
    if (isset($filters['shipping_required']) && trim($filters['shipping_required']) <> "") {
        $shipping_required = secure($filters['shipping_required']);
        $sql .= " AND shipping_required = '{$shipping_required}'";
    }
    if (isset($filters['coupon_code']) && trim($filters['coupon_code']) <> "") {
        $coupon_code = secure($filters['coupon_code']);
        $sql .= " AND coupon_code = '{$coupon_code}'";
    }
    if (isset($filters['discount_label']) && trim($filters['discount_label']) <> "") {
        $discount_label = secure($filters['discount_label']);
        $sql .= " AND discount_label = '{$discount_label}'";
    }
    if (isset($filters['wallet_used']) && trim($filters['wallet_used']) <> "") {
        $wallet_used = secure($filters['wallet_used']);
        $sql .= " AND wallet_used = '{$wallet_used}'";
    }
    if (isset($filters['payment_method']) && trim($filters['payment_method']) <> "") {
        $payment_method = secure($filters['payment_method']);
        $sql .= " AND payment_method = '{$payment_method}'";
    }
    if (isset($filters['payment_method_id']) && trim($filters['payment_method_id']) <> "") {
        $payment_method_id = secure($filters['payment_method_id']);
        $sql .= " AND payment_method_id = '{$payment_method_id}'";
    }
    if (isset($filters['language']) && trim($filters['language']) <> "") {
        $language = secure($filters['language']);
        $sql .= " AND language = '{$language}'";
    }
    if (isset($filters['customer_comments']) && trim($filters['customer_comments']) <> "") {
        $customer_comments = secure($filters['customer_comments']);
        $sql .= " AND customer_comments LIKE '%{$customer_comments}%'";
    }
    if (isset($filters['admin_comments']) && trim($filters['admin_comments']) <> "") {
        $admin_comments = secure($filters['admin_comments']);
        $sql .= " AND admin_comments = '%{$admin_comments}%'";
    }
    if (isset($filters['cancelled_by']) && trim($filters['cancelled_by']) <> "") {
        $cancelled_by = secure($filters['cancelled_by']);
        $sql .= " AND cancelled_by = '{$cancelled_by}'";
    }
    if (isset($filters['is_cod']) && trim($filters['is_cod']) <> "") {
        $is_cod = secure($filters['is_cod']);
        $sql .= " AND is_cod = '{$is_cod}'";
    }
    if (isset($filters['affiliate_id']) && trim($filters['affiliate_id']) <> "") {
        $affiliate_id = secure($filters['affiliate_id']);
        $sql .= " AND affiliate_id = '{$affiliate_id}'";
    }
    if (isset($filters['referrer_id']) && trim($filters['referrer_id']) <> "") {
        $referrer_id = secure($filters['referrer_id']);
        $sql .= " AND referrer_id = '{$referrer_id}'";
    }
    if (isset($filters['ip_address']) && trim($filters['ip_address']) <> "") {
        $ip_address = secure($filters['ip_address']);
        $sql .= " AND ip_address = '{$ip_address}'";
    }
    if (isset($filters['forwarded_ip_address']) && trim($filters['forwarded_ip_address']) <> "") {
        $forwarded_ip_address = secure($filters['forwarded_ip_address']);
        $sql .= " AND forwarded_ip_address = '{$forwarded_ip_address}'";
    }
    if (isset($filters['added_timestamp']) && trim($filters['added_timestamp']) <> "") {
        $added_timestamp = secure($filters['added_timestamp']);
        $sql .= " AND added_timestamp = '{$added_timestamp}'";
    }
    if (isset($filters['orderdate_from']) && trim($filters['orderdate_from']) <> "") {
        $orderdate_from = secure($filters['orderdate_from']);
        $sql .= " AND added_timestamp >= '{$orderdate_from}'";
    }
    if (isset($filters['orderdate_to']) && trim($filters['orderdate_to']) <> "") {
        $orderdate_to = secure($filters['orderdate_to']);
        $sql .= " AND added_timestamp <= '{$orderdate_to}'";
    }
    if (isset($filters['updated_timestamp']) && trim($filters['updated_timestamp']) <> "") {
        $updated_timestamp = secure($filters['updated_timestamp']);
        $sql .= " AND updated_timestamp = '{$updated_timestamp}'";
    }
    if (isset($filters['order_status']) && trim($filters['order_status']) <> "") {
        $order_status = secure($filters['order_status']);
        $sql .= " AND order_status = '{$order_status}'";
    }
    if (isset($filters['payment_status']) && trim($filters['payment_status']) <> "") {
        $payment_status = secure($filters['payment_status']);
        $sql .= " AND payment_status = '{$payment_status}'";
    }
    if (isset($filters['query']) && trim($filters['query']) <> "") {
        $q = secure($filters['query']);
        $sql .= " AND (reference_number LIKE '%{$q}%' OR invoice_number LIKE '%{$q}%' OR name LIKE '%{$q}%' OR phone LIKE '%{$q}%' OR email LIKE '%{$q}%'"
                . " OR billing_name LIKE '%{$q}%' OR billing_mobile LIKE '%{$q}%' OR billing_phone LIKE '%{$q}%' OR billing_email LIKE '%{$q}%'"
                . " OR billing_address LIKE '%{$q}%' OR billing_locality LIKE '%{$q}%' OR billing_landmark LIKE '%{$q}%' OR billing_city LIKE '%{$q}%'"
                . " OR billing_state LIKE '%{$q}%' OR billing_pincode LIKE '%{$q}%' OR billing_country LIKE '%{$q}%' OR shipping_name LIKE '%{$q}%'"
                . " OR shipping_mobile LIKE '%{$q}%' OR shipping_phone LIKE '%{$q}%' OR shipping_email LIKE '%{$q}%' OR shipping_address LIKE '%{$q}%'"
                . " OR shipping_locality LIKE '%{$q}%' OR shipping_landmark LIKE '%{$q}%' OR shipping_city LIKE '%{$q}%' OR shipping_state LIKE '%{$q}%'"
                . " OR shipping_pincode LIKE '%{$q}%' OR shipping_country LIKE '%{$q}%' OR shipping_method LIKE '%{$q}%' OR coupon_code LIKE '%{$q}%'"
                . " OR discount_label LIKE '%{$q}%' OR payment_method LIKE '%{$q}%' OR language LIKE '%{$q}%' OR customer_comments LIKE '%{$q}%'"
                . " OR admin_comments LIKE '%{$q}%' OR note LIKE '%{$q}%' OR additional_information LIKE '%{$q}%' OR ip_address LIKE '%{$q}%'"
                . " OR forwarded_ip_address LIKE '%{$q}%' OR user_agent LIKE '%{$q}%')";
    }
    $sql .= " ORDER BY {$order_by} {$order}";
    if ($limit != -1 && is_numeric($offset) && is_numeric($limit)) {
        $sql .= " LIMIT {$offset}, {$limit}";
    }
    $results = $conn->query($sql);
    $GLOBALS['queryerrormsg'] = $conn->error;
    while ($row = $results->fetch_assoc()) {
        if (isset($filters['with_products'])) {
            $row['products'] = getOrderProducts($filters['with_products'], array("order_id" => $row['id']));
        }
        $data[] = $row;
    }
    return $data;
}

function getOrder($id, $columns = array(), $with_products = false) {
    global $conn;
    $id = secure($id);
    $data = null;
    $sql = "SELECT * FROM " . T_ORDERS . " WHERE id = '{$id}'";
    if (!empty($columns) && is_array($columns)) {
        $sql = "SELECT `" . implode("`,`", $columns) . "` FROM " . T_ORDERS . " WHERE id = '{$id}'";
    }
    $results = $conn->query($sql);
    while ($row = $results->fetch_assoc()) {
        if ($with_products) {
            $row['products'] = getOrderProducts(array(), array("order_id" => $row['id']));
        }
        $data = $row;
    }
    return $data;
}

function addOrder($order) {
    global $conn;
    $reference_number = isset($order['reference_number']) ? secure($order['reference_number']) : "";
    $invoice_number = isset($order['invoice_number']) ? secure($order['invoice_number']) : "";
    $user_id = secure($order['user_id']);
    $name = secure($order['name']);
    $phone = secure($order['phone']);
    $email = secure($order['email']);
    $billing_name = secure($order['billing_name']);
    $billing_mobile = secure($order['billing_mobile']);
    $billing_phone = secure($order['billing_phone']);
    $billing_email = secure($order['billing_email']);
    $billing_address = secure($order['billing_address']);
    $billing_locality = secure($order['billing_locality']);
    $billing_landmark = secure($order['billing_landmark']);
    $billing_city = secure($order['billing_city']);
    $billing_state = secure($order['billing_state']);
    $billing_pincode = secure($order['billing_pincode']);
    $billing_country = secure($order['billing_country']);
    $billing_country_id = secure($order['billing_country_id']);
    $billing_address_type = secure($order['billing_address_type']);
    $shipping_name = secure($order['shipping_name']);
    $shipping_mobile = secure($order['shipping_mobile']);
    $shipping_phone = secure($order['shipping_phone']);
    $shipping_email = secure($order['shipping_email']);
    $shipping_address = secure($order['shipping_address']);
    $shipping_locality = secure($order['shipping_locality']);
    $shipping_landmark = secure($order['shipping_landmark']);
    $shipping_city = secure($order['shipping_city']);
    $shipping_state = secure($order['shipping_state']);
    $shipping_pincode = secure($order['shipping_pincode']);
    $shipping_country = secure($order['shipping_country']);
    $shipping_country_id = secure($order['shipping_country_id']);
    $shipping_address_type = secure($order['shipping_address_type']);
    $shipping_method = secure($order['shipping_method']);
    $shipping_required = secure($order['shipping_required']);
    $cart_quantity = secure($order['cart_quantity']);
    $cart_total = secure($order['cart_total']);
    $cgst = secure($order['cgst']);
    $sgst = secure($order['sgst']);
    $igst = secure($order['igst']);
    $vat_percentage = secure($order['vat_percentage']);
    $vat = secure($order['vat']);
    $tax_total = secure($order['tax_total']);
    $shipping_charges = secure($order['shipping_charges']);
    $pg_charges = secure($order['pg_charges']);
    $total_amount = secure($order['total_amount']);
    $coupon_code = secure($order['coupon_code']);
    $coupon_amount = secure($order['coupon_amount']);
    $discount_label = secure($order['discount_label']);
    $discount_amount = secure($order['discount_amount']);
    $payable_amount = secure($order['payable_amount']);
    $credits_used = secure($order['credits_used']);
    $paid_amount = secure($order['paid_amount']);
    $wallet_used = secure($order['wallet_used']);
    $payment_method = secure($order['payment_method']);
    $payment_method_id = secure($order['payment_method_id']);
    $site_commission = secure($order['site_commission']);
    $language = secure($order['language']);
    $customer_comments = secure($order['customer_comments']);
    $admin_comments = secure($order['admin_comments']);
    $cancelled_by = secure($order['cancelled_by']);
    $is_cod = secure($order['is_cod']);
    $note = secure($order['note']);
    $additional_information = mysqli_real_escape_string($conn, $order['additional_info']);
    $affiliate_id = secure($order['affiliate_id']);
    $affiliate_commission = secure($order['affiliate_commission']);
    $referrer_id = secure($order['referrer_id']);
    $referrer_reward_points = secure($order['referrer_reward_points']);
    $referee_reward_points = secure($order['referee_reward_points']);
    $ip_address = isset($order['ip_address']) ? $order['ip_address'] : $_SERVER['REMOTE_ADDR'];
    $forwarded_ip_address = isset($order['forwarded_ip_address']) ? $order['forwarded_ip_address'] : @$_SERVER['HTTP_X_FORWARDED_FOR'];
    $user_agent = isset($order['user_agent']) ? $order['user_agent'] : $_SERVER['HTTP_USER_AGENT'];
    $added_timestamp = isset($order['added_timestamp']) ? $order['added_timestamp'] : date("Y-m-d H:i:s");
    $updated_timestamp = isset($order['updated_timestamp']) ? $order['updated_timestamp'] : date("Y-m-d H:i:s");
    $order_status = isset($order['order_status']) ? $order['order_status'] : "1";
    $payment_status = isset($order['payment_status']) ? $order['payment_status'] : "1";

    $products = $order['products'];

    //checking/generating refrence number
    while (isReferenceNumberExists($reference_number)) {
        $reference_number = getReferenceNumber();
    }
    //checking/generating invoice number
    while (isInvoiceNumberExists($invoice_number)) {
        $invoice_number = getInvoiceNumber();
    }

    $sql = "INSERT INTO " . T_ORDERS . " (reference_number, invoice_number, user_id, name, phone, email, billing_name, billing_mobile, billing_phone, billing_email,"
            . " billing_address, billing_locality, billing_landmark, billing_city, billing_state, billing_pincode, billing_country, billing_country_id, billing_address_type,"
            . " shipping_name, shipping_mobile, shipping_phone, shipping_email, shipping_address, shipping_locality, shipping_landmark, shipping_city, shipping_state,"
            . " shipping_pincode, shipping_country, shipping_country_id, shipping_address_type, shipping_method, shipping_required, cart_quantity, cart_total, cgst, sgst, igst,"
            . " vat_percentage, vat, tax_total, shipping_charges, pg_charges, total_amount, coupon_code, coupon_amount, discount_label, discount_amount, payable_amount,"
            . " credits_used, paid_amount, wallet_used, payment_method, payment_method_id, site_commission, language, customer_comments, admin_comments, cancelled_by, is_cod,"
            . " note, additional_information, affiliate_id, affiliate_commission, referrer_id, referrer_reward_points, referee_reward_points, ip_address, forwarded_ip_address,"
            . " user_agent, added_timestamp, updated_timestamp, order_status, payment_status)"
            . " VALUES ('{$reference_number}', '{$invoice_number}', '{$user_id}', '{$name}', '{$phone}', '{$email}', '{$billing_name}', '{$billing_mobile}', '{$billing_phone}', '{$billing_email}',"
            . " '{$billing_address}', '$billing_locality', '{$billing_landmark}', '{$billing_city}', '{$billing_state}', '{$billing_pincode}', '{$billing_country}', '{$billing_country_id}', '{$billing_address_type}',"
            . " '{$shipping_name}', '{$shipping_mobile}', '{$shipping_phone}', '{$shipping_email}', '{$shipping_address}', '{$shipping_locality}', '{$shipping_landmark}', '{$shipping_city}', '{$shipping_state}',"
            . " '{$shipping_pincode}', '{$shipping_country}', '{$shipping_country_id}', '{$shipping_address_type}', '{$shipping_method}', '{$shipping_required}', '{$cart_quantity}', '{$cart_total}', '{$cgst}', '{$sgst}', '{$igst}',"
            . " '{$vat_percentage}', '{$vat}', '{$tax_total}', '{$shipping_charges}', '{$pg_charges}', '{$total_amount}', '{$coupon_code}', '{$coupon_amount}', '{$discount_label}', '{$discount_amount}', '{$payable_amount}',"
            . " '{$credits_used}', '{$paid_amount}', '{$wallet_used}', '{$payment_method}', '{$payment_method_id}', '{$site_commission}', '{$language}', '{$customer_comments}', '{$admin_comments}', '{$cancelled_by}', '{$is_cod}',"
            . " '{$note}', '{$additional_information}', '{$affiliate_id}', '{$affiliate_commission}', '{$referrer_id}', '{$referrer_reward_points}', '{$referee_reward_points}', '{$ip_address}', '{$forwarded_ip_address}',"
            . " '{$user_agent}', '{$added_timestamp}', '{$updated_timestamp}', '{$order_status}', '{$payment_status}')";

    $qstatus = $conn->query($sql);
    $GLOBALS['queryerrormsg'] = $conn->error;
    if ($qstatus) {
        $order_id = $qstatus = $conn->insert_id;
        $i = 1;
        foreach ($products as $product) {
            $product['order_id'] = $order_id;
            $product['invoice_number'] = $invoice_number . "-S" . str_pad($i++, 4, 0, STR_PAD_LEFT);
            $product['user_id'] = isset($product['user_id']) ? $product['user_id'] : $user_id;
            addOrderProduct($product);
        }
    }

    return $qstatus;
}

function updateOrder($order) {
    global $conn;
    //to be updated
}

function getOrderProducts($columns = array(), $filters = array(), $offset = 0, $limit = -1, $order_by = 'added_timestamp', $order = 'ASC') {
    global $conn;
    $data = array();
    $sql = "SELECT op.*, p.images, p.slug  FROM " . T_ORDERS_PRODUCTS . " AS op LEFT JOIN " . T_PRODUCTS . " AS p ON op.product_id = p.id WHERE 1";
    $sub_sql = "";
    if (!empty($columns) && is_array($columns) && in_array("images", $columns)) {
        $sub_sql .= ",p.images";
        if (($key = array_search("images", $columns)) !== false) {
            unset($columns[$key]);
        }
    }
    if (!empty($columns) && is_array($columns) && in_array("slug", $columns)) {
        $sub_sql .= ",p.slug";
        if (($key = array_search("slug", $columns)) !== false) {
            unset($columns[$key]);
        }
    }
    if (!empty($columns) && is_array($columns)) {
        $sql = "SELECT op." . implode(",op.", $columns) . "$sub_sql FROM " . T_ORDERS_PRODUCTS . " AS op LEFT JOIN " . T_PRODUCTS . " AS p ON op.product_id = p.id WHERE 1";
    }
    if (isset($filters['invoice_number ']) && trim($filters['invoice_number ']) <> "") {
        $invoice_number = secure($filters['invoice_number ']);
        $sql .= " AND op.invoice_number = '{$invoice_number }'";
    }
    if (isset($filters['product_id']) && trim($filters['product_id']) <> "") {
        $product_id = secure($filters['product_id']);
        $sql .= " AND op.product_id = '{$product_id}'";
    }
    if (isset($filters['shop_id']) && trim($filters['shop_id']) <> "") {
        $shop_id = secure($filters['shop_id']);
        $sql .= " AND op.shop_id = '{$shop_id}'";
    }
    if (isset($filters['order_id']) && trim($filters['order_id']) <> "") {
        $order_id = secure($filters['order_id']);
        $sql .= " AND op.order_id = '{$order_id}'";
    }
    if (isset($filters['user_id']) && trim($filters['user_id']) <> "") {
        $user_id = secure($filters['user_id']);
        $sql .= " AND op.user_id = '{$user_id}'";
    }
    if (isset($filters['product_type']) && trim($filters['product_type']) <> "") {
        $product_type = secure($filters['product_type']);
        $sql .= " AND op.product_type = '{$product_type}'";
    }
    if (isset($filters['product_sku']) && trim($filters['product_sku']) <> "") {
        $product_sku = secure($filters['product_sku']);
        $sql .= " AND op.product_sku = '{$product_sku}'";
    }
    if (isset($filters['product_name']) && trim($filters['product_name']) <> "") {
        $product_name = secure($filters['product_name']);
        $sql .= " AND op.product_name = '{$product_name}'";
    }
    if (isset($filters['product_brand']) && trim($filters['product_brand']) <> "") {
        $product_brand = secure($filters['product_brand']);
        $sql .= " AND op.product_brand = '{$product_brand}'";
    }
    if (isset($filters['product_model']) && trim($filters['product_model']) <> "") {
        $product_model = secure($filters['product_model']);
        $sql .= " AND op.product_model = '{$product_model}'";
    }
    if (isset($filters['shop_name']) && trim($filters['shop_name']) <> "") {
        $shop_name = secure($filters['shop_name']);
        $sql .= " AND op.shop_name = '{$shop_name}'";
    }
    if (isset($filters['shop_owner_name']) && trim($filters['shop_owner_name']) <> "") {
        $shop_owner_name = secure($filters['shop_owner_name']);
        $sql .= " AND op.shop_owner_name = '{$shop_owner_name}'";
    }
    if (isset($filters['shop_owner_username']) && trim($filters['shop_owner_username']) <> "") {
        $shop_owner_username = secure($filters['shop_owner_username']);
        $sql .= " AND op.shop_owner_username = '{$shop_owner_username}'";
    }
    if (isset($filters['shop_owner_email']) && trim($filters['shop_owner_email']) <> "") {
        $shop_owner_email = secure($filters['shop_owner_email']);
        $sql .= " AND op.shop_owner_email = '{$shop_owner_email}'";
    }
    if (isset($filters['shop_owner_phone']) && trim($filters['shop_owner_phone']) <> "") {
        $shop_owner_phone = secure($filters['shop_owner_phone']);
        $sql .= " AND op.shop_owner_phone = '{$shop_owner_phone}'";
    }
    if (isset($filters['sale_price']) && trim($filters['sale_price']) <> "") {
        $sale_price = secure($filters['sale_price']);
        $sql .= " AND op.sale_price = '{$sale_price}'";
    }
    if (isset($filters['discount']) && trim($filters['discount']) <> "") {
        $discount = secure($filters['discount']);
        $sql .= " AND op.discount = '{$discount}'";
    }
    if (isset($filters['price']) && trim($filters['price']) <> "") {
        $price = secure($filters['price']);
        $sql .= " AND op.price = '{$price}'";
    }
    if (isset($filters['commission_percent']) && trim($filters['commission_percent']) <> "") {
        $commission_percent = secure($filters['commission_percent']);
        $sql .= " AND op.commission_percent = '{$commission_percent}'";
    }
    if (isset($filters['commission']) && trim($filters['commission']) <> "") {
        $commission = secure($filters['commission']);
        $sql .= " AND op.commission = '{$commission}'";
    }
    if (isset($filters['shipping_free']) && trim($filters['shipping_free']) <> "") {
        $shipping_free = secure($filters['shipping_free']);
        $sql .= " AND op.shipping_free = '{$shipping_free}'";
    }
    if (isset($filters['shipping_required']) && trim($filters['shipping_required']) <> "") {
        $shipping_required = secure($filters['shipping_required']);
        $sql .= " AND op.shipping_required = '{$shipping_required}'";
    }
    if (isset($filters['shipping_id']) && trim($filters['shipping_id']) <> "") {
        $shipping_id = secure($filters['shipping_id']);
        $sql .= " AND op.shipping_id = '{$shipping_id}'";
    }
    if (isset($filters['shipping_days']) && trim($filters['shipping_days']) <> "") {
        $shipping_days = secure($filters['shipping_days']);
        $sql .= " AND op.shipping_days = '{$shipping_days}'";
    }
    if (isset($filters['shipping_company']) && trim($filters['shipping_company']) <> "") {
        $shipping_company = secure($filters['shipping_company']);
        $sql .= " AND op.shipping_company = '{$shipping_company}'";
    }
    if (isset($filters['shipping_label']) && trim($filters['shipping_label']) <> "") {
        $shipping_label = secure($filters['shipping_label']);
        $sql .= " AND op.shipping_label = '{$shipping_label}'";
    }
    if (isset($filters['tax_free']) && trim($filters['tax_free']) <> "") {
        $tax_free = secure($filters['tax_free']);
        $sql .= " AND op.tax_free = '{$tax_free}'";
    }
    if (isset($filters['is_cod']) && trim($filters['is_cod']) <> "") {
        $is_cod = secure($filters['is_cod']);
        $sql .= " AND op.is_cod = '{$is_cod}'";
    }
    if (isset($filters['affiliate_id']) && trim($filters['affiliate_id']) <> "") {
        $affiliate_id = secure($filters['affiliate_id']);
        $sql .= " AND op.affiliate_id = '{$affiliate_id}'";
    }
    if (isset($filters['ip_address']) && trim($filters['ip_address']) <> "") {
        $ip_address = secure($filters['ip_address']);
        $sql .= " AND op.ip_address = '{$ip_address}'";
    }
    if (isset($filters['added_timestamp']) && trim($filters['added_timestamp']) <> "") {
        $added_timestamp = secure($filters['added_timestamp']);
        $sql .= " AND op.added_timestamp = '{$added_timestamp}'";
    }
    if (isset($filters['updated_timestamp']) && trim($filters['updated_timestamp']) <> "") {
        $updated_timestamp = secure($filters['updated_timestamp']);
        $sql .= " AND op.updated_timestamp = '{$updated_timestamp}'";
    }
    if (isset($filters['status']) && trim($filters['status']) <> "") {
        $status = secure($filters['status']);
        $sql .= " AND op.status = '{$status}'";
    }
    if (isset($filters['query']) && trim($filters['query']) <> "") {
        $q = secure($filters['query']);
        $sql .= " AND (op.invoice_number LIKE '%{$q}%' OR op.product_sku LIKE '%{$q}%' OR op.product_name LIKE '%{$q}%' OR op.product_brand LIKE '%{$q}%'"
                . " OR op.product_model LIKE '%{$q}%' OR op.shop_name LIKE '%{$q}%' OR op.shop_owner_name LIKE '%{$q}%' OR op.shop_owner_username LIKE '%{$q}%'"
                . " OR op.shop_owner_email LIKE '%{$q}%' OR op.shop_owner_phone LIKE '%{$q}%' OR op.ip_address LIKE '%{$q}%' OR op.user_agent LIKE '%{$q}%')";
    }
    $sql .= " ORDER BY op.{$order_by} {$order}";
    if ($limit != -1 && is_numeric($offset) && is_numeric($limit)) {
        $sql .= " LIMIT {$offset}, {$limit}";
    }
    $results = $conn->query($sql);
    $GLOBALS['queryerrormsg'] = $conn->error;
    while ($row = $results->fetch_assoc()) {
        if (isset($row['images'])) {
            $images = explode(",", $row['images']);
            $row['first_image'] = isset($images[0]) ? $images[0] : "";
        }
        $data[] = $row;
    }
    return $data;
}

function addOrderProduct($product) {
    global $conn;
    $invoice_number = secure($product['invoice_number']);
    $product_id = secure($product['product_id']);
    $shop_id = secure($product['shop_id']);
    $order_id = secure($product['order_id']);
    $user_id = secure($product['user_id']);
    $product_type = secure($product['product_type']);
    $product_sku = secure($product['product_sku']);
    $product_name = secure($product['product_name']);
    $product_brand = secure($product['product_brand']);
    $product_model = secure($product['product_model']);
    $shop_name = secure($product['shop_name']);
    $shop_owner_name = secure($product['shop_owner_name']);
    $shop_owner_username = secure($product['shop_owner_username']);
    $shop_owner_email = secure($product['shop_owner_email']);
    $shop_owner_phone = secure($product['shop_owner_phone']);
    $sale_price = secure($product['sale_price']);
    $discount = secure($product['discount']);
    $price = secure($product['price']);
    $price_gst = secure($product['price_gst']);
    $commission_percent = secure($product['commission_percent']);
    $commission = secure($product['commission']);
    $commission_gst = secure($product['commission_gst']);
    $customization_string = mysqli_real_escape_string($conn, $product['customization_string']);
    $customization_price = secure($product['customization_price']);
    $customization_price_gst = secure($product['customization_price_gst']);
    $shipping_free = secure($product['shipping_free']);
    $shipping_required = secure($product['shipping_required']);
    $shipping_id = secure($product['shipping_id']);
    $shipping_days = secure($product['shipping_days']);
    $shipping_company = secure($product['shipping_company']);
    $shipping_label = secure($product['shipping_label']);
    $shipping_charges = secure($product['shipping_charges']);
    $shipped_datetime = secure($product['shipped_datetime']);
    $delivered_datetime = secure($product['delivered_datetime']);
    $completion_datetime = secure($product['completion_datetime']);
    $amount = secure($product['amount']);
    $quantity = secure($product['quantity']);
    $total = secure($product['total']);
    $tax_free = secure($product['tax_free']);
    $refund_quantity = secure($product['refund_quantity']);
    $refund_amount = secure($product['refund_amount']);
    $refund_amount_gst = secure($product['refund_amount_gst']);
    $refund_commission = secure($product['refund_commission']);
    $refund_commission_gst = secure($product['refund_commission_gst']);
    $refund_total = secure($product['refund_total']);
    $affiliate_commission_percentage = secure($product['affiliate_commission_percentage']);
    $affiliate_commission = secure($product['affiliate_commission']);
    $is_cod = secure($product['is_cod']);
    $note = secure($product['note']);
    $additional_information = mysqli_real_escape_string($conn, $product['additional_info']);
    $ip_address = isset($product['ip_address']) ? $product['ip_address'] : $_SERVER['REMOTE_ADDR'];
    $user_agent = isset($product['user_agent']) ? $product['user_agent'] : $_SERVER['HTTP_USER_AGENT'];
    $added_timestamp = isset($product['added_timestamp']) ? $product['added_timestamp'] : date("Y-m-d H:i:s");
    $updated_timestamp = isset($product['updated_timestamp']) ? $product['updated_timestamp'] : date("Y-m-d H:i:s");
    $status = isset($product['status']) ? $product['status'] : "1";

    $price_cgst = isset($product['price_cgst']) ? secure($product['price_cgst']) : "0.00";
    $price_sgst = isset($product['price_sgst']) ? secure($product['price_sgst']) : "0.00";
    $price_igst = isset($product['price_igst']) ? secure($product['price_igst']) : "0.00";
    $commission_cgst = isset($product['commission_cgst']) ? secure($product['commission_cgst']) : "0.00";
    $commission_sgst = isset($product['commission_sgst']) ? secure($product['commission_sgst']) : "0.00";
    $commission_igst = isset($product['commission_igst']) ? secure($product['commission_igst']) : "0.00";
    $customization_price_cgst = isset($product['customization_price_cgst']) ? secure($product['customization_price_cgst']) : "0.00";
    $customization_price_sgst = isset($product['customization_price_sgst']) ? secure($product['customization_price_sgst']) : "0.00";
    $customization_price_igst = isset($product['customization_price_igst']) ? secure($product['customization_price_igst']) : "0.00";
    $refund_amount_cgst = isset($product['refund_amount_cgst']) ? secure($product['refund_amount_cgst']) : "0.00";
    $refund_amount_sgst = isset($product['refund_amount_sgst']) ? secure($product['refund_amount_sgst']) : "0.00";
    $refund_amount_igst = isset($product['refund_amount_igst']) ? secure($product['refund_amount_igst']) : "0.00";
    $refund_commission_cgst = isset($product['refund_commission_cgst']) ? secure($product['refund_commission_cgst']) : "0.00";
    $refund_commission_sgst = isset($product['refund_commission_sgst']) ? secure($product['refund_commission_sgst']) : "0.00";
    $refund_commission_igst = isset($product['refund_commission_igst']) ? secure($product['refund_commission_igst']) : "0.00";

    $options = $product['options'];

    $sql = "INSERT INTO " . T_ORDERS_PRODUCTS . " (invoice_number, product_id, shop_id, order_id, user_id, product_type, product_sku, product_name,"
            . " product_brand, product_model, shop_name, shop_owner_name, shop_owner_username, shop_owner_email, shop_owner_phone, sale_price, discount, price, price_gst,"
            . " commission_percent, commission, commission_gst, customization_string, customization_price, customization_price_gst, shipping_free, shipping_required,"
            . " shipping_id, shipping_days, shipping_company, shipping_label, shipping_charges, shipped_datetime, delivered_datetime, completion_datetime,"
            . " amount, quantity, total, tax_free, refund_quantity, refund_amount, refund_amount_gst, refund_commission, refund_commission_gst, refund_total,"
            . " affiliate_commission_percentage, affiliate_commission, is_cod, note, additional_information, ip_address, user_agent, added_timestamp, updated_timestamp, status)"
            . " VALUES ('{$invoice_number}', '{$product_id}', '{$shop_id}', '{$order_id}', '{$user_id}', '{$product_type}', '{$product_sku}', '{$product_name}',"
            . " '{$product_brand}', '$product_model', '{$shop_name}', '{$shop_owner_name}', '{$shop_owner_username}', '{$shop_owner_email}', '{$shop_owner_phone}', '{$sale_price}', '{$discount}', '{$price}', '{$price_gst}',"
            . " '{$commission_percent}', '{$commission}', '{$commission_gst}', '{$customization_string}', '{$customization_price}', '{$customization_price_gst}', '{$shipping_free}', '{$shipping_required}',"
            . " '{$shipping_id}', '{$shipping_days}', '{$shipping_company}', '{$shipping_label}', '{$shipping_charges}', '{$shipped_datetime}', '{$delivered_datetime}', '{$completion_datetime}',"
            . " '{$amount}', '{$quantity}', '{$total}', '{$tax_free}', '{$refund_quantity}', '{$refund_amount}', '{$refund_amount_gst}', '{$refund_commission}', '{$refund_commission_gst}', '{$refund_total}',"
            . " '{$affiliate_commission_percentage}', '{$affiliate_commission}', '{$is_cod}', '{$note}', '{$additional_information}', '{$ip_address}', '{$user_agent}', '{$added_timestamp}', '{$updated_timestamp}', '{$status}')";
    $qstatus = $conn->query($sql);
    $GLOBALS['queryerrormsg'] = $conn->error;
    if ($qstatus) {
        $order_product_id = $conn->insert_id;
        /* inserting product gst breakups */
        $sql2 = "INSERT INTO " . T_ORDERS_PRODUCTS_GST . " (order_id, order_product_id, product_id, price_cgst, price_sgst, price_igst, commission_cgst, commission_sgst, commission_igst,"
                . " customization_price_cgst, customization_price_sgst, customization_price_igst, refund_amount_cgst, refund_amount_sgst, refund_amount_igst, refund_commission_cgst, refund_commission_sgst, refund_commission_igst)"
                . " VALUES ('{$order_id}','{$order_product_id}', '{$product_id}', '{$price_cgst}', '{$price_sgst}', '{$price_igst}', '{$commission_cgst}', '{$commission_sgst}', '{$commission_igst}',"
                . " '{$customization_price_cgst}', '{$customization_price_sgst}', '{$customization_price_igst}', '{$refund_amount_cgst}', '{$refund_amount_sgst}', '{$refund_amount_igst}', '{$refund_commission_cgst}', '{$refund_commission_sgst}', '{$refund_commission_igst}')";
        $conn->query($sql2);
        /* inserting product options */
        $sql3 = "INSERT INTO " . T_ORDERS_PRODUCTS_OPTIONS . " (order_id, order_product_id, product_id, product_option_id, product_option_value_id, option_name, option_value, option_type) VALUES";
        $values = array();
        foreach ($options as $option) {
            $product_option_id = secure($option['product_option_id']);
            $product_option_value_id = secure($option['product_option_value_id']);
            $option_name = secure($option['option_name']);
            $option_value = secure($option['option_value']);
            $option_type = secure($option['option_type']);
            $values[] = "('{$order_id}', '{$order_product_id}', '{$product_id}', '{$product_option_id}', '{$product_option_value_id}', '{$option_name}', '{$option_value}', '{$option_type}')";
        }
        $sql3 = $sql3 . implode(",", $values);
        $conn->query($sql3);
    }
    return $qstatus;
}

function updateOrderProduct($product) {
    global $conn;
    $id = secure($product['id']);
    $invoice_number = secure($product['invoice_number']);
    $product_id = secure($product['product_id']);
    $shop_id = secure($product['shop_id']);
    $order_id = secure($product['order_id']);
    $user_id = secure($product['user_id']);
    $product_type = secure($product['product_type']);
    $product_sku = secure($product['product_sku']);
    $product_name = secure($product['product_name']);
    $product_brand = secure($product['product_brand']);
    $product_model = secure($product['product_model']);
    $shop_name = secure($product['shop_name']);
    $shop_owner_name = secure($product['shop_owner_name']);
    $shop_owner_username = secure($product['shop_owner_username']);
    $shop_owner_email = secure($product['shop_owner_email']);
    $shop_owner_phone = secure($product['shop_owner_phone']);
    $sale_price = secure($product['sale_price']);
    $discount = secure($product['discount']);
    $price = secure($product['price']);
    $price_gst = secure($product['price_gst']);
    $commission_percent = secure($product['commission_percent']);
    $commission = secure($product['commission']);
    $commission_gst = secure($product['commission_gst']);
    $customization_string = mysqli_real_escape_string($conn, $product['customization_string']);
    $customization_price = secure($product['customization_price']);
    $customization_price_gst = secure($product['customization_price_gst']);
    $shipping_free = secure($product['shipping_free']);
    $shipping_required = secure($product['shipping_required']);
    $shipping_id = secure($product['shipping_id']);
    $shipping_days = secure($product['shipping_days']);
    $shipping_company = secure($product['shipping_company']);
    $shipping_label = secure($product['shipping_label']);
    $shipping_charges = secure($product['shipping_charges']);
    $shipped_datetime = secure($product['shipped_datetime']);
    $delivered_datetime = secure($product['delivered_datetime']);
    $completion_datetime = secure($product['completion_datetime']);
    $amount = secure($product['amount']);
    $quantity = secure($product['quantity']);
    $total = secure($product['total']);
    $tax_free = secure($product['tax_free']);
    $refund_quantity = secure($product['refund_quantity']);
    $refund_amount = secure($product['refund_amount']);
    $refund_amount_gst = secure($product['refund_amount_gst']);
    $refund_commission = secure($product['refund_commission']);
    $refund_commission_gst = secure($product['refund_commission_gst']);
    $refund_total = secure($product['refund_total']);
    $affiliate_commission_percentage = secure($product['affiliate_commission_percentage']);
    $affiliate_commission = secure($product['affiliate_commission']);
    $is_cod = secure($product['is_cod']);
    $note = secure($product['note']);
    $additional_information = mysqli_real_escape_string($conn, $product['additional_info']);
    $ip_address = isset($product['ip_address']) ? $product['ip_address'] : $_SERVER['REMOTE_ADDR'];
    $user_agent = isset($product['user_agent']) ? $product['user_agent'] : $_SERVER['HTTP_USER_AGENT'];
    $added_timestamp = isset($product['added_timestamp']) ? $product['added_timestamp'] : date("Y-m-d H:i:s");
    $updated_timestamp = isset($product['updated_timestamp']) ? $product['updated_timestamp'] : date("Y-m-d H:i:s");
    $status = isset($product['status']) ? $product['status'] : "1";

    $price_cgst = isset($product['price_cgst']) ? secure($product['price_cgst']) : "0.00";
    $price_sgst = isset($product['price_sgst']) ? secure($product['price_sgst']) : "0.00";
    $price_igst = isset($product['price_igst']) ? secure($product['price_igst']) : "0.00";
    $commission_cgst = isset($product['commission_cgst']) ? secure($product['commission_cgst']) : "0.00";
    $commission_sgst = isset($product['commission_sgst']) ? secure($product['commission_sgst']) : "0.00";
    $commission_igst = isset($product['commission_igst']) ? secure($product['commission_igst']) : "0.00";
    $customization_price_cgst = isset($product['customization_price_cgst']) ? secure($product['customization_price_cgst']) : "0.00";
    $customization_price_sgst = isset($product['customization_price_sgst']) ? secure($product['customization_price_sgst']) : "0.00";
    $customization_price_igst = isset($product['customization_price_igst']) ? secure($product['customization_price_igst']) : "0.00";
    $refund_amount_cgst = isset($product['refund_amount_cgst']) ? secure($product['refund_amount_cgst']) : "0.00";
    $refund_amount_sgst = isset($product['refund_amount_sgst']) ? secure($product['refund_amount_sgst']) : "0.00";
    $refund_amount_igst = isset($product['refund_amount_igst']) ? secure($product['refund_amount_igst']) : "0.00";
    $refund_commission_cgst = isset($product['refund_commission_cgst']) ? secure($product['refund_commission_cgst']) : "0.00";
    $refund_commission_sgst = isset($product['refund_commission_sgst']) ? secure($product['refund_commission_sgst']) : "0.00";
    $refund_commission_igst = isset($product['refund_commission_igst']) ? secure($product['refund_commission_igst']) : "0.00";

    $options = $product['options'];

    /*
     * prepared statement to update in future
      $stmt = $conn->prepare("UPDATE " . T_ORDERS_PRODUCTS . " SET invoice_number = ?, product_id = ? WHERE id = ?");
      $stmt->bind_param('sss', $invoice_number, $product_id, $id);
      $stmt->execute();
     */
    $sql = "UPDATE " . T_ORDERS_PRODUCTS . " SET invoice_number = '{$invoice_number}', product_id = '{$product_id}', shop_id = '{$shop_id}', order_id = '{$order_id}', user_id = '{$user_id}', product_type = '{$product_type}', product_sku = '{$product_sku}', product_name = '{$product_name}', "
            . " product_brand = '{$product_brand}', product_model = '{$product_model}', shop_name = '{$shop_name}', shop_owner_name = '{$shop_owner_name}', shop_owner_username = '{$shop_owner_username}', shop_owner_email = '{$shop_owner_email}', shop_owner_phone = '{$shop_owner_phone}',"
            . " sale_price = '{$sale_price}', discount = '$discount', price = '{$price}', price_gst = '{$price_gst}', commission_percent = '{$commission_percent}', commission = '{$commission}', commission_gst = '{$commission_gst}',"
            . " customization_string = '{$customization_string}', customization_price = '{$customization_price}', customization_price_gst = '{$customization_price_gst}', shipping_free = '{$shipping_free}', shipping_required = '{$shipping_required}',"
            . " shipping_id = '{$shipping_id}', shipping_days = '{$shipping_days}', shipping_company = '{$shipping_company}', shipping_label = '{$shipping_label}', shipping_charges = '{$shipping_charges}', shipped_datetime = '{$shipped_datetime}', delivered_datetime = '{$delivered_datetime}', completion_datetime = '{$completion_datetime}',"
            . " amount = '{$amount}', quantity = '{$quantity}', total = '{$total}', tax_free = '{$tax_free}', refund_quantity = '{$refund_quantity}', refund_amount = '{$refund_amount}', refund_amount_gst = '{$refund_amount_gst}', refund_commission = '{$refund_commission}', refund_commission_gst = '{$refund_commission_gst}', refund_total = '{$refund_total}',"
            . " affiliate_commission_percentage = '{$affiliate_commission_percentage}', affiliate_commission = '{$affiliate_commission}', is_cod = '{$is_cod}', note = '{$note}', additional_information = '{$additional_information}', ip_address = '{$ip_address}', user_agent = '{$user_agent}', added_timestamp = '{$added_timestamp}',"
            . " updated_timestamp = '{$updated_timestamp}', status = '{$status}' WHERE id = '{$id}'";

    $qstatus = $conn->query($sql);
    $GLOBALS['queryerrormsg'] = $conn->error;
    if ($qstatus) {
        /* updating product gst breakups */
        $sql2 = "UPDATE " . T_ORDERS_PRODUCTS_GST . " SET price_cgst = '{$price_cgst}', price_sgst = '{$price_sgst}', price_igst = '{$price_igst}', commission_cgst = '{$commission_cgst}', commission_sgst = '{$commission_sgst}', commission_igst = '{$commission_igst}', customization_price_cgst = '{$customization_price_cgst}', customization_price_sgst = '{$customization_price_sgst}', customization_price_igst = '{$customization_price_igst}',"
                . " refund_amount_cgst = '{$refund_amount_cgst}', refund_amount_sgst = '{$refund_amount_sgst}', refund_amount_igst = '{$refund_amount_igst}', refund_commission_cgst = '{$refund_commission_cgst}', refund_commission_sgst = '{$refund_commission_sgst}', refund_commission_igst = '{$refund_commission_igst}' WHERE order_id = '{$order_id}' AND order_product_id = '{$id}' AND product_id = '{$product_id}'";
        $conn->query($sql2);
        /* updating product options */
        $sql3 = "";
        foreach ($options as $option) {
            $oid = secure($option['id']);
            $product_option_id = secure($option['product_option_id']);
            $product_option_value_id = secure($option['product_option_value_id']);
            $option_name = secure($option['option_name']);
            $option_value = secure($option['option_value']);
            $option_type = secure($option['option_type']);
            $sql3 .= "UPDATE " . T_ORDERS_PRODUCTS_OPTIONS . " SET order_id = '{$order_id}', order_product_id = '{$id}', product_id = '{$product_id}', product_option_id = '{$product_option_id}', product_option_value_id = '{$product_option_value_id}', option_name = '{$option_name}', option_value = '{$option_value}', option_type = '{$option_type}' WHERE id = '{$oid}';";
        }
        $conn->multi_query($sql3);
    }
    return $qstatus;
}

function getOrderStatuses($columns = array(), $filters = array(), $offset = 0, $limit = -1, $order_by = 'added_datetime', $order = 'ASC') {
    global $conn;
    $data = array();
    $sql = "SELECT *  FROM " . T_ORDERS_STATUS_HISTORY . " WHERE 1";
    if (!empty($columns) && is_array($columns)) {
        $sql = "SELECT " . implode(",", $columns) . " FROM " . T_ORDERS_STATUS_HISTORY . " WHERE 1";
    }
    if (isset($filters['order_id']) && trim($filters['order_id']) <> "") {
        $order_id = secure($filters['order_id']);
        $sql .= " AND order_id = '{$order_id }'";
    }
    if (isset($filters['order_product_id']) && trim($filters['order_product_id']) <> "") {
        $order_product_id = secure($filters['order_product_id']);
        $sql .= " AND order_product_id = '{$order_product_id}'";
    }
    if (isset($filters['status']) && trim($filters['status']) <> "") {
        $status = secure($filters['status']);
        $sql .= " AND status = '{$status}'";
    }
    if (isset($filters['added_datetime']) && trim($filters['added_datetime']) <> "") {
        $added_datetime = secure($filters['added_datetime']);
        $sql .= " AND added_datetime = '{$added_datetime}'";
    }
    if (isset($filters['comments']) && trim($filters['comments']) <> "") {
        $comments = secure($filters['comments']);
        $sql .= " AND comments LIKE '%{$comments}%'";
    }
    if (isset($filters['tracking_number']) && trim($filters['tracking_number']) <> "") {
        $tracking_number = secure($filters['tracking_number']);
        $sql .= " AND tracking_number = '{$tracking_number}'";
    }
    if (isset($filters['append_comment']) && trim($filters['append_comment']) <> "") {
        $append_comment = secure($filters['append_comment']);
        $sql .= " AND append_comment = '{$append_comment}'";
    }
    if (isset($filters['payment_status']) && trim($filters['payment_status']) <> "") {
        $payment_status = secure($filters['payment_status']);
        $sql .= " AND payment_status = '{$payment_status}'";
    }
    if (isset($filters['customer_notified']) && trim($filters['customer_notified']) <> "") {
        $customer_notified = secure($filters['customer_notified']);
        $sql .= " AND customer_notified = '{$customer_notified}'";
    }
    if (isset($filters['query']) && trim($filters['query']) <> "") {
        $q = secure($filters['query']);
        $sql .= " AND (comments LIKE '%{$q}%' OR tracking_number LIKE '%{$q}%')";
    }
    $sql .= " ORDER BY {$order_by} {$order}";
    if ($limit != -1 && is_numeric($offset) && is_numeric($limit)) {
        $sql .= " LIMIT {$offset}, {$limit}";
    }

    $results = $conn->query($sql);
    $GLOBALS['queryerrormsg'] = $conn->error;
    while ($row = $results->fetch_assoc()) {
        $data[] = $row;
    }
    return $data;
}

function addOrderStatus($status) {
    global $conn;
    $order_id = secure($status['order_id']);
    $order_product_id = secure($status['order_product_id']);
    $ostatus = secure($status['status']);
    $added_datetime = isset($status['added_datetime']) ? $status['added_datetime'] : date("Y-m-d H:i:s");
    $comments = secure($status['comments']);
    $tracking_number = secure($status['tracking_number']);
    $append_comment = secure($status['append_comment']);
    $payment_status = secure($status['payment_status']);
    $customer_notified = secure($status['customer_notified']);

    $sql = "INSERT INTO " . T_ORDERS_STATUS_HISTORY . " (order_id, order_product_id, status, added_datetime, comments, tracking_number, append_comment, payment_status, customer_notified)"
            . " VALUES ('{$order_id}', '{$order_product_id}', '{$ostatus}', '{$added_datetime}', '{$comments}', '{$tracking_number}', '$append_comment', '{$payment_status}', '{$customer_notified}')";
    $qstatus = $conn->query($sql);
    $GLOBALS['queryerrormsg'] = $conn->error;
    return $qstatus;
}

function updateOrderStatus($status) {
    global $conn;
    $id = secure($status['id']);
    $order_id = secure($status['order_id']);
    $order_product_id = secure($status['order_product_id']);
    $ostatus = secure($status['status']);
    $added_datetime = isset($status['added_datetime']) ? $status['added_datetime'] : date("Y-m-d H:i:s");
    $comments = secure($status['comments']);
    $tracking_number = secure($status['tracking_number']);
    $append_comment = secure($status['append_comment']);
    $payment_status = secure($status['payment_status']);
    $customer_notified = secure($status['customer_notified']);

    $sql = "UPDATE " . T_ORDERS_STATUS_HISTORY . " SET order_id = '{$order_id}', order_product_id = '{$order_product_id}', status = '{$ostatus}', added_datetime = '{$added_datetime}', comments = '{$comments}', "
            . " tracking_number = '{$tracking_number}', append_comment = '{$append_comment}', payment_status = '{$payment_status}', customer_notified = '{$customer_notified}' WHERE id = '{$id}'";

    $qstatus = $conn->query($sql);
    $GLOBALS['queryerrormsg'] = $conn->error;
    return $qstatus;
}

function getOrderPayments($columns = array(), $filters = array(), $offset = 0, $limit = -1, $order_by = 'payment_datetime', $order = 'ASC') {
    global $conn;
    $data = array();
    $sql = "SELECT *  FROM " . T_ORDERS_PAYMENTS . " WHERE 1";
    if (!empty($columns) && is_array($columns)) {
        $sql = "SELECT " . implode(",", $columns) . " FROM " . T_ORDERS_PAYMENTS . " WHERE 1";
    }
    if (isset($filters['order_id']) && trim($filters['order_id']) <> "") {
        $order_id = secure($filters['order_id']);
        $sql .= " AND order_id = '{$order_id }'";
    }
    if (isset($filters['payment_method']) && trim($filters['payment_method']) <> "") {
        $payment_method = secure($filters['payment_method']);
        $sql .= " AND payment_method = '{$payment_method}'";
    }
    if (isset($filters['payment_method_key']) && trim($filters['payment_method_key']) <> "") {
        $payment_method_key = secure($filters['payment_method_key']);
        $sql .= " AND payment_method_key = '{$payment_method_key}'";
    }
    if (isset($filters['amount']) && trim($filters['amount']) <> "") {
        $amount = secure($filters['amount']);
        $sql .= " AND amount = '{$amount}'";
    }
    if (isset($filters['pg_txn_id']) && trim($filters['pg_txn_id']) <> "") {
        $pg_txn_id = secure($filters['pg_txn_id']);
        $sql .= " AND pg_txn_id = '{$pg_txn_id}'";
    }
    if (isset($filters['pg_response']) && trim($filters['pg_response']) <> "") {
        $pg_response = secure($filters['pg_response']);
        $sql .= " AND pg_response LIKE '%{$pg_response}%'";
    }
    if (isset($filters['pg_status']) && trim($filters['pg_status']) <> "") {
        $pg_status = secure($filters['pg_status']);
        $sql .= " AND pg_status = '{$pg_status}'";
    }
    if (isset($filters['comments']) && trim($filters['comments']) <> "") {
        $comments = secure($filters['comments']);
        $sql .= " AND comments LIKE '%{$comments}%'";
    }
    if (isset($filters['payment_datetime']) && trim($filters['payment_datetime']) <> "") {
        $payment_datetime = secure($filters['payment_datetime']);
        $sql .= " AND payment_datetime = '{$payment_datetime}'";
    }
    if (isset($filters['query']) && trim($filters['query']) <> "") {
        $q = secure($filters['query']);
        $sql .= " AND (pg_response LIKE '%{$q}%' OR comments LIKE '%{$q}%')";
    }
    $sql .= " ORDER BY {$order_by} {$order}";
    if ($limit != -1 && is_numeric($offset) && is_numeric($limit)) {
        $sql .= " LIMIT {$offset}, {$limit}";
    }
    $results = $conn->query($sql);
    $GLOBALS['queryerrormsg'] = $conn->error;
    while ($row = $results->fetch_assoc()) {
        $data[] = $row;
    }
    return $data;
}

function getOrderPayment($id, $columns = array()) {
    global $conn;
    $id = secure($id);
    $data = null;
    $sql = "SELECT * FROM " . T_ORDERS_PAYMENTS . " WHERE id = '{$id}'";
    if (!empty($columns) && is_array($columns)) {
        $sql = "SELECT `" . implode("`,`", $columns) . "` FROM " . T_ORDERS_PAYMENTS . " WHERE id = '{$id}'";
    }
    $results = $conn->query($sql);
    while ($row = $results->fetch_assoc()) {
        $data = $row;
    }
    return $data;
}

function addOrderPayment($payment) {
    global $conn;
    $order_id = secure($payment['order_id']);
    $payment_method = secure($payment['payment_method']);
    $payment_method_key = secure($payment['payment_method_key']);
    $amount = secure($payment['amount']);
    $pg_txn_id = secure($payment['pg_txn_id']);
    $pg_response = mysqli_real_escape_string($conn, $payment['pg_response']);
    $pg_status = secure($payment['pg_status']);
    $comments = secure($payment['comments']);
    $payment_datetime = isset($payment['payment_datetime']) ? secure($payment['payment_datetime']) : date("Y-m-d H:i:s");

    $sql = "INSERT INTO " . T_ORDERS_PAYMENTS . " (order_id, payment_method, payment_method_key, amount, pg_txn_id, pg_response, pg_status, comments, payment_datetime)"
            . " VALUES ('{$order_id}', '{$payment_method}', '{$payment_method_key}', '{$amount}', '{$pg_txn_id}', '{$pg_response}', '{$pg_status}', '{$comments}', '{$payment_datetime}')";

    if (!$conn->query($sql)) {
        $GLOBALS['queryerrormsg'] = $conn->error;
        return false;
    }
    return $conn->insert_id;
}

function updateOrderPayment($payment) {
    global $conn;
    $id = secure($payment['id']);
    $order_id = secure($payment['order_id']);
    $payment_method = secure($payment['payment_method']);
    $payment_method_key = secure($payment['payment_method_key']);
    $amount = secure($payment['amount']);
    $pg_txn_id = secure($payment['pg_txn_id']);
    $pg_response = mysqli_real_escape_string($conn, $payment['pg_response']);
    $pg_status = secure($payment['pg_status']);
    $comments = secure($payment['comments']);
    $payment_datetime = isset($payment['payment_datetime']) ? secure($payment['payment_datetime']) : date("Y-m-d H:i:s");

    $sql = "UPDATE " . T_ORDERS_PAYMENTS . " SET order_id = '{$order_id}', payment_method = '{$payment_method}', payment_method_key = '{$payment_method_key}', amount = '{$amount}', pg_txn_id = '{$pg_txn_id}', "
            . " pg_response = '{$pg_response}', pg_status = '{$pg_status}', comments = '{$comments}', payment_datetime = '{$payment_datetime}' WHERE id = '{$id}'";

    $qstatus = $conn->query($sql);
    $GLOBALS['queryerrormsg'] = $conn->error;
    return $qstatus;
}

function addOrderCancelRequest($request) {
    global $conn;
    $user_id = secure($request['user_id']);
    $order_id = secure($request['order_id']);
    $reason_id = secure($request['reason_id']);
    $message = secure($request['message']);
    $request_date = isset($request['request_date']) ? secure($request['request_date']) : date("Y-m-d H:i:s");
    $request_status = secure($request['request_status']);

    $sql = "INSERT INTO " . T_ORDERS_CANCEL_REQUESTS . " (user_id, order_id, reason_id, message, request_date, request_status)"
            . " VALUES ('{$user_id}', '{$order_id}', '{$reason_id}', '{$message}', '{$request_date}', '{$request_status}')";
    $qstatus = $conn->query($sql);
    $GLOBALS['queryerrormsg'] = $conn->error;
    return $qstatus;
}

function getOrderCancelRequest($request = array()) {
    global $conn;
    $order_id = isset($request['order_id']) ? secure($request['order_id']) : "";
    $user_id = isset($request['user_id']) ? secure($request['user_id']) : "";
    $reason_id = isset($request['reason_id']) ? secure($request['reason_id']) : "";
    $request_date = isset($request['request_date']) ? secure($request['request_date']) : "";
    $request_status = isset($request_status) ? secure($request['request_status']) : "";

    $sql = "SELECT * FROM " . T_ORDERS_CANCEL_REQUESTS . " WHERE 1";

    if ($order_id <> "") {
        $sql .= " AND order_id = '{$order_id}'";
    }
    if ($user_id <> "") {
        $sql .= " AND user_id = '{$user_id}'";
    }
    if ($reason_id <> "") {
        $sql .= " AND reason_id = '{$reason_id}'";
    }
    if ($request_date <> "") {
        $sql .= " AND request_date = '{$request_date}'";
    }
    if ($request_status <> "") {
        $sql .= " AND request_status = '{$request_status}'";
    }

    $result = $conn->query($sql);
    $GLOBALS['queryerrormsg'] = $conn->error;
    if (mysqli_num_rows($result) > 0) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    } else {
        return false;
    }
}

/* Order Functions End */

function addInSearch($keyword, $ip_address) {
    global $conn;
    $fkeyword = secure($keyword);
    $fip_address = secure($ip_address);
    $scresult = $conn->query("SELECT previous_search_counts FROM " . T_SEARCHES_HISTORY . " WHERE keyword = '{$fkeyword}'");
    $psc = mysqli_num_rows($scresult);
    return $conn->query("INSERT INTO " . T_SEARCHES_HISTORY . " (keyword, s_date, search_timestamp, ip_address, previous_search_counts) VALUES('{$fkeyword}', '" . date("Y-m-d") . "','" . time() . "', '{$fip_address}', '{$psc}')");
}

function getSuggestionsByQuery($query, $offset = 0, $limit = 10) {
    global $conn;
    $suggestions = array();
    $fquery = secure($query);
    $result = $conn->query("SELECT DISTINCT(keyword) FROM " . T_SEARCHES_HISTORY . " WHERE keyword LIKE '%$fquery%' LIMIT $limit OFFSET $offset");
    echo mysqli_error($conn);
    while ($row = $result->fetch_assoc()) {
        $suggestions[] = $row;
    }
    return $suggestions;
}

function getProductsSuggestionsByQuery($query, $offset = 0, $limit = 10, $order = 'DESC') {
    global $conn;
    $suggestions = array();
    $result = $conn->query("SELECT * FROM " . T_PRODUCTS . " WHERE name LIKE '%$query%' ORDER BY id $order LIMIT $limit OFFSET $offset");
    while ($row = $result->fetch_assoc()) {
        $suggestions[] = $row;
    }
    return $suggestions;
}

function getSearches($from, $to) {
    global $conn;
    $ffrom = secure($from);
    $fto = secure($to);
    $sresult = $conn->query("SELECT * FROM " . T_SEARCHES_HISTORY . " WHERE s_date >= '{$ffrom}' AND s_date <= '{$fto}'");
    $sarray = array();
    while ($row = mysqli_fetch_assoc($sresult)) {
        $sarray[] = $row;
    }
    return $sarray;
}

function generateWalletTransactionID() {
    global $conn;
    $postfix = "A";
    $txnid = TXNID_PREFIX . date("YmdHis");
    $result = $conn->query("SELECT * FROM " . T_WALLET . " WHERE txnid = '{$txnid}'");
    while (mysqli_num_rows($result) > 0) {
        $txnid = TXNID_PREFIX . date("YmdHis") . $postfix++;
        $result = $conn->query("SELECT * FROM " . T_WALLET . " WHERE txnid = '{$txnid}'");
    }
    return $txnid;
}

function addWalletTransaction($wallet) {
    global $conn;
    $txnid = generateWalletTransactionID();
    $userid = secure($wallet['userid']);
    $usertype = secure($wallet['usertype']);
    $credit = secure($wallet['credit']) + 0;
    $debit = secure($wallet['debit']) + 0;
    $balance = getWalletBalance($userid) + $credit - $debit;
    $description = secure($wallet['description']);
    $txn_timestamp = isset($wallet['txn_timestamp']) ? secure($wallet['txn_timestamp']) : time();
    $tstatus = isset($wallet['status']) ? secure($wallet['status']) : "C";

    $status = $conn->query("INSERT INTO " . T_WALLET . " (`txnid`, `userid`, `user_type`, `credit`, `debit`, `balance`, `description`, `txn_timestamp`, `status`) VALUES ('{$txnid}', '{$userid}', '{$usertype}', '{$credit}', '{$debit}', '{$balance}', '{$description}', '{$txn_timestamp}', '{$tstatus}')");
    $GLOBALS['queryerrormsg'] = mysqli_error($conn);
    return $status;
}

function getWalletTransactions($userid = null, $fromdate = null, $todate = null, $offset = 0, $limit = 10, $status = "C") {
    global $conn;
    $query = "SELECT * FROM " . T_WALLET . " WHERE 1";
    if ($userid != null) {
        $userid = secure($userid);
        $query .= " AND userid = '{$userid}'";
    }
    if ($fromdate != null) {
        $stimestamp = strtotime($fromdate);
        $query .= " AND txn_timestamp >= $stimestamp";
    }
    if ($todate != null) {
        $totimestamp = strtotime($todate . " 24:00:00");
        $query .= " AND txn_timestamp <= $totimestamp";
    }
    $query .= " AND status = '{$status}' ORDER BY id DESC LIMIT $offset, $limit";
    $data = array();
    $result = $conn->query($query);
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    return $data;
}

function getWalletBalance($userid, $status = "C") {
    global $conn;
    $userid = secure($userid);
    $balance = 0;
    $result = $conn->query("SELECT balance FROM " . T_WALLET . " WHERE userid = '{$userid}' AND status = '{$status}' ORDER BY id DESC LIMIT 1");
    while ($row = $result->fetch_assoc()) {
        $balance = $row['balance'];
    }
    return $balance;
}

/* Seller Functions Start
  function addSeller($seller) {
  global $conn;
  $fsellername = secure($seller['name']);
  $femail = secure($seller['email']);
  $fusername = secure($seller['username']);
  $fpassword = trim($seller['password']);
  $fmobile = secure($seller['mobile']);
  $fphone = secure($seller['phone']);
  $faddress = secure($seller['address']);
  $fcity = secure($seller['city']);
  $fstate = secure($seller['state']);
  $fcountry = secure($seller['country']);
  $fstatus = secure($seller['status']);
  $lastlogin = $createdtime = $updatedtime = time();

  $insertquery = "INSERT INTO " . T_SELLER . " (name, email, username, password, mobile, phone, address, city, state, country, status, last_login_timestamp, created_timestamp, updated_timestamp) VALUES('{$fsellername}', '{$femail}', '{$fusername}', '{$fpassword}','{$fmobile}','{$fphone}','{$faddress}','{$fcity}','{$fstate}','{$fcountry}','{$fstatus}','{$lastlogin}','$createdtime','{$updatedtime}')";
  if (!$conn->query($insertquery)) {
  $GLOBALS['queryerrormsg'] = mysqli_error($conn);
  return false;
  }
  return true;
  }

  function updateSeller($seller) {
  global $conn;
  $fsellerid = secure($seller['id']);
  $fsellername = secure($seller['name']);
  $femail = secure($seller['email']);
  $fusername = secure($seller['username']);
  $fpassword = trim($seller['password']);
  $fmobile = secure($seller['mobile']);
  $fphone = secure($seller['phone']);
  $faddress = secure($seller['address']);
  $fcity = secure($seller['city']);
  $fstate = secure($seller['state']);
  $fcountry = secure($seller['country']);
  $fstatus = secure($seller['status']);
  $updatetime = time();

  $updatequery = "UPDATE " . T_SELLER . " SET name = '{$fsellername}', email = '{$femail}', username = '{$fusername}', password = '{$fpassword}', mobile = '{$fmobile}', phone = '{$fphone}', address = '{$faddress}', city = '{$fcity}', state = '{$fstate}', country = '{$fcountry}', status = '{$fstatus}', updated_timestamp = '{$updatetime}' WHERE id = '{$fsellerid}'";
  if (!$conn->query($updatequery)) {
  $GLOBALS['queryerrormsg'] = "Please try again later";
  echo mysqli_error($conn);
  return false;
  }
  return true;
  }

  function deleteSeller($id) {
  global $conn;
  $sid = secure($id);
  return $conn->query("UPDATE " . T_SELLER . " set status = 'T' WHERE id='{$sid}'");
  }

  function getSellers($pid = null, $color = "NA", $size = "NA") {
  global $conn;
  $data = array();
  if ($pid != null) {
  $pid = secure($pid);
  $sresult = $conn->query("SELECT * FROM ".T_SELLER);
  while ($fetched_data = mysqli_fetch_assoc($sresult)) {
  $spresult = $conn->query("SELECT * FROM " . T_PRODUCT_SELLER_PRICE . " WHERE product_id = '{$pid}' AND seller_id = '{$fetched_data['id']}'");
  while($fetched_data2 = mysqli_fetch_assoc($spresult)) {
  $fetched_data['prices'][] = $fetched_data2;
  }
  $data[$fetched_data['id']] = $fetched_data;
  }
  return $data;
  //$result = $conn->query("SELECT seller.id, seller.name, seller.email, seller.username, seller.password, seller.mobile, seller.phone, seller.address, seller.city, seller.state, seller.country, seller.status, seller.last_login_timestamp, seller.created_timestamp, seller.updated_timestamp, pprice.color, pprice.size, pprice.price, pprice.shipping, pprice.marketplace_fees, pprice.tax, pprice.selling_price, pprice.in_stock FROM " . T_SELLER . " As seller LEFT JOIN " . T_PRODUCT_SELLER_PRICE . " As pprice ON seller.id = pprice.seller_id WHERE pprice.product_id = '{$pid}' AND seller.status='".ACTIVE."'");
  } else {
  $result = $conn->query("SELECT * FROM " . T_SELLER);
  }

  $GLOBALS['queryerrormsg'] = mysqli_error($conn);
  while ($fetched_data = $result->fetch_assoc()) {
  $data[$fetched_data['id']] = $fetched_data;
  }



  return $data;
  }

  function getSeller($sid) {
  global $conn;
  $data = null;
  $id = secure($sid);
  $result = $conn->query("SELECT * FROM " . T_SELLER . " WHERE id='{$id}'");

  while ($fetched_data = $result->fetch_assoc()) {
  $data = $fetched_data;
  }

  return $data;
  }

  Seller Functions End */

/* Shop Functions Start */

function isOwnerAlreadyHaveShop($owner_id) {
    global $conn;
    $owner_id = secure($owner_id);
    $results = $conn->query("SELECT * FROM " . T_SHOPS . " WHERE owner_id = '{$owner_id}'");
    if ($results->num_rows > 0) {
        return true;
    }
    return false;
}

function isShopNameExists($shopname) {
    global $conn;
    $shopname = secure($shopname);
    $results = $conn->query("SELECT * FROM " . T_SHOPS . " WHERE name = '{$shopname}'");
    if ($results->num_rows > 0) {
        return true;
    }
    return false;
}

function isShopUrlExists($url) {
    global $conn;
    $url = secure($url);
    $results = $conn->query("SELECT * FROM " . T_SHOPS . " WHERE url = '{$url}'");
    if ($results->num_rows > 0) {
        return true;
    }
    return false;
}

function getShops($columns = array(), $filters = array(), $offset = 0, $limit = 12, $order_by = 'id', $order = 'DESC') {
    global $conn;
    $data = array();
    $sql = "SELECT * FROM " . T_SHOPS . " WHERE status != 'T'";
    if (!empty($columns) && is_array($columns)) {
        $sql = "SELECT `" . implode("`,`", $columns) . "` FROM " . T_SHOPS . " WHERE status != 'T'";
    }
    if (isset($filters['owner_id']) && trim($filters['owner_id']) <> "") {
        $owner_id = secure($filters['owner_id']);
        $sql .= " AND owner_id = '{$owner_id}'";
    }
    if (isset($filters['name']) && trim($filters['name']) <> "") {
        $name = secure($filters['name']);
        $sql .= " AND name LIKE '%{$name}%'";
    }
    if (isset($filters['url']) && trim($filters['url']) <> "") {
        $url = secure($filters['url']);
        $sql .= " AND url = '{$url}'";
    }
    if (isset($filters['featured']) && trim($filters['featured']) <> "") {
        $featured = secure($filters['featured']);
        $sql .= " AND featured='{$featured}')";
    }
    if (isset($filters['cod_enabled']) && trim($filters['cod_enabled']) <> "") {
        $cod_enabled = secure($filters['cod_enabled']);
        $sql .= " AND cod_enabled='{$cod_enabled}')";
    }
    if (isset($filters['contact_person_name ']) && trim($filters['contact_person_name']) <> "") {
        $contact_person_name = secure($filters['contact_person_name']);
        $sql .= " AND contact_person_name='{$contact_person_name}')";
    }
    if (isset($filters['phone ']) && trim($filters['phone']) <> "") {
        $phone = secure($filters['phone']);
        $sql .= " AND phone='{$phone}')";
    }
    if (isset($filters['address1 ']) && trim($filters['address1']) <> "") {
        $address1 = secure($filters['address1']);
        $sql .= " AND address1 LIKE '%{$address1}%')";
    }
    if (isset($filters['address2 ']) && trim($filters['address2']) <> "") {
        $address2 = secure($filters['address2']);
        $sql .= " AND address2 LIKE '%{$address2}%')";
    }
    if (isset($filters['city ']) && trim($filters['city']) <> "") {
        $city = secure($filters['city']);
        $sql .= " AND city = '{$city}')";
    }
    if (isset($filters['state ']) && trim($filters['state']) <> "") {
        $state = secure($filters['state']);
        $sql .= " AND state = '{$state}')";
    }
    if (isset($filters['pincode ']) && trim($filters['pincode']) <> "") {
        $pincode = secure($filters['pincode']);
        $sql .= " AND pincode = '{$pincode}')";
    }
    if (isset($filters['country ']) && trim($filters['country']) <> "") {
        $country = secure($filters['country']);
        $sql .= " AND country = '{$country}')";
    }
    if (isset($filters['seller_information ']) && trim($filters['seller_information']) <> "") {
        $seller_information = secure($filters['seller_information']);
        $sql .= " AND seller_information LIKE '%{$seller_information}%')";
    }
    if (isset($filters['status']) && trim($filters['status']) <> "") {
        $status = secure($filters['status']);
        $sql .= " AND status = '{$status}'";
    }
    if (isset($filters['q']) && trim($filters['q']) <> "") {
        $q = secure($filters['q']);
        $sql .= " AND (name LIKE '%{$q}%' OR url LIKE '%{$q}%' OR description LIKE '%{$q}%' "
                . "OR contact_person_name LIKE '%{$q}%' OR address1 LIKE '%{$q}%' OR address2 LIKE '%{$q}%' "
                . "OR city LIKE '%{$q}%' OR state LIKE '%{$q}%' OR country LIKE '%{$q}%' OR seller_information LIKE '%{$q}%' "
                . "OR meta_title LIKE '%{$q}%' OR meta_keywords LIKE '%{$q}%' OR meta_description LIKE '%{$q}%')";
    }
    $sql .= " ORDER BY {$order_by} {$order}";
    if ($limit != -1 && is_numeric($offset) && is_numeric($limit)) {
        $sql .= " LIMIT {$offset}, {$limit}";
    }
    $results = $conn->query($sql);
    $GLOBALS['queryerrormsg'] = $conn->error;
    while ($row = $results->fetch_assoc()) {
        $data[$row['id']] = $row;
    }
    return $data;
}

function getShop($id, $columns = array()) {
    global $conn;
    $data = array();
    $id = secure($id);
    $sql = "SELECT * FROM " . T_SHOPS . " WHERE id = '{$id}'";
    if (!empty($columns) && is_array($columns)) {
        $sql = "SELECT `" . implode("`,`", $columns) . "` FROM " . T_SHOPS . " WHERE id = '{$id}'";
    }
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
        $data = $row;
    }
    return $data;
}

function addShop($shop) {
    global $conn;
    $GLOBALS['queryerrormsg'] = "";

    $owner_id = secure($shop['owner_id']);
    $shopname = secure($shop['name']);
    $url = url_slug(secure($shop['url']));
    $description = secure($shop['description']);
    $logo = secure($shop['logo']);
    $banner = secure($shop['banner']);
    $featured = secure($shop['featured']);
    $cod_enabled = secure($shop['cod_enabled']);
    $contact_person_name = secure($shop['contact_person_name']);
    $phone = secure($shop['phone']);
    $address1 = secure($shop['address1']);
    $address2 = secure($shop['address2']);
    $city = secure($shop['city']);
    $state = secure($shop['state']);
    $pincode = secure($shop['pincode']);
    $country = secure($shop['country']);
    $payment_policy = secure($shop['payment_policy']);
    $delivery_policy = secure($shop['delivery_policy']);
    $refund_policy = secure($shop['refund_policy']);
    $additional_information = trim($shop['additional_information']);
    $seller_information = secure($shop['seller_information']);
    $items_count = secure($shop['items_count']);
    $reviews_count = secure($shop['reviews_count']);
    $reports_count = secure($shop['reports_count']);
    $meta_title = secure($shop['meta_title']);
    $meta_keywords = secure($shop['meta_keywords']);
    $meta_description = secure($shop['meta_description']);
    $added_timestamp = isset($shop['added_timestamp']) ? secure($shop['added_timestamp']) : date("Y-m-d H:i:s");
    $updated_timestamp = isset($shop['updated_timestamp']) ? secure($shop['updated_timestamp']) : date("Y-m-d H:i:s");
    $status_message = isset($shop['status_message']) ? secure($shop['status_message']) : "";
    $status = isset($shop['status']) ? secure($shop['status']) : "A";

    $insertquery = "INSERT INTO " . T_SHOPS . " (`owner_id`, `name`, `url`, `description`, `logo`, `banner`, `featured`, `cod_enabled`, `contact_person_name`, `phone`, `address1`, `address2`, `city`, `state`, `pincode`, `country`, `payment_policy`, `delivery_policy`, `refund_policy`, `additional_information`, `seller_information`, `items_count`, `reviews_count`, `reports_count`, `meta_title`, `meta_keywords`, `meta_description`, `added_timestamp`, `updated_timestamp`, `status_message`, `status`) "
            . "VALUES ('{$owner_id}', '{$shopname}', '{$url}', '{$description}', '{$logo}', '{$banner}', '{$featured}', '{$cod_enabled}', '{$contact_person_name}', '{$phone}', '{$address1}', '{$address2}', '{$city}', '{$state}', '{$pincode}', '{$country}', '{$payment_policy}', '{$delivery_policy}', '{$refund_policy}', '{$additional_information}', '{$seller_information}', '{$items_count}', '{$reviews_count}', '{$reports_count}', '{$meta_title}', '{$meta_keywords}', '{$meta_description}', '{$added_timestamp}', '{$updated_timestamp}', '{$status_message}', '{$status}')";
    if (isOwnerAlreadyHaveShop($owner_id)) {
        $GLOBALS['queryerrormsg'] = "Owner already have shop!";
        return false;
    }
    if (isShopNameExists($shopname)) {
        $GLOBALS['queryerrormsg'] = "Shop name already exists!";
        return false;
    }
    if (isShopUrlExists($url)) {
        $GLOBALS['queryerrormsg'] = "Shop url already exists!";
        return false;
    }
    if (!$conn->query($insertquery)) {
        $GLOBALS['queryerrormsg'] = $conn->error;
        return false;
    }
    return $conn->insert_id;
}

function updateShop($shop) {
    global $conn;
    $GLOBALS['queryerrormsg'] = "";

    $shopid = secure($shop['id']);
    //$owner_id = secure($shop['owner_id']);
    $shopname = secure($shop['name']);
    $url = url_slug(secure($shop['url']));
    $description = secure($shop['description']);
    $logo = secure($shop['logo']);
    $banner = secure($shop['banner']);
    $featured = secure($shop['featured']);
    $cod_enabled = secure($shop['cod_enabled']);
    $contact_person_name = secure($shop['contact_person_name']);
    $phone = secure($shop['phone']);
    $address1 = secure($shop['address1']);
    $address2 = secure($shop['address2']);
    $city = secure($shop['city']);
    $state = secure($shop['state']);
    $pincode = secure($shop['pincode']);
    $country = secure($shop['country']);
    $payment_policy = secure($shop['payment_policy']);
    $delivery_policy = secure($shop['delivery_policy']);
    $refund_policy = secure($shop['refund_policy']);
    $additional_information = trim($shop['additional_information']);
    $seller_information = secure($shop['seller_information']);
    $items_count = secure($shop['items_count']);
    $reviews_count = secure($shop['reviews_count']);
    $reports_count = secure($shop['reports_count']);
    $meta_title = secure($shop['meta_title']);
    $meta_keywords = secure($shop['meta_keywords']);
    $meta_description = secure($shop['meta_description']);
    //$added_timestamp = isset($shop['added_timestamp']) ? secure($shop['added_timestamp']) : time();
    $updated_timestamp = isset($shop['updated_timestamp']) ? secure($shop['updated_timestamp']) : time();
    $status_message = isset($shop['status_message']) ? secure($shop['status_message']) : "";
    $status = isset($shop['status']) ? secure($shop['status']) : "A";

    $updatequery = "UPDATE " . T_SHOPS . " SET `name`='{$shopname}',`url`='{$url}',`description`='{$description}',"
            . "`logo`='{$logo}',`banner`='{$banner}',`featured`='{$featured}',`cod_enabled`='{$cod_enabled}',"
            . "`contact_person_name`='{$contact_person_name}',`phone`='{$phone}',`address1`='{$address1}',"
            . "`address2`='{$address2}',`city`='{$city}',`state`='{$state}',`pincode`='{$pincode}',`country`='{$country}',"
            . "`payment_policy`='{$payment_policy}',`delivery_policy`='{$delivery_policy}',`refund_policy`='{$refund_policy}',"
            . "`additional_information`='{$additional_information}',`seller_information`='{$seller_information}',"
            . "`items_count`='{$items_count}',`reviews_count`='{$reviews_count}',`reports_count`='{$reports_count}',"
            . "`meta_title`='{$meta_title}',`meta_keywords`='{$meta_keywords}',`meta_description`='{$meta_description}',"
            . "`updated_timestamp`='{$updated_timestamp}',`status_message`='{$status_message}',`status`='{$status}' WHERE `id` = '{$shopid}'";
    if (!$conn->query($updatequery)) {
        $GLOBALS['queryerrormsg'] = $conn->error;
        return false;
    }
    return true;
}

function uploadShopLogo($fileElement) {
    if (empty($_FILES[$fileElement]['name'])) {
        $GLOBALS['uploaderrormsg'] = "File not selected";
        return false;
    }
    $allowed = 'jpg,png,jpeg,gif';
    $extension_allowed = explode(',', $allowed);
    $tmp = explode(".", $_FILES[$fileElement]["name"]);
    $file_extension = strtolower(end($tmp));
    if (!in_array($file_extension, $extension_allowed)) {
        $GLOBALS['uploaderrormsg'] = "File type not allowed";
        return false;
    }
    $dir = "uploads/shopimages";
    $filename = $dir . '/shoplogo_' . generateKey() . '_' . date('d') . '_' . md5(time()) . '.' . $file_extension;
    if (move_uploaded_file($_FILES[$fileElement]["tmp_name"], $filename)) {
        return $filename;
    }
    $GLOBALS['uploaderrormsg'] = "Could not move file";
    return false;
}

function uploadShopBanner($fileElement) {
    if (empty($_FILES[$fileElement]['name'])) {
        $GLOBALS['uploaderrormsg'] = "File not selected";
        return false;
    }
    $allowed = 'jpg,png,jpeg,gif';
    $extension_allowed = explode(',', $allowed);
    $tmp = explode(".", $_FILES[$fileElement]["name"]);
    $file_extension = strtolower(end($tmp));
    if (!in_array($file_extension, $extension_allowed)) {
        $GLOBALS['uploaderrormsg'] = "File type not allowed";
        return false;
    }
    $dir = "uploads/shopimages";
    $filename = $dir . '/shopbanner_' . generateKey() . '_' . date('d') . '_' . md5(time()) . '.' . $file_extension;
    if (move_uploaded_file($_FILES[$fileElement]["tmp_name"], $filename)) {
        return $filename;
    }
    $GLOBALS['uploaderrormsg'] = "Could not move file";
    return false;
}

/* Shop Functions End */

/* HSN Code Functions Start */

function isHsnExists($hsn) {
    global $conn;
    $hsn = secure($hsn);
    $results = $conn->query("SELECT * FROM " . T_HSN_CODES . " WHERE code ='{$hsn}'");
    if ($results->num_rows > 0) {
        return true;
    }
    return false;
}

function getHSNCodes($columns = array(), $filters = array(), $offset = 0, $limit = 12, $order_by = 'code', $order = 'ASC') {
    global $conn;
    $data = array();
    $sql = "SELECT * FROM " . T_HSN_CODES . " WHERE 1 ";
    if (isset($filters['code']) && trim($filters['code']) <> "") {
        $code = trim($filters['code']);
        $sql .= " code = '{$code}'";
    }
    if (isset($filters['q']) && trim($filters['q']) <> "") {
        $q = Secure($filters['q']);
        $sql .= " AND (code LIKE '%{$q}%' OR description LIKE '%{$q}%')";
    }
    $sql .= " ORDER BY {$order_by} {$order}";
    if ($limit != -1 && is_numeric($offset) && is_numeric($limit)) {
        $sql .= " LIMIT {$offset}, {$limit}";
    }
    $result = $conn->query($sql);
    $GLOBALS['queryerrormsg'] = $conn->error;
    while ($row = $result->fetch_assoc()) {
        $data[$row['code']] = $row;
    }
    return $data;
}

function getHSNCode($id, $columns = array()) {
    global $conn;
    $data = array();
    $id = secure($id);
    $sql = "SELECT * FROM " . T_HSN_CODES . " WHERE id = '{$id}'";
    if (!empty($columns) && is_array($columns)) {
        $sql = "SELECT `" . implode("`,`", $columns) . "` FROM " . T_HSN_CODES . " WHERE id = '{$id}'";
    }
    $result = $conn->query($sql);
    $GLOBALS['queryerrormsg'] = $conn->error;
    while ($row = $result->fetch_assoc()) {
        $data = $row;
    }
    return $data;
}

function addHSNCode($hsn) {
    global $conn;
    $code = $hsn['code'];
    $tax_rate = $hsn['tax_rate'];
    $description = $hsn['description'];
    $added_timestamp = isset($hsn['added_timestamp']) ? $hsn['added_timestamp'] : time();
    $updated_timestamp = isset($hsn['updated_timestamp']) ? $hsn['updated_timestamp'] : time();

    if (trim($code) == '' && trim($tax_rate) == '') {
        $GLOBALS['queryerrormsg'] = "Some fields are blank";
        return false;
    }
    if (isHsnExists($code)) {
        $GLOBALS['queryerrormsg'] = "HSN or SAC Code already exists!";
        return false;
    }
    $qstatus = $conn->query("INSERT INTO " . T_HSN_CODES . " (code, tax_rate, description, added_timestamp, updated_timestamp) VALUES('{$code}', '{$tax_rate}', '{$description}', '{$added_timestamp}','{$updated_timestamp}')");
    $GLOBALS['queryerrormsg'] = "There is some problem! " . $conn->error;
    return $qstatus;
}

function updateHSNCode($hsn) {
    global $conn;
    $id = $hsn['id'];
    $code = $hsn['code'];
    $tax_rate = $hsn['tax_rate'];
    $description = $hsn['description'];
    $updated_timestamp = isset($hsn['updated_timestamp']) ? $hsn['updated_timestamp'] : time();

    $results = $conn->query("SELECT id FROM " . T_HSN_CODES . " WHERE id != '{$id}' AND code = '{$code}'");
    if ($results->num_rows > 0) {
        $GLOBALS['queryerrormsg'] = "HSN or SAC Code already exists!";
        return false;
    }
    $qstatus = $conn->query("UPDATE " . T_HSN_CODES . " SET code='{$code}', tax_rate='{$tax_rate}', description='{$description}', updated_timestamp='{$updated_timestamp}' WHERE id='{$id}'");
    $GLOBALS['queryerrormsg'] = "There is some problem! " . $conn->error;
    return $qstatus;
}

function deleteHSNCode($id) {
    global $conn;
    $id = secure($id);
    if ($conn->query("DELETE FROM " . T_HSN_CODES . " WHERE id='$id'")) {
        return true;
    }
}

/* HSN Code Functions End */

/* Brand Functions Start */

function isBrandNameExists($brandname) {
    global $conn;
    $brandname = secure($brandname);
    $result = $conn->query("SELECT * FROM " . T_BRANDS . " WHERE name = '{$brandname}'");
    if (mysqli_num_rows($result) > 0) {
        return true;
    }
    return false;
}

function isBrandUrlExists($slug) {
    global $conn;
    $slug = secure($slug);
    $result = $conn->query("SELECT * FROM " . T_BRANDS . " WHERE slug = '{$slug}'");
    if (mysqli_num_rows($result) > 0) {
        return true;
    }
    return false;
}

function getBrands($columns = array(), $filters = array(), $offset = 0, $limit = 12, $order_by = 'id', $order = 'DESC') {
    global $conn;
    $data = array();
    $sql = "SELECT * FROM " . T_BRANDS . " WHERE status != 'T'";
    if (!empty($columns) && is_array($columns)) {
        $sql = "SELECT `" . implode("`,`", $columns) . "` FROM " . T_BRANDS . " WHERE status != 'T'";
    }
    if (isset($filters['name']) && trim($filters['name']) <> "") {
        $name = secure($filters['name']);
        $sql .= " AND name LIKE '%{$name}%'";
    }
    if (isset($filters['status']) && trim($filters['status']) <> "") {
        $status = secure($filters['status']);
        $sql .= " AND status = '{$status}'";
    }
    if (isset($filters['q']) && trim($filters['q']) <> "") {
        $q = secure($filters['q']);
        $sql .= " AND (name LIKE '%{$q}%' OR description LIKE '%{$q}%' OR slug LIKE '%{$q}%' OR meta_title LIKE '%{$q}%' OR meta_keywords LIKE '%{$q}%' OR meta_description LIKE '%{$q}%')";
    }
    $sql .= " ORDER BY {$order_by} {$order}";
    if ($limit != -1 && is_numeric($offset) && is_numeric($limit)) {
        $sql .= " LIMIT {$offset}, {$limit}";
    }
    $results = $conn->query($sql);
    while ($row = $results->fetch_assoc()) {
        $data[] = $row;
    }
    return $data;
}

function getBrand($id, $columns = array()) {
    global $conn;
    $data = array();
    $id = secure($id);
    $sql = "SELECT * FROM " . T_BRANDS . " WHERE id = '{$id}'";
    if (!empty($columns) && is_array($columns)) {
        $sql = "SELECT `" . implode("`,`", $columns) . "` FROM " . T_BRANDS . " WHERE id = '{$id}'";
    }
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
        $data = $row;
    }
    return $data;
}

function addBrand($brand) {
    global $conn;
    $GLOBALS['queryerrormsg'] = "";

    $name = secure($brand['name']);
    $slug = url_slug(secure($brand['slug']));
    $description = secure($brand['description']);
    $image = secure($brand['image']);
    $items_count = secure($brand['items_count']);
    $meta_title = secure($brand['meta_title']);
    $meta_keywords = secure($brand['meta_keywords']);
    $meta_description = secure($brand['meta_description']);
    $status = isset($brand['status']) ? secure($brand['status']) : "A";

    $sql = "INSERT INTO " . T_BRANDS . " (`name`, `slug`, `description`, `image`, `items_count`, `meta_title`, `meta_keywords`, `meta_description`, `status`) VALUES ('{$name}', '{$slug}', '{$description}', '{$image}', '{$items_count}', '{$meta_title}', '{$meta_keywords}', '{$meta_description}', '{$status}')";
    if (isBrandNameExists($name)) {
        $GLOBALS['queryerrormsg'] = "Brand name already exists!";
        return false;
    }
    if (isBrandUrlExists($slug)) {
        $GLOBALS['queryerrormsg'] = "Brand url already exists!";
        return false;
    }
    if (!$conn->query($sql)) {
        $GLOBALS['queryerrormsg'] = $conn->error;
        return false;
    }
    return true;
}

function updateBrand($brand) {
    global $conn;
    $GLOBALS['queryerrormsg'] = "";

    $id = secure($brand['id']);
    $name = secure($brand['name']);
    $slug = url_slug(secure($brand['slug']));
    $description = secure($brand['description']);
    $image = secure($brand['image']);
    $items_count = secure($brand['items_count']);
    $meta_title = secure($brand['meta_title']);
    $meta_keywords = secure($brand['meta_keywords']);
    $meta_description = secure($brand['meta_description']);
    $status = isset($brand['status']) ? secure($brand['status']) : "A";

    $updatequery = "UPDATE " . T_BRANDS . " SET `name`='{$name}',`slug`='{$slug}',`description`='{$description}',`image`='{$image}',`items_count`='{$items_count}',`meta_title`='{$meta_title}',`meta_keywords`='{$meta_keywords}',`meta_description`='{$meta_description}',`status`='{$status}' WHERE `id` = '{$id}'";
    if (!$conn->query($updatequery)) {
        $GLOBALS['queryerrormsg'] = mysqli_error($conn);
        return false;
    }
    return true;
}

function deleteBrand($id) {
    global $conn;
    $id = secure($id);
    return $conn->query("DELETE FROM " . T_BRANDS . " WHERE id='{$id}'");
}

function uploadBrandImage($fileElement) {
    if (empty($_FILES[$fileElement]['name'])) {
        $GLOBALS['uploaderrormsg'] = "File not selected";
        return false;
    }
    $allowed = 'jpg,png,jpeg,gif';
    $extension_allowed = explode(',', $allowed);
    $tmp = explode(".", $_FILES[$fileElement]["name"]);
    $file_extension = strtolower(end($tmp));
    if (!in_array($file_extension, $extension_allowed)) {
        $GLOBALS['uploaderrormsg'] = "File type not allowed";
        return false;
    }
    $dir = "uploads/brandimages";
    $filename = $dir . '/brandimage_' . generateKey() . '_' . date('d') . '_' . md5(time()) . '.' . $file_extension;
    if (move_uploaded_file($_FILES[$fileElement]["tmp_name"], $filename)) {
        return $filename;
    }
    $GLOBALS['uploaderrormsg'] = "Could not move file";
    return false;
}

/* Brand Functions End */

/* ======================  ======================= */

function getConfig($key = '') {
    global $conn;
    $data = array();
    $sql = "SELECT * FROM " . T_CONFIG;
    if (!empty($key)) {
        $key = secure($key);
        $sql .= " WHERE option_name = '{$key}'";
    }
    $results = $conn->query($sql);
    while ($row = $results->fetch_assoc()) {
        $data[$row['option_name']] = $row['option_value'];
    }
    if (!empty($key)) {
        if (isset($data[$key])) {
            return $data[$key];
        }
        return null;
    }
    return $data;
}

function saveAllConfig($config) {
    global $conn;
    $qstatus = FALSE;
    foreach ($config as $option_name => $option_value) {
        $option_name = secure($option_name);
        $option_value = mysqli_real_escape_string($conn, $option_value);
        $autoload = "yes";
        $results = $conn->query("SELECT * FROM " . T_CONFIG . " WHERE option_name = '{$option_name}'");
        if ($results->num_rows > 0) {
            $qstatus = $conn->query("UPDATE " . T_CONFIG . " SET option_value = '{$option_value}', autoload = '{$autoload}' WHERE option_name = '{$option_name}'");
        } else {
            $qstatus = $conn->query("INSERT INTO " . T_CONFIG . " (option_name, option_value, autoload) VALUES('{$option_name}', '{$option_value}', '{$autoload}')");
        }
        if (!$qstatus) {
            break;
        }
    }
    return $qstatus;
}

function saveConfig($option_name, $option_value, $autoload = "yes") {
    global $conn;
    $option_name = secure($option_name);
    $option_value = mysqli_real_escape_string($conn, $option_value);
    $autoload = secure($autoload);

    $results = $conn->query("SELECT * FROM " . T_CONFIG . " WHERE option_name = '{$option_name}'");
    if ($results->num_rows > 0) {
        $sql = "UPDATE " . T_CONFIG . " SET option_value = '{$option_value}' WHERE option_name = '{$option_name}'";
    } else {
        $sql = "INSERT INTO " . T_CONFIG . " (option_name, option_value, autoload) VALUES('{$option_name}', '{$option_value}', '{$autoload}')";
    }

    $qstatus = $conn->query($sql);
    return $qstatus;
}

function getLanguages() {
    $data = array();
    $dir = scandir(dirname(dirname(__FILE__)) . '/languages');
    $languages_name = array_diff($dir, array(
        ".",
        "..",
        "error_log",
        "index.html",
        ".htaccess",
        "_notes"
    ));
    return $languages_name;
}

function upload($media) {
    global $sys;
    $last_data = array("error" => true);
    $upload_root = $sys['upload_root'];

    if (!file_exists($upload_root . '/' . date('Y'))) {
        @mkdir($upload_root . '/' . date('Y'), 0777, true);
    }
    if (!file_exists($upload_root . '/' . date('Y') . '/' . date('m'))) {
        @mkdir($upload_root . '/' . date('Y') . '/' . date('m'), 0777, true);
    }
    //upload and cropping logic goes here
    if (empty($media)) {
        return false;
    }

    $allowedExtensions = isset($sys['allowedExtensions']) ? explode(',', $sys['allowedExtensions']) : array('jpg', 'jpeg', 'png', 'gif', 'pdf', 'mp3', 'mp4', 'webm', 'flv', 'wav');
    $allowedMimeTypes = array();//will be used later
    $allowedFileSize = isset($sys['maxUpload']) ? returnBytes($sys['maxUpload']) : min(returnBytes(ini_get('upload_max_filesize')), returnBytes(ini_get('post_max_size')), returnBytes(ini_get('memory_limit')));

    $file_name = pathinfo($media['name'], PATHINFO_FILENAME);
    $file_extension = strtolower(pathinfo($media['name'], PATHINFO_EXTENSION));
    if (!in_array($file_extension, $allowedExtensions)) {
        $last_data['errormsg'] = "File Type Not Allowed";
        return $last_data;
    }
    
    if ($media['size'] > $allowedFileSize) {
        $last_data['errormsg'] = "File Size Greater Than Max Upload Size";
        return $last_data;
    }

    $dir = date('Y') . '/' . date('m');
    $file_path = $dir . '/' . $media['name'];
    $i = 1;
    while (file_exists($upload_root . "/" . $file_path)) {
        $file_name = $file_name . "_" . $i++;
        $file_path = $dir . "/" . $file_name . "." . $file_extension;
    }
    if (move_uploaded_file($media['tmp_name'], $upload_root . "/" . $file_path)) {
        
        $last_data['upload_root'] = $upload_root;
        $last_data['file_name'] = $file_name;
        $last_data['file_extension'] = $file_extension;
        $last_data['file_path'] = $file_path;
        $last_data['file_type'] = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $upload_root . "/" . $file_path);
        $last_data['file_size'] = $media['size'];
        
        list($width, $height) = @getimagesize(str_replace(' ', '%20', $upload_root . "/" . $file_path));
        $file_metadata['width'] = $width;
        $file_metadata['height'] = $height;
        $file_metadata['file'] = $file_path;
        
        if (in_array($file_extension, array('jpg', 'jpeg', 'png', 'gif'))) {
            $tmp_path = $upload_root . "/" . $file_path;
            @compressImage($tmp_path, $tmp_path, 60);
            //creating copies of different sizes
            $tmp_file_path = explode('.', $file_path);
            $ext = end($tmp_file_path);
            $file = array_shift($tmp_file_path);

            $thumnail_size_w = isset($sys['thumbnail_size_w']) && trim($sys['thumbnail_size_w']) <> "" ? $sys['thumbnail_size_w'] : 150;
            $thumnail_size_h = isset($sys['thumbnail_size_h']) && trim($sys['thumbnail_size_h']) <> "" ? $sys['thumbnail_size_h'] : 150;
            $thumbnail_file = $file . '-' . $thumnail_size_w . 'x' . $thumnail_size_h . '.' . $ext;
            resizeCropImage($thumnail_size_w, $thumnail_size_h, $upload_root . "/" . $file_path, $upload_root . "/" . $thumbnail_file, 100);

            $medium_size_w = isset($sys['medium_size_w']) && trim($sys['medium_size_w']) <> "" ? $sys['medium_size_w'] : 300;
            $medium_size_h = isset($sys['medium_size_h']) && trim($sys['medium_size_h']) <> "" ? $sys['medium_size_h'] : 300;
            $medium_file = $file . '-' . $medium_size_w . 'x' . $medium_size_h . '.' . $ext;
            resizeCropImage($medium_size_w, $medium_size_h, $upload_root . "/" . $file_path, $upload_root . "/" . $medium_file, 100);

            $large_size_w = isset($sys['large_size_w']) && trim($sys['large_size_w']) <> "" ? $sys['large_size_w'] : 1024;
            $large_size_h = isset($sys['large_size_h']) && trim($sys['large_size_h']) <> "" ? $sys['large_size_h'] : 1024;
            $large_file = $file . '-' . $large_size_w . 'x' . $large_size_w . '.' . $ext;
            resizeCropImage($large_size_w, $large_size_h, $upload_root . "/" . $file_path, $upload_root . "/" . $large_file, 100);

            $file_metadata['sizes'] = array(
                "thumbnail" => array("file" => $thumbnail_file, "width" => $thumnail_size_w, "height" => $thumnail_size_h, "mime-type" => $last_data['file_type']),
                "medium" => array("file" => $medium_file, "width" => $medium_size_w, "height" => $medium_size_h, "mime-type" => $last_data['file_type']),
                "large" => array("file" => $large_file, "width" => $large_size_w, "height" => $large_size_h, "mime-type" => $last_data['file_type'])
            );
        }
        $last_data['metadata'] = $file_metadata;
        $last_data['error'] = false;
        $last_data['errormsg'] = "";
    } else {
        $last_data['errormsg'] = "Cannot Move File";
    }

    return $last_data;
}

function uploadProfilePic($media) {
    global $sys;
    $last_data = array("error" => true);

    if (!file_exists('uploads/photos')) {
        @mkdir('uploads/photos', 0777, true);
    }
    //upload and cropping logic goes here
    if (empty($media)) {
        return false;
    }

    $allowed = 'jpg,jpeg,png,gif';

    $new_string = pathinfo($media['name'], PATHINFO_FILENAME) . '.' . strtolower(pathinfo($media['name'], PATHINFO_EXTENSION));
    $extension_allowed = explode(',', $allowed);
    $file_extension = pathinfo($new_string, PATHINFO_EXTENSION);
    if (!in_array($file_extension, $extension_allowed)) {
        $last_data['errormsg'] = "File Type Not Allowed";
        return $last_data;
    }
    if (isset($sys['maxUpload']) && $media['size'] > $sys['maxUpload']) {
        $last_data['errormsg'] = "File Size Greater Than Max Upload Size";
        return $last_data;
    }

    $dir = "uploads/photos";
    $filename = $dir . "/" . generateKey() . "_photo." . strtolower(pathinfo($media['name'], PATHINFO_EXTENSION));
    if (move_uploaded_file($media['tmp_name'], $filename)) {
        if (in_array($file_extension, array('jpg', 'jpeg', 'png', 'gif'))) {
            @compressImage($filename, $filename, 60);
            @resizeCropImage(96, 96, $filename, $filename, 100);
        }
        $last_data['filename'] = $filename;
        $last_data['name'] = $media['name'];
        $last_data['error'] = false;
    } else {
        $last_data['errormsg'] = "Cannot Move File";
    }

    return $last_data;
}

function uploadLogo($media) {
    global $sys;
    if (empty($media)) {
        return false;
    }
    $allowed = 'jpg,png,jpeg,gif';
    $new_string = pathinfo($data['name'], PATHINFO_FILENAME) . '.' . strtolower(pathinfo($data['name'], PATHINFO_EXTENSION));
    $extension_allowed = explode(',', $allowed);
    $file_extension = pathinfo($new_string, PATHINFO_EXTENSION);
    if (!in_array($file_extension, $extension_allowed)) {
        return false;
    }
    $dir = "themes/" . $sys['theme'] . "/img/";
    $filename = $dir . "logo.{$file_extension}";
    if (move_uploaded_file($data['file'], $filename)) {
        if (saveConfig('logo_extension', $file_extension)) {
            return true;
        }
    }
}

function importImageFromUrl($media) {
    if (empty($media)) {
        return $wo['userDefaultAvatar'];
    }
    if (!file_exists('upload/photos/' . date('Y'))) {
        mkdir('upload/photos/' . date('Y'), 0777, true);
    }
    if (!file_exists('upload/photos/' . date('Y') . '/' . date('m'))) {
        mkdir('upload/photos/' . date('Y') . '/' . date('m'), 0777, true);
    }
    $size = getimagesize($media);
    $extension = image_type_to_extension($size[2]);
    if (empty($extension)) {
        $extension = '.jpg';
    }
    $dir = 'upload/photos/' . date('Y') . '/' . date('m');
    $file_dir = $dir . '/' . generateKey() . '_url_image' . $extension;
    $importImage = @file_put_contents($file_dir, file_get_contents($media));
    if ($importImage) {
        Resize_Crop_Image(400, 400, $file_dir, $file_dir, 80);
    }
    if (file_exists($file_dir)) {
        return $file_dir;
    } else {
        return $wo['userDefaultAvatar'];
    }
}

function search($search_qeury) {
    global $conn;
    $search_qeury = secure($search_qeury);
    $data = array();
    $query = $conn->query(" SELECT user_id FROM " . T_USERS . " WHERE ((username LIKE '%$search_qeury%') OR CONCAT( first_name,  ' ', last_name ) LIKE '%$search_qeury%') AND active = '1' LIMIT 3");
    while ($fetched_data = mysqli_fetch_assoc($query)) {
        $data[] = UserData($fetched_data['user_id']);
    }
    $query = $conn->query(" SELECT page_id FROM " . T_PAGES . " WHERE ((page_name LIKE '%$search_qeury%') OR page_title LIKE '%$search_qeury%') AND active = '1' LIMIT 3");
    while ($fetched_data = mysqli_fetch_assoc($query)) {
        $data[] = PageData($fetched_data['page_id']);
    }
    $query = $conn->query(" SELECT id FROM " . T_GROUPS . " WHERE ((group_name LIKE '%$search_qeury%') OR group_title LIKE '%$search_qeury%') AND active = '1' LIMIT 3");
    while ($fetched_data = mysqli_fetch_assoc($query)) {
        $data[] = GroupData($fetched_data['id']);
    }
    return $data;
}

function getRecentSearches() {
    global $conn, $wo;
    if (IsLogged() === false) {
        return false;
    }
    $user_id = secure($wo['user']['user_id']);
    $data = array();
    $query = $conn->query("SELECT search_id,search_type FROM " . T_RECENT_SEARCHES . " WHERE user_id = {$user_id} ORDER BY id DESC LIMIT 10");
    while ($fetched_data = mysqli_fetch_assoc($query)) {
        if ($fetched_data['search_type'] == 'user') {
            $fetched_data_2 = UserData($fetched_data['search_id']);
        } else if ($fetched_data['search_type'] == 'page') {
            $fetched_data_2 = PageData($fetched_data['search_id']);
        } else if ($fetched_data['search_type'] == 'group') {
            $fetched_data_2 = GroupData($fetched_data['search_id']);
        } else {
            return false;
        }
        $data[] = $fetched_data_2;
    }
    return $data;
}

function getSearchFilter($result, $limit = 30) {
    global $wo, $conn;
    $data = array();
    $time = time() - 60;
    if (empty($result)) {
        return array();
    }
    if (!empty($result['query'])) {
        $result['query'] = secure($result['query']);
    }
    if (!empty($result['country'])) {
        $result['country'] = secure($result['country']);
    }
    if (!empty($result['gender'])) {
        $result['gender'] = secure($result['gender']);
    }
    if (!empty($result['status'])) {
        $result['status'] = secure($result['status']);
    }
    if (!empty($result['image'])) {
        $result['image'] = secure($result['image']);
    }
    $query = " SELECT user_id FROM " . T_USERS . " WHERE (username LIKE '%" . $result['query'] . "%' OR CONCAT( first_name,  ' ', last_name ) LIKE  '%" . $result['query'] . "%')";
    if (isset($result['gender'])) {
        $result['gender'] = secure($result['gender']);
        if ($result['gender'] == 'male') {
            $query .= " AND (gender = 'male') ";
        } else if ($result['gender'] == 'female') {
            $query .= " AND (gender = 'female') ";
        }
    }
    if (isset($result['country'])) {
        $result['country'] = secure($result['country']);
        if ($result['country'] != 'all') {
            $query .= " AND (country_id = " . $result['country'] . ') ';
        }
    }
    if (isset($result['status'])) {
        $result['status'] = secure($result['status']);
        if ($result['status'] == 'on') {
            $query .= " AND (lastseen >= {$time}) ";
        } else if ($result['status'] == 'off') {
            $query .= " AND (lastseen <= {$time}) ";
        }
    }
    if (isset($result['image'])) {
        $result['image'] = secure($result['image']);
        $d_image = secure($wo['userDefaultAvatar']);
        if ($result['image'] == 'yes') {
            $query .= " AND (avatar <> '{$d_image}') ";
        } else if ($result['image'] == 'no') {
            $query .= " AND (avatar = '{$d_image}') ";
        }
    }
    if (IsLogged() === true) {
        $user_id = secure($wo['user']['user_id']);
        $query .= " AND user_id <> {$user_id}";
    }
    $query .= " AND active = '1' ";
    if (!empty($limit)) {
        $limit = secure($limit);
        $query .= " ORDER BY first_name LIMIT {$limit}";
    }
    $sql_query_one = $conn->query($query);
    while ($fetched_data = mysqli_fetch_assoc($sql_query_one)) {
        $data[] = UserData($fetched_data['user_id']);
    }
    return $data;
}

function getBanned($type = '') {
    global $conn;
    $data = array();
    $results = $conn->query("SELECT * FROM " . T_BANNED_IPS . " ORDER BY id DESC");
    if ($type == 'user') {
        while ($row = $results->fetch_assoc()) {
            $data[] = $row['ip_address'];
        }
    } else {
        while ($row = $results->fetch_assoc()) {
            $data[] = $row;
        }
    }
    return $data;
}

function banNewIp($ip) {
    global $conn;
    $ip = secure($ip);
    $results = $conn->query("SELECT COUNT(id) as count FROM " . T_BANNED_IPS . " WHERE ip_address = '{$ip}'");
    $row = $results->fetch_assoc();
    if ($row['count'] > 0) {
        return false;
    }
    $time = time();
    $qstatus = $conn->query("INSERT INTO " . T_BANNED_IPS . " (ip_address,time) VALUES ('{$ip}','{$time}')");
    if ($qstatus) {
        return true;
    }
}

function isIpBanned($id) {
    global $conn;
    $id = secure($id);
    $result = $conn->query("SELECT COUNT(id) as count FROM " . T_BANNED_IPS . " WHERE id = '{$id}'");
    $row = $result->fetch_assoc();
    if ($row['count'] > 0) {
        return true;
    } else {
        return false;
    }
}

function deleteBanned($id) {
    global $conn;
    $id = secure($id);
    if (isIpBanned($id) === false) {
        return false;
    }
    $qstatus = $conn->query("DELETE FROM " . T_BANNED_IPS . " WHERE id = {$id}");
    if ($qstatus) {
        return true;
    }
}

/* ======================  ======================= */

/* Logs and Other Useful Functions Start */

function sendMessage($data = array()) {
    global $sys, $mail;
    if ($sys['smtp_or_mail'] == 'mail') {
        $mail->IsMail();
    } else if ($sys['smtp_or_mail'] == 'smtp') {
        $mail->isSMTP();
        $mail->Host = $sys['smtp_host']; // Specify main and backup SMTP servers
        $mail->SMTPAuth = true; // Enable SMTP authentication
        $mail->Username = $sys['smtp_username']; // SMTP username
        $mail->Password = $sys['smtp_password']; // SMTP password
        $mail->SMTPSecure = $sys['smtp_encryption']; // Enable TLS encryption, ssl also accepted
        $mail->Port = $sys['smtp_port'];
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
    } else {
        return false;
    }
    $data['from_email'] = secure($data['from_email']);
    $data['to_email'] = secure($data['to_email']);
    $data['charSet'] = secure($data['charSet']);
    $mail->IsHTML($data['is_html']);
    $mail->setFrom($data['from_email'], $data['from_name']);
    $mail->addAddress($data['to_email'], $data['to_name']); // Add a recipient
    $mail->Subject = $data['subject'];
    $mail->CharSet = $data['charSet'];
    $mail->MsgHTML($data['message_body']);
    $mstatus = $mail->send();
    $mail->clearAddresses();
    return $mstatus;
}

function sendMessageTemplate($data, $template_code, $ref) {
    global $conn;
    if (sendMessage($data)) {
        $from_email = secure($data['from_email']);
        $to_email = secure($data['to_email']);
        $template_code = secure($template_code);
        $ref = secure($ref);
        $subject = secure($data['subject']);
        $message = mysqli_real_escape_string($conn, $data['message_body']);
        $mail_sent_on = date("Y-m-d H:i:s");

        $sql = "INSERT INTO " . T_EMAIL_LOGS . " (`from_email`, `to_email`, `template_code`, `ref`, `subject`, `message`, `mail_sent_on`) "
                . "VALUES ('{$from_email}', '{$to_email}', '{$template_code}', '{$ref}', '{$subject}', '{$message}', '{$mail_sent_on}')";
        $status = $conn->query($sql);
        $GLOBALS['queryerrormsg'] = $conn->error;
        return $status;
    }
}

function isMessageAlreadySent($data, $template_code = null, $ref = null) {
    global $conn;
    $sql = "SELECT id FROM " . T_EMAIL_LOGS . " WHERE 1";
    if (isset($data['from_email'])) {
        $from_email = secure($data['from_email']);
        $sql .= " AND from_email = '{$from_email}'";
    }
    if (isset($data['to_email'])) {
        $to_email = secure($data['to_email']);
        $sql .= " AND to_email = '{$to_email}'";
    }
    if ($template_code != null) {
        $template_code = secure($template_code);
        $sql .= " AND template_code = '{$template_code}'";
    }
    if ($ref != null) {
        $ref = secure($ref);
        $sql .= " AND ref = '{$ref}'";
    }
    if (isset($data['subject'])) {
        $subject = secure($data['subject']);
        $sql .= " AND subject = '{$subject}'";
    }
    $result = $conn->query($sql);
    $GLOBALS['queryerrormsg'] = $conn->error;
    if ($result->num_rows > 0) {
        return true;
    }
    return false;
}

function emo($string = '') {
    global $emo;
    foreach ($emo as $code => $name) {
        $code = $code;
        $name = '<i class="twa-lg twa twa-' . $name . '"></i>';
        $string = str_replace($code, $name, $string);
    }
    return $string;
}

function addLog($log) {
    global $conn;
    $user_id = secure($log['user_id']);
    $role = secure($log['role']); //it can system,user,admin,etc
    $ip_address = secure($log['ip_address']);
    $country_code = secure($log['country_code']);
    $country = secure($log['country']);
    $region = secure($log['region']);
    $state = secure($log['state']);
    $city = secure($log['city']);
    $address = secure($log['address']);
    $source = secure($log['source']);
    $destination = secure($log['destination']);
    $additional_info = mysqli_escape_string($conn, $log['additional_info']);
    $action = secure($log['action']);
    $action_date = isset($log['action_date']) ? secure($log['action_date']) : date("Y-m-d");
    $action_time = isset($log['action_time']) ? secure($log['action_time']) : date("H:i:s");

    $sql = "INSERT INTO " . T_LOGS . " (`user_id`, `role`, `ip_address`, `country_code`, `country`, `region`, `state`, `city`, `address`, `source`, `destination`, `additional_info`, `action`, `action_date`, `action_time`) "
            . "VALUES ('{$user_id}', '{$role}', '{$ip_address}', '{$country_code}', '{$country}', '{$region}', '{$state}', '{$city}', '{$address}', '{$source}', '{$destination}', '{$additional_info}', '{$action}', '{$action_date}', '{$action_time}')";
    $status = $conn->query($sql);
    $GLOBALS['queryerrormsg'] = $conn->error;
    return $status;
}

function getLogs($columns = array(), $filters = array(), $offset = 0, $limit = 100, $orderby = "id", $order = "DESC") {
    global $conn;
    $sql = "SELECT * FROM " . T_LOGS . " WHERE 1";
    if (!empty($columns) && is_array($columns)) {
        $sql = "SELECT `" . implode("`,`", $columns) . "` FROM " . T_LOGS . " WHERE 1";
    }
    if (isset($filters['user_id']) && !empty($filters['user_id'])) {
        $user_id = secure($filters['user_id']);
        $sql .= " AND user_id = '{$user_id}'";
    }
    if (isset($filters['role']) && !empty($filters['role'])) {
        $role = secure($filters['role']);
        $sql .= " AND role = '{$role}'";
    }
    if (isset($filters['ip_address']) && !empty($filters['ip_address'])) {
        $ip_address = secure($filters['ip_address']);
        $sql .= " AND ip_address = '{$ip_address}'";
    }
    if (isset($filters['country_code']) && !empty($filters['country_code'])) {
        $country_code = secure($filters['country_code']);
        $sql .= " AND country_code = '{$country_code}'";
    }
    if (isset($filters['country']) && !empty($filters['country'])) {
        $country = secure($filters['country']);
        $sql .= " AND country = '{$country}'";
    }
    if (isset($filters['region']) && !empty($filters['region'])) {
        $region = secure($filters['region']);
        $sql .= " AND region = '{$region}'";
    }
    if (isset($filters['state']) && !empty($filters['state'])) {
        $state = secure($filters['state']);
        $sql .= " AND state = '{$state}'";
    }
    if (isset($filters['city']) && !empty($filters['city'])) {
        $city = secure($filters['city']);
        $sql .= " AND city = '{$city}'";
    }
    if (isset($filters['address']) && !empty($filters['address'])) {
        $address = secure($filters['address']);
        $sql .= " AND address LIKE '%{$address}%'";
    }
    if (isset($filters['source']) && !empty($filters['source'])) {
        $source = secure($filters['source']);
        $sql .= " AND source LIKE '%{$source}%'";
    }
    if (isset($filters['destination']) && !empty($filters['destination'])) {
        $destination = secure($filters['destination']);
        $sql .= " AND destination LIKE '%{$destination}%'";
    }
    if (isset($filters['action']) && !empty($filters['action'])) {
        $action = secure($filters['action']);
        $sql .= " AND action LIKE '%{$action}%'";
    }
    if (isset($filters['action_date']) && !empty($filters['action_date'])) {
        $action_date = secure($filters['action_date']);
        $sql .= " AND action_date = '{$action_date}'";
    }
    if (isset($filters['action_time']) && !empty($filters['action_time'])) {
        $action_time = secure($filters['action_time']);
        $sql .= " AND action_time = '{$action_time}'";
    }
    if (isset($filters['start_date']) && trim($filters['start_date']) <> "") {
        $start_date = secure($filters['start_date']);
        $sql .= " AND action_date >= '{$start_date}'";
    }
    if (isset($filters['end_date']) && trim($filters['end_date']) <> "") {
        $end_date = secure($filters['end_date']);
        $sql .= " AND action_date <= '{$end_date}'";
    }
    $sql .= " ORDER BY $orderby $order";
    if ($limit != -1) {
        $sql .= " LIMIT $offset, $limit";
    }
    $data = array();
    $result = $conn->query($sql);
    $GLOBALS['queryerrormsg'] = $conn->error;
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    return $data;
}

function getRowsCount($table, $column = "*", $filters = array()) {
    global $conn;
    $total = 0;
    $sql = "SELECT COUNT($column) AS total FROM " . $table . " WHERE 1";
    foreach ($filters as $key => $value) {
        if ($value != null && $value <> "") {
            $value = Secure($value);
            $sql .= " AND `$key`='{$value}'";
        }
    }

    $results = $conn->query($sql);
    $GLOBALS['queryerrormsg'] = $conn->error;
    while ($row = $results->fetch_assoc()) {
        $total = $row['total'];
    }
    return $total;
}

function update($table, $data = array(), $where = array()) {
    global $conn;
    if (empty($data) || empty($where)) {
        return true;
    }
    $sql = "UPDATE " . $table . " SET";
    foreach ($data as $key => $value) {
        if ($key != null && $key <> "" && $value != null && $value <> "") {
            $key = Secure($key);
            $value = Secure($value);
            $sql .= " `$key`='{$value}',";
        }
    }
    $sql = trim($sql, ",");
    $sql .= " WHERE 1";
    foreach ($where as $key => $value) {
        $key = Secure($key);
        $value = Secure($value);
        $sql .= " AND `$key`='{$value}',";
    }
    $sql = trim($sql, ",");
    $qstatus = $conn->query($sql);
    $GLOBALS['queryerrormsg'] = $conn->error;
    return $qstatus;
}

function updateWhereIn($table, $data = array(), $column = "", $values = array()) {
    global $conn;
    if (empty($data) || $column == "" || empty($values)) {
        return true;
    }
    $sql = "UPDATE " . $table . " SET";
    foreach ($data as $key => $value) {
        if ($key != null && $key <> "" && $value != null && $value <> "") {
            $key = Secure($key);
            $value = Secure($value);
            $sql .= " `$key`='{$value}',";
        }
    }
    $sql = trim($sql, ",");
    $sql .= " WHERE $column IN ('" . implode("','", $values) . "')";

    $qstatus = $conn->query($sql);
    $GLOBALS['queryerrormsg'] = $conn->error;
    return $qstatus;
}

function getNextIncrement($table) {
    global $conn, $sql_db_name;
    $next_increment = 1;
    $table = secure($table);
    $sql = "SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA = '$sql_db_name' AND TABLE_NAME = '$table'";
    $results = $conn->query($sql);
    while ($row = $results->fetch_assoc()) {
        $next_increment = $row['AUTO_INCREMENT'];
    }
    return $next_increment;
}

function updateSlugForRewrite(&$items, $parentId = "0", $parentItem = array()) {
    // Parent items control
    $isParentItem = false;
    foreach ($items as $item) {
        if ($item['parent'] == $parentId) {
            $isParentItem = true;
            break;
        }
    }

    // Prepare items
    if ($isParentItem) {
        foreach ($items as &$item) {
            if ($item['parent'] == $parentId) {
                $item['slug'] = isset($parentItem['slug']) ? ($parentItem['slug'] . "/" . $item['slug']) : $item['slug'];
                updateSlugForRewrite($items, $item['id'], $item);
            }
        }
    }
}

function updateRewriteRules($additional_rules = array(), $location = "bottom") {
    $rewrite_rules = array();
    //adding additional rules in array at the top
    if (!empty($additional_rules) && $location == 'top') {
        foreach ($additional_rules as $key => $value) {
            $rewrite_rules[$key] = $value;
        }
    }
    $rewrite_rules['sitemap_index\.xml$'] = 'index.php?sitemap=1';
    $rewrite_rules['([^/]+?)-sitemap([0-9]+)?\.xml$'] = 'index.php?sitemap=$matches[1]&sitemap_n=$matches[2]';
    $rewrite_rules['([a-z]+)?-?sitemap\.xsl$'] = 'index.php?yoast-sitemap-xsl=$matches[1]';
    /* product category and product rewrites */
    $rewrite_rules['product-category/([^/]+)/?$'] = 'index.php?productcategory=$matches[1]';
    $rewrite_rules['product/([^/]+)/?$'] = 'index.php?product=$matches[1]';
    /* login, logout & register rewrites */
    $rewrite_rules['login$'] = 'index.php?login=1';
    $rewrite_rules['logout$'] = 'index.php?logout=1';
    $rewrite_rules['register$'] = 'index.php?register=1';
    $rewrite_rules['account$'] = 'index.php?account=1';
    $rewrite_rules['account/([^/]+)/?$'] = 'index.php?account=1&section=$matches[1]';
    /* cart and checkout rewrites */
    $rewrite_rules['cart'] = 'index.php?cart=1';
    $rewrite_rules['checkout$'] = 'index.php?checkout=1';
    /* order placed and order details */
    $rewrite_rules['payment-failed'] = 'index.php?paymentfailed=1';
    $rewrite_rules['pay-response'] = 'index.php?payresponse=1';
    $rewrite_rules['pay'] = 'index.php?pay=1';
    $rewrite_rules['order-placed$'] = 'index.php?orderplaced=1';
    $rewrite_rules['order-details$'] = 'index.php?orderdetails=1';
    $rewrite_rules['order-cancel-request'] = 'index.php?ordercancelrequest=1';
    $rewrite_rules['order-cancel-request-saved'] = 'index.php?ordercancelrequestsaved=1';
    /* categories rewrites */
    $categories = getTerms(array(), array("taxonomy" => "category"), 0, -1);
    updateSlugForRewrite($categories, 0);
    foreach ($categories as $c) {
        $rewrite_rules['(' . $c['slug'] . ')/page/?([0-9]{1,})/?$'] = 'index.php?category_name=$matches[1]&paged=$matches[2]';
        $rewrite_rules['(' . $c['slug'] . ')/?$'] = 'index.php?category_name=$matches[1]';
    }
    /* tag rewrites */
    $rewrite_rules['tag/([^/]+)/page/?([0-9]{1,})/?$'] = 'index.php?tag=$matches[1]&paged=$matches[2]';
    $rewrite_rules['tag/([^/]+)/?$'] = 'index.php?tag=$matches[1]';
    /* blogs paging */
    $rewrite_rules['page/?([0-9]{1,})/?$'] = 'index.php?&paged=$matches[1]';
    /* search rewrites */
    $rewrite_rules['search/(.+)/page/?([0-9]{1,})/?$'] = 'index.php?s=$matches[1]&paged=$matches[2]';
    $rewrite_rules['search/(.+)/?$'] = 'index.php?s=$matches[1]';
    /* author rewrites */
    $rewrite_rules['author/([^/]+)/page/?([0-9]{1,})/?$'] = 'index.php?author_name=$matches[1]&paged=$matches[2]';
    $rewrite_rules['author/([^/]+)/?$'] = 'index.php?author_name=$matches[1]';
    /* year month day wise posts */
    $rewrite_rules['([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/page/?([0-9]{1,})/?$'] = 'index.php?year=$matches[1]&month=$matches[2]&day=$matches[3]&paged=$matches[4]';
    $rewrite_rules['([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/?$'] = 'index.php?year=$matches[1]&month=$matches[2]&day=$matches[3]';
    $rewrite_rules['([0-9]{4})/([0-9]{1,2})/page/?([0-9]{1,})/?$'] = 'index.php?year=$matches[1]&month=$matches[2]&paged=$matches[3]';
    $rewrite_rules['([0-9]{4})/([0-9]{1,2})/?$'] = 'index.php?year=$matches[1]&month=$matches[2]';
    $rewrite_rules['([0-9]{4})/page/?([0-9]{1,})/?$'] = 'index.php?year=$matches[1]&paged=$matches[2]';
    $rewrite_rules['([0-9]{4})/?$'] = 'index.php?year=$matches[1]';
    /**/
    $rewrite_rules['(.?.+?)/page/?([0-9]{1,})/?$'] = 'index.php?pagename=$matches[1]&paged=$matches[2]';
    $rewrite_rules['(.?.+?)(?:/([0-9]+))?/?$'] = 'index.php?pagename=$matches[1]&page=$matches[2]';
    $rewrite_rules['([^/]+)/page/?([0-9]{1,})/?$'] = 'index.php?name=$matches[1]&paged=$matches[2]';
    $rewrite_rules['([^/]+)(?:/([0-9]+))?/?$'] = 'index.php?name=$matches[1]&page=$matches[2]';

    //adding additional rules in array in the bottom
    if (!empty($additional_rules) && $location == 'bottom') {
        foreach ($additional_rules as $key => $value) {
            $rewrite_rules[$key] = $value;
        }
    }
    return saveConfig('rewrite_rules', serialize($rewrite_rules));
}

function parse_request($extra_query_vars = '') {
    global $sys;

    $rewrite_index = "index.php";
    // Process PATH_INFO, REQUEST_URI, and 404 for permalinks.
    // Fetch the rewrite rules.
    $rewrite = unserialize(getConfig("rewrite_rules"));

    if (!empty($rewrite)) {
        // If we match a rewrite rule, this will be cleared.
        $error = '404';

        $pathinfo = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
        list( $pathinfo ) = explode('?', $pathinfo);
        $pathinfo = str_replace('%', '%25', $pathinfo);

        list( $req_uri ) = explode('?', $_SERVER['REQUEST_URI']);
        $self = $_SERVER['PHP_SELF'];
        $home_path = trim(parse_url($sys['site_url'], PHP_URL_PATH), '/');
        $home_path_regex = sprintf('|^%s|i', preg_quote($home_path, '|'));

        // Trim path info from the end and the leading home path from the
        // front. For path info requests, this leaves us with the requesting
        // filename, if any. For 404 requests, this leaves us with the
        // requested permalink.
        $req_uri = str_replace($pathinfo, '', $req_uri);
        $req_uri = trim($req_uri, '/');
        $req_uri = preg_replace($home_path_regex, '', $req_uri);
        $req_uri = trim($req_uri, '/');
        $pathinfo = trim($pathinfo, '/');
        $pathinfo = preg_replace($home_path_regex, '', $pathinfo);
        $pathinfo = trim($pathinfo, '/');
        $self = trim($self, '/');
        $self = preg_replace($home_path_regex, '', $self);
        $self = trim($self, '/');

        // The requested permalink is in $pathinfo for path info requests and
        //  $req_uri for other requests.
        if (!empty($pathinfo) && !preg_match('|^.*' . $rewrite_index . '$|', $pathinfo)) {
            $requested_path = $pathinfo;
        } else {
            // If the request uri is the index, blank it out so that we don't try to match it against a rule.
            if ($req_uri == $rewrite_index) {
                $req_uri = '';
            }
            $requested_path = $req_uri;
        }
        $requested_file = $req_uri;

        // Look for matches.
        $request_match = $requested_path;
        if (empty($request_match)) {
            // An empty request could only match against ^$ regex
            if (isset($rewrite['$'])) {
                $query = $rewrite['$'];
                $matches = array('');
            }
        } else {
            foreach ((array) $rewrite as $match => $query) {
                // If the requested file is the anchor of the match, prepend it to the path info.
                if (!empty($requested_file) && strpos($match, $requested_file) === 0 && $requested_file != $requested_path) {
                    $request_match = $requested_file . '/' . $requested_path;
                }

                if (preg_match("#^$match#", $request_match, $matches) || preg_match("#^$match#", urldecode($request_match), $matches)) {
                    // Got a match.
                    $matched_rule = $match;
                    break;
                }
            }
        }

        if (isset($matched_rule)) {
            // Trim the query of everything up to the '?'.
            $query = preg_replace('!^.+\?!', '', $query);

            // Substitute the substring matches into the query.
            $matched_query = addslashes(MatchesMapRegex::apply($query, $matches));

            // Parse the query.
            parse_str($matched_query, $perma_query_vars);

            // If we're processing a 404 request, clear the error var since we found something.
            if ('404' == $error) {
                unset($error, $_GET['error']);
            }
        }

        // If req_uri is empty or if it is a request for ourself, unset error.
        if (empty($requested_path) || $requested_file == $self) {
            unset($error, $_GET['error']);

            if (isset($perma_query_vars)) {
                unset($perma_query_vars);
            }
        }
    }

    if (isset($perma_query_vars)) {
        return $perma_query_vars;
    }
    return null;
}

/* Logs and Other Useful Functions End */
?>
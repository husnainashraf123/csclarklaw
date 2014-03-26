<?php

global $wpdb, $table_prefix;
if (!isset($wpdb)) {
    require_once('../../../../wp-config.php');
    require_once('../../../../wp-includes/wp-db.php');
}
global $data;

$book_type = $_POST['book'];
$book_id = $_POST['bookid'];
$name = $_POST['name' . $book_id . ''];
$phone = $_POST['phone' . $book_id . ''];
$email = $_POST['email' . $book_id . ''];
$pdf_url = $_POST['pdfurl'];
if (strlen($phone) >= 15) {
    print "alert |-|An ERROR occured while processing your request, please try again.";
    exit();
}

/* ...........................Funcation For server side Validation................... */

function PhoneVal($phone) {
    $phone = strip_tags($phone);

    if (empty($phone)) {
        echo '*Please Enter Phone Number<br />';
        exit();
    } else {
        if (strlen($phone) >= 15) {
            echo '*Phone number Must be 10 Characters Long <br />';
            exit();
        } else {
            $phone_format = preg_replace("/[^0-9,.]/", "", $phone);

            if ($phone_format == '' || strlen($phone_format) != '10') {
                echo '*Please enter 10 Digit phone number<br />';
                exit();
            } else {
                return 1;
            }
        }
    }
}

function EmailVal($email) {
    $email = strip_tags($email);
    if (empty($email)) {
        echo '*Please Enter Email<br />';
        exit();
    } else {
        if (!preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $email)) {
            echo '*Please Enter Valid Email Address<br />';
            exit();
        } else {
            return 1;
        }
    }
}

/* .......................................End................................................. */

$check_phone = PhoneVal($phone);
$check_email = EmailVal($email);

if ($check_phone == 1 && $check_email == 1) {
// to detect outside the domain hits
    $refer_by = substr($_SERVER['HTTP_REFERER'], 7);
    $refer_domain = explode('.', $refer_by);
//get website url without htttp://
    $site_url = substr(get_bloginfo('url'), 7);
    $hosted_domain = explode('.', $site_url);

    if ($refer_domain[0] != $hosted_domain[0]) {
        print "alert |-|An ERROR occured while processing your request, please try again.";
        exit();
    }
    if (empty($name) || empty($phone) || empty($email) || empty($book_type)) {
        print "alert |-|Please fill all fields.";
        exit();
    }
    $phone_format = preg_replace("/[^0-9,.]/", "", $phone);

    if ($phone_format == '' || strlen($phone_format) != '10') {
        print "alert |-| Please enter valid phone number";
        exit();
    }

    $lawyer_name = $data['lawyer_name'];
    $to = $data['alert_emails'];
    $subject = 'This person downloaded the ' . $book_type . ' guide from your website';
    $lead_source = 'Book-' . $book_type;
// Formate email template and replace shortcodes
    $email_temp = $data['book_email_temp'];
    $email_temp = @str_replace("[name]", $name, $email_temp);
    $email_temp = @str_replace("[phone]", $phone, $email_temp);
    $email_temp = @str_replace("[email]", $email, $email_temp);
    $email_temp = @str_replace("[lawyername]", $lawyer_name, $email_temp);

// To send HTML mail, the Content-type header must be set
    $headers = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
    $headers .= "From: $site_url" . "\r\n";
    $headers .= 'Reply-To: ' . $email . "\r\n";
// Mail it
    $respons = mail($to, $subject, $email_temp, $headers);
    if ($respons) {
        print "alert alert-success|-| Sent successfully |-| $pdf_url";
    } else {
        print "error|-|An ERROR occured while processing your request, please try again.";
    }
// check if smartsheet alerts are enabled
    if($respons){
    if ($data['check_smartsheet'] == 1) {
        date_default_timezone_set("America/New_York");
        $lead_data2 = array();
        $main_sheet_id = '8eda7e4a0e5548fda3cc511af3e4e729';
        $lead_data2['ctlForm'] = 'ctlForm';
        $lead_data2['formName'] = 'fn_row';
        $lead_data2["formAction"] = "fa_insertRow";
        $lead_data2["parm1"] = $main_sheet_id;
        $lead_data2["parm2"] = "";
        $lead_data2["parm3"] = "";
        $lead_data2["parm4"] = "";
        $lead_data2["sk"] = "";
        $lead_data2["SFReturnURL"] = "";
        $lead_data2["SFImageURL"] = "";
        $lead_data2["SFImage2URL"] = "";
        $lead_data2["SFTaskNumber"] = "";
        $lead_data2["SFTaskID"] = "";
        $lead_data2["SFInstructions"] = "";
        $lead_data2["EQBCT"] = $main_sheet_id;
        $lead_data2["29239059"] = date('m/d/Y'); /* Date */
        $lead_data2["29239060"] = $lead_source; /* Phone call */
        $lead_data2["29239061"] = "[$lawyer_name] - $site_url"; /* Client Name */
        $lead_data2["30451525"] = $lead_source; /* Lead Type */
        $lead_data2["29239058"] = $phone; /* Caller id */
        $lead_data2["29241150"] = ' '; /* Call duration */
        $lead_data2["29239062"] = $name; /* Caller name */
        $lead_data2["29239063"] = $email; /* Caller Email */
        $lead_data2["29241135"] = ' '; /* Book Type */
        $lead_data2["29241136"] = ' '; /* Office Location */
//create the final string to be posted using implode()
        $post_items_2 = array();
        foreach ($lead_data2 as $key => $value) {
            $post_items_2[] = $key . '=' . $value;
        }
        $post_string_2 = implode('&', $post_items_2);
//create cURL connection
        $url_connection_2 = curl_init('https://www.smartsheet.com/b/publish');
//set options
        curl_setopt($url_connection_2, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($url_connection_2, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/535.2 (KHTML, like Gecko) Chrome/18.6.872.0 Safari/535.2 UNTRUSTED/1.0 3gpp-gba UNTRUSTED/1.0");
        curl_setopt($url_connection_2, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url_connection_2, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($url_connection_2, CURLOPT_FOLLOWLOCATION, 1);
//set data to be posted
        curl_setopt($url_connection_2, CURLOPT_POSTFIELDS, $post_string_2);
//perform our request
        $result_2 = curl_exec($url_connection_2);
//show information regarding the request
        curl_getinfo($url_connection_2);
        curl_errno($url_connection_2) . '-' . curl_error($url_connection_2);
//close the connection
        curl_close($url_connection_2);
    }
}
}
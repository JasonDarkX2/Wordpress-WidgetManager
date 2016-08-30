<?php

/*
  setter class for Wordpress Widget Manager
 */

class WidgetAction{
    
    function __construct() {
        
    }
    function delete_widget() {

    $_POST['wpdir'] = $_GET['wpdir'];
    $_POST['w'] = $_GET['w'];
    $form_fields = array('wpdir', 'w');
    $nonce = $_REQUEST['_wpnonce'];
    $name = 'delete-' . $_GET['w'];
    if (wp_verify_nonce($nonce, $name) == 2) {
        die();
        header('Location: ' . menu_page_url('widgetM') . '&del=true');
    }
    if (connect_fs($url, "POST", get_option('widgetdir'), $form_fields)) {
        //deletion  process
        global $wp_filesystem;
        $custwid = get_option('custom-widget');
        $widgets = get_option('widgetid');
        $widgetid = $_GET['w'];
        $wdir = get_option('widgetdir');
        if (file_exists($wdir . '/' . $custwid[$widgetid]['file']) === TRUE) {
            $toDel = explode("/", $custwid[$widgetid]['file']);
            $del = $wdir . $toDel[0];
            display_msg($wp_filesystem->rmdir($del, true), TRUE);
            header('Location: ' . menu_page_url('widgetM') . '&del=true');
            session_start();
            $_SESSION['deletion'] = display_msg($wp_filesystem->rmdir($del, true), TRUE);
        }
    } else {
        
    }
}

function add_widget() {
    $name = $_FILES["widgetToUpload"]['name'];
    $tmp = $_FILES['widgetToUpload']["tmp_name"];

    $dest = wp_upload_dir();
    if ($name == null) {
        //session_start();
        $name = $_SESSION['name'];
    }
    if ($name != null) {
        $destination = $dest['basedir'] . '/' . $name;
        move_uploaded_file($tmp, $destination);
        $file = str_replace('//', '/', str_replace('\\', '/', $destination));
        $_POST['file'] = $file;
        //session_start();
        $_SESSION['name'] = $name;
        $form_fields = array('file');
    }
    if (connect_fs('', "POST", get_option('widgetdir'), $form_fields)) {
        $destination = get_option('widgetdir');
        $file = $_POST['file'];
        $unzip = unzip_file($file, $destination);
        if (is_wp_error($unzip)) {
            //session_start();
            $_SESSION['errors'] = ' <div class="errorNotfi">' . $unzip->get_error_message() . '</div>';
        } else {
            $_SESSION['errors'] = NULL;
        }
        header('Location: ' . menu_page_url('widgetM'));
        unlink($file);
    }
}

function display_msg($output, $del) {
    if ($del) {
        $msgType = "Deleted";
    }
    if ($output == true) {
        return '<div class="notfi">Successfully ' . $msgType . ' </div>';
    } else if (is_wp_error($output) && $output != NULL) {
        ?>
        <div class="errorNotfi"><?php $output->get_error_message(); ?></div>
        <div><a href="<?php menu_page_url('cwop') ?>">Return to Custom Widgets Options</a>|<a href="<?php menu_page_url('widgetM') ?>">Return to Widgets Manager</a></div>
    <?php } else { ?>
        <div class="errorNotfi"> Unable to perform action</div>
        <div><a href="<?php menu_page_url('cwop') ?>">Return to Custom Widgets Options</a>|<a href="<?php menu_page_url('widgetM') ?>">Return to Widgets Manager</a></div>
        <?php
    }
}

function enable_all(&$widgets) {
    foreach ($widgets as $widgetId) {

        $widgets[$widgetId['key']]['status'] = TRUE;
        update_option('widgetid', $widgets);
    }
}

function disable_all(&$widgets) {
    foreach ($widgets as $widgetId) {
        $widgets[$widgetId['key']]['status'] = false;
        update_option('widgetid', $widgets);
    }
}

function disable_types(&$w, $type) {
    foreach ($w as $wid) {
        if ($wid['type']!= $type){ 
            continue;
        }else{
            $w[$wid['key']]['status'] = FALSE;
        }
    }
    update_option('widgetid', $w);
}

function get_count($type) {

    $w = get_option('widgetid');
    $count = 0;
    foreach ($w as $wid) {
        if (strtolower($wid['type']) == strtolower($type)) {
            $count++;
        }
    }
    return $count;
}

function status_count($wl,&$enablecon, &$disabledcon){ 
    foreach ($wl as $wid) {
        if ($wid['status']){ 
            $enablecon ++;
        } else {
            $disabledcon ++;
        }
    }
}
    
    
}
?>
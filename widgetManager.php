<?php
/*
Plugin Name: Wordpress Widget Manager
Plugin URI: https://github.com/JasonDarkX2/Wordpress-WidgetManager
Description:Simply a Wordpress Plugin dedicated to help easily manage custom and default Wordpress widgets
Author:Jason Dark X2
version: 0.1
Author URI:http://www.jasondarkx2.com/ 
*/ 
?>
<?php

function remove_disable_widget() {
	$d=get_option('widgetid');
        $dis=get_option('disabled_widgets');
        $e=get_option('enabled_widgets');
        foreach($d as $widget){
            if($dis[$widget['key']]==TRUE){
            unregister_widget($widget['key']);
            }else{ 
            register_widget($widget['key']);
            }
            }
        }

//add_action( 'widgets_init', 'remove_disable_widget' );


add_action('admin_menu', 'widget_manager_create_menu');



function widget_manager_create_menu() {

	//create new top-level menu
	add_menu_page('Widget Manager Settings', 'Widget Manager', 'administrator', __FILE__, 
                'my_cool_plugin_settings_page' 
                , plugins_url('/img/WMIconHolder.png', __FILE__) );

	//call register settings function
	add_action( 'admin_init', 'register_widget_manager_settings' );
}


function register_widget_manager_settings() {
	//register our settings
	register_setting( 'WM-setting', 'widgetid' );
	register_setting( 'WM-setting', 'enabled_widgets' );
	register_setting( 'WM-setting', 'disabled_widgets' );
}

function my_cool_plugin_settings_page() {?>


    <h1> Widget Manager</h1>
 <form method="post" action="<?php echo plugins_url('options.php', __FILE__); ?>">
    <?php settings_fields( 'WM-setting' ); ?>
    <?php do_settings_sections( 'WM-setting' ); ?>
    <table border='1px' >
        <tr><th> Widgets</th><th>Type</th><th> Enabled</th><th>Disabled</th></tr>
    <?php  
    
        $defaultWidgets=array();
        $customWidgets=Array();
    $w=get_option('widgetid');
    if(empty($w)){
        $widgets = array_keys( $GLOBALS['wp_widget_factory']->widgets );  
        $w=($GLOBALS['wp_widget_factory']->widgets);
  foreach($widgets as $keys){
          if(empty($widgetsId)){
              $type=get_type($keys);
  $widgetsId=array($keys => array('key'=>$keys,'name'=>get_name($keys),'Description'=>get_description($keys),'type'=> $type, 'status'=>TRUE));
  }  else {
      $type=get_type($keys);
      array_push($widgetsId, $widgetsId[$keys]=array('key'=>$keys,'name'=>get_name($keys),'Description'=>get_description($keys), 'type'=>$type,'status'=>TRUE));
      array_pop($widgetsId);
 }
  }
        update_option('widgetid', $widgetsId);
        $widgets=$widgetsId;
        
    }else{
        $widgets = array_keys( $GLOBALS['wp_widget_factory']->widgets );  
        $wid=($GLOBALS['wp_widget_factory']->widgets);
  foreach($widgets as $keys){
if(array_key_exists($keys,$w)==FALSE){
    echo"<h2>Notfication:</h2>";
    echo"*recently added widgets*<ul>";
     echo "<li>". get_name($keys) ."</li>";
     echo"</ul>";
$type=get_type($keys);
$des=get_option($key);
        array_push($w,$w[$keys]=array('key'=>$keys,'name'=> get_name($keys),'Description'=>get_description($keys), 'type'=>$type,'status'=>TRUE));
        array_pop($w);
        update_option('widgetid', $w);
}
      }
  $widgets=$w; 
    }
    foreach($widgets as $widget):?>
        <input type='hidden' name='count' value='$num' id='count'>
 <?php
?>
        <tr>
            <td><input type='hidden' name='widgetid[]' value='<?php echo  $widget['key'] ?>' id='widgetId'> 
                <?php echo $widget['name']; ?></td>
            <td><?php echo $widget['type']; ?></td>
            <td><input type="radio" name="<?php echo $widget['key']; ?>" value="enable" <?php if( !empty($e) ){ checked( 1,$widget['status'] ); } ?> ><?php //echo get_option($widget['key']);?></td>
            <td><input type="radio" name="<?php echo $widget['key'];?>" <?php checked('',$widget['status'] ); ?> value="disable"></td>
        </tr>
    <?php endforeach;?>
        <tr>
        <tr>
            <td><strong>Quick Options</strong></td>
            <td colspan="3">
                <b>|Enable Defaults Widgets Only:</b><input type="radio" name="quickOp" value="enbDefault">
                <b>|Disable Defaults Widgets Only:</b><input type="radio" name="quickOp" value="disDefault">
                <b>|Disable all custom widgets:</b><input type="radio" name="quickOp" value="disCust">
                <b>|Enable all Widgets:</b> <input type="radio" name="quickOp" value="enbwid">
                 <b>|Disable all Widgets:</b> <input type="radio" name="quickOp" value="diswid">
            </td>
        </tr>
    </table>
     <?php submit_button(); ?>
    </form>
    <form action="upload.php" method="post" enctype="multipart/form-data">
    Upload a custom Widget
    <input type="file" name="widgetToUpload" id="widgetToUpload">
    <input type="submit" value="Upload" name="submit">
</form>
    <?php echo "<b>DEBUG Section:</b>";
    
$s=get_option('widgetid');
var_dump($s);
//update_option('widgetid', "");
   ?>

    
<?php } 
function get_type($keys){
    if(preg_match("/WP_(Widget|Nav)/", $keys)){
    $type="default";
}else{
    $type="custom";
}
update_option('defaults', $defaultWidgets);
return $type;
}
function get_name($key){
    $wid=($GLOBALS['wp_widget_factory']->widgets);
     $name=$wid[$key]->name;
     return  $name;
}
function get_description($key){
    $wid=($GLOBALS['wp_widget_factory']->widgets);
 return $wid[$key]->widget_options['description'];
}
?>
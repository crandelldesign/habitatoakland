<?php
function insertuser($form_name, $form_data) {
  global $wpdb;

    try
    {
        $table_name = $wpdb->prefix . 'forms';
        $wpdb->insert( $table_name, array(
            'name' => (array_key_exists('contact_name',$form_data))?$form_data['contact_name']:'',
            'email' => (array_key_exists('email',$form_data))?$form_data['email']:'',
            'form_name' => $form_name,
            'data' => json_encode($form_data)
        ) );
    }
    catch(Exception $e)
    {
        echo $e->getMessage();
    }
}

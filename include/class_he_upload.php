<?php

class he_upload
{
    var $user_id;
    var $instance_type;
    
    function he_upload( $user_id = 0, $instance_type = '' )
    {
        $this->user_id = $user_id;
        $this->instance_type = $instance_type;
    }
    
    function new_upload( $instance_id = 0, $filename = '', $title = '' )
    {
        global $actions;
        
        $query = he_database::placeholder( "INSERT INTO `se_he_uploads` 
            (
              `uploads_instance_id`, 
              `uploads_instance_type`, 
              `uploads_user_id`,
              `uploads_datecreated`,
              `uploads_title`, 
              `uploads_filename`
            )
            VALUES (?, '?', ?, ?, '?', '?')", 
              $instance_id, 
              $this->instance_type,
              $this->user_id,
              time(),
              $title,
              $filename );

        he_database::query($query);
              
        return he_database::insert_id();
    }
    
    function delete_upload( $upload_id )
    {
        if ( !$upload_id )
        {
            return false;
        }
        
        $query = he_database::placeholder( "DELETE FROM `se_he_uploads` WHERE `uploads_id`=?", $upload_id );
        
        he_database::query($query);
    }
    
    function delete_instance_uploads( $instance_id )
    {
        if ( !$instance_id )
        {
            return false;
        }
        
        $query = he_database::placeholder( "DELETE FROM `se_he_uploads` 
           WHERE `uploads_instance_type`='?' AND `uploads_instance_id`=?", 
           $this->instance_type, $instance_id );
        
        he_database::query($query);
    }
    
    function get_instance_uploads( $instance_id )
    {
        if ( !$instance_id )
        {
            return array();
        }
        
        $query = he_database::placeholder( "SELECT * FROM `se_he_uploads` 
           WHERE `uploads_instance_type`='?' AND `uploads_instance_id`=?",
           $this->instance_type, $instance_id );

        return he_database::fetch_array($query);
    }
    
    function get_instance_uploads_limited( $instance_id, $start=0, $limit=7, $order_by='`uploads_datecreated` DESC' )
    {
        if ( !$instance_id )
        {
            return array();
        }
        
        $query = he_database::placeholder( "SELECT `up`.*, `us`.`user_username` FROM `se_he_uploads` AS `up`
           LEFT JOIN `se_users` AS `us` ON (`us`.`user_id`=`up`.`uploads_user_id`)
           WHERE `up`.`uploads_instance_type`='?' AND `up`.`uploads_instance_id`=? ORDER BY '?' LIMIT ?, ?",
           $this->instance_type, $instance_id, $order_by, $start, $limit);

        return he_database::fetch_array($query);
    }
    
    function get_total_instance_uploads( $instance_id )
    {
        if ( !$instance_id )
        {
            return false;
        }
        
        $query = he_database::placeholder( "SELECT COUNT(`uploads_id`) AS `total_uploads` FROM `se_he_uploads` 
           WHERE `uploads_instance_type`='?' AND `uploads_instance_id`=?",
           $this->instance_type, $instance_id );

        return he_database::fetch_field($query, 'total_uploads');
    }
    
    function get_upload( $upload_id )
    {
        if ( !$upload_id )
        {
            return array();
        }
        
        $query = he_database::placeholder( "SELECT * FROM `se_he_uploads` 
           WHERE `uploads_id`=?", $upload_id );
        
        return he_database::fetch_row($query);
    }
    
    function save_upload( $upload_id, $filename )
    {
        if ( !$upload_id || !$filename )
        {
           return false;
        }
        
        $query = he_database::placeholder( "UPDATE `se_he_uploads` SET `uploads_filename`='?'
            WHERE `uploads_id`=?", $filename, $upload_id );
        
        he_database::query($query);
    }
    
    function delete_user_uploads()
    {
        $query = he_database::placeholder( "DELETE FROM `se_he_uploads`
           WHERE `uploads_user_id`=? AND `uploads_instance_type`='?'",
           $this->user_id, $this->instance_type );
        
        he_database::query($query);
    }
    
    function get_user_uploads()
    {
        
        $query = he_database::placeholder( "SELECT * FROM `se_he_uploads`
           WHERE `uploads_user_id`=? AND `uploads_instance_type`='?'",
           $this->user_id, $this->instance_type );

        return he_database::fetch_array($query);
    }
}

?>
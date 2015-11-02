<?php


class he_quiz
{
    function photo_url()
    {
        return './uploads_quiz/';
    }

    function photo_dir()
    {
        return '.' . DIRECTORY_SEPARATOR . 'uploads_quiz' . DIRECTORY_SEPARATOR;
    }

    function save_quiz( $quiz_id, $user_id, $name, $description, $cat_id )
    {
        if ( !$name || !$cat_id )
        {
            return false;
        }

        if ( $quiz_id )
        {
            $result = he_quiz::update_quiz($quiz_id, $name, $description, $cat_id);
        }
        else
        {
            $result = he_quiz::create_quiz($user_id, $name, $description, $cat_id);
        }

        return $result;
    }

    function create_quiz( $user_id, $name, $description = '', $cat_id )
    {
        global $setting;

        $query = he_database::placeholder( "INSERT INTO `se_he_quiz` (`user_id`, `name`, `description`, `approved`, `cat_id`)
            VALUES(?, '?', '?', ?, ?)", $user_id, $name, $description, $setting['setting_he_quiz_approval_status'], $cat_id );
        
        he_database::query($query);
        
        $quiz_id = he_database::insert_id();

        $query = he_database::placeholder( "UPDATE `se_he_quiz` SET `quiz_id`=? WHERE `id`=?", $quiz_id, $quiz_id );
        
        he_database::query($query);
        
        return $quiz_id;
    }

    function update_quiz( $quiz_id, $name, $description, $cat_id )
    {
        $query = he_database::placeholder( "UPDATE `se_he_quiz` SET `name`='?', `description`='?', `cat_id`=?
            WHERE `quiz_id`=?", $name, $description, $cat_id, $quiz_id );

        $result = he_database::query($query);

        return $quiz_id;
    }

    function save_photo( $entity_id, $tmp_file, $entity = 'quiz', $filename = 'photo', $size = 100 )
    {
        $result = array( 'result' => false, 'error' => '' );

        if ( $tmp_file['error'] !== 0 )
        {
            $result['error'] = 'No file was upload';
 
            return $result;
        }

        $name_arr = explode('.', $tmp_file['name']);
        $file_extension = array_pop($name_arr);

        if ( !in_array(strtolower($file_extension), array( 'jpg', 'jpeg', 'jpe', 'gif', 'png', 'bmp' )) )
        {
            @unlink($tmp_file['tmp_name']);
            $result['error'] = 'File upload stopped by extension';

            return $result;
        }

        $old_file = he_quiz::get_photo($entity_id, $entity);

        if ( $old_file )
        {
            $old_src = he_quiz::photo_dir() . $old_file; 
            @unlink($old_src);
        }

        $new_file = "{$entity}_$entity_id.$file_extension";
        $new_src = he_quiz::photo_dir() . $new_file;

        $upload = new se_upload();
        $upload->new_upload($filename, 2*1024*1024, 'jpg');
        $upload->upload_photo($new_src, $size, $size);

        switch ( $entity )
        {
        	case 'result':
		        $query = he_database::placeholder( "UPDATE `se_he_quiz_result` SET `photo`='?'
		            WHERE `id`=?", $new_file, $entity_id );
            break;
            
        	case 'question':
		        $query = he_database::placeholder( "UPDATE `se_he_quiz_question` SET `photo`='?'
		            WHERE `id`=?", $new_file, $entity_id );
            break;
            
            default:
                $query = he_database::placeholder( "UPDATE `se_he_quiz` SET `photo`='?'
                    WHERE `quiz_id`=?", $new_file, $entity_id );
            break;
        }

        he_database::query($query);

        $result['result'] = true;

        return $result;
    }

    function general_info( $quiz_id )
    {
        if ( !$quiz_id )
        {
            return array();
        }

        $query = he_database::placeholder( "SELECT * FROM `se_he_quiz` WHERE `quiz_id`=?", $quiz_id );

        $general_info = he_database::fetch_row($query);
        $general_info['photo'] = ( $general_info['photo'] )
            ? he_quiz::photo_url() . $general_info['photo']
            : false;

        return $general_info;
    }

    function get_photo( $entity_id, $entity )
    {
        if ( !$entity_id )
        {
        	return false;
        }
    	
        switch ( $entity )
        {
            case 'result':
                $query = he_database::placeholder( "SELECT `photo` FROM `se_he_quiz_result`
                    WHERE `id`=?", $entity_id );
            break;

            case 'question':
                $query = he_database::placeholder( "SELECT `photo` FROM `se_he_quiz_question`
                    WHERE `id`=?", $entity_id );
            break;

    		default:
                $query = he_database::placeholder( "SELECT `photo` FROM `se_he_quiz`
                    WHERE `quiz_id`=?", $entity_id );
            break;
        }

        return he_database::fetch_field($query);
    }

    function get_owner( $quiz_id )
    {
        $query = he_database::placeholder( "SELECT `user_id` FROM `se_he_quiz`
            WHERE `quiz_id`=?", $quiz_id );

        return he_database::fetch_field($query);
    }

    function get_results( $quiz_id, $auto_fill = false, $id_as_index = false )
    {
        global $setting;
        
        $min_count = $setting['setting_he_quiz_min_result'];
        $result_arr = array();

        $query = he_database::placeholder( "SELECT * FROM `se_he_quiz_result`
            WHERE `quiz_id`=? ORDER BY `id`", $quiz_id );

        $key = ( $id_as_index ) ? 'id' : null; 
        $result_arr = he_database::fetch_array($query, $key);

        if ( !$auto_fill )
        {
            return $result_arr;
        }

        $count = count($result_arr);

        if ( $count < $min_count )
        {
            for ( $i = 1; $i <= $min_count - $count; $i++ )
            {
                $result_arr[] = array( 'id' => 0, 'quiz_id' => $quiz_id, 'title' => '', 'description' => '' );
            }
        }

        return $result_arr;
    }

    function save_results( $quiz_id, $result_arr )
    {
        if ( !$quiz_id )
        {
            return false;
        }

        $result_ids = array();
        $new_uploads = new se_upload();

        foreach ( $result_arr as $result )
        {
            $result_id = $result['id'];

            if ( $result_id )
            {
                he_quiz::update_result($result_id, $result['title'], $result['description']);
            }
            else
            {
                $result_id = he_quiz::create_result($quiz_id, $result['title'], $result['description']);
            }

            $photo = $result['filename'];
            
            if ( isset($_FILES[$photo]['error']) &&  $_FILES[$photo]['error']==0 )
            {
            	$tmp_file = $_FILES[$photo];
            	he_quiz::save_photo($result_id, $tmp_file, 'result', $photo, 200);       	
            }
            
            $result_ids[] = $result_id;
        }

        //delete removed results
        $result_str = implode(', ', $result_ids);

        $query = he_database::placeholder( "SELECT `id` FROM `se_he_quiz_result`
            WHERE `quiz_id`=? AND `id` NOT IN($result_str) ORDER BY `id`", $quiz_id );

        $removed_ids = he_database::fetch_column($query);

        he_quiz::delete_results($removed_ids);
    }

    function create_result($quiz_id, $title, $description)
    {
        $query = he_database::placeholder( "INSERT INTO `se_he_quiz_result` (`quiz_id`, `title`, `description`)
            VALUES(?, '?', '?')", $quiz_id, $title, $description );

        he_database::query($query);

        return he_database::insert_id();
    }

    function update_result($result_id, $title, $description)
    {
        $query = he_database::placeholder( "UPDATE `se_he_quiz_result` SET `title`='?', `description`='?'
            WHERE `id`=?", $title, $description, $result_id );

        he_database::query($query);
    }

    function delete_results( $removed_ids )
    {
        if ( !$removed_ids )
        {
            return false;
        }

        $removed_str = implode(', ', $removed_ids);
        
        $query = "SELECT `photo` FROM `se_he_quiz_result` WHERE `id` IN ($removed_str)";
        $photo_arr = he_database::fetch_column($query);

        foreach ($photo_arr as $photo)
        {
            @unlink(he_quiz::photo_dir() . $photo);
        }

        $query = "DELETE FROM `se_he_quiz_answer` WHERE `result_id` IN ($removed_str)";
        he_database::query($query);

        $query = "DELETE FROM `se_he_quiz_play` WHERE `result_id` IN ($removed_str)";
        he_database::query($query);

        $query = "DELETE FROM `se_he_quiz_result` WHERE `id` IN ($removed_str)";
        he_database::query($query);
    }

    function get_questions( $quiz_id, $auto_fill = false )
    {
        global $setting;
        $min_count = $setting['setting_he_quiz_min_question'];

        $question_arr = array();

        $query = he_database::placeholder( "SELECT * FROM `se_he_quiz_question`
            WHERE `quiz_id`=? ORDER BY `id`", $quiz_id );

        $question_arr = he_database::fetch_array($query);

        if ( !$auto_fill )
        {
            return $question_arr;
        }

        $count = count($question_arr);

        if ( $count < $min_count )
        {
            for ( $i = 1; $i <= $min_count - $count; $i++ )
            {
                $question_arr[] = array( 'id' => 0, 'quiz_id' => $quiz_id, 'text' => '' );
            }
        }

        return $question_arr;
    }

    function get_answers( $quiz_id, $question_arr )
    {
        $question_ids = array();
        foreach ( $question_arr as $question )
        {
            $question_ids[] = $question['id'];
        }

        $answer_arr = array();

        //get results
        $query = he_database::placeholder( "SELECT `id` FROM `se_he_quiz_result`
            WHERE `quiz_id`=? ORDER BY `id`", $quiz_id );

        $results = he_database::fetch_column($query);

        //get answers
        $answer_arr = array();

        if ( array_sum($question_ids) )
        {
            $question_str = implode(', ', $question_ids);

            $query = "SELECT * FROM `se_he_quiz_answer` WHERE `question_id` IN ($question_str)";

            $res = he_database::query($query);

            while ( $answer = he_database::fetch_row_from_resource($res) )
            {
                $answer_arr[$answer['question_id']][$answer['result_id']] = $answer;
            }
        }

        foreach ( $question_arr as $key => $question )
        {
            $answers = array();
            foreach ($results as $result_id)
            {
                $answer = isset($answer_arr[$question['id']][$result_id]) 
                    ? $answer_arr[$question['id']][$result_id]
                    : array( 'id' => 0, 'label' => '' ); 

                $answers[$result_id] = array(
                    'id' => $answer['id'],
                    'question_id' => 0,
                    'result_id' => $result_id,
                    'label' => $answer['label']
                );
            }

            $question_arr[$key]['answers'] = $answers;
        }

        return $question_arr;
    }

    function save_questions( $quiz_id, $question_arr )
    {
        if ( !$quiz_id || !$question_arr )
        {
            return false;
        }

        $question_ids = array();
        foreach ( $question_arr as $question )
        {
            $question_id = $question['id'];
            $question_ids[] = $question_id;

            if ( $question_id )
            {
                he_quiz::update_question($question['id'], $question['text']);
            }
            else
            {
                $question_id = he_quiz::create_question($quiz_id, $question['text']);
                $question_ids[] = $question_id;
            }
            
            $photo = $question['filename'];
            
            if ( isset($_FILES[$photo]['error']) &&  $_FILES[$photo]['error']==0 )
            {
                $tmp_file = $_FILES[$photo];
                he_quiz::save_photo($question_id, $tmp_file, 'question', $photo, 200);          
            }

            foreach ( $question['answers'] as $answer )
            {
                $answer_id = $answer['id'];

                if ( $answer_id )
                {
                    he_quiz::update_answer($answer_id, $answer['label']);
                }
                else
                {
                    $answer_id = he_quiz::create_answer($question_id, $answer['result_id'], $answer['label']);
                }
            }
        }

        if ( !$question_ids )
        {
            return false;
        }

        $question_str = implode(', ', $question_ids);

        //get removed questions
        $query = he_database::placeholder( "SELECT `id` FROM `se_he_quiz_question`
            WHERE `id` NOT IN ($question_str) AND `quiz_id`=?", $quiz_id );

        $removed_ids = he_database::fetch_column($query);

        he_quiz::delete_questions($removed_ids);
    }

    function update_question( $question_id, $text )
    {
        $query = he_database::placeholder( "UPDATE `se_he_quiz_question` SET `text`='?'
            WHERE `id`=?", $text, $question_id );

        he_database::query($query);
    }

    function create_question( $quiz_id, $text )
    {
        $query = he_database::placeholder( "INSERT INTO `se_he_quiz_question` (`quiz_id`, `text`)
            VALUES(?, '?')", $quiz_id, $text );

        he_database::query($query);

        return he_database::insert_id();
    }

    function update_answer( $answer_id, $text )
    {
        $query = he_database::placeholder( "UPDATE `se_he_quiz_answer` SET `label`='?'
            WHERE `id`=?", $text, $answer_id );

        he_database::query($query);
    }

    function create_answer( $question_id, $result_id, $text )
    {
        $query = he_database::placeholder( "INSERT INTO `se_he_quiz_answer`
            (`question_id`, `result_id`, `label`) VALUES(?, ?, '?')", 
            $question_id, $result_id, $text );

        he_database::query($query);

        return he_database::insert_id();
    }

    function check_steps( $quiz_id )
    {
        global $setting;
        $min_result_count = $setting['setting_he_quiz_min_result'];
        $min_question_count = $setting['setting_he_quiz_min_question'];

        $steps = array( 'general' => 1, 'results' => 0, 'questions' => 0, 'publish' => 0 );

        if ( !$quiz_id )
        {
            return $steps;
        }

        $query = he_database::placeholder( "SELECT `name` FROM `se_he_quiz` WHERE `quiz_id`=?", $quiz_id );
        $name = he_database::fetch_field($query);

        if ( !strlen($name) )
        {
            return $steps;
        }

        $steps['results'] = 1;

        $query = he_database::placeholder( "SELECT COUNT(*) FROM `se_he_quiz_result`
            WHERE `quiz_id`=?", $quiz_id );
        $result_count = he_database::fetch_field($query);

        if ( $min_result_count > $result_count )
        {
            return $steps;
        }

        $steps['questions'] = 1;

        $query = he_database::placeholder( "SELECT COUNT(*) FROM `se_he_quiz_question`
            WHERE `quiz_id`=?", $quiz_id );
        $question_count = he_database::fetch_field($query);

        if ( $min_question_count > $question_count )
        {
            return $steps;
        }

        $steps['publish'] = 1;

        return $steps;
    }

    function publish_quiz($quiz_id, $status)
    {
        if ( !$quiz_id )
        {
            return false;
        }

        $query = he_database::placeholder( "UPDATE `se_he_quiz` SET `status`=?
            WHERE `quiz_id`=?", $status, $quiz_id );

        he_database::query($query);
    }

    function user_quiz_list( $user_id, $first, $count )
    {
        if ( !$user_id )
        {
            return array();
        }

        $query = he_database::placeholder( "SELECT * FROM `se_he_quiz`
            WHERE `user_id`=? ORDER BY `status` DESC LIMIT ?, ?", $user_id, $first, $count );

        $res = he_database::query($query);
        $quiz_list = array();
        $quiz_ids = array();
        while ( $quiz = he_database::fetch_row_from_resource($res) )
        {
            $quiz_ids[] = $quiz['quiz_id'];

            $steps = he_quiz::check_steps($quiz['quiz_id']);

            $quiz['takes'] = 0;
            $quiz['photo_url'] = ( $quiz['photo'] ) ? he_quiz::photo_url() . $quiz['photo'] : '';
            $quiz['steps'] = $steps;

            $quiz['can_publish'] = ( array_sum($steps) == 4 ); 

            $quiz_list[$quiz['quiz_id']] = $quiz;
        }

        if ( count($quiz_ids) )
        {
            $quiz_ids_str = implode(', ', $quiz_ids);

            $res = he_database::query("SELECT `quiz_id`, COUNT(`id`) AS `count` FROM `se_he_quiz_play`
                WHERE `quiz_id` IN ($quiz_ids_str) GROUP BY `quiz_id`");

            while ( $take = he_database::fetch_row_from_resource($res) )
            {
                $quiz_list[$take['quiz_id']]['takes'] = $take['count'];
            }
        }

        return $quiz_list;
    }

    function user_quiz_total( $user_id )
    {
        if ( !$user_id )
        {
            return array();
        }

        $query = he_database::placeholder( "SELECT COUNT(*) FROM `se_he_quiz`
            WHERE `user_id`=?", $user_id );

        return he_database::fetch_field($query);
    }

    function quiz_list( $first, $count, $sort = 'popular', $where = '' )
    {
        $sort = ( $sort == 'popular' ) ? '`takes` DESC' : '`quiz_id` DESC';
        
        $query = he_database::placeholder( "SELECT `quiz`.*, COUNT(`play`.`id`) AS `takes`
            FROM `se_he_quiz` AS `quiz`
            LEFT JOIN `se_he_quiz_play` AS `play` ON(`quiz`.`quiz_id`=`play`.`quiz_id`)
            WHERE `quiz`.`status`=1 AND `quiz`.`approved`=1 $where
            GROUP BY `quiz`.`quiz_id`
            ORDER BY $sort LIMIT ?, ?", $first, $count );

        $res = he_database::query($query);
        $quiz_list = array();
        while ( $quiz = he_database::fetch_row_from_resource($res) )
        {
            $quiz['comments'] = 0;
        	$quiz['photo_url'] = ( $quiz['photo'] ) ? he_quiz::photo_url() . $quiz['photo'] : '';

            $quiz_list[$quiz['quiz_id']] = $quiz;
        }
        
        $quiz_ids = array_keys($quiz_list);
        
        if ( !$quiz_ids )
        {
            return $quiz_list;
        }
        
        $quiz_ids_str = implode(',', $quiz_ids);

        $query = "SELECT `he_quizcomment_quiz_id` AS `quiz_id`, COUNT(`he_quizcomment_id`) AS `comments`
            FROM `se_he_quizcomments`
            WHERE `he_quizcomment_quiz_id` IN ($quiz_ids_str)
            GROUP BY `he_quizcomment_quiz_id`";
            
        $res = he_database::query($query);
        while ( $comment_info = he_database::fetch_row_from_resource($res) )
        {
        	$quiz_list[$comment_info['quiz_id']]['comments'] = $comment_info['comments'];
        }

        return $quiz_list;
    }
    
    function recent_commented_list( $first, $count, $where = '' )
    {
        $query = he_database::placeholder( "SELECT `quiz`.`quiz_id`, `quiz`.`name`, `quiz`.`photo`,
            `rc`.`comment_date`, `c`.`he_quizcomment_authoruser_id` AS `user_id`, `c`.`he_quizcomment_body`
            FROM `se_he_quiz` AS `quiz`
            INNER JOIN (
                SELECT MAX(`he_quizcomment_date`) AS `comment_date`, `he_quizcomment_quiz_id` AS `quiz_id`
                FROM `se_he_quizcomments`
                GROUP BY `he_quizcomment_quiz_id`
            ) AS `rc`
            ON (`quiz`.`id`=`rc`.`quiz_id`)
            INNER JOIN `se_he_quizcomments` AS `c` 
            ON (
                `rc`.`comment_date`=`c`.`he_quizcomment_date` AND
                `rc`.`quiz_id`=`c`.`he_quizcomment_quiz_id`
            )
            WHERE `quiz`.`status`=1 AND `quiz`.`approved`=1 $where
            ORDER BY `rc`.`comment_date` DESC LIMIT ?, ?", $first, $count );

        $res = he_database::query($query);
        $quiz_list = array();
        while ( $quiz = he_database::fetch_row_from_resource($res) )
        {
        	$quiz['user'] = new se_user(array($quiz['user_id']));
            $quiz['photo_url'] = ( $quiz['photo'] ) ? he_quiz::photo_url() . $quiz['photo'] : '';

            $quiz_list[$quiz['quiz_id']] = $quiz;
        }

        return $quiz_list;
    }

    function quiz_total( $where = '' )
    {
        $query = "SELECT COUNT(*) FROM `se_he_quiz` AS `quiz` WHERE `quiz`.`status`=1 AND `quiz`.`approved`=1 $where";

        return he_database::fetch_field($query);
    }
    
    function recent_commented_total( $where = '' )
    {
        $query = "SELECT COUNT(`quiz`.`quiz_id`) FROM `se_he_quiz` AS `quiz`
            INNER JOIN (
                SELECT `he_quizcomment_quiz_id` AS `quiz_id` 
                FROM `se_he_quizcomments`
                GROUP BY `he_quizcomment_quiz_id`
            ) AS `comm`
            ON ( `quiz`.`quiz_id`=`comm`.`quiz_id` )
            WHERE `quiz`.`status`=1 AND `quiz`.`approved`=1 $where";

        return he_database::fetch_field($query);     
    }

    function delete_quiz( $quiz_id )
    {
        //delete questions
    	$query = he_database::placeholder( "SELECT `id` FROM `se_he_quiz_question` 
            WHERE `quiz_id`=?", $quiz_id );
    	$question_ids = he_database::fetch_column($query);
        
    	he_quiz::delete_questions($question_ids);

        //delete results
        $query = he_database::placeholder( "SELECT `id` FROM `se_he_quiz_result`
            WHERE `quiz_id`=?", $quiz_id );
        $result_ids = he_database::fetch_column($query);
        
        he_quiz::delete_results($result_ids);

        //delete plays
        $query = he_database::placeholder( "DELETE FROM `se_he_quiz_play`
            WHERE `quiz_id`=?", $quiz_id );
        he_database::query($query);

        //get quiz photo
        $query = he_database::placeholder( "SELECT `photo` FROM `se_he_quiz`
            WHERE `quiz_id`=?", $quiz_id );
        $photo = he_database::fetch_field($query);

        if ( $photo )
        {
            //delete photo
            @unlink(he_quiz::photo_dir() . $photo);
        }
        
        //delete comments
        $query = he_database::placeholder( "DELETE FROM `se_he_quizcomments`
            WHERE `he_quizcomment_quiz_id`=?", $quiz_id );

        //delete quiz
        $query = he_database::placeholder( "DELETE FROM `se_he_quiz` WHERE `quiz_id`=?", $quiz_id );
        he_database::query($query);
    }

    function get_quiz_info( $quiz_id )
    {
        if ( !$quiz_id )
        {
            return false;
        }

        $query = he_database::placeholder( "SELECT * FROM `se_he_quiz`
            WHERE `quiz_id`=? AND `status`=1 AND `approved`=1", $quiz_id );

        $quiz_info = he_database::fetch_row($query);

        if ( !$quiz_info )
        {
            return array();
        }

        $quiz_info['photo_src'] = ( $quiz_info['photo'] ) ? he_quiz::photo_url() . $quiz_info['photo'] : '';

        return $quiz_info;
    }

    function get_quiz_questions( $quiz_id )
    {
        $question_arr = he_quiz::get_questions($quiz_id);
        $question_arr = he_quiz::get_answers($quiz_id, $question_arr);

        foreach ( $question_arr as $index => $question )
        {
            shuffle($question_arr[$index]['answers']);
        }

        return $question_arr;
    }

    function save_user_play( $user_id, $quiz_id, $answers )
    {
        if ( !$user_id || !$quiz_id || count($answers) == 0 )
        {
            return false;
        }

        $answer_str = implode(', ', $answers);

        $query = "SELECT `result_id`, COUNT(`result_id`) AS `count` FROM `se_he_quiz_answer`
            WHERE `id` IN ($answer_str)
            GROUP BY `result_id`
            ORDER BY `count` DESC LIMIT 1";

        $result_id = he_database::fetch_field($query);

        $query = he_database::placeholder( "SELECT `id` FROM `se_he_quiz_play`
            WHERE `user_id`=? AND `quiz_id`=?", $user_id, $quiz_id );

        $play_id = he_database::fetch_field($query);

        if ( $play_id )
        {
            $query = he_database::placeholder( "UPDATE `se_he_quiz_play` SET `result_id`=?, `play_stamp`=?
                WHERE `id`=?", $result_id, time(), $play_id );
        }
        else
        {
            $query = he_database::placeholder( "INSERT INTO `se_he_quiz_play` (`user_id`, `quiz_id`, `result_id`, `play_stamp`)
                VALUES(?, ?, ?, ?)", $user_id, $quiz_id, $result_id, time() );
        }

        he_database::query($query);

        return $result_id;
    }

    function result_info( $result_id )
    {
        if ( !$result_id )
        {
            return array();
        }

        $query = he_database::placeholder( "SELECT * FROM `se_he_quiz_result` WHERE `id`=?", $result_id );

        return he_database::fetch_row($query);
    }

    function delete_questions( $question_arr )
    {
        if ( !$question_arr )
        {
            return false;
        }

        $question_str = implode(', ', $question_arr);
        
        $query = "SELECT `photo` FROM `se_he_quiz_question` WHERE `id` IN ($question_str)";
        $photo_arr = he_database::fetch_column($query);

        foreach ($photo_arr as $photo)
        {
            @unlink(he_quiz::photo_dir() . $photo);
        }
        
        //delete answers
        $query = "DELETE FROM `se_he_quiz_answer` WHERE `question_id` IN ($question_str)";
        he_database::query($query);

        //delete questions
        $query = "DELETE FROM `se_he_quiz_question` WHERE `id` IN ($question_str)";
        he_database::query($query);
    }

    function user_result( $user_id, $quiz_id )
    {
        if ( !$user_id || !$quiz_id )
        {
            return false;
        }

        $query = he_database::placeholder( "SELECT `result_id` FROM `se_he_quiz_play`
            WHERE `user_id`=? AND `quiz_id`=?", $user_id, $quiz_id );

        return he_database::fetch_field($query);
    }

    function get_quiz_takes( $quiz_id )
    {
        $query = he_database::placeholder( "SELECT * FROM `se_he_quiz_play`
            WHERE `quiz_id`=? ORDER BY `result_id`", $quiz_id );

        $res = he_database::query($query);
        $quiz_takes = array();
        while ( $quiz_take = he_database::fetch_row_from_resource($res) )
        {
            $quiz_take['user'] = new se_user(array( $quiz_take['user_id'] ));

            $quiz_takes[$quiz_take['result_id']][] = $quiz_take;
        }

        return $quiz_takes;
    }

    function delete_user_info( $user_id )
    {
        //get user quizzes
        $query = he_database::placeholder( "SELECT `id` FROM `se_he_quiz`
            WHERE `user_id`=?", $user_id );
        $quiz_ids = he_database::fetch_column($query);

        foreach ( $quiz_ids as $quiz_id )
        {
            he_quiz::delete_quiz($quiz_id);
        }

        //delete user plays
        $query = he_database::placeholder( "DELETE FROM `se_he_quiz_play`
            WHERE `user_id`=?", $user_id );

        he_database::query($query);
    }

    function get_index_list( $count, $type )
    {
        switch ( $type )
        {
        	case 'popular':
                $query = he_database::placeholder( "SELECT `quiz`.*, COUNT(`play`.`id`) AS `takes` FROM `se_he_quiz` AS `quiz`
					LEFT JOIN `se_he_quiz_play` AS `play` ON(`quiz`.`quiz_id`=`play`.`quiz_id`)
					WHERE `quiz`.`status`=1 AND `quiz`.`approved`=1
					GROUP BY `quiz`.`quiz_id`
					ORDER BY `takes` DESC LIMIT ?, ?", 0, $count );
        	break;

        	case 'commented':
                $query = he_database::placeholder( "SELECT `quiz`.*, COUNT(`comment`.`he_quizcomment_id`) AS `comment`
                    FROM `se_he_quiz` AS `quiz`
                    LEFT JOIN `se_he_quizcomments` AS `comment` ON(`quiz`.`quiz_id`=`comment`.`he_quizcomment_quiz_id`)
                    WHERE `quiz`.`status`=1 AND `quiz`.`approved`=1
                    GROUP BY `quiz`.`quiz_id`
                    ORDER BY `comment` DESC LIMIT ?, ?", 0, $count );
        	break;
        	
        	default:
                $query = he_database::placeholder( "SELECT * FROM `se_he_quiz` 
                    WHERE `status`=1 AND `approved`=1
                    ORDER BY `id` DESC LIMIT ?, ?", 0, $count );
        	break;
        }

        $res = he_database::query($query);
        $quiz_list = array();
        while ( $quiz = he_database::fetch_row_from_resource($res) )
        {
            $quiz['photo_url'] = ( $quiz['photo'] ) ? he_quiz::photo_url() . $quiz['photo'] : '';
            $quiz['photo_dir'] = ( $quiz['photo'] ) ? he_quiz::photo_dir() . $quiz['photo'] : '';

            $quiz['size'] = getimagesize($quiz['photo_dir']);

            $quiz_list[$quiz['quiz_id']] = $quiz;
        }

        return $quiz_list;
    }

    function get_quizzes( $first, $count )
    {
        $query = he_database::placeholder( "SELECT q.`quiz_id`, q.`name`, q.`description`, q.`status`, 
            q.`approved`, u.`user_username`
            FROM `se_he_quiz` AS q
            LEFT JOIN `se_users` AS u ON( q.`user_id`=u.`user_id` ) 
            LIMIT ?, ?", $first, $count );

        return he_database::fetch_array($query);
    }

    function count_quizzes()
    {
        $query = "SELECT COUNT(*) FROM `se_he_quiz`";

        return (int)he_database::fetch_field($query);
    }

    function approve_quizz( $quiz_id )
    {
        $query = he_database::placeholder( "UPDATE `se_he_quiz` SET `approved`=1 WHERE `quiz_id`=?", $quiz_id );

        return he_database::query($query);
    }

    function disapprove_quizz( $quiz_id )
    {
        $query = he_database::placeholder( "UPDATE `se_he_quiz` SET `approved`=0 WHERE `quiz_id`=?", $quiz_id );

        return he_database::query($query);
    }

    function find_cats()
    {
        $query = "SELECT * FROM `se_he_quiz_cat` WHERE 1";
        
        return he_database::fetch_array($query);
    }

    function add_cat( $label )
    {
        $label = trim($label);

        if ( !$label )
        {
            return false;
        }

        $query = he_database::placeholder( "INSERT INTO `se_he_quiz_cat` ( `label` ) VALUES( '?' )", $label );

        he_database::query($query);

        return he_database::insert_id();
    }

    function update_cat( $cat_id, $label )
    {
        $cat_id = (int)$cat_id;
        $label = trim($label);
        
        if ( !$cat_id || !$label )
        {
            return false;
        }

        $query = he_database::placeholder( "UPDATE `se_he_quiz_cat` SET `label`='?'
          WHERE `id`=?", $label, $cat_id );

        he_database::query($query);
    }

    function delete_cats( $except_cats )
    {
        if ( count($except_cats) == 0 )
        {
            return false;
        }

        $except_cats[] = 1;
        $except_cats_str = implode(',', $except_cats);

        $query = "DELETE FROM `se_he_quiz_cat` WHERE `id` NOT IN ($except_cats_str)";

        he_database::query($query);

        $query = "UPDATE `se_he_quiz` SET `cat_id`=1 WHERE `cat_id` NOT IN ($except_cats_str)";

        he_database::query($query);
    }
    
    function recent_taked_quizzes( $count = 10 )
    {
        $query = he_database::placeholder( "SELECT `play`.`user_id` AS `play_user_id`, `play`.`play_stamp`, `quiz`.*
			FROM `se_he_quiz_play` AS `play`
			INNER JOIN ( 
			    SELECT MAX(`play_stamp`) AS `play_stamp` FROM `se_he_quiz_play`
			    WHERE `play_stamp`!=0
			    GROUP BY `quiz_id`
			) AS `last` ON (`play`.`play_stamp`=`last`.`play_stamp`)
			INNER JOIN `se_he_quiz` AS `quiz` ON (`play`.`quiz_id`=`quiz`.`quiz_id`)
			WHERE `quiz`.approved = 1 AND `quiz`.`status`=1
			LIMIT ?", $count );
        
        $takes = array();
        $res = he_database::query($query);
        $quiz_arr = array();
        while ( $quiz = he_database::fetch_row_from_resource($res) )
        {
        	$quiz['user'] = new se_user(array( $quiz['play_user_id'] ));
        	$quiz['photo_url'] = ( $quiz['photo'] ) ? he_quiz::photo_url() . $quiz['photo'] : '';
        	
        	$quiz_arr[$quiz['quiz_id']] = $quiz;
        }
        
        return $quiz_arr;
    }
}

class se_quiz
{
	var $quiz_exists = false;
	
	function se_quiz( $user_id, $quiz_id )
	{
		$quiz_info = he_quiz::get_quiz_info($quiz_id);
		
		$this->quiz_exists = ( $quiz_info ) ? true : false;
	}
	
	function quiz_privacy_max( $cur_user )
	{
		return ( $cur_user->user_exists ) ? 1 : 0;
	}
}

?>
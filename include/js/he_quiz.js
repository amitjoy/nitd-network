
/**
 * @author Ermek
 * @copyright Hire-Experts LLC
 * @version Quiz 1.03
 */

var he_quiz = 
{
    check_results: function( count )
    {    
        var result = true;
        var result_count = 0;
        var $titles = $$(".he_quiz_results .result_title");

        $titles.getParent('tr').removeClass("he_quiz_error_bg");
        
        for(var i=0; i<$titles.length; i++)
        {
            var $title = $titles[i];
            var $parent = $title.getParent('tbody');
            var $description = $parent.getElements('textarea');
            
            var title = $title.get('value');
            title = ( title ) ? title.trim() : '';

            var description = $description.get('value');
            
            description = ( description ) ? description[0].trim() : '';
    
            if ( title.length == 0 && description.length > 0 ) 
            {
                $title.getParent('tr').addClass("he_quiz_error_bg");
                result = false;
            }
            
            if ( title.length > 0 )
            {
                result_count++;
            }
        }

        if ( result_count < count ) {
            var title = SELanguage.TranslateFormatted(690691100, [count]);
            var text = SELanguage.Translate(690691101);
            this.show_message(title, text, 'error');
            
            result = false;
        }
        
        return result;
    },
    
    show_message: function(title, text, type)
    {
        $message_node = $('he_message');
        
        $message_node.setStyle('display', 'none');
        $message_node.getElement('.t').set('text', title);
        $message_node.getElement('.c').set('text', text);
        $message_node.setProperty('class', '').addClass('he_message_' + type);
        
        window.setTimeout(function() {
            $message_node.setStyle('display', 'block');
        }, 100);
    },
    
    add_result: function(number)
    {
        var self = this;
        
    	this.result_number = ( this.result_number==undefined ) ? number + 1 : this.result_number + 1;

        $result_node = $('he_quiz_result_tpl').clone();
        $result_node.removeProperty('id');
        $result_node.getElement('.he_result_number').set('text', this.result_number);
        $result_node.getElement('.result_photo').setProperty('name', 'photo_' + this.result_number);
        
        $result_node.getElement('a.he_result_del').addEvent('click', function()
        {
        	self.delete_result(this);            
        });
        
        $results_node = $$('.he_quiz_results');

        $results_node.adopt($result_node);
    },
    
    delete_result: function($node)
    {
        var result = window.confirm(SELanguage.Translate(690691102));
        if ( !result ) {
            return false;            
        }
                
        var $parent = $($node).getParent('.he_quiz_result');
        
        $parent.dispose();
        
        this.result_number--;
        this.set_result_numbers();
    },
    
    set_result_numbers: function()
    {
        var $result_numbers = $$('.he_quiz_results').getElements('.he_result_number');
        var $result_files = $$('.he_quiz_results').getElements('.result_photo');
        
        for(var i=0; i<$result_numbers[0].length; i++)
        {
            $result_numbers[0][i].set('text', i + 1);
            $result_files[0][i].setProperty('name', 'photo_' + (i + 1));
        }
        
        this.result_number = $result_numbers[0].length;
    },
    
    check_questions: function(count)
    {    
        var result = true;
        var question_count = 0;
        var $questions = $$('.he_quiz_questions .quiz_question');
         
        for(var i=0; i<$questions.length; i++)
        {
            var $question = $questions[i];
            var $parent = $question.getParent('table');
            var $first_parent = $question.getParent('tr');    

            var question = $question.get('value');
            question = ( question ) ? question.trim() : '';

            $first_parent.removeClass('he_quiz_error_bg');

            if ( question.length == 0 )
            {
                 $first_parent.addClass('he_quiz_error_bg');
                result = false;
            }

            var $answers = $parent.getElements('.quiz_answer_label');
            for(var k=0; k<$answers.length; k++)
            {
                var $answer = $answers[k];
                var answer = $answer.get('value');
                answer = ( answer ) ? answer.trim() : '';

                $answer.getParent('tr').removeClass('he_quiz_error_bg');
                if ( answer.length == 0 )
                {
                    result = false;
                    $answer.getParent('tr').addClass('he_quiz_error_bg');
                }        
            }
        }
        
        if ( $questions.length < count ) 
        {        
            var title = SELanguage.TranslateFormatted(690691103, [count]);
            var text = SELanguage.Translate(690691104);
            
            this.show_message(title, text, 'error');
            
            result = false;
        }
        
        return result;
    },
    
    add_question: function(number)
    {
    	var self = this;
    	
        this.question_number = ( this.question_number==undefined ) ? number + 1 : this.question_number + 1;

        $question_node = $('he_quiz_question_tpl').clone();

        $question_node.removeProperty('id');
        $question_node.getElement('.he_question_number').set('text', this.question_number);
        $question_node.getElements('.quiz_question_number').set('value', this.question_number);
        $question_node.getElements('.quiz_answer_id').set('name', 'answer_id[' + this.question_number + '][]');
        $question_node.getElements('.quiz_answer_result_id').set('name', 'answer_result_id[' + this.question_number + '][]');
        $question_node.getElements('.quiz_answer_label').set('name', 'answer_label[' + this.question_number + '][]');    
        $question_node.getElement('.question_photo').setProperty('name', 'photo_' + this.question_number);
        
        $question_node.getElement('a.delete_question_btn').addEvent('click', function()
        {
        	self.delete_question(this);
        });
        
        $questions_node = $$('.he_quiz_questions');
        $questions_node.adopt($question_node);
    },
    
    delete_question: function($node)
    {
        var result = window.confirm(SELanguage.Translate(690691105));
        if ( !result ) {
            return false;            
        }
                
        var $parent = $($node).getParent('.he_quiz_question');
        
        $parent.dispose();
        
        this.question_number--;
        this.set_question_numbers();
    },
    
    set_question_numbers: function()
    {
        var $question_numbers = $$('.he_quiz_questions').getElements('.he_question_number');
        var $question_files = $$('.he_quiz_questions').getElements('.question_photo');
        
        for(var i=0; i<$question_numbers[0].length; i++)
        {
            $question_numbers[0][i].set('text', i + 1);
            $question_files[0][i].setProperty('name', 'photo_' + (i + 1));
        }
        
        this.question_number = $question_numbers[0].length;
    },
    
    switch_tab: function(node, id)
    {
        var $node = $(node);
        var $parent = $node.getParent('.he_quiz_list_block');
        
        $parent.getElements('.he_quiz_tab').removeClass('active_tab');
        $node.addClass('active_tab');
        
        $parent.getElements('.he_quiz_list').set('tween', {duration: 250});
        $parent.getElements('.he_quiz_list').fade('out');
        
        window.setTimeout(function(){
            $parent.getElements('.he_quiz_list').removeClass('active_tab');
            $(id).set('tween', {duration: 250});
            $(id).fade('in');
            $(id).addClass('active_tab');
        }, 250);
    },
    
    check_general: function($form)
    {
    	var $name_input = $form.getElement('input[name="name"]');
    	
    	if ( $name_input.value.trim().length == 0 )
    	{
    		this.show_message(SELanguage.Translate(690691090), SELanguage.Translate(690691094), 'error');
    		return false;
    	}
    	
    	return true;
    }
};

var he_quiz_play = 
{
    construct: function()
    {
        var self = this;
        
        this.current = 1;
        this.size = 610;
        this.step = 61;
        this.left = 0;
        this.interval = 15;
        
        this.navigator = {right: true, left: true};
        
        this.$questions = $$('.he_quiz_question');
        this.count = this.$questions.length;
        this.width = this.size * this.count;
        this.$cont = $$('.he_quiz_questions');
        this.$bar = $$('.he_quiz_status_bar');
        this.$border = $$('.he_quiz_status_border');
        
        this.$cont.setStyle('width', this.width);
        this.bar_size = this.$border.getStyle('width')[0].toInt();
        
        this.set_bar_size();
        
        $('he_quiz_next').addEvent('click', function(){
            self.move_question('right');
        });
        $('he_quiz_prev').addEvent('click', function(){
            self.move_question('left');
        });    
        
        $$('.he_quiz_answer input[type=radio]').set('checked', false);

        $$('.he_quiz_answer').addEvent('click', function(){
            $(this).getParent('.he_quiz_question').getElements('.he_quiz_answer').removeClass('he_quiz_checked');
            
            $(this).getElement('input[type=radio]').set('checked', true);
            $(this).addClass('he_quiz_checked');
            window.setTimeout(function(){
                self.move_question('right');
            }, 100);
        });
    },
    
    move_question: function(direction, rollover)
    {
        var dir = ( direction=='right' ) ? -1 : 1;  
        
        if ( !this.navigator[direction] && !rollover ) {
            return false;
        }
        
        if ( this.current==1 && dir==1 ) {
            this.left = -this.width;
            this.current = this.count+1;
        }
        else if ( this.current==this.count && dir==-1 ) {
            this.left = this.size;
            this.current = 0;
        }
        
        if ( $$('.he_quiz_checked').length == this.count )
        {
            this.set_bar_size();
            $('he_quiz_form').submit();
            return false;
        }        

        this.current -= dir;
        this.navigator[direction] = false;
        
        var self = this;
        var i_count = this.size/this.step; 
        var interval = window.setInterval(function(){
            if (i_count==1) {
                window.clearInterval(interval);
                self.navigator[direction] = true;
            }
            self.left += dir * self.step; 
            self.$cont.setStyle('left', self.left);
            i_count--;
        }, this.interval);
        
        this.set_bar_size();
    },
    
    set_bar_size: function()
    {
        var answers = $$('.he_quiz_checked').length; 
        var width = answers*this.bar_size/this.count;

        this.$bar.setStyle('width', width);
    },
    
    check_answers: function()
    {
        for ( var i=0; i<this.count; i++ )
        {            
            var $question = this.$questions[i];
            var checked = ( $question.getElements('.he_quiz_checked').length==1 ) ? true : false;
            
            if ( !checked )
            {
                var error_question = $question.get('id').substr(14);
                
                this.error_question(error_question);
                
                return false;
            }
        }

        return true;
    },
    
    error_question: function(error_question)
    {
        var title = SELanguage.Translate(690691106);
        var text = SELanguage.Translate(690691107);
        
        he_quiz.show_message(title, text, 'error');
        
        if ( this.current>error_question )    
        {
            var count = this.current - error_question; 
            for ( var i=0; i<count; i++ )
            {
                this.move_question('left', true);
            }
        }
        else if ( this.current<error_question )
        {
            var count = error_question - this.current; 
            for ( var i=0; i<count; i++ )
            {
                this.move_question('right', true);
            }
        }
    }
};
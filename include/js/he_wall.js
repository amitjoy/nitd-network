
/**
 * @author Ermek
 * @copyright Hire-Experts LLC
 * @version Wall 3.1
 */

var he_wall = 
{
	action_ids: [],
	wall_object: '',
	wall_object_id: 0,
	action_page: false,
	actions_per_page: 10,
	allow_post: false,
	show_video_player: false,
	
	construct: function( wall_id )
	{
		var self = this;
		this.wall_id = wall_id;
		this.$wall_container = $('wall_' + this.wall_id);
		this.$actions = [];
		
		this.like_btn_locked = false;
		this.del_comment_btn_locked = false;
		this.show_more_btn_locked = false;
		
		
		this.$hide_action_tpl = $('hide_action_tpl');
		this.$remove_action_tpl = $('remove_action_tpl');
		this.$comment_tpl = $('comment_tpl');
		this.$show_more_btn = $('show_more_btn');
		
		this.$action_input = this.$wall_container.getElement('.post_action_input');
		this.$action_btn = this.$wall_container.getElement('.post_action_btn');
		
		this.action_ids.each(function(action_id, index)
		{
			var $action_node = $('wall_action_' + action_id);
			self.$actions[action_id] = $action_node;
			
			self.prepare_action(action_id);
			
			self.last_action_id = action_id;
			
			if ( index == 0 )
			{
				self.first_action_id = action_id;
			}
		});
		if (this.action_page)
		{
			return false;
		}
		this.$show_more_btn.addEvent('click', function()
		{
			self.show_more();
			this.blur();
		});
		
		if ( SocialEngine.Viewer.user_exists && this.allow_post )
		{
			this.prepare_post_action();
		}
	},
	
	prepare_action: function( action_id )
	{
		if ( SocialEngine.Viewer.user_exists )
		{
			this.prepare_delete_comment(action_id);
			this.prepare_action_like(action_id);
			this.prepare_action_comment(action_id);
			this.prepare_action_hide(action_id);
			this.prepare_action_remove(action_id);
		}
	},
	
	prepare_post_action: function()
	{
		var self = this;
		var start_height = this.$action_input.getStyle('height').toInt();
		var active_height = 0;
		var $action_input_div = this.$action_input.getParent('.input_div');
		var $upload_photo_form = this.$wall_container.getElement('.upload_photo_form');
		var $post_link_btn = this.$wall_container.getElement('.post_link_btn');
		var $post_video_btn = this.$wall_container.getElement('.post_video_btn');
		var $video_providers = this.$wall_container.getElement('.video_provider');
		
		this.prepare_action_tabs();
		
		this.$action_input.addEvent('focus', function()
		{			
			$action_input_div.addClass('active');
			this.value = ( this.value == SELanguage.Translate(690706010) ) ? '' : this.value;
			
			if ( active_height == 0 )
			{
				active_height = this.getStyle('height').toInt();
			}
			
			if ( this.value == '' )
			{
				this.setStyle('height', active_height);
			}
		})
		.addEvent('blur', function()
		{
			if ( this.value != '' ) { return false; }
			
			$action_input_div.removeClass('active');
			this.setStyle('height', start_height);
			this.value = ( this.value == '' ) ? SELanguage.Translate(690706010) : this.value;
		})
		.addEvent('scroll', function()
		{
			var inc_height = ( Browser.Engine.trident ) ? 16 : 7;
			var height = this.getStyle('height').toInt();
			if ( height > 180 ) { return false; }
			
			this.setStyle('height', height + inc_height);
		});
		
		this.$action_btn.addEvent('click', function()
		{
			self.post_action();
		});
		
		
		$post_link_btn.addEvent('click', function()
		{
			self.post_link(this);
		});
		
		$post_video_btn.addEvent('click', function()
		{
			self.post_video(this);
		});
		
		$video_providers.addEvent('change', function()
		{
			var $parent_cont = this.getParent('.video_provider_div');
			
			$parent_cont.removeClass('youtube_video').removeClass('vimeo_video');
			
			if ( this.value == 'vimeo' )
			{
				$parent_cont.addClass('vimeo_video');
			}
			else if ( this.value == 'youtube' )
			{
				$parent_cont.addClass('youtube_video');
			}
		});
		
		this.prepare_action_privacy();
	},
	
	prepare_action_tabs: function()
	{
		var self = this;
		var $tab_icons = this.$wall_container.getElements('.wall_tab_icon');
		var $upload_photo_btn = this.$wall_container.getElement('.upload_photo_btn');
		var $add_link_btn = this.$wall_container.getElement('.add_link_btn');
		var $add_music_btn = this.$wall_container.getElement('.add_music_btn');
		var $add_video_btn = this.$wall_container.getElement('.add_video_btn');
		var $default_tab = this.$wall_container.getElement('.default_tab');
		var $upload_photo_tab = this.$wall_container.getElement('.upload_photo_tab');
		var $add_link_tab = this.$wall_container.getElement('.add_link_tab');
		var $add_music_tab = this.$wall_container.getElement('.add_music_tab');
		var $add_video_tab = this.$wall_container.getElement('.add_video_tab');
		var $close_tab_btn = this.$wall_container.getElements('.tab_contents .close_tab');
		
		$tab_icons
		.addEvent('mouseover', function()
		{
			var $tooltip = this.getElement('.wall_tooltip_cont');
			$tooltip.removeClass('display_none');
		})
		.addEvent('mouseout', function()
		{
			var $tooltip = this.getElement('.wall_tooltip_cont');
			$tooltip.addClass('display_none');
		});
		
		$upload_photo_btn.addEvent('click', function()
		{
			var $tooltip = this.getElement('.wall_tooltip_cont');
			$tooltip.addClass('display_none');
			
			$default_tab.addClass('display_none');
			$upload_photo_tab.getElement('.btn_div').grab(self.$action_privacy_block);
			$upload_photo_tab.removeClass('display_none');
			
			self.$action_input.fireEvent('focus');
		});
		
		$add_link_btn.addEvent('click', function()
		{
			var $tooltip = this.getElement('.wall_tooltip_cont');
			$tooltip.addClass('display_none');
			
			$default_tab.addClass('display_none');
			$add_link_tab.getElement('.btn_div').grab(self.$action_privacy_block);
			$add_link_tab.removeClass('display_none');
			
			self.$action_input.fireEvent('focus');
		});
		
		$add_music_btn.addEvent('click', function()
		{
			var $tooltip = this.getElement('.wall_tooltip_cont');
			$tooltip.addClass('display_none');
			
			$default_tab.addClass('display_none');
			$add_music_tab.getElement('.btn_div').grab(self.$action_privacy_block);
			$add_music_tab.removeClass('display_none');
			
			self.$action_input.fireEvent('focus');
		});
		
		$add_video_btn.addEvent('click', function()
		{
			var $tooltip = this.getElement('.wall_tooltip_cont');
			$tooltip.addClass('display_none');
			
			$default_tab.addClass('display_none');
			$add_video_tab.getElement('.btn_div').grab(self.$action_privacy_block);
			$add_video_tab.removeClass('display_none');
			
			self.$action_input.fireEvent('focus');
		});
		
		$close_tab_btn.addEvent('click', function()
		{
			this.getParent('.tab_content').addClass('display_none');
			$default_tab.getElement('.btn_div').grab(self.$action_privacy_block);
			$default_tab.removeClass('display_none');

			this.blur();
		});
	},
	
	prepare_action_like: function( action_id )
	{
		var self = this;
		var $action_node = this.$actions[action_id];
		var $like_action = $action_node.getElement('.like_btn');
		
		$like_action.addEvent('click', function()
		{
			if ( self.like_btn_locked ) { return false; }
			
			self.like_action(action_id, this);
			this.blur();
		});
	},
	
	prepare_action_comment: function( action_id )
	{
		var self = this;
		var $action_node = this.$actions[action_id];
		var $comment_action = $action_node.getElement('.comment_btn');
		var $comment_add_box = $action_node.getElement('.comment_add');
		var $comment_input = $comment_add_box.getElement('.comment_text_input');
		var $add_comment_btn = $comment_add_box.getElement('.add_comment_btn');
		
		$comment_action.addEvent('click', function()
		{
			$comment_add_box.addClass('active');
			$comment_add_box.removeClass('display_none');
			$comment_input.fireEvent('focus');
			self.toggle_comment_box($action_node);
			this.blur();
			$comment_input.focus();
		});
				
		$comment_input.addEvent('focus', function()
		{
			$comment_add_box.addClass('active');
			this.value = ( this.value == SELanguage.Translate(690706010) ) ? '' : this.value;
		})
		.addEvent('blur', function()
		{
			if ( this.value.trim() != '' ) { return false; }
			
			$comment_add_box.removeClass('active');
			this.value = ( this.value.trim() == '' ) ? SELanguage.Translate(690706010) : this.value;
		});
		
		$add_comment_btn.addEvent('click', function()
		{
			self.add_comment(action_id, this, $comment_input);
		});
	},
	
	prepare_delete_comment: function( action_id )
	{
		var self = this;
		var $action_node = this.$actions[action_id];
		var $delete_comments = $action_node.getElements('.delete_comment');
		
		$delete_comments.addEvent('click', function()
		{
			self.delete_comment(action_id, this);
		});		
	},
	
	prepare_action_hide: function( action_id )
	{
		var self = this;
		var $action_node = this.$actions[action_id];
		var $hide_action_btn = $action_node.getElement('.hide_action');
		
		if ( $hide_action_btn == null ) { return false; }
		
		$action_node
		.addEvent('mouseover', function()
		{
			$hide_action_btn.setStyle('opacity', 1);
		})
		.addEvent('mouseout', function()
		{
			$hide_action_btn.setStyle('opacity', 0);
		});
		
		$hide_action_btn.addEvent('click', function()
		{
			var $hide_action = self.$hide_action_tpl.clone().removeProperty('id');
			
			$hide_action.getElement('.cancel_btn').addEvent('click', function()
			{
				$hide_action.dispose();
				$action_node.setStyle('display', 'block');
			});
			
			$hide_action.getElement('.hide_btn').addEvent('click', function()
			{
				self.hide_action(action_id, $hide_action);
			});
			
			$action_node.setStyle('display', 'none');
			$hide_action.inject($action_node, 'before');
		});
	},
	
	prepare_action_remove: function( action_id )
	{
		var self = this;
		var $action_node = this.$actions[action_id];
		var $remove_action_btn = $action_node.getElement('.remove_action');
		
		if ( $remove_action_btn == null ) { return false; }
		
		$action_node
		.addEvent('mouseover', function()
		{
			$remove_action_btn.setStyle('opacity', 1);
		})
		.addEvent('mouseout', function()
		{
			$remove_action_btn.setStyle('opacity', 0);
		});
		
		$remove_action_btn.addEvent('click', function()
		{
			var $remove_action = self.$remove_action_tpl.clone().removeProperty('id');
		
			$remove_action.getElement('.cancel_btn').addEvent('click', function()
			{
				$remove_action.dispose();
				$action_node.setStyle('display', 'block');
			});
			
			$remove_action.getElement('.remove_btn').addEvent('click', function()
			{
				self.remove_action(action_id, $remove_action);
			});
			
			$action_node.setStyle('display', 'none');
			$remove_action.inject($action_node, 'before');
		});
	},
	
	prepare_action_privacy: function()
	{
		var self = this;
		
		this.action_privacy_level = 63;
		
		this.$action_privacy_block = this.$wall_container.getElement('.privacy_block');
		
		this.$action_privacy_btn = this.$wall_container.getElement('.action_privacy_btn');
		this.$action_privacy_levels_cont = this.$wall_container.getElement('.wall_action_privacy');
		this.$action_privacy_levels = this.$action_privacy_levels_cont.getElements('.privacy_option');
		this.$action_active_privacy = this.$action_privacy_levels_cont.getElement('.active');
		
		if ( this.$action_active_privacy != null )
		{
			this.action_privacy_level = this.$action_active_privacy.getProperty('id').substr('8');
		}
		
		this.$action_privacy_btn.addEvent('click', function()
		{
			self.hide_privacy_block = false;
			
			self.$action_privacy_levels_cont.removeClass('display_none');
			this.blur();
		});
		
		this.$action_privacy_levels.addEvent('click', function()
		{
			self.action_privacy_level = this.getProperty('id').substr('8');
			
			self.$action_active_privacy.removeClass('active');
			this.addClass('active');
			self.$action_active_privacy = this;
			
			self.$action_privacy_levels_cont.addClass('display_none');
		});
		
		self.hide_privacy_block = false;
		
		this.$action_privacy_levels_cont.addEvent('mouseout', function()
		{
			if ( !self.$action_privacy_levels_cont.hasClass('display_none') )
			{
				self.hide_privacy_block = true;
			}
		})
		.addEvent('mouseover', function()
		{
			self.hide_privacy_block = false;
		});
		
		$$('body').addEvent('click', function()
		{
			if ( !self.hide_privacy_block )
			{
				self.hide_privacy_block= true;
			}
			else
			{
				self.$action_privacy_levels_cont.addClass('display_none');
				self.hide_privacy_block = false;
			}
		});
	},
	
	hide_action: function( action_id, $hide_node )
	{
		var self = this;
		var $action_node = this.$actions[action_id];
		
		$hide_node.addClass('hide_loading');
		$hide_node.getElements('.button')
			.setProperty('disabled', 'disabled')
			.addClass('disabled');
		
		new Request.JSON({
			method: 'post',
			url: 'he_wall_ajax_request.php?wall_object=' + self.wall_object + '&wall_object_id=' + self.wall_object_id,
			data: {	'task': 'hide_action', 'action_id': action_id, 'no_cache': Math.random() },
			onSuccess : function(result) {
				if ( result )
				{
					$hide_node.dispose();
					$action_node.dispose();
				}
			}
		}).send();
	},
	
	remove_action: function( action_id, $remove_node )
	{
		var self = this;
		var $action_node = this.$actions[action_id];
		
		$remove_node.addClass('hide_loading');
		$remove_node.getElements('.button')
		.setProperty('disabled', 'disabled')
		.addClass('disabled');
		
		new Request.JSON({
			method: 'post',
			url: 'he_wall_ajax_request.php?wall_object=' + self.wall_object + '&wall_object_id=' + self.wall_object_id,
			data: {	'task': 'remove_action', 'action_id': action_id, 'no_cache': Math.random() },
			onSuccess : function(result) {
				if ( result )
				{
					$remove_node.dispose();
					$action_node.dispose();
				}
			}
		}).send();
	},
	
	like_action: function( action_id, $like_btn )
	{		
		var self = this;
		var $action_node = this.$actions[action_id];
		var $like_box = $action_node.getElement('.like_box');
		var $like_value = $action_node.getElement('.like_content');
		var	$like_loading = $like_btn.getParent('.wall_action_options');
		
		this.like_btn_locked = true;
		$like_loading.addClass('hide_loading');
		
		new Request.JSON({
			method: 'post',
			url: 'he_wall_ajax_request.php?wall_object=' + self.wall_object + '&wall_object_id=' + self.wall_object_id,
			data: {	'task': 'like_action', 'action_id': action_id, 'no_cache': Math.random() },
			onSuccess : function(result)
			{				
				var visible = ( result.value != '' ); 
				var lang_id = ( result.like == 1 ) ? 690706009 : 690706008;
				
				$like_btn.set('text', SELanguage.Translate(lang_id));
				$like_value.set('html', result.value);
				
				if ( visible ) { $like_box.removeClass('display_none'); }
				else { $like_box.addClass('display_none'); }
				
				self.toggle_comment_box($action_node);
				
				self.like_btn_locked = false;
				$like_loading.removeClass('hide_loading');
			}
		}).send();
	},
	
	add_comment: function( action_id, $btn_node, $input )
	{
		var self = this;
		
		if ( $input.value.length > 1000 )
		{
			alert(SELanguage.Translate(690706058));
			$input.fireEvent('focus');
			
			return false;
		}
		
		$btn_node.setProperty('disabled', 'disabled').addClass('disabled');
		$input.addClass('loading');
		$input.disabled = true;
		
		new Request.JSON({
			method: 'post',
			url: 'he_wall_ajax_request.php?wall_object=' + self.wall_object + '&wall_object_id=' + self.wall_object_id,
			data: {	'task': 'add_comment', 'action_id': action_id, text: $input.value, 'no_cache': Math.random() },
			onSuccess : function(data)
			{
				$btn_node.removeProperty('disabled').removeClass('disabled');
				$input.removeClass('loading');
				$input.disabled = false;
				$input.fireEvent('blur');
				
				if ( data.result == 1 && data.comment_info )
				{
					self.new_comment(action_id, data.comment_info);
					$input.value = '';
					$input.fireEvent('focus').fireEvent('blur');
				}
				else
				{
					alert(data.message);
					$input.fireEvent('focus');
				}
			}
		}).send(); 
	},
	
	new_comment: function( action_id, comment_info )
	{
		if ( comment_info.id == undefined ) { return false; }
		
		var self = this;
		var $action_node = this.$actions[action_id];
		var $feed_comments = $action_node.getElement('.feed_comments');
		var $comment = this.$comment_tpl.clone();
		
		$comment.setProperty('id', 'comment_' + comment_info.id);
		$comment.getElement('.comment_actual_text').set('html', comment_info.text);
		$comment.getElement('.date_time').set('text', comment_info.posted_date);
		
		$comment.getElement('.delete_comment').addEvent('click', function()
		{
			self.delete_comment(action_id, this);
		});
		
		$feed_comments.grab($comment, 'bottom');
	},
	
	delete_comment: function( action_id, $delete_btn )
	{
		var c_result = confirm(SELanguage.Translate(690706011));
		
		if ( !c_result ) { return false; }
		if ( this.del_comment_btn_locked ) { return false; }
		
		var self = this;
		var $action_node = this.$actions[action_id];
		var $feed_comments = $action_node.getElement('.feed_comments');
		var $del_comment_loading = $delete_btn.getParent('.wall_delete_comment');
		var $comment = $delete_btn.getParent('.comment');
		var comment_id = $comment.getProperty('id').substr(8);
		
		this.del_comment_btn_locked = true;
		$del_comment_loading.addClass('hide_loading');
		
		new Request.JSON({
			method: 'post',
			url: 'he_wall_ajax_request.php?wall_object=' + self.wall_object + '&wall_object_id=' + self.wall_object_id,
			data: {	'task': 'delete_comment', 'comment_id': comment_id, 'no_cache': Math.random() },
			onSuccess : function(result)
			{
				if ( result == 1 )
				{
					$comment.dispose();
					
					if ( $feed_comments.getElements('.comment').length == 0 )
					{
						self.toggle_comment_box($action_node);
					}
				}
				
				self.del_comment_btn_locked = false;
				$del_comment_loading.removeClass('hide_loading');
			}
		}).send();
	},
	
	toggle_comment_box: function( $action_node )
	{
		var $comment_box = $action_node.getElement('.comment_box');
		var visible = ( $comment_box.getElements('.display_none').length != 2 || $comment_box.getElements('.comment').length != 0 );
		
		if ( visible ) { $comment_box.removeClass('display_none'); }
		else { $comment_box.addClass('display_none'); }
	},
	
	show_more: function()
	{		
		if ( this.show_more_btn_locked ) { return false; }

		this.show_more_btn_locked = true;
		
		var self = this;
		var $show_more_block = this.$show_more_btn.getParent('.wall_show_more');
		var $wall_actions = this.$wall_container.getElement('.wall_actions');
		
		$show_more_block.addClass('hide_loading');
		
		new Request.JSON({
			method: 'post',
			url: 'he_wall_ajax_request.php?wall_object=' + self.wall_object + '&wall_object_id=' + self.wall_object_id,
			data: {	'task': 'show_more', 'last_action_id': self.last_action_id, 'no_cache': Math.random() },
			onSuccess : function(result)
			{					
				if ( result.action_ids.length != 0 )
				{
					var $container = new Element('div');
					$container.innerHTML = result.html;
					$wall_actions.grab($container, 'bottom');
					
					result.action_ids.each(function(action_id, index)
					{
						self.action_ids.push(action_id);
						
						var $action_node = $('wall_action_' + action_id);
						self.$actions[action_id] = $action_node;
						
						self.prepare_action(action_id);
						self.last_action_id = action_id; 
					});
					
					if ( self.actions_per_page == result.action_ids.length )
					{
						self.show_more_btn_locked = false;
					}
					else
					{
						self.$show_more_btn.addClass('display_none');
						$show_more_block.getElement('.no_more_info').removeClass('display_none');
					}
					
				}
				else
				{
					self.$show_more_btn.addClass('display_none');
					$show_more_block.getElement('.no_more_info').removeClass('display_none');
				}
				
				$show_more_block.removeClass('hide_loading');
			}
		}).send();
	},
	
	post_action: function()
	{
		if ( this.$action_input.value == SELanguage.Translate(690706010) || this.$action_input.value.trim() == '' )
		{
			this.$action_input.fireEvent('focus');
			
			return false;
		}
		else if ( this.$action_input.value.length > 1000 )
		{
			alert(SELanguage.Translate(690706058));
			this.$action_input.fireEvent('focus');
			
			return false;
		}
		
		var self = this;
		
		var $wall_tabs = this.$wall_container.getElement('.wall_post_tabs');
		
		this.$action_btn.setProperty('disabled', 'disabled').addClass('disabled');
		this.$action_input.addClass('loading');
		this.$action_input.disabled = true;
		
		$wall_tabs.addClass('hide_loading');		
		
		new Request.JSON({
			method: 'post',
			url: 'he_wall_ajax_request.php?wall_object=' + self.wall_object + '&wall_object_id=' + self.wall_object_id,
			data: 
			{
				'task': 'post_action',
				'text': self.$action_input.value,
				'first_action_id': self.first_action_id,
				'action_privacy_level': self.action_privacy_level,
				'no_cache': Math.random() 
			},
			onSuccess : function(result)
			{
				if ( result.action_ids.length != 0 )
				{
					self.grab_new_actions(result);
				}
				
				self.$action_btn.removeProperty('disabled').removeClass('disabled');
				self.$action_input.removeClass('loading').value = '';
				self.$action_input.disabled = false;
				self.$action_input.fireEvent('blur');
				
				$wall_tabs.removeClass('hide_loading');
			}
		}).send();
	},
	
	grab_new_actions: function(result)
	{
		var self = this;
		var $wall_actions = this.$wall_container.getElement('.wall_actions');
		
		var $container = new Element('div');
		
		$container.innerHTML = result.html;		
		
		$wall_actions.grab($container, 'top');
		
		if ( !Browser.Engine.gecko )
		{
			var $music_players = $container.getElements('div.wall_music_player script');

			$music_players.each(function($player_js, index)
			{
				var player_js = $player_js.get('html');
				eval(player_js);
			});
		}
		
		result.action_ids.each(function(action_id, index)
		{
			self.action_ids.push(action_id);
			
			var $action_node = $('wall_action_' + action_id);
			self.$actions[action_id] = $action_node;
			
			self.prepare_action(action_id);
			
			if ( index == 0 )
			{
				self.first_action_id = action_id;
			}
		});
	},
	
	post_photo: function($node)
	{
		var self = this;
		
		var $upload_photo_tab = this.$wall_container.getElement('.upload_photo_tab');
		var $tab_title = $upload_photo_tab.getElement('.tab_title');
		var $wall_photo = $upload_photo_tab.getElement('.wall_photo');
		var $wall_photo_text = $upload_photo_tab.getElement('.share_photo_text');
		var $wall_photo_privacy = $upload_photo_tab.getElement('.share_photo_privacy');
		
		if ( $wall_photo.value.trim() == '' && this.$action_input.value.trim() == '' )
		{
			return false;
		}
		else if ( this.$action_input.value.length > 1000 )
		{
			alert(SELanguage.Translate(690706058));
			this.$action_input.fireEvent('focus');
			
			return false;
		}
		
		return AIM.submit($node, {
			'onStart': function()
			{
				self.$action_btn.setProperty('disabled', 'disabled').addClass('disabled');
				self.$action_input.addClass('loading');
				self.$action_input.disabled = true;
				$tab_title.addClass('hide_loading');
				
				$wall_photo_text.value = self.$action_input.value;
				$wall_photo_privacy.value = self.action_privacy_level;
			},
			'onComplete': function(response)
			{
				eval('var response = ' + response);
				$tab_title.removeClass('hide_loading');

				if ( response.result == 1 )
				{
					self.load_new_actions($wall_photo, $wall_photo_text);
					if ( self.wall_object == 'group' )
					{
						window.setTimeout(function(){
							SocialEngine.GroupFiles.getFiles();
						}, 400);
					}
				}
				else
				{
					alert(response.message);
					
					$wall_photo.value = '';
					$wall_photo_text.value = '';
					self.$action_btn.removeProperty('disabled').removeClass('disabled');
					self.$action_input.removeClass('loading').value = '';
					self.$action_input.disabled = false;
					self.$action_input.fireEvent('blur');
				}
			}
		});
	},
	
	post_music: function($node)
	{
		var self = this;
		
		var $add_music_tab = this.$wall_container.getElement('.add_music_tab');
		var $tab_title = $add_music_tab.getElement('.tab_title');
		var $wall_music = $add_music_tab.getElement('.wall_music');
		var $wall_music_text = $add_music_tab.getElement('.share_music_text');
		var $wall_music_privacy = $add_music_tab.getElement('.share_music_privacy');
		
		if ( $wall_music.value.trim() == '' && this.$action_input.value.trim() == '' )
		{
			return false;
		}
		else if ( this.$action_input.value.length > 1000 )
		{
			alert(SELanguage.Translate(690706058));
			this.$action_input.fireEvent('focus');
			
			return false;
		}
		
		return AIM.submit($node, {
			'onStart': function()
			{
				self.$action_btn.setProperty('disabled', 'disabled').addClass('disabled');
				self.$action_input.addClass('loading');
				self.$action_input.disabled = true;
				$tab_title.addClass('hide_loading');
				
				$wall_music_text.value = self.$action_input.value;
				$wall_music_privacy.value = self.action_privacy_level;
			},
			'onComplete': function(response)
			{
				eval('var response = ' + response);
				
				$tab_title.removeClass('hide_loading');
				
				if ( response.result == 1 )
				{
					self.load_new_actions($wall_music, $wall_music_text, 'music');
				}
				else
				{
					if ( response.message )
					{
						alert(response.message);
					}
					
					$wall_music.value = '';
					$wall_music_text.value = '';
					self.$action_btn.removeProperty('disabled').removeClass('disabled');
					self.$action_input.removeClass('loading').value = '';
					self.$action_input.disabled = false;
					self.$action_input.fireEvent('blur');
				}
			}
		});
	},
	
	load_new_actions: function($wall_photo, $wall_photo_text, action_type)
	{
		var self = this;
		
		new Request.JSON({
			method: 'post',
			url: 'he_wall_ajax_request.php?wall_object=' + self.wall_object + '&wall_object_id=' + self.wall_object_id,
			data: {	'task': 'new_actions', 'first_action_id': self.first_action_id, 'no_cache': Math.random() },
			onSuccess : function(result)
			{
				if ( result.action_ids.length != 0 )
				{
					self.grab_new_actions(result);
				}
				
				$wall_photo.value = '';
				$wall_photo_text.value = '';
				self.$action_btn.removeProperty('disabled').removeClass('disabled');
				self.$action_input.removeClass('loading').value = '';
				self.$action_input.disabled = false;
				self.$action_input.fireEvent('blur');
				
				self.$wall_container.getElement('.upload_photo_tab .close_tab').fireEvent('click');
				self.$wall_container.getElement('.add_music_tab .close_tab').fireEvent('click');
			}
		}).send();
	},
	
	post_link: function( $post_link_btn )
	{
		var self = this;
		var $link_input = this.$wall_container.getElement('.share_link_input');
		var link = $link_input.value.trim();
		var $tab_title = this.$wall_container.getElement('.add_link_tab .tab_title');
		
		if ( link.length == 0 )
		{
			$link_input.focus();
			return false;
		}
		else if ( this.$action_input.value.length > 1000 )
		{
			alert(SELanguage.Translate(690706058));
			this.$action_input.fireEvent('focus');
			
			return false;
		}
		
		$post_link_btn.setProperty('disabled', 'disabled').addClass('disabled');
		this.$action_input.addClass('loading');
		this.$action_input.disabled = true;
		$tab_title.addClass('hide_loading');

		new Request.JSON({
			method: 'post',
			url: 'he_wall_ajax_request.php?wall_object=' + self.wall_object + '&wall_object_id=' + self.wall_object_id,
			data: {	'task': 'post_link', 'text': this.$action_input.value, 'action_privacy_level': self.action_privacy_level, 'link': link, 'first_action_id': this.first_action_id, 'no_cache': Math.random() },
			onSuccess : function(result)
			{
				if ( result.action_ids.length != 0 )
				{
					self.grab_new_actions(result);
				}
				
				$tab_title.removeClass('hide_loading');
				$post_link_btn.removeProperty('disabled').removeClass('disabled');
				$link_input.value = '';
				
				self.$action_input.removeClass('loading').value = '';
				self.$action_input.disabled = false;
				self.$action_input.fireEvent('blur');
				
				self.$wall_container.getElement('.add_link_tab .close_tab').fireEvent('click');
			}
		}).send();
	},
	
	post_video: function( $post_video_btn )
	{
		var self = this;
		var $video_provider = this.$wall_container.getElement('.video_provider');
		var $video_url = this.$wall_container.getElement('.video_url');
		
		var video_provider = $video_provider.value.trim();
		var video_url = $video_url.value.trim();
		
		if ( video_provider.length == 0 )
		{
			alert(SELanguage.Translate(690706072));
			$video_provider.focus();
			
			return false;
	}

		if ( video_url.length == 0 )
		{
			alert(SELanguage.Translate(690706073));
			$video_url.focus();
			
			return false;
		}
		
		var $tab_title = this.$wall_container.getElement('.add_video_tab .tab_title');
		
		$post_video_btn.setProperty('disabled', 'disabled').addClass('disabled');
		this.$action_input.addClass('loading');
		this.$action_input.disabled = true;
		$tab_title.addClass('hide_loading');
		
		new Request.JSON({
			method: 'post',
			url: 'he_wall_ajax_request.php?wall_object=' + self.wall_object + '&wall_object_id=' + self.wall_object_id,
			data: {	
				'task': 'post_video',
				'text': this.$action_input.value,
				'action_privacy_level': self.action_privacy_level,
				'video_provider': video_provider,
				'video_url': video_url,
				'first_action_id': this.first_action_id,
				'no_cache': Math.random() 
			},
			onSuccess : function(result)
{
				if ( result.action_ids != null && result.action_ids.length != 0 )
				{
					self.grab_new_actions(result);
				}
				else if ( result.result == 0 )
				{
					alert(result.message);
				}
				
				$tab_title.removeClass('hide_loading');
				$post_video_btn.removeProperty('disabled').removeClass('disabled');
				
				$video_provider.value = '';
				$video_provider.fireEvent('change');
				$video_url.value = '';
				
				self.$action_input.removeClass('loading').value = '';
				self.$action_input.disabled = false;
				self.$action_input.fireEvent('blur');
				
				self.$wall_container.getElement('.add_video_tab .close_tab').fireEvent('click');
			}
		}).send();
	}
};

var wall_comment = 
{

	count : 0,
		
	lock_page_button : false,

	construct : function( total, count, action_id )
	{
		var self = this;
		this.count = count;
		this.total = total;

		if ( this.total <= this.count )
		{
			$('comment_paging').dispose();
		}

		this.action_id = action_id;
		this.$page_button = $$('.page_button');
		
		if ( SocialEngine.Viewer.user_exists )
		{
			$$('.comment_add').removeClass('display_none');
		}

		this.$page_button.addEvent('focus', function()
		{
			this.blur();
		});

		this.$page_button.addEvent('click', function(e)
		{
			e.stop();
			
			if ( self.total <= self.count )
			{
				$('comment_paging').dispose();

				return false;
			}

			if ( self.lock_page_button )
			{
				return false;
			}

			this.addClass('hide_loading');
			var commentRequest = new Request.JSON({
				method : 'get',
				url : 'he_wall_ajax_request.php',
				onRequest : function()
				{
					self.lock_page_button = true;
				},
				onSuccess : function(result)
				{
						self.$page_button.removeClass('hide_loading');
						self.count += result.count;
						self.lock_page_button = false;

						var newDiv = new Element('div', {'html': result.html});
						var $delete_buttons = newDiv.getElements('.delete_comment');
						
						$delete_buttons.addEvent('click', function()
						{
							he_wall.delete_comment(result.action_id, this);
						});

						$('comments_container').grab(newDiv, 'top');
						$('total_current').innerHTML = self.count;
						
						if ( self.total <= self.count )
						{
							window.setTimeout(function()
							{
								$('comment_paging').dispose();
								return false;
							}, 1000);
						}
				}
			}).send('action_id='+action_id+'&task=paging&count='+self.count);
		});
	}
};


// Methods

function he_wall_show_more( $node )
{
	$node.blur();
	
	$node = $$($node);
	$node.addClass('display_none');
	$node.getPrevious().addClass('display_none');
	$node.getNext().removeClass('display_none');
}

function he_wall_show_player( $node )
{
	$node.blur();
	
	$node = $($node);
	
	if ( !he_wall.show_video_player )
	{
		var video_url = $node.getParent().getElement('.video_title').href;
		window.location.href = video_url;
		
		return false;
	}
	
	$node.addClass('display_none');
	$node.getNext().removeClass('display_none');
}
{include file='admin_header.tpl'}

{*  $Id: admin_userconnection.tpl 2009-09-07 09:36:11Z SocialEngineAddOns $ * }
<h2>{lang_print id=650002001}</h2>
 {lang_print id=650002003} <br>
  <br>

{if !empty($success_message)}
	  <div class="success"><img src="../images/success.gif" class="icon" border="0"> {lang_print id=$success_message}</div>
{/if}		
{if !empty($error_message_lsetting)}
  <div class='error'><img src='../images/error.gif' border='0' class='icon'> {$error_message_lsetting}</div>
{/if}
	<form action='admin_userconnection.php' method='post'>
	
	{ * License key * }	
	
	  <table cellpadding="0" cellspacing="0" width="600">
        <tbody>
          <tr>
            <td class='header' style="background:#213e67;font-size:13px;">
            	{lang_print id=650002057}
						</td>
          </tr>
          <tr>
            <td class="setting1">{lang_print id=650002058} {lang_print id=650002059}.</td>
          </tr>
          <tr>
            <td class="setting2">
							 <input type='text' class='text' name='lsettings' value='{$result.license_key}' size='50' maxlength='100'>
              {lang_print id=650002060}
						</td>
          </tr>
        </tbody>
      </table>
			<br />
			
{ * License key end here * }		
	
{ * Profile page code start from here * }	
	<table cellpadding='0' cellspacing='0' width='600'>
  		<tr>
    		<td class='header' style="background:#213e67;font-size:13px;">{lang_print id=650002045}</td>
  		</tr>
  		<td class='setting1'>
      	{lang_print id=650002052}
    	</td>
   		<tr>
    		<td class='setting2'>
      		<table cellpadding='2' cellspacing='0'>
        		<tr>
        			<td>
        				<input style="vertical-align:bottom;" type='radio' name='profile_page' id='profile_page_1' onclick="showContent('show_page')" value='1'{if !empty($result.profile_page)  } checked{/if} ><label for="profile_page_1">
        				{lang_print id=650002047}</label>
        			</td>	
  					</tr>
  					<tr>
  						<td>
  							<input style="vertical-align:bottom;" type='radio' name='profile_page' id='profile_page_0' onclick="hideContent('show_page')" value='0'{if empty($result.profile_page)  } checked{/if} ><label for="profile_page_0">
        				{lang_print id=650002053}</label>
  						</td>
  					</tr>
  				</table>
    		</td>
  		</tr>
	</table>
{ * Profile page code end here * }		
	
	<br />
<div id="show_page" {if empty($result.profile_page)} style="display:none"{/if}>
{ * User Connection Path Position start from here * }
  	<table cellpadding='0' cellspacing='0' width='600'>
  		<tr>
    		<td class='header'>{lang_print id=650002014}</td>
  		</tr>
    	<td class='setting1'>
      	{lang_print id=650002015}
    	</td>
  		<tr>
    		<td class='setting2'>
      		<table cellpadding='2' cellspacing='0'>
        		<tr>
          		<td><input style="vertical-align:bottom;" type='radio' name='userconnection_position' id='userconnection_position_0' value='0'{if $result.userconnection_position eq '0' } checked{/if}  >
          		<label for='userconnection_position_0'>{lang_print id=650002020}</label></td>
          		<td><a href="javascript:TB_show('{lang_print id=650002020}', 'admin_userconnections_layout_preview.php?preview_number=0&TB_iframe=true&height=500&width=500', '', './images/trans.gif');" title="click here">{lang_print id=650002043}</a></td>
          		
        		</tr>
          	<tr>
          		<td><input style="vertical-align:bottom;" type='radio' name='userconnection_position' id='userconnection_position_3' value='3'{if $result.userconnection_position eq '3' } checked{/if}  >
          		<label for='userconnection_position_3'>{lang_print id=650002034}</label></td>
          		<td><a href="javascript:TB_show('{lang_print id=650002034}', 'admin_userconnections_layout_preview.php?preview_number=3&TB_iframe=true&height=500&width=500', '', './images/trans.gif');" title="click here">{lang_print id=650002043}</a></td>
        		</tr>
        		<tr>
          		<td><input style="vertical-align:bottom;" type='radio' name='userconnection_position' id='userconnection_position_2' value='2'{if $result.userconnection_position eq '2' } checked{/if}  >
          		<label for='userconnection_position_2'>{lang_print id=650002017}</label></td>
          		<td><a href="javascript:TB_show('{lang_print id=650002017}', 'admin_userconnections_layout_preview.php?preview_number=2&TB_iframe=true&height=500&width=500', '', './images/trans.gif');" title="click here">{lang_print id=650002043}</a></td>
        		</tr>
        		<tr>
          		<td><input style="vertical-align:bottom;" type='radio' name='userconnection_position' id='userconnection_position_1' value='1'{if  $result.userconnection_position eq '1' } checked{/if} >
          		<label for='userconnection_position_1'>{lang_print id=650002016}</label></td>
          		<td> <a href="javascript:TB_show('{lang_print id=650002016}', 'admin_userconnections_layout_preview.php?preview_number=1&TB_iframe=true&height=300&width=700', '', './images/trans.gif');" title="click here">{lang_print id=650002043}</a></td>
        		</tr>
      		</table>
    		</td>
  		</tr>
		</table>
		<br />
{ * User Connection Path Position end here * }	
	
{ * Level setting start from here * }
		<table cellpadding='0' cellspacing='0' width='600'>
  		<tr>
    		<td class='header'>{lang_print id=650002008}</td>
  		</tr>
  		<td class='setting1'>
      	{lang_print id=650002009}
    	</td>
  		<tr>
    		<td class='setting2'>
      		<table cellpadding='2' cellspacing='0'>
        		<tr>
        			{if !empty($result)}
  							<input type="text" name="level"  value='{$result.level}'><br />
  						{else}
  							<input type="text" name="level"  value='3'><br />
  						{/if}
  						{if !empty($error_level_value)}
	  						<div class='error'>{lang_print id=$error_level_value}</div>
							{/if}
  					</tr>
  				</table>
    		</td>
  		</tr>
		</table>
		<br />
		
		
{ * Level setting end here * }


{ * Message setting start from here * }
		<table cellpadding='0' cellspacing='0' width='600'>
  		<tr>
    		<td class='header'>{lang_print id=650002004}</td>
  		</tr>
    	<td class='setting1'>
      	{lang_print id=650002005}
    	</td>
  		<tr>
    		<td class='setting2'>
      		<table cellpadding='2' cellspacing='0'>
        		<tr>
          		<td><input style="vertical-align:bottom;" type='radio' name='is_message' id='permission_userconnection_1' value='1'{if  !empty($result.is_message) } checked{/if} onclick="showContent('id_message_show')">
          		<label for='permission_userconnection_1'>{lang_print id=650002006}</label></td>
        		</tr>
          	<tr id="id_message_show" {if empty($result.is_message) } style="display:none;" {/if} >
          	  <td style="padding-left:25px;"><label for='id_message'>{lang_print id=650002013}</label><br />
          	  <textarea name='message' id='id_message' >{$result.message}</textarea>
          		</td>
          		
       			</tr>

       			{if !empty($error_message)} 
       				<tr><td style="padding-left:25px;"><div class='error'>{lang_print id=$error_message}</div><td></tr>
						{/if}	
      
						<tr>
          		<td><input style="vertical-align:bottom;" type='radio' name='is_message' id='permission_userconnection_0' value='0'{if empty($result.is_message) } checked{/if}  onclick="hideContent('id_message_show')">
          		<label for='permission_userconnection_0'>{lang_print id=650002007}</label></td>
        		</tr>
      		</table>
    		</td>
  		</tr>
		</table>
{ * Message setting end here * }
		

{ * Arrow design start from here * }
		<table cellpadding='0' cellspacing='0' width='600'>
  		<tr>
    		<td class='header'>{lang_print id=650002021}</td>
  		</tr>
    	<td class='setting1'>
      	{lang_print id=650002023}
    	</td>
  		<tr>
    		<td class='setting2'>
      		<table cellpadding='2' cellspacing='0'>
        		<tr>
          		<td><input style="vertical-align:top;" type='radio' name='userconnection_arrow' id='userconnection_arrow_0' value='0'{if  $result.userconnection_arrow eq '0' } checked{/if} >
          		<label for='userconnection_arrow_0'><img src="../images/icons/userconnection-arrow-green.gif" alt="arrow" width="18" title="user connection path" ></label></td>
        		
          		<td><input style="vertical-align:top;" type='radio' name='userconnection_arrow' id='userconnection_arrow_1' value='1'{if $result.userconnection_arrow eq '1' } checked{/if}  >
          		<label for='userconnection_arrow_1'><img src="../images/icons/userconnection-arrow-yellow.gif" alt="arrow" width="18" title="user connection path" > </label></td>
        		</tr>
        		<tr>
          		<td><input style="vertical-align:top;" type='radio' name='userconnection_arrow' id='userconnection_arrow_2' value='2'{if $result.userconnection_arrow eq '2' } checked{/if}  >
          		<label for='userconnection_arrow_2'> <img src="../images/icons/userconnection-arrow-orange.gif" alt="arrow" width="18" title="user connection path" > </label></td>
        		
          		<td><input style="vertical-align:top;" type='radio' name='userconnection_arrow' id='userconnection_arrow_3' value='3'{if $result.userconnection_arrow eq '3' } checked{/if}  >
          		<label for='userconnection_arrow_3'><img src="../images/icons/userconnection-arrow-blue.gif" alt="arrow" width="18" title="user connection path" > </label></td>
        		</tr>
      		</table>
    		</td>
  		</tr>
		
		<br />
{ * Arrow design end here * }		


{ * Degree design start from here * }
		
    	<td class='setting1'>
      	{lang_print id=650002024}
    	</td>
  		<tr>
    		<td class='setting2'>
      		<table cellpadding='2' cellspacing='0'>
        		<tr>
          		<td><input style="vertical-align:top;" type='radio' name='userconnection_degree' id='userconnection_degree_0' value='0'{if  $result.userconnection_degree eq '0'} checked{/if} >
          		<label for='userconnection_degree_0'><img src="../images/icons/user-connection-degree-green.gif" alt="arrow" width="18" title="degree" ></label></td>
        		
          		<td><input style="vertical-align:top;" type='radio' name='userconnection_degree' id='userconnection_degree_1' value='1'{if $result.userconnection_degree eq '1' } checked{/if}  >
          		<label for='userconnection_degree_1'><img src="../images/icons/user-connection-degree-yellow.gif" alt="arrow" width="18" title="degree" ></label></td>
        		</tr>
        		<tr>
          		<td><input style="vertical-align:top;" type='radio' name='userconnection_degree' id='userconnection_degree_2' value='2'{if $result.userconnection_degree eq '2' } checked{/if}  >
          		<label for='userconnection_degree_2'><img src="../images/icons/user-connection-degree-orange.gif" alt="arrow" width="18" title="degree" ></label></td>
        		
          		<td><input style="vertical-align:top;" type='radio' name='userconnection_degree' id='userconnection_degree_3' value='3'{if $result.userconnection_degree eq '3' } checked{/if}  >
          		<label for='userconnection_degree_3'><img src="../images/icons/user-connection-degree-blue.gif" alt="arrow" width="18" title="degree" ></label></td>
        		</tr>
      		</table>
    		</td>
  		</tr>
		</table>
		<br />
{ * Degree design end here * }		

</div>
{ * My Network page code start from here * } 
	<table cellpadding='0' cellspacing='0' width='600'>
  		<tr>
    		<td class='header' style="background:#213e67;font-size:13px;">{lang_print id=650002049}</td>
  		</tr>
  		<td class='setting1'>
      	{lang_print id=650002044}
    	</td>
   		<tr>
    		<td class='setting2'>
      		<table cellpadding='2' cellspacing='0'>
        		<tr>
        			<td>
        				<input style="vertical-align:bottom;" type='radio' name='user_home_page' id='user_home_page_1' value='1'{if  !empty($result.user_home_page)} checked{/if} ><label for="user_home_page_1">
        				{lang_print id=650002048}</label>
        			</td>
  					</tr>
  					<tr>
        			<td>
        				<input style="vertical-align:bottom;" type='radio' name='user_home_page' id='user_home_page_0' value='0'{if  empty($result.user_home_page)} checked{/if} ><label for="user_home_page_0">
        				{lang_print id=650002010}</label>
        			</td>
  					</tr>
  				</table>
    		</td>
  		</tr>
	</table>
{ * My Network page code end here * } 
		<br>
  	<input type='submit' class='button' value='{lang_print id=173}'>&nbsp;
  	<input type='hidden' name='task' value='edit'>
  </form>

 {literal}
		<script>
		function showContent(id) { 
    	document.getElementById(id).style.display = 'block';
		}

		function hideContent(id) { 
    	document.getElementById(id).style.display = 'none'; 
		}
		</script>
	{/literal}
{include file='admin_footer.tpl'}
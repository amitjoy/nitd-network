{include file='admin_header.tpl'}

{* $Id: admin_blog.tpl 5 2009-01-11 06:01:16Z john $ *}

<h2>{lang_print id=1500003}</h2>
{lang_print id=1500084}
<br />
<br />

{if $result}<div class='success'><img src='../images/success.gif' class='icon' border='0'> {lang_print id=191}</div>{/if}

<form action='admin_blog.php' method='POST' name='info'>

  {* JAVASCRIPT FOR ADDING BLOG CATEGORIES *}
  {literal}
  <script type="text/javascript">
  <!--
  
  function createNewCategory()
  {
    // Display
    $('newCategoryInput').value = '';
    $('newCategoryContainer').style.display = '';
    $('newCategoryLink').style.display = 'none';
  }
  
  function editNewCategory()
  {
    var newCategoryTitle = $('newCategoryInput').value;
    
    // Display
    $('newCategoryInput').value = '';
    $('newCategoryContainer').style.display = 'none';
    $('newCategoryLink').style.display = '';
    
    // Ajax
    var request = new Request.JSON({
      'method' : 'post',
      'url' : 'admin_blog.php',
      'data' : {
        'task' : 'createblogentrycat',
        'blogentrycat_title' : newCategoryTitle
      },
      'onComplete':function(responseObject)
      {
        if( $type(responseObject)!="object" || !responseObject.result || responseObject.result=="failure" )
        {
          alert('ERR');
        }
        
        else
        {
          var blogentrycat_id = responseObject.blogentrycat_id;
          var blogentrycat_languagevar_id = responseObject.blogentrycat_languagevar_id;
          var innerHTML = '';
          
          //innerHTML += '<td>';
          innerHTML += '<span class="oldCategoryContainer">';
          innerHTML += '<a href="javascript:void(0);" onclick="switchOldCategory(' + blogentrycat_id + ');">';
          innerHTML += newCategoryTitle;
          innerHTML += '</a>';
          innerHTML += '</span>';
          innerHTML += '<span class="oldCategoryInput" style="display:none;">';
          innerHTML += "<input type='text' class='text' size='30' maxlength='50' onblur='editOldCategory(" + blogentrycat_id + ");' value='" + newCategoryTitle + "' />";
          innerHTML += '</span>';
          innerHTML += '<span class="oldCategoryLangVar">';
          innerHTML += '&nbsp;(Language Variable #<a href="admin_language_edit.php?language_id=1&phrase_id=' + blogentrycat_languagevar_id + '">';
          innerHTML += blogentrycat_languagevar_id;
          innerHTML += '</a>)';
          innerHTML += '</span>';
          //innerHTML += '</td>';
          
          //alert(innerHTML);
          
          var newCategoryRow = new Element('tr', {'id' : 'blogEntryCatRow_' + blogentrycat_id});
          var newCategoryData = new Element('td', {'html' : innerHTML});
          
          newCategoryRow.inject($('newCategoryRow'), 'before');
          newCategoryData.inject(newCategoryRow);
        }
      }
    });
    
    request.send();
  }
  
  function switchOldCategory(blogentrycat_id)
  {
    var categoryRow = $('blogEntryCatRow_' + blogentrycat_id);
    categoryRow.getElement('.oldCategoryContainer').style.display = 'none';
    categoryRow.getElement('.oldCategoryInput').style.display = '';
    categoryRow.getElement('input').focus();
  }
  
  function unswitchOldCategory(blogentrycat_id)
  {
    var categoryRow = $('blogEntryCatRow_' + blogentrycat_id);
    categoryRow.getElement('.oldCategoryContainer').style.display = '';
    categoryRow.getElement('.oldCategoryInput').style.display = 'none';
  }
  
  function editOldCategory(blogentrycat_id)
  {
    var categoryRow = $('blogEntryCatRow_' + blogentrycat_id);
    var newCategoryTitle = categoryRow.getElement('input').value;
    
    // DELETE
    if( newCategoryTitle.trim()=='' )
    {
      deleteCategory(blogentrycat_id);
      return;
    }
    
    categoryRow.getElement('.oldCategoryContainer').getElement('a').innerHTML = newCategoryTitle;
    unswitchOldCategory(blogentrycat_id);
    
    // Ajax
    var request = new Request.JSON({
      'method' : 'post',
      'url' : 'admin_blog.php',
      'data' : {
        'task' : 'editblogentrycat',
        'blogentrycat_id' : blogentrycat_id,
        'blogentrycat_title' : newCategoryTitle
      },
      'onComplete':function(responseObject)
      {
        if( $type(responseObject)!="object" || !responseObject.result || responseObject.result=="failure" )
        {
          alert('ERR');
        }
      }
    });
    
    request.send();
  }
  
  function deleteCategory(blogentrycat_id)
  {
    var categoryRow = $('blogEntryCatRow_' + blogentrycat_id);
    
    categoryRow.destroy();
    
    // Ajax
    var request = new Request.JSON({
      'method' : 'post',
      'url' : 'admin_blog.php',
      'data' : {
        'task' : 'deleteblogentrycat',
        'blogentrycat_id' : blogentrycat_id
      },
      'onComplete':function(responseObject)
      {
        if( $type(responseObject)!="object" || !responseObject.result || responseObject.result=="failure" )
        {
          alert('ERR');
        }
      }
    });
    
    request.send();
  }
  // -->
  </script>
  {/literal}


<table cellpadding='0' cellspacing='0' width='600'>
  <tr>
    <td class='header'>{lang_print id=1500085}</td>
  </tr>
    <td class='setting1'>
      {lang_print id=1500086}
    </td>
  </tr>
  <tr>
    <td class='setting2'>
      <table cellpadding='2' cellspacing='0'>
        <tr>
          <td><input type='radio' name='setting_permission_blog' id='permission_blog_1' value='1'{if  $setting.setting_permission_blog} checked{/if}></td>
          <td><label for='permission_blog_1'>{lang_print id=1500087}</label></td>
        </tr>
        <tr>
          <td><input type='radio' name='setting_permission_blog' id='permission_blog_0' value='0'{if !$setting.setting_permission_blog} checked{/if}></td>
          <td><label for='permission_blog_0'>{lang_print id=1500088}</label></td>
        </tr>
      </table>
    </td>
  </tr>
</table>
<br />


<table cellpadding='0' cellspacing='0' width='600'>
  <tr>
    <td class='header'>{lang_print id=1500089}</td>
  </tr>
  <tr>
    <td class='setting1'>{lang_print id=1500090}</td>
  </tr>
  <tr>
    <td class='setting2'>
      <table cellpadding='2' cellspacing='0'>
        <tbody>
          <tr>
            <td><b>{$admin_blog15}</b></td>
          </tr>
          {section name=blogentrycats_loop loop=$blogentrycats}
          <tr id="blogEntryCatRow_{$blogentrycats[blogentrycats_loop].blogentrycat_id}">
            <td>
              <span class="oldCategoryContainer"><a href="javascript:void(0);" onclick="switchOldCategory({$blogentrycats[blogentrycats_loop].blogentrycat_id});">{$blogentrycats[blogentrycats_loop].blogentrycat_title}</a></span>
              <span class="oldCategoryInput" style="display:none;"><input type='text' class='text' size='30' maxlength='50' onblur="editOldCategory({$blogentrycats[blogentrycats_loop].blogentrycat_id});" value="{$blogentrycats[blogentrycats_loop].blogentrycat_title}" /></span>
              <span class="oldCategoryLangVar">&nbsp;(Language Variable #<a href="admin_language_edit.php?language_id=1&phrase_id={$blogentrycats[blogentrycats_loop].blogentrycat_languagevar_id}">{$blogentrycats[blogentrycats_loop].blogentrycat_languagevar_id}</a>)</span>
            </td>
          </tr>
          {/section}
          <tr id="newCategoryRow">
            <td style="padding-top: 5px;">
              <span id="newCategoryContainer" style="display:none;"><input type='text' id='newCategoryInput' class='text' size='30' maxlength='50' onblur="editNewCategory();" /></span>
              <span id="newCategoryLink"><a href="javascript:void(0);" onclick="createNewCategory();">{lang_print id=1500091}</a></span>
            </td>
          </tr>
        </tbody>
      </table>
      <input type='hidden' name='num_blogcategories' value='{$num_cats}' />
    </td>
  </tr>
</table>
<br />

{lang_block id=173 var=langBlockTemp}<input type='submit' class='button' value='{$langBlockTemp}' />{/lang_block}
<input type='hidden' name='task' value='dosave' />
</form>


{include file='admin_footer.tpl'}
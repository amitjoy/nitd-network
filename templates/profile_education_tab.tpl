{if $owner->level_info.level_education_allow != 0 AND $total_educations > 0 }

  <div class='profile_headline'>
    {lang_print id=11040101} ({$total_educations})
  </div>


  {foreach from=$educations item=education}
    <h3 class="education_header">{$education.search_education_name} {$education.search_education_year}</h3>
    <table cellpadding='0' cellspacing='0' class="education">
      {if $education.search_education_for != ""}<tr><td width="130">{lang_print id=11040104}</td><td>{$education.search_education_for}</td></tr>{/if}
      {if $education.search_education_degree != ""}<tr><td width="130">{lang_print id=11040105}</td><td>{$education.search_education_degree}</td></tr>{/if}
      {if $education.search_education_concentration1 != "" || $education.search_education_concentration2 != "" || $education.search_education_concentration3 != ""}
      <tr><td width="130">{lang_print id=11040106}</td><td>{$education.search_education_concentration1}
{if $education.search_education_concentration2 != ""}, {$education.search_education_concentration2}{/if}
{if $education.search_education_concentration3 != ""}, {$education.search_education_concentration3}{/if}
      </td></tr>{/if}
    </table>
  {/foreach}
 
{/if}

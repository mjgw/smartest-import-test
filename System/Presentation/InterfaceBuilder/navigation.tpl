<div id="admin-menu">
  <ul>
    {if $show_left_nav_options}
    {* <li class="site-top-level{if $section == "desktop" && $method != 'aboutSmartest'} on{else} off{/if}"><a href="{$domain}smartest" style="float:left">Home</a> </li> *}
    <li class="top-level{if $section == "websitemanager"} on{else} off{/if}" id="nav-pages"><a href="{$domain}smartest/pages"><span class="option-holder"><i class="flaticon solid network"></i>{$_l10n_global_strings.main_nav.pages}</span></a></li>
    <li class="top-level{if $section == "datamanager" || $section == "sets"} on{else} off{/if}" id="nav-items"><a href="{$domain}smartest/models"><span class="option-holder"><i class="fa fa-cube"></i>{$_l10n_global_strings.main_nav.items}</span></a></li>
    <li class="top-level{if $section == "assets"} on{else} off{/if}" id="nav-files"><a href="{$domain}smartest/files"><span class="option-holder"><i class="flaticon solid media-gallery-1"></i>{$_l10n_global_strings.main_nav.files}</span></a></li>
    <li class="top-level{if $section == "templates"} on{else} off{/if}" id="nav-templates"><a href="{$domain}smartest/templates"><span class="option-holder"><i class="flaticon solid notepad-1"></i>{$_l10n_global_strings.main_nav.templates}</span></a></li>
    <li class="top-level{if $section == "users"} on{else} off{/if}" id="nav-users"><a href="{$domain}smartest/users"><span class="option-holder"><i class="flaticon solid user-3"></i>{$_l10n_global_strings.main_nav.users}</span></a></li>
    <li class="top-level{if $section == "metadata"} on{else} off{/if}" id="nav-metadata"><a href="{$domain}smartest/metadata"><span class="option-holder"><i class="flaticon solid tag-1"></i>{$_l10n_global_strings.main_nav.metadata}</span></a></li>
    <li class="top-level{if $section == "settings"} on{else} off{/if}" id="nav-settings"><a href="{$domain}smartest/settings"><span class="option-holder"><i class="flaticon solid slider-1"></i>{$_l10n_global_strings.main_nav.settings}</span></a></li>
    {else}
    <li class="site-top-level{if $section == "desktop" && $method != 'aboutSmartest'} on{else} off{/if}" id="nav-sites"><a href="{$domain}smartest"><span class="option-holder"><i class="flaticon solid earth-1"></i>{$_l10n_global_strings.main_nav.websites}</span></a></li>
    <li class="top-level{if $section == "users"} on{else} off{/if}" id="nav-profile"><a href="{$domain}smartest/profile"><span class="option-holder"><i class="flaticon solid user-3"></i>{$_l10n_global_strings.main_nav.user_profile}</span></a></li>
    {/if}
    <li class="top-level{if $section == "desktop" && $method == 'aboutSmartest'} on{else} off{/if}" id="nav-about"><a href='{$domain}smartest/about'><span class="option-holder"><i class="flaticon solid info-2"></i>{$_l10n_global_strings.main_nav.about}</span></a></li>
    {* <li class="top-level off"><a href='{$domain}smartest/logout'>{$_l10n_global_strings.main_nav.sign_out}</a></li> *}
  </ul>
</div>
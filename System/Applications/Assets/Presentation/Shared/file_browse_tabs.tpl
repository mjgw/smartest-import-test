<ul class="tabset">
    <li{if $method == "getAssetTypes"} class="current"{/if}><a href="{$domain}smartest/files/types">{$_l10n_strings.tabs.file_types}</a></li>
    <li{if $method == "assetGroups"} class="current"{/if}><a href="{$domain}smartest/files/groups">{$_l10n_strings.tabs.file_groups}</a></li>
    <li{if $method == "assetGalleries"} class="current"{/if}><a href="{$domain}smartest/files/galleries">{$_l10n_strings.tabs.file_galleries}</a></li>
</ul>
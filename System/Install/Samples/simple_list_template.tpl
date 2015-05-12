<!--To edit this template, see %LOCATION%-->

<div id="sm-list-%LISTNAME%"><!--Feel free to change the ID per your needs-->
  <h3><?sm:$title:?></h3><!--Title of the list is always contained in $title-->
  <ul>
<?sm:foreach from=$items item="item":?><!--Items in the list are always contained in $items-->
    <li><?sm:$item.name:?></li>
<?sm:/foreach:?>
  </ul>
</div>
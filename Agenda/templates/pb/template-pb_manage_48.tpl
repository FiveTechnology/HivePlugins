{*pb_auth_role smc_roles='Content Management'*}
{*

 Display but also allow to build out items and notes dynamically.  The display
 of an outline is essentially a recursive call as each level may contain more

 1. Main Item
 1.1 Sub Item

*}

<div id="page">
  <div id="page_patern">
    <div id="container">

    <div class="agenda" style="color: #260C00; float: left; line-height: 1.2em; width:100%; padding: 20px 0 200px 20px;">

      <h1>{$item->title->value} - {$item->date}</h1>
      <p>
        Put some editable notes about the agenda here for example time and attendees.  Look into document/image upload
        to agenda (easier) and/or each item (layout work)
      </p>
      <p>&nbsp</p>

      <ol>
      {* We have to have an item to start with *}
      {$item->itemCheck()}

      {assign var="lastLevel" value=0}

      {foreach from=$item->getAll() item="li"}

        {* If level drops close out any open <ol> *}
        {if $li->level < $lastLevel}
          {section name="closer" loop=$lastLevel start=$li->level}
            </ol></li>
          {/section}
        {/if}

        <li data-id="{$li->id}" data-order="{$li->sort_order}">
          <span data-type="title" class="agenda-item">{$li->item_text}</span>
          <div data-type="note"   class="agenda-item-note sticky taped"  {if ! $li->note->value}style="display: none"{/if}>{$li->note}</div>
          <div data-type="motion" class="agenda-item-motion sticky taped" {if ! $li->motion->value}style="display: none"{/if}>{$li->motion}</div>
          {if $li->sub_items->value}
            <ol>
          {else}
            </li>
          {/if}
          {assign var="lastLevel" value=$li->level}
      {/foreach}
      </ol>
     </div>

    </div>
  </div>
</div>


<script type="text/javascript">

  var lit = '<span data-type="title" class="agenda-item">New Agenda Item</span>';
  lit += '<div data-type="note" class="sticky taped" style="display: none"></div>';
  lit += '<div data-type="motion" class="sticky taped motion" style="display: none"></div>';

  var agendaId = '{$item->id}';

  {literal}

  head.load('/res/agenda/css/agenda.css','/res/agenda/js/jQuery-contextMenu/jquery.contextMenu.css');
  head.load('//cdn.ckeditor.com/4.6.1/full/ckeditor.js', '//use.fontawesome.com/9b168d8632.js', '/res/agenda/js/manage.js', '/res/agenda/js/jQuery-contextMenu/jquery.contextMenu.min.js');
  head.ready(function() {

    // Doubleclick to edit
    $('[data-type]').on('dblclick', function() {
      replaceDiv(this);
    });

    $.contextMenu({
      // define which elements trigger this menu
      selector: "ol li",
      trigger: 'right',
      // define the elements of the menu
      items: {
        edit: {
          name: "Edit",
          icon: 'fa-edit',
          callback: function(key, opt) {
            replaceDiv($(this).find('.agenda-item'));
          }
        },
        child: {
          name: "Child",
          icon: 'fa-arrow-right',
          callback: function(key, opt) {
            agendaAddChild(this);
          }
        },
        sibling: {
          name: "Sibling",
          icon: 'fa-arrow-down',
          callback: function(key, opt) {
            agendaAddSibling(this);
          }
        },
        motion: {
          name: "Motion",
          icon: 'fa-gavel',
          callback: function(key, opt) {
            this.find('[data-type="motion"]').trigger('dblclick');
          }
        },
        note: {
          name: "Note",
          icon: 'fa-file',
          callback: function(key, opt) {
            this.find('[data-type="note"]').trigger('dblclick');
          }
        },
        sep2: '---------',
        delete_item: {
          name: "Delete",
          icon: 'fa-trash',
          callback: function(key, opt) {
            var li = this;
            setTimeout(function() {
              agendaDeleteItem(li);
            }, 200);
          }
        }
      }
    });
  });


</script>
{/literal}

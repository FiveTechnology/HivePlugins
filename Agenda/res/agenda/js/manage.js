
var editor;

/*
  Replaces the node with a ckeditor instance.  Whatever is innerHTML becomes
  the editable text.  node can be the actual object or an id
*/
function replaceDiv( node ) {

  node = $(node);

  // TODO: Save the existing open ckeditor
  if ( editor ) {
    editor.destroy();
  }

  var postData = {
    id: node.parent().data('id'),
    agenda: agendaId,
    nodeType: node.data('type'),
    order: node.parent().data('order'),
    parent: node.parent().data('parent'),
    insert: node.parent().data('insert')
  };

  CKEDITOR.plugins.addExternal( 'inlinesave', '/res/agenda/js/inlinesave/');
  CKEDITOR.plugins.addExternal( 'inlinecancel', '/res/agenda/js/inlinecancel/');

  editor = CKEDITOR.replace(
    node[0],
    {
      enterMode : CKEDITOR.ENTER_BR,
      shiftEnterMode: CKEDITOR.ENTER_P,
      width: '800px',
      height: '400px',
      extraPlugins: 'inlinesave,inlinecancel',
      inlinesave: {
        postUrl: '/res/agenda/xhr-save.php',
        postData: postData,
        onSuccess: function() {
          editor.destroy();
        },
        onFailure: function() {
          editor.destroy();
        }
      },
      inlinecancel: {
        onCancel: function(editor) {
          editor.destroy(true);
        }
      },
      toolbar_Five: [
        ['Inlinesave', 'Inlinecancel', 'Source', ' DocProps', '-', 'Preview', '-', 'Templates'],
        ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteWord', '-', 'Print',' atd-ckeditor'],
        ['Undo', 'Redo', '-', 'Find', 'Replace', '-', 'SelectAll', 'RemoveFormat'],
        ['Bold', 'Italic', 'Underline', 'Strike', '-', 'Subscript', 'Superscript'],
        ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent'],
        ['JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyFull'],
        ['Link', 'Unlink', 'Anchor'],
        ['Image', 'Table', 'Rule', 'SpecialChar'],
        ['Style', 'FontFormat', 'FontName', 'FontSize'],
        ['TextColor', 'BGColor']
      ],
      toolbar: 'Five'
    });
}

/*
 width: '800px',
 height: '200px',
 extraPlugins: 'pbsave,pbcancel,atd-ckeditor',
 enterMode: CKEDITOR.ENTER_BR,
 shiftEnterMode: CKEDITOR.ENTER_P,
 disableNativeSpellChecker: false,

 filebrowserBrowseUrl: '/pb/plugin/plugin.php?template=doc_manager&lcommtypeid=11',
 filebrowserImageBrowseUrl: '/pb/plugin/plugin.php?template=doc_manager&lcommtypeid=11',
 filebrowserFlashBrowseUrl: '/pb/plugin/plugin.php?template=doc_manager&lcommtypeid=11'
 */

/*
  Remove a node and it's children via xhr.  Re-order is same
  process when adding
*/
function agendaDeleteItem(li) {

  if(confirm('Really delete it?')) {
    $.ajax({
      url: '/res/agenda/delete_item.php',
      data: {id: $(li).data('id')}
    }).done(function(res) {
      if (res.error) {
        alert(res.error);
      }
    });

    li.remove();
  }
}


function agendaAddSibling(li) {
  /*
    Append new node after the one we clicked on but before any
    sibling if there is one.  The new li is created an inserted
    into the document immediately.  It's not saved until save
    in the editor is pressed
  */
  var newli = $('<li>');
  newli.html(lit);
  newli.data('parent', li.closest('ol').parent().data('id'));
  newli.data('insert', 'sibling');
  newli.data('order', parseInt(li.data('order')) + 0.5);
  newli.insertAfter(li);

  newli.find('[data-type]').on('dblclick', function() {
    replaceDiv(this);
  });

  newli.find('span').trigger('dblclick');

}

function agendaAddChild(li) {
  /*
    Adding child section, which is a new ol.  The li.id will be the
    new items parent
  */
  var newol = $('<ol>');
  var newli = $('<li>');

  newli.data('parent', li.data('id'));
  newli.data('insert', 'child');

  // lit defined in manage template
  newli.html(lit);

  newol.append(newli);

  var child = li.children().first();

  while(child.length) {
    if (child[0].nodeName == 'OL') {
      /*
        Already has child "group" insert as the first child of that
        group
      */
      child.prepend(newli);
      newli.find('[data-type]').on('dblclick', function() {
        replaceDiv(this);
      });

      newli.find('span').trigger('dblclick');
      return;
    }
    child = child.next();
  }

  // No children yet
  li.append(newol);
  newli.find('[data-type]').on('dblclick', function() {
    replaceDiv(this);
  });

  newli.find('span').trigger('dblclick');

}
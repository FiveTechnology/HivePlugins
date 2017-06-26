<?php
  namespace FiveTechnology\Plugins;
  use FiveTechnology\Core\App;

  // with recursive tree as ( select id, pb_parent_item as parents, pb_item_text from pb.pb_agenda_item WHERE pb_parent_item = '{}' union all select ai.id, tree.parents || ai.pb_parent_item, ai.pb_item_text from pb.pb_agenda_item ai, tree WHERE ai.pb_parent_item = array[tree.id] ) SELECT * from tree;

  // http://illuminatedcomputing.com/posts/2014/09/postgres-cte-for-threaded-comments/

  //with recursive tree as ( select pageid, array[pageparentid] as parents, pagename, lft from tblpage WHERE pageid=0 union all select ai.pageid, tree.parents || array[ai.lft] || ai.pageparentid, ai.pagename, ai.lft from tblpage ai, tree WHERE ai.pageparentid  = tree.pageid ) SELECT * from tree order by parents;

  class Agenda extends \FiveTechnology\Core\pb_item {

    /**
     *  Using adjacent list method of storage.  Get the full tree sorted correctly
     *
     * @return array All the agenda items sorted in correct tree traversal order
     */
    public function getAll() {
      $sql = "with recursive tree as ( select id, array[-1, coalesce(pb_sort_order::int, 0)] as parents FROM pb.pb_agenda_item WHERE pb_agenda = array[$this->id] 
        union all select ai.id, tree.parents || array[coalesce(ai.pb_sort_order::int, 0)] || pb_parent_item from pb.pb_agenda_item ai, tree WHERE pb_parent_item  = array[tree.id] ) SELECT * from tree order by parents;";
      //echo $sql; print_r($this->db->getAll($sql));
      $items = array();
      $rs = $this->db->getAll($sql);
      foreach($rs as $row) {
        $item = App::getItem('agenda_item', $row['id']);
        $item->level = (substr_count($row['parents'], ',') - 1) / 2;
        $items[] = $item;
      }
      return $items;
    }

    /**
     * An Agenda must have at least one item before we can edit it.  This check and creates the item if one
     * does not exist. Returns the item as array for use in Smarty manage template.
     *
     * @return array|bool
     */
    public function itemCheck() {
      if (! $this->items->value) {
        $item = App::getItem('agenda_item');
        $item->item_text = "First Item";
        $item->sort_order = 1;
        $item->agenda = $this->id;
        $item->save();
      }
    }

  }

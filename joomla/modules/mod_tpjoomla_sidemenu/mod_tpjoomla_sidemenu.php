<?php
?>
<style type="text/css">
a.sub-menu-active
{
    text-decoration: none;
color: #69C;
}
a.sub-menu-passive
{
    text-decoration: none;
color: #666;
}
</style>
<?php
$sidemenu = new TPJoomla_Sidemenu();
$sidemenu->create_menu();
class TPJoomla_Sidemenu
{
    function __construct()
    {
    }

    /*
     * Create the side menu.
     */
    public function create_menu()
    {
        $active_menu = &JSite::getMenu('topmenu')->getActive();
        $active = array( 
                'menutype'  => $active_menu->menutype,
                'id'        => $active_menu->id,
                'tree'      => $active_menu->tree,
                'query'     => $active_menu->query,
                'sublevel'  => $active_menu->sublevel,
                'parent'    => $active_menu->parent
                );
        if( ! is_array($active['tree']) )
        {
            $active['tree'] = array($active['tree']);
        }

        $menu = &JSite::getMenu();
        $items = $menu->getMenu();
        $width = "140px";
        $orig = $items;
        /* Obtain the current menu and arrange its structure. */
        $sub_menu = array();
        foreach( $items as $item )
        {
            $save = false;
            $tree = (isset($item->tree))? $item->tree : array();
            if( ! is_array($tree) )
            {
                $tree = array($tree);
            }
            if( in_array( $item->id, $active['tree'] ) )
            {
                $save = true;
            }
            elseif( in_array($active['id'], $tree) )
            {
                $save = true;
            }
            elseif( isset($tree[0]) && in_array($tree[0], $active['tree']) )
            {
                $save = true;
            }
            if( $save )
            {
                $sub_menu[] = $item;
            }
        }
        if( count($sub_menu) )
        {
            ?><div style="width: <?php echo $width; ?>; float: left;"><?php
                $this->output_menu( $sub_menu, $sub_menu, $active );
            ?></div><?php
        }
    }
    /*
     * Recursively output menu items.  Creates a sidebar menu.
     */
    function output_menu( &$sub_menu, &$full_menu, &$active, $show_children = false, $level = 1 )
    {
        foreach( $sub_menu as $item )
        {
            if( $item->sublevel == $level )
            {
                // Indentation multiplier.
                $left = $level*8;
                // Get the list of child menu items.
                $children = $this->get_children( $item, $full_menu );
                $is_active_item = ($item->id == $active['id']);

                /* Determine if we should show the children.  We only want to
                 * show children if a child item is the active menu item, or if the parent
                 * is the active menu item.
                 */
                if( ($active['parent'] != 0 || $level > 1) && ! empty($children) )
                {
                    foreach( $children as $child )
                    {
                        if( isset($child->tree) && is_array($child->tree))
                        {
                            if( in_array($active['id'], $child->tree) )
                            {
                                $show_children = true;
                            }
                        }
                    }
                }
                // Prepare the menu style
                $border = "1px solid #ddd";
                $style = array();
                if($level == 1)
                {
                    $style[] = "border-bottom: {$border};";
                    $style[] = "width: {$width};";
                }
                if( ! $show_children )
                {
                    $style[] = "border-right: {$border};";
                }
                $style[] = "text-decoration: none;";
                $link_style = array();
                $style[] = "margin-left: {$left}px;";
                $style = join("", $style);

                // Output parent label and open the container div.
                echo "<div id=\"level{$item->sublevel}_id{$item->id}\" style=\"{$style}\">";
                if( ($show_children && ! empty($children)) || $is_active_item )
                {
                    $link_class = "sub-menu-active";
                }
                else
                {
                    $link_class = "sub-menu-passive";
                }

                $query_params = preg_split('/(\?|&)/', $item->link);
                // Check for certain parameters
                $has = array(
                        'id'        => false,
                        'option'    => false,
                        'view'      => false,
                        );
                foreach( $query_params as $p )
                {
                    foreach( $has as $query_key )
                    {
                        if( $has[$query_key] || preg_match("/^{$query_key}=/", $p) )
                        {
                            $has[$query_key] = true;
                        }
                    }
                }
                if( ! $has['id'] )
                {
                    $item->link .= "&id={$item->id}";
                }
                if( ! $has['option'] )
                {
                    $item->link .= "&option=com_content";
                }
                if( ! $has['view'] )
                {
                    $item->link .= "&view=article";
                }
                // Add the ItemID to identify the correct menu.
                $item->link .= "&Itemid={$item->id}";

                // Display the article link.
                echo "<a class=\"{$link_class}\" href=\"{$item->link}\">{$item->name}</a>";

                // Recursively output the child items.
                if( $show_children )
                {
                    $this->output_menu( $children, $full_menu, $active, $show_children, ($level + 1) );
                }

                // Close the container div.
                echo "</div>";
            }
            if( $level == 1 )
            {
                $show_children = false;
            }
        }
    }
    /*
     * Obtain a list of the child elements from the menu object.
     */
    function get_children( $parent, &$full_menu )
    {
        $children = array();
        foreach( $full_menu as $item )
        {
            if( $parent->id === $item->parent )
            {
                $children[] = $item;
            }
        }
        return $children;
    }
}

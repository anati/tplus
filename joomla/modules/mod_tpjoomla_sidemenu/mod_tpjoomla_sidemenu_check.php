<?php
/*
 * Determine if the active item has children to display.
 */
function active_item_has_relatives()
{
    $active_menu = &JSite::getMenu('topmenu')->getActive();
    if( $active_menu->parent != 0 )
    {
        return true;
    }
    $menu = &JSite::getMenu();
    $items = $menu->getMenu();
    foreach( $items as $item )
    {
        if( $item->parent == $active_menu->id )
        {
            return true;
        }
    }
    return false;
}

<?php

/**
 * @author owliber <owliber@yahoo.com>
 * @date Jul 7, 2014
 * @filename UserMenu
 */

class UserMenu extends Controller
{
    
    public function userMenus()
    {
        $model = new AccessRights();
        $model->account_type_id = Yii::app()->session['account_type_id'];
        
        if(Yii::app()->user->isAdmin())
        {
            $menus = $model->getAllMenus();

            foreach($menus as $menu)
            {
                $menu_id = $menu['menu_id'];
                $model->menu_id = $menu_id;
                $submenus = $model->getAllSubMenus($menu_id); 
                $sub_items = array();

                if(!empty($submenus))
                {
                    foreach($submenus as $submenu)
                    {
                        
                        if($submenu['is_url'] == 1 )
                        {
                            $url = $submenu['submenu_link'];
                            $linkOptions = array('target' => '_blank');
                        }
                        else
                        {
                            $url = array($submenu['submenu_link']);
                            $linkOptions = array();
                        }

                        $sub_items[] = array(
                            'label' => $submenu['submenu_name'],
                            'url'   => $url,
                            'linkOptions' => $linkOptions,
                        );
                    }

                }
                
                if($menu['is_url'] == 1 )
                {
                    $menu_url = $menu['menu_link'];
                    $menuLinkOptions = array('target' => '_blank');
                }
                else
                {
                    $menu_url = array($menu['menu_link']);
                    $menuLinkOptions = array();
                }

                 $items[] = array(
                    'label' => $menu['menu_name'],
                    'icon'  => $menu['menu_icon'],
                    'url'   => $menu_url,
                    'items' => $sub_items,
                    'linkOptions'=> $menuLinkOptions,
                );   

            }
        }
        else
        {


            $menus = $model->getMenus();

            foreach($menus as $menu)
            {
                $menu_id = $menu['menu_id'];
                $model->menu_id = $menu_id;
                $submenus = $model->getSubMenus(); 
                $sub_items = array();

                if(!empty($submenus))
                {
                    foreach($submenus as $submenu)
                    {
                        if($submenu['is_url'] == 1 )
                        {
                            $url = $submenu['submenu_link'];
                            $linkOptions = array('target' => '_blank');
                        }
                        else
                        {
                            $url = array($submenu['submenu_link']);
                            $linkOptions = array();
                        }
                        
                        $sub_items[] = array(
                            'label' => $submenu['submenu_name'],
                            'url'   => $url,
                            'linkOptions' => $linkOptions,
                        );
                    }

                }
                
                if($menu['is_url'] == 1 )
                {
                    $menu_url = $menu['menu_link'];
                    $menuLinkOptions = array('target' => '_blank');
                }
                else
                {
                    $menu_url = array($menu['menu_link']);
                    $menuLinkOptions = array();
                }
                
                 $items[] = array(
                    'label' => $menu['menu_name'],
                    'icon'  => $menu['menu_icon'],
                    'url'   => $menu_url,
                    'items' => $sub_items,
                    'linkOptions' => $menuLinkOptions,
                );   

            }

        }

        $this->menu = $items;
        return $this->menu;       
        
    }
    /*
    public function userLinks()
    {
        $model = new AccessRights();
        $links = $model->getAllLinks();
        $items = array();
        if(!empty($links))
        {
            foreach($links as $link)
            {
                if($link['is_url'] == 1 )
                {
                    $url = $link['submenu_link'];
                    $linkOptions = array('target' => '_blank');
                }
                else
                {
                    $url = array($link['submenu_link']);
                    $linkOptions = array();
                }
                
                $items[] = array(
                    'label' => $link['submenu_name'],
                    'url'   => $url,
                    'linkOptions' => $linkOptions,
                );
            }

        }
        
        $this->menu = $items;
        return $this->menu;
    }
     * 
     */
    
}

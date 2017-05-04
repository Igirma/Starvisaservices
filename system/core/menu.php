<?php

/**
 * Menu class
 *
 * @description   Menu system
 */

class menu
{
    var $db;
    var $url;
    var $items = array();
    var $secondmenu = array();

    public function __construct()
    {
        $this->db =& load_class('db', 'core');
        $this->url =& load_class('url', 'core');
        $this->items = $this->fetch_pages('main_menu');
        $this->secondmenu = $this->fetch_pages('menu');
    }
    
    /**
     * Get pages hierarchy
     *
     * @access  public
     * @return  array
     */
    function fetch_pages($field = 'main_menu', $parent_id = 0, $slug = '', $level = 0)
    {
        $menu = array();
		
		$sql = '
		  SELECT 
			`page`.page_id,
			`page`.parent_id,
			`page`.controller,
			`page`.external,
			`page_content`.slug, 
			`page_content`.menu_title, 
			`page_content`.content_title, 
			`page_content`.ex_url
		  FROM `page` 
		  INNER JOIN `page_content` 
			ON `page`.page_id = `page_content`.page_id 
		  WHERE `page`.parent_id = ? 
			AND `page`.' . $field . ' = ? 
			AND `page`.active = ? 
			AND `page_content`.sub_active = ? 
			AND `page_content`.language_id = ? 
		  ORDER BY `page`.order ASC
		';
		
		/**
		* If parent_id is '0' it means we're currently selected main menu items.
		* If it is not '0' it means we're selecting children of a main menu item.
		* To make submenus work, children elements don't have to be main menu elements.
		* This assuming we're working on inheritance principle, that being said we have:
		* [main menu parent] -> [main menu] <=> [main menu child] -> [main menu]
		*/
		if($parent_id == 0) {
			$data = $this->db->query($sql, array($parent_id, 1, 1, 1, CUR_LANG));
		}
		else {
			$data = $this->db->query($sql, array($parent_id, 0, 1, 1, CUR_LANG));
		}

        if (isset($this->error) && $this->error !== false || $this->db->num_rows == 0) {
            return false;
        }

        foreach ($data as $k => $row) {
            $menu[$k] = $row;
            $menu[$k]['menu_title'] = strlen($row['menu_title']) > 0 ? $row['menu_title'] : $row['content_title'];
            $menu[$k]['menu_level'] = $level + 1;
            $menu[$k]['menu_slug'] = $row['slug'];
            if ($row['external'] != 1) {
                $menu[$k]['menu_url'] = ($parent_id == 0 ? SITE_URL . (CUR_LANG != 1 ? LANG_CODE . '/' : '') : '') . (strlen($slug) > 0 ? $slug . '/' : '') . $row['slug'];
                $menu[$k]['is_active'] = $this->url->segment($level) != '' && $this->url->segment($level) == $row['slug'];
                $menu[$k]['children'] = $this->fetch_pages($field, $row['page_id'], $menu[$k]['menu_url'], $menu[$k]['menu_level']);
            } else {
                $menu[$k]['menu_url'] = $row['ex_url'];
                $menu[$k]['is_active'] = false;
                $menu[$k]['children'] = $this->fetch_pages($field, $row['page_id'], '', $menu[$k]['menu_level']);
            }
            unset($menu[$k]['content_title']);
            unset($menu[$k]['ex_url']);
        }
        
        return $menu;
    }
	
    /**
     * Get menu icon
     *
     * @access  private
     * @return  string
     */
    private function get_media($page_id)
    {
        $sql = '
          SELECT 
            `media`.filename 
          FROM `media` 
          INNER JOIN `media_content`
            ON `media_content`.media_id = `media`.media_id
          INNER JOIN `media_type`
            ON `media_type`.media_type_id = `media`.media_type_id
          WHERE `media`.table_id = ? 
            AND `media_type`.name = ? 
            AND `media`.controller = ? 
            AND `media_content`.language_id = ? 
          ORDER BY `media`.order ASC 
          LIMIT 1
        ';
        $data = $this->db->query($sql, array($page_id, 'background', 'pages', CUR_LANG));

        if (isset($this->error) && $this->error !== false || $this->db->num_rows == 0) {
            return false;
        }
        return SITE_URL . MEDIA_DIR . 'pages/max/' . str_replace(' ', '%20', $data[0]['filename']);
    }

    /**
     * Get products list
     *
     * @access  public
     * @return  array
     */
    public function get_products($category_id, $menu_url, $level)
    {
        $sql = '
          SELECT 
            `product`.product_id, 
            `product_content`.title, 
            `product_content`.description, 
            `product_content`.slug 
          FROM `product` 
          INNER JOIN `product_content` 
            ON `product`.product_id = `product_content`.product_id 
          WHERE `product_content`.language_id = ? 
            AND `product`.product_id IN (
              SELECT table_id FROM `category_selected` 
              WHERE `category_selected`.category_id = ? 
              AND `category_selected`.controller = ? 
            )
            AND `product`.active = 1 
            AND `product_content`.sub_active = 1 
          ORDER BY `product`.order ASC
        ';
        $data = $this->db->query($sql, array(CUR_LANG, $category_id, 'products'));

        if (isset($this->error) && $this->error !== false || $this->db->num_rows == 0) {
            return false;
        }

        $products = array();
        foreach ($data as $product) {
            array_push($products, array(
                'menu_id' => $product['product_id'],
                'menu_title' => $product['title'],
                'menu_description' => $product['description'],
                'menu_url' => $menu_url . '/' . $product['slug'],
                'menu_level' => ($level + 1),
                'is_active' => ($this->url->segment(1) != '' && $this->url->segment(1) == $product['slug']),
                'media' => $this->get_product_media($product['product_id']),
                'children' => false
            ));
        }
        return $products;
    }

    /**
     * Get product categories list
     *
     * @access  public
     * @return  array
     */
    public function get_categories($menu_url, $level)
    {
        $sql = '
          SELECT 
            `category`.category_id, 
            `category_content`.title, 
            `category_content`.description, 
            `category_content`.slug 
          FROM `category` 
          INNER JOIN `category_content` 
            ON `category`.category_id = `category_content`.category_id 
          WHERE `category`.controller = ? 
            AND `category_content`.language_id = ? 
            AND `category`.active = ? 
            AND `category_content`.sub_active = ? 
          ORDER BY `category`.order ASC
        ';
        $data = $this->db->query($sql, array('products', CUR_LANG, 1, 1));

        if (isset($this->error) && $this->error !== false || $this->db->num_rows == 0) {
            return false;
        }
        
        $categories = array();
        foreach ($data as $category) {
            array_push($categories, array(
                'menu_id' => $category['category_id'],
                'menu_title' => $category['title'],
                'menu_description' => $category['description'],
                'menu_url' => $menu_url . '/' . $category['slug'],
                'menu_level' => ($level + 1),
                'is_active' => ($this->url->segment(1) != '' && $this->url->segment(1) == $category['slug']),
                'children' => false
            ));
        }
        return $categories;
    }

    /**
     * Get product media
     *
     * @access  private
     * @return  array
     */
    private function get_product_media($product_id)
    {
        $sql = '
          SELECT 
            `media`.filename 
          FROM `media` 
          INNER JOIN `media_content`
            ON `media_content`.media_id = `media`.media_id
          INNER JOIN `media_type`
            ON `media_type`.media_type_id = `media`.media_type_id
          WHERE `media`.table_id = ? 
            AND `media_type`.name = ? 
            AND `media`.controller = ? 
            AND `media_content`.language_id = ? 
          ORDER BY `media`.order ASC 
          LIMIT 1
        ';
        $data = $this->db->query($sql, array($product_id, 'logo', 'products', CUR_LANG));
        
        if (isset($this->error) && $this->error !== false || $this->db->num_rows == 0) {
            return false;
        }
        return SITE_URL . MEDIA_DIR . 'products/normal/' . str_replace(' ', '%20', $data[0]['filename']);
    }
}

?>
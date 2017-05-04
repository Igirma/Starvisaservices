<?php 
class categories_model extends model
{
  function __construct()
  {
    
  }
  
  function fetch_categories($language, $limit = 0, $random = false)
  {
    $sql = '
      SELECT * FROM `category`, `category_content` 
      WHERE `category`.controller = ? 
        AND `category`.category_id = `category_content`.category_id 
        AND `category`.active = ? 
        AND `category_content`.language_id = ? 
        AND `category_content`.sub_active = ? 
      ORDER BY ' . (!$random ? '`category`.order ASC' : 'RAND()');

    if ($limit > 0) {
      $sql .= ' LIMIT ' . (int) $limit;
    }
    $r = $this->db->query($sql, array('products', 1, $language, 1));
    
    if (!empty($r) && !$this->db->error)
    {
      $result = $r;
      foreach ($result as $k => $page)
      {
        $sql = '
          SELECT `media`.filename FROM `media` 
          INNER JOIN `media_content`
            ON `media_content`.media_id = `media`.media_id
          WHERE `media`.table_id = ?
            AND `media`.controller = ?
            AND `media_content`.language_id = ?
          ORDER BY `media`.album_thumb DESC, `media`.order ASC
          LIMIT 1
        ';

        $result[$k]['media'] = $this->db->query($sql, array($page['category_id'], 'categories', $language));
      }
      return $result;
    }
    return false;
  }

  function get_category($language_id, $slug)
  {
    $sql = '
      SELECT * FROM `category` 
      INNER JOIN `category_content` 
        ON `category`.category_id = `category_content`.category_id 
      WHERE `category_content`.slug = ? 
        AND `category_content`.language_id = ? 
        AND `category`.controller = ? 
        AND `category`.active = ? 
        AND `category_content`.sub_active = ? 
      LIMIT 1
    ';
    $r = $this->db->query($sql, array($slug, $language_id, 'products', 1, 1));

    if (!isset($r[0])) {
        return false;
    }

    $sql = '
      SELECT `media`.filename FROM `media` 
      INNER JOIN `media_content`
        ON `media_content`.media_id = `media`.media_id
      WHERE `media`.table_id = ?
        AND `media`.controller = ?
        AND `media_content`.language_id = ?
      ORDER BY `media`.album_thumb DESC, `media`.order ASC
      LIMIT 1
    ';
    $r[0]['media'] = $this->db->query($sql, array($r[0]['category_id'], 'categories', $language_id));
    
    if (isset($r[0]['media'][0])) {
        $r[0]['media'] = $r[0]['media'][0]['filename'];
    } else {
        $r[0]['media'] = false;
    }

    return $r[0];
  }
  
  function get_products($category_id, $language, $limit = false, $slug = false)
  {
      if ($slug !== false) {
          $sql = '
            SELECT * FROM `product` 
            INNER JOIN `product_content` 
              ON `product`.product_id = `product_content`.product_id 
            WHERE `product_content`.slug = ? 
              AND `product_content`.language_id = ? 
              AND `product`.active = ? 
              AND `product_content`.sub_active = ? 
            LIMIT 1
          ';
          $data = $this->db->query($sql, array($slug, $language, 1, 1));
      } else {
          $sql = '
            SELECT * FROM `product` 
            INNER JOIN `product_content` 
              ON `product`.product_id = `product_content`.product_id 
            WHERE `product_content`.language_id = ? 
              AND `product`.category_id = ? 
              AND `product`.active = ? 
              AND `product_content`.sub_active = ? 
            ORDER BY `product`.order ASC
          ';
          if ($limit !== false) {
              $sql .= ' LIMIT ' . (int) $limit;
          }
          $data = $this->db->query($sql, array($language, $category_id, 1, 1));
      }

      if (!empty($data) && !$this->db->error)
      {
        foreach ($data as $k => $product)
        {
          $sql = '
            SELECT `media`.filename FROM `media` 
            INNER JOIN `media_content` 
              ON `media_content`.media_id = `media`.media_id 
            INNER JOIN `media_type`
              ON `media_type`.media_type_id = `media`.media_type_id
            WHERE `media`.table_id = ? 
              AND `media_type`.name = ? 
              AND `media`.controller = ? 
              AND `media_content`.language_id = ? 
              AND `media`.album_id = ? 
            ORDER BY `media`.order ASC 
            LIMIT 1
          ';
          $data[$k]['media']['cover'] = $this->db->query($sql, array($product['product_id'], 'img', 'products', $language, 0));

          $sql = '
            SELECT `media`.filename FROM `media` 
            INNER JOIN `media_content` 
              ON `media_content`.media_id = `media`.media_id 
            INNER JOIN `media_type`
              ON `media_type`.media_type_id = `media`.media_type_id
            WHERE `media`.table_id = ? 
              AND `media_type`.name = ? 
              AND `media`.controller = ? 
              AND `media_content`.language_id = ? 
              AND `media`.album_id = ? 
            ORDER BY `media`.order ASC 
          ';
          $data[$k]['media']['photos'] = $this->db->query($sql, array($product['product_id'], 'img', 'products', $language, 1));
        }
        return $data;
      }
      return false;
  }

  function portfolio($language, $limit = false)
  {
      $sql = '
        SELECT * FROM `project` 
        INNER JOIN `project_content` ON `project`.project_id = `project_content`.project_id 
        WHERE `project_content`.language_id = ? 
          AND `project_content`.sub_active = ? 
          AND `project`.active = ? 
        ORDER BY `project`.project_date DESC
      ';
      if ($limit !== false) {
          $sql .= ' LIMIT ' . (int) $limit;
      }
      $data = $this->db->query($sql, array($language, 1, 1));

      if (!empty($data) && !$this->db->error)
      {
        $result = $data;

        foreach ($result as $k => $page)
        {
            $sql = '
              SELECT `media`.filename FROM `media` 
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
            $result[$k]['media'] = $this->db->query($sql, array($page['project_id'], 'img', 'projects', $language));
            if (isset($result[$k]['media'][0]['filename'])) {
                $result[$k]['image'] = $result[$k]['media'][0]['filename'];
                unset($result[$k]['media']);
            } else {
                unset($result[$k]);
            }
        }
        if (!count($result)) {
            return false;
        }
        return $result;
      }
      return false;
  }

  function get_juices($language)
  {
      $sql = '
        SELECT * FROM `reference`
        INNER JOIN `reference_content`
          ON `reference`.reference_id = `reference_content`.reference_id
        WHERE `reference_content`.language_id = ? 
          AND `reference_content`.sub_active = ? 
          AND `reference`.active = ? 
        ORDER BY `reference`.order ASC
      ';
      $data = $this->db->query($sql, array($language, 1, 1));

      if (!empty($data) && !$this->db->error)
      {
        $result = $data;
        foreach ($result as $k => $page)
        {
          $sql = '
            SELECT `media`.filename FROM `media` 
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
          $result[$k]['media'] = $this->db->query($sql, array($page['reference_id'], 'img', 'references', $language));
        }
        return $result;
      }
      return false;
  }


  function fetch_products($language, $category_id = 0, $page = 0, $per_page = 0, $product_id = 0, $search_word = '', $offer = 0)
  {
    if($category_id != 0){
      
      $sql = '
        SELECT * FROM `product`
        INNER JOIN `product_content`
        ON `product`.product_id = `product_content`.product_id
        WHERE `product_content`.language_id = :language_id
        AND `product`.product_id IN (
              SELECT table_id FROM `category_selected` WHERE `category_selected`.category_id = :category_id AND `category_selected`.controller = :controller 
              )
        AND `product`.active = 1
        AND `product_content`.sub_active = 1
        '.(($product_id != 0)?"AND `product`.product_id <> ".$product_id:"").'
        ';
      if($search_word != '')
        $sql .= 'AND (`product_content`.title LIKE "%'.$search_word.'%" OR `product_content`.tags LIKE "%'.$search_word.'%" OR `product_content`.description LIKE "%'.$search_word.'%" OR `product_content`.content LIKE "%'.$search_word.'%")';

      $sql .= 'ORDER BY `product`.date_created DESC';
      if($per_page > 0) $sql .= ' LIMIT '.(($page-1)*$per_page).','. ($per_page);
      $r = $this->db->query($sql, array('language_id' => $language, 'category_id' => $category_id, 'controller' => 'products'));
    }
    else{
      $sql = '
        SELECT * FROM `product`
        INNER JOIN `product_content`
        ON `product`.product_id = `product_content`.product_id
        WHERE `product_content`.language_id = :language_id
        AND `product`.active = 1
        AND `product_content`.sub_active = 1
        '.(($product_id != 0)?" AND `product`.product_id <> ".$product_id:"")
        .(($offer != 0)?" AND `product`.highlight = 1":"");
      if($search_word != '')
        $sql .= 'AND (`product_content`.title LIKE "%'.$search_word.'%" OR `product_content`.tags LIKE "%'.$search_word.'%" OR `product_content`.description LIKE "%'.$search_word.'%" OR `product_content`.content LIKE "%'.$search_word.'%")';
      
      $sql .= ' ORDER BY `product`.date_created DESC';
      if($per_page > 0) $sql .= ' LIMIT '.(($page-1)*$per_page).','. ($per_page);
      
      $r = $this->db->query($sql, array('language_id' => $language));
    
    }

    if(isset($r)){
      foreach($r as $k => $res){
        
        $result[$k] = $res;
          
        $sql = '
            SELECT *
            FROM `media`
            INNER JOIN `media_content`
            ON `media_content`.media_id = `media`.media_id
            INNER JOIN `media_type`
            ON `media_type`.media_type_id = `media`.media_type_id
            WHERE `media`.table_id = ?
            AND `media_type`.name = "img"
            AND `media`.controller = "products"
            AND `media_content`.language_id = ?
            ORDER BY `media`.album_thumb DESC, `media`.order ASC
        ';
        
        $result[$k]['media'] = $this->db->query($sql, array($res['product_id'], $language));
        
        $discounts = $this->checkDiscounts($res['product_id'], array('discount_percent' => $res['discount_percent'], 'discount_price' => $res['discount_price']), $language);
        if(isset($discounts)){
          $result[$k]['discount_percent'] = $discounts['discount_percent'];
          $result[$k]['discount_price'] = $discounts['discount_price'];
        }
        
        $result[$k]['real_price'] = getProductPrice($result[$k]['price'], $result[$k]['discount_percent'], $result[$k]['discount_price'], $result[$k]['has_vat'], $result[$k]['highlight'], $result[$k]['offer_price']);
      }	
    }	
    if(isset($result)) return $result;
    else return false;
  }

  function fetch_products_count($language, $category_id = 0)
  {			
    if($category_id != 0){
      $sql = '
        SELECT COUNT(*) as count
        FROM `product`
        INNER JOIN `product_content`
        ON `product`.product_id = `product_content`.product_id
        WHERE `product_content`.language_id = :language_id
        AND `product`.category_id = :category_id
        AND `product`.active = 1
        AND `product_content`.sub_active = 1
        ';
      if(isset($_SESSION['jetparts']['filter']) && $_SESSION['jetparts']['filter'] != 0){
        $sql .= 'AND `product`.product_id IN 
            ( SELECT `product`.product_id FROM `product_options_item_saved`, `product`
            WHERE 
              `product_options_item_saved`.product_options_item_id = '.$_SESSION['jetparts']['filter'].'
              AND `product_options_item_saved`.saved = 1
              AND `product_options_item_saved`.table_id = `product`.product_id							
            )';
      }
      $sql .= 'ORDER BY `product`.date_created DESC';
      $r = $this->db->query($sql, array('language_id' => $language, 'category_id' => $category_id));
    }
    else{
      $sql = '
        SELECT COUNT(*) as count
        FROM `product`
        INNER JOIN `product_content`
        ON `product`.product_id = `product_content`.product_id
        WHERE `product_content`.language_id = :language_id
        AND `product`.active = 1
        AND `product_content`.sub_active = 1
        ';
      if(isset($_SESSION['jetparts']['filter']) && $_SESSION['jetparts']['filter'] != 0){
        $sql .= 'AND `product`.product_id IN 
            ( SELECT `product`.product_id FROM `product_options_item_saved`, `product`
            WHERE 
              `product_options_item_saved`.product_options_item_id = '.$_SESSION['jetparts']['filter'].'
              AND `product_options_item_saved`.saved = 1
              AND `product_options_item_saved`.table_id = `product`.product_id
            )';
      }
      $sql .= 'ORDER BY `product`.date_created DESC';
      $r = $this->db->query($sql, array('language_id' => $language));
    
    }
  
    if(!empty($r) && !$this->db->error)
    {
      $result = $r[0]['count'];
      return $result;
    }
    else
    {
      return false;
    }
  }
    
  function fetch_product($language, $product, $product_id = 0)
  {			
    if($product_id != 0){
      $sql = '
        SELECT * FROM `product`
        INNER JOIN `product_content`
        ON `product`.product_id = `product_content`.product_id
        WHERE `product_content`.language_id = :language_id
        AND `product`.active = 1
        AND `product_content`.sub_active = 1
        AND `product`.product_id = :product_id
      ';
      $r = $this->db->query($sql, array('language_id' => $language, 'product_id' => $product_id));
    }
    else{
      $sql = '
        SELECT * FROM `product`
        INNER JOIN `product_content`
        ON `product`.product_id = `product_content`.product_id
        WHERE `product_content`.language_id = :language_id
        AND `product`.active = 1
        AND `product_content`.sub_active = 1
        AND `product_content`.slug = :product
      ';
      $r = $this->db->query($sql, array('language_id' => $language, 'product' => $product));
    
    }
    if(isset($r) && count($r) > 0){
      $result = $r[0];
      $sql = '
            SELECT *
            FROM `media`
            INNER JOIN `media_content`
            ON `media_content`.media_id = `media`.media_id
            INNER JOIN `media_type`
            ON `media_type`.media_type_id = `media`.media_type_id
            WHERE `media`.table_id = ?
            AND `media_type`.name = "img"
            AND `media`.controller = "products"
            AND `media_content`.language_id = ?
            AND `media`.album_thumb = 0
            ORDER BY `media`.order ASC
        ';
        
      $result['media'] = $this->db->query($sql, array($result['product_id'], $language));
      
      $result['options'] = $this->fetch_product_options($language, $result['product_id'], $result['product_id']);	
      
      $discounts = $this->checkDiscounts($result['product_id'], array('discount_percent' => $result['discount_percent'], 'discount_price' => $result['discount_price']), $language);
      if(isset($discounts)){
        $result['discount_percent'] = $discounts['discount_percent'];
        $result['discount_price'] = $discounts['discount_price'];
      }
      
      $result['real_price'] = getProductPrice($result['price'], $result['discount_percent'], $result['discount_price'], $result['has_vat'], $result['highlight'], $result['offer_price']);
    }	
    if(isset($result)) return $result;
    else return false;
  }
  
  function fetch_product_options($language_id, $category_id, $prod_id = 0, $product_options_id = 0)
  {
    
    if($product_options_id != 0){
      $sql = '
      SELECT * 
      FROM `product_options`, `product_options_heading`, `product_options_item_category`
      WHERE `product_options`.product_options_id = `product_options_heading`.product_options_id 
      AND `product_options_heading`.language_id = :lang 
      AND `product_options_item_category`.product_options_item_id = `product_options`.product_options_id
      AND `product_options_item_category`.category_id IN (
            SELECT category_id FROM `category_selected` WHERE `category_selected`.table_id = :prod_id AND `category_selected`.controller = :controller 
          ) 
      AND `product_options`.product_options_id = :product_options_id 
      ORDER BY `product_options`.order ASC
      ';
      
      $data = $this->db->query($sql, array('lang' => $language_id, 'prod_id' => $prod_id, 'product_options_id' => $product_options_id, 'controller' => 'products')); //
      
    
    }else{
      $sql = '
      SELECT * 
      FROM `product_options`, `product_options_heading`, `product_options_item_category`
      WHERE `product_options`.product_options_id = `product_options_heading`.product_options_id 
      AND `product_options_heading`.language_id = :lang 
      AND `product_options_item_category`.product_options_item_id = `product_options`.product_options_id
      AND `product_options_item_category`.category_id IN (
            SELECT category_id FROM `category_selected` WHERE `category_selected`.table_id = :prod_id AND `category_selected`.controller = :controller 
          ) 
      ORDER BY `product_options`.order ASC
      ';
      
      $data = $this->db->query($sql, array('lang' => $language_id, 'prod_id' => $prod_id, 'controller' => 'products')); //
    }
    
    $i = 0;
    
    if(count($data) > 0)
      foreach($data as $product_options)
      {
        $i = $product_options['product_options_id'];
        
        $return[$i] = $product_options;
            
        
        $sql = '
        SELECT *
        FROM `product_options`
        WHERE `product_options`.product_options_id = :product_options_id
        ';
        
        $type = $this->db->query($sql, array('product_options_id' => $product_options['product_options_id']));
        
        $type = $type[0]['type'];
        $return[$i]['type'] = $type;
        
        $sql = '
        SELECT *, `product_options_item`.title as option_title
        FROM `product_options_item`, `product_options_heading`
        WHERE `product_options_item`.product_options_heading_id 	= `product_options_heading`.product_options_heading_id 
        AND `product_options_heading`.language_id 					= :lang 
        AND `product_options_heading`.product_options_id 			= :product_options_id 
        ORDER BY `product_options_item`.product_options_item_id
        ';
        
        $subelements2 = $this->db->query($sql, array('lang' => $language_id, 'product_options_id' => $product_options['product_options_id']));
        
        if($type != 1){
          
          foreach($subelements2 as $sub_element){
            $return[$i]['subelements'][$sub_element['product_options_item_id']] = $sub_element;
            $return[$i]['type'] = $type;
            $sql = '
              SELECT * 
              FROM `product_options_item_saved`
              WHERE `product_options_item_saved`.product_options_item_id = :product_options_item_id
              AND `product_options_item_saved`.value = ""
              AND `product_options_item_saved`.saved = 1
              AND `product_options_item_saved`.table_id = :table_id 
              ';
            $selected = $this->db->query($sql, array('product_options_item_id' => $sub_element['product_options_item_id'], 'table_id' => $prod_id));
            
            if($selected && count($selected) >= 1){
              foreach($selected as $option){
                $return[$i]['selected'][] = $option['product_options_item_id'];
              }
            }else{
              if($prod_id != 0){
                unset($return[$i]['subelements'][$sub_element['product_options_item_id']]);
              }
            }
          }
        }else{
          $return[$i]['type'] = $type;
            $sql = '
              SELECT * 
              FROM `product_options_item_saved`,`product_options_heading`
              WHERE `product_options_item_saved`.product_options_item_id = :product_options_item_id
              AND `product_options_item_saved`.saved = 0
              AND `product_options_heading`.language_id = :language_id
              AND `product_options_heading`.product_options_heading_id = `product_options_item_saved`.product_options_heading_id
              AND `product_options_item_saved`.table_id = :table_id 
              LIMIT 1';
            $selected = $this->db->query($sql, array('language_id' => $language_id, 'product_options_item_id' => $product_options['product_options_id'], 'table_id' => $prod_id));
          
          if($selected && count($selected) >= 1) $return[$i]['selected_value'] = $selected[0]['value'];
            else $return[$i]['selected_value'] = '';
        }
        $i++;
      }
    if(isset($return)) return $return;
  }

  function checkDiscounts($product_id, $result, $language_id){
    
    if($product_id != 0){
        $sql = '
          SELECT *
          FROM `category`
          INNER JOIN `category_content`
          ON `category_content`.category_id = `category`.category_id
          WHERE 
          `category`.category_id IN (
              SELECT category_id FROM `category_selected` WHERE `category_selected`.table_id = ? AND `category_selected`.controller = ?
            ) 
          AND `category_content`.language_id = ?
          ORDER BY `category_content`.discount_primary DESC, `category_content`.discount_price DESC, `category_content`.discount_percent DESC
        ';
        
        $cats = $this->db->query($sql, array($product_id,'products' , $language_id));

      }
      if(isset($cats) && $cats && count($cats) > 0){
        if($cats[0]['discount_primary'] == 1){
          $discount_percent = $cats[0]['discount_percent'];
          $discount_price = $cats[0]['discount_price'];
        }
      }
      
      if(isset($discount_percent) && isset($discount_price)){
        $result['discount_percent'] = $discount_percent;
        $result['discount_price'] = $discount_price;
      }
    if(isset($result)) return $result;
  }
  
}
?>
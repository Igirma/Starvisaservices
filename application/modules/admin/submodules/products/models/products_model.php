<?php

class products_model extends model
{
  function fetch_all($controller, $stock = 1)
  {
    
    if(isset($_SESSION['category_id']) && $_SESSION['category_id'] != 0){
      
      $extra_sql = '';
      $subcats = $this->fetch_drop_down($controller, $_SESSION['category_id']);
      if(isset($subcats) && count($subcats) > 0){
        foreach($subcats as $cat){
          $extra_sql .= " OR `product`.product_id IN (
                    SELECT table_id FROM `category_selected` WHERE `category_selected`.category_id = ".$cat['category_id']." AND `category_selected`.controller = '".$controller."' 
                    ) ";
        }
      }
      if($extra_sql != ''){
        $extra_sql = " AND (`product`.product_id IN (
                    SELECT table_id FROM `category_selected` WHERE `category_selected`.category_id = ".$_SESSION['category_id']." AND `category_selected`.controller = '".$controller."' 
                    ) ".$extra_sql.") ";
      }
      else{
        $extra_sql .= " AND `product`.product_id IN (
                    SELECT table_id FROM `category_selected` WHERE `category_selected`.category_id = ".$_SESSION['category_id']." AND `category_selected`.controller = '".$controller."'
                    ) ";
      }
            
      if(isset($_SESSION['prod_search'])) $word = $_SESSION['prod_search'];
      else $word = '';
      $sql = '
      SELECT DISTINCT * FROM `product`
      INNER JOIN `product_content`
      ON `product`.product_id = `product_content`.product_id
      WHERE `product_content`.language_id = :language_id
      '.$extra_sql.'
      AND (
      `product`.articlenumber LIKE "%'.$word.'%"
      OR `product`.EAN LIKE "%'.$word.'%"
      OR `product_content`.title LIKE "%'.$word.'%"
      )
      ';
      if($stock > 0) $sql .= ' AND `product`.stock > 0';
      else $sql .= ' AND `product`.stock = 0';
      $sql .= ' ORDER BY `product`.order ASC';
      
      $data['products'] = $this->db->query($sql, array('language_id' => $this->config->item('default_language')));
    }else{
      if(isset($_SESSION['prod_search'])) $word = $_SESSION['prod_search'];
      else $word = '';
      $sql = '
      SELECT * FROM `product`
      INNER JOIN `product_content`
      ON `product`.product_id = `product_content`.product_id
      WHERE `product_content`.language_id = ?
      AND (
      `product`.articlenumber LIKE "%'.$word.'%"
      OR `product`.EAN LIKE "%'.$word.'%"
      OR `product_content`.title LIKE "%'.$word.'%"
      )';
      if($stock > 0) $sql .= ' AND `product`.stock > 0';
      else $sql .= ' AND `product`.stock = 0';
      $sql .= ' ORDER BY `product`.order ASC
            LIMIT 50';
      
      $data['products'] = $this->db->query($sql, array($this->config->item('default_language')));
    }
    return $data;
  }
  
  function fetch($product_id, $language_id)
  {	
    $sql = '
    SELECT *
    FROM `product`
    INNER JOIN `product_content`
    ON `product_content`.product_id = `product`.product_id
    WHERE `product`.product_id = ?
    AND `product_content`.language_id = ?
    ';
    
    $r = $this->db->query($sql, array($product_id, $language_id));
    $data['product'] = $r[0];

    $sql = '
      SELECT * FROM `media`
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
    $data['media']['photo'] = $this->db->query($sql, array($data['product']['product_id'], 'img', CONTROLLER, $language_id, 0));
    
    $sql = '
      SELECT * FROM `media`
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
    $data['media']['photos'] = $this->db->query($sql, array($data['product']['product_id'], 'img', CONTROLLER, $language_id, 1));

    $sql = '
      SELECT *
      FROM `media`
      INNER JOIN `media_type`
      ON `media_type`.media_type_id = `media`.media_type_id
      WHERE `media`.table_id = ?
      AND `media_type`.name = "doc"
      AND `media`.controller = ?
      ORDER BY `media`.filename ASC
    ';
    
    $data['docs'] = $this->db->query($sql, array($data['product']['product_id'], CONTROLLER));
  
    $sql = '
      SELECT *
      FROM `product_prices`
      WHERE `product_prices`.product_id = ?
      ORDER BY `product_prices`.product_prices_id ASC
    ';
    
    $data['prices'] = $this->db->query($sql, array($data['product']['product_id']));
  
    return $data;
  }
  
  function add($post)
  {
    $order = $this->db->query('SELECT MAX(`order`) + 1 AS `order` FROM `product`');

    if($order[0]['order'] == '')
    {
      $order = 0;
    }
    else
    {
      $order = $order[0]['order'];
    }

    $sql = '
    INSERT INTO `product`
    (
      date_created,
      last_update,
      articlenumber,
      EAN,
      stock,
      category_id,
      option_1,
      option_2,
      option_3,
      value_1,
      value_2,
      value_3,
      highlight,
      `order`
    )
    VALUES
    (
      :date_created,
      :last_update,
      :articlenumber,
      :EAN,
      :stock,
      :category_id,
      :option_1,
      :option_2,
      :option_3,
      :value_1,
      :value_2,
      :value_3,
      :highlight,
      :order
    )';
      
    
    $this->db->query($sql, array(
        'date_created' 	=> strtotime($post['product']['date_created']),
        'last_update' 	=> strtotime($post['product']['date_created']),
        'articlenumber' => $post['product']['articlenumber'],
        'EAN' 			=> $post['product']['EAN'],
        'stock' 		=> $post['product']['stock'],
        'category_id' => $post['product']['category_id'],
        'option_1' 		=> $post['product']['option_1'],
        'option_2' 		=> $post['product']['option_2'],
        'option_3' 		=> $post['product']['option_3'],
        'value_1' 		=> $post['product']['value_1'],
        'value_2' 		=> $post['product']['value_2'],
        'value_3' 		=> $post['product']['value_3'],
        'highlight' 	=> $post['product']['highlight'],
        'order' 		=> $order
    ));
    
    $id = $this->db->last_insert_id;

    $sql = '
    INSERT INTO `product_content`
    (
      product_id,
      language_id,
      slug,
      price,
      has_vat,
      discount_percent,
      discount_price,
      tags,
      delivered,
      title,
      description,
      content,
      meta_title,
      meta_desc,
      meta_keyw,
      sub_active
    )
    VALUES
    (
      :product_id,
      :language_id,
      :slug,
      :price,
      :has_vat,
      :discount_percent,
      :discount_price,
      :tags,
      :delivered,
      :title,
      :description,
      :content,
      :meta_title,
      :meta_desc,
      :meta_keyw,
      :sub_active
    )';
      
    $this->db->query($sql, array(
        'product_id' 		=> $id,
        'language_id' 		=> $this->config->item('default_language'),
        'slug' 				=> $this->url->string_to_url($post['product']['slug']),
        'price' 			=> str_replace(',','.',$post['product']['price']),
        'has_vat' 			=> $post['product']['has_vat'],
        'discount_percent' 	=> str_replace(',','.',$post['product']['discount_percent']),
        'discount_price' 	=> str_replace(',','.',$post['product']['discount_price']),
        'tags' 				=> $post['product']['tags'],
        'delivered' 		=> $post['product']['delivered'],
        'title' 			=> $post['product']['title'],
        'description' 		=> $post['product']['description'],
        'content' 			=> $post['product']['content'],
        'meta_title' 		=> $post['product']['meta_title'],
        'meta_desc' 		=> $post['product']['meta_desc'],
        'meta_keyw' 		=> $post['product']['meta_keyw'],
        'sub_active' 		=> $post['product']['sub_active']
    ));
    
    $languages = $this->db->query('SELECT `language`.language_id FROM `language` WHERE `language`.language_id != ?', array($this->config->item('default_language')));
    
    foreach($languages as $language)
    {				
      $sql = '
      INSERT INTO `product_content`
      (
        product_id,
        language_id,
        sub_active
      )
      VALUES
      (
        :product_id,
        :language_id,
        :sub_active
      )';
      
      $this->db->query($sql, array(
        'product_id' 	=> $id,
        'language_id' 	=> $language['language_id'],
        'sub_active' 	=> 0
      ));
    }
    
    $this->members_model->attach_caregories($post['product']['category_id'], $id, CONTROLLER);		
    
    return $id;
  }
  
  function edit($post, $id, $language_id)
  {
    $sql = 'SELECT `product_content`.slug FROM `product_content` WHERE `product_content`.product_id = ? AND `product_content`.language_id = ?';
    $content = $this->db->query($sql, array($id, $language_id));
    
    $old_slug = $content[0]['slug'];
    
    if($old_slug != $this->url->string_to_url($post['product']['slug']))
    {
      $sql = '
      UPDATE `product_content`
      SET `product_content`.slug_301 = :slug_301
      WHERE `product_content`.product_id = :product_id
      AND `product_content`.language_id = :language_id
      ';
      
      $this->db->query($sql, array(
        'product_id' => $id,
        'language_id' => $language_id,
        'slug_301' => $old_slug
      ));
    }
    
    $sql = '
    UPDATE `product`, `product_content`
    SET 
      `product`.date_created 				= :date_created,
      `product`.highlight 				= :highlight,
      `product`.articlenumber 			= :articlenumber,
      `product`.EAN 						= :EAN,
      `product`.stock 					= :stock,
      `product`.category_id 		= :category_id,
      `product`.option_1 					= :option_1,
      `product`.option_2 					= :option_2,
      `product`.option_3 					= :option_3,
      `product`.value_1 					= :value_1,
      `product`.value_2 					= :value_2,
      `product`.value_3 					= :value_3,
      `product`.last_update 				= :last_update,
      `product_content`.title 			= :title,
      `product_content`.description 		= :description,
      `product_content`.content 			= :content,
      `product_content`.meta_title 		= :meta_title,
      `product_content`.meta_desc 		= :meta_desc,
      `product_content`.meta_keyw 		= :meta_keyw,
      `product_content`.sub_active 		= :sub_active,
      `product_content`.price 			= :price,
      `product_content`.has_vat 			= :has_vat,			
      `product_content`.discount_percent 	= :discount_percent,
      `product_content`.discount_price 	= :discount_price,
      `product_content`.tags 				= :tags,
      `product_content`.delivered 		= :delivered,
      `product_content`.slug 				= :slug
    WHERE `product`.product_id 				= :product_id
    AND `product_content`.product_id 		= :product_id
    AND `product_content`.language_id 		= :language_id
    ';
    
    $this->db->query($sql, array(
      'date_created' 		=> strtotime($post['product']['date_created']),
      'last_update' 		=> time(),
      'title' 			=> ucfirst($post['product']['title']),
      'description' 		=> $post['product']['description'],
      'content' 			=> $post['product']['content'],
      'meta_title' 		=> $post['product']['meta_title'],
      'meta_desc' 		=> $post['product']['meta_desc'],
      'meta_keyw' 		=> $post['product']['meta_keyw'],
      'sub_active' 		=> $post['product']['sub_active'],
      'highlight' 		=> $post['product']['highlight'],
      'articlenumber' 	=> $post['product']['articlenumber'],
      'EAN' 				=> $post['product']['EAN'],
      'stock' 			=> $post['product']['stock'],
      'category_id' => $post['product']['category_id'],
      'option_1' 			=> $post['product']['option_1'],
      'option_2' 			=> $post['product']['option_2'],
      'option_3' 			=> $post['product']['option_3'],
      'value_1' 			=> $post['product']['value_1'],
      'value_2' 			=> $post['product']['value_2'],
      'value_3' 			=> $post['product']['value_3'],
      'price' 			=> str_replace(',','.',$post['product']['price']),
      'has_vat' 			=> $post['product']['has_vat'],
      'discount_percent' 	=> str_replace(',','.',$post['product']['discount_percent']),
      'discount_price' 	=> str_replace(',','.',$post['product']['discount_price']),
      'tags' 				=> $post['product']['tags'],
      'delivered' 		=> $post['product']['delivered'],
      'slug' 				=> $this->url->string_to_url($post['product']['slug']),
      'product_id' 		=> $id,
      'language_id' 		=> $language_id
    ));
    
    $controller = 'products';
    $sql = '
          SELECT * 
          FROM `filter`
          WHERE `filter`.controller = :controller
          ';	
      $data = $this->db->query($sql, array('controller' => $controller));
      if(!empty($data))
      {
        foreach($data as $filter)
        {
          $sql = '
                SELECT *
                  FROM `filter`, `filter_item`, `filter_heading`
                  WHERE `filter_heading`.filter_heading_id = `filter_item`.filter_heading_id
                  AND `filter_heading`.filter_id = `filter`.filter_id
                  AND `filter`.filter_id = :filter_id	
          ';
          $subelements = $this->db->query($sql, array('filter_id' => $filter['filter_id']));
          if(isset($subelements) && $subelements && count($subelements) > 0)
            foreach($subelements as $j => $sub_element)
              $this->db->query('DELETE FROM `filter_item_saved` WHERE `filter_item_saved`.table_id=? AND `filter_item_saved`.filter_item_id = ?', array($id, $sub_element['filter_item_id']));		
        }
      }	
    $arr_filters = array();
    if(isset($post['product']['filters']) && $post['product']['filters'] && count($post['product']['filters']) > 0)
      foreach($post['product']['filters'] as $k => $filters)
      {
        if(is_array($filters)){
          foreach($filters as $l => $filters_subelements){
            $sql = 'INSERT INTO `filter_item_saved`
            (
              `filter_item_saved`.saved,
              `filter_item_saved`.table_id,
              `filter_item_saved`.filter_item_id
            )
            VALUES
            (
              :saved,
              :table_id,
              :filter_item_id
            )
            ';
            $this->db->query($sql, array(
              'saved' 					=> 1,
              'table_id' 					=> $id,
              'filter_item_id' 			=> $filters_subelements
            ));
            $arr_filters[] = $filters_subelements;
            
            
              $sql2 = '
              SELECT `filter_item`.* 
              FROM `filter`, `filter_item`
              WHERE `filter_item`.filter_item_id = "'.$filters_subelements.'"
              AND `filter`.controller = "'.$controller.'"';
              
              $elem = $this->db->query($sql2);
              
              if(isset($elem) && count($elem) > 0){
                $sql2 = '
                    SELECT `filter_item`.* 
                    FROM `filter`, `filter_item`, `filter_heading` 
                    WHERE `filter_item`.filter_heading_id = `filter_heading`.filter_heading_id 
                    AND `filter_item`.filter_item_identify = "'.$elem[0]['filter_item_identify'].'"
                    AND `filter_item`.filter_item_id <> "'.$filters_subelements.'" 
                    AND `filter`.filter_id = `filter_heading`.filter_id 
                    AND `filter`.controller = "'.$controller.'"';
                    
                $return = $this->db->query($sql2);
                    
                foreach($return as $item){
                  $sql = 'INSERT INTO `filter_item_saved`
                    (
                      `filter_item_saved`.saved,
                      `filter_item_saved`.table_id,
                      `filter_item_saved`.filter_item_id
                    )
                    VALUES
                    (
                      :saved,
                      :table_id,
                      :filter_item_id
                    )
                    ';
                    $this->db->query($sql, array(
                      'saved' 					=> 1,
                      'table_id' 					=> $id,
                      'filter_item_id' 			=> $item['filter_item_id']
                    ));
                  $arr_filters[] = $item['filter_item_id'];
                }
              }		
          }
        }else{
          $sql = 'INSERT INTO `filter_item_saved`
          (
            `filter_item_saved`.saved,
            `filter_item_saved`.table_id,
            `filter_item_saved`.filter_item_id
          )
          VALUES
          (
            :saved,
            :table_id,
            :filter_item_id
          )
          ';
          $this->db->query($sql, array(
            'saved' 					=> 1,
            'table_id' 					=> $id,
            'filter_item_id' 			=> $filters
          ));
          $arr_filters[] = $filters;
          
          
            $sql2 = '
              SELECT `filter_item`.* 
              FROM `filter`, `filter_item`
              WHERE `filter_item`.filter_item_id = "'.$filters.'"
              AND `filter`.controller = "'.$controller.'"';
              
              $elem = $this->db->query($sql2);
              
              if(isset($elem) && count($elem) > 0){
                $sql2 = '
                    SELECT `filter_item`.* 
                    FROM `filter`, `filter_item`, `filter_heading` 
                    WHERE `filter_item`.filter_heading_id = `filter_heading`.filter_heading_id 
                    AND `filter_item`.filter_item_identify = "'.$elem[0]['filter_item_identify'].'"
                    AND `filter_item`.filter_item_id <> "'.$filters.'" 
                    AND `filter`.filter_id = `filter_heading`.filter_id 
                    AND `filter`.controller = "'.$controller.'"';
                    
                $return = $this->db->query($sql2);
                    
                foreach($return as $item){
                  $sql = 'INSERT INTO `filter_item_saved`
                    (
                      `filter_item_saved`.saved,
                      `filter_item_saved`.table_id,
                      `filter_item_saved`.filter_item_id
                    )
                    VALUES
                    (
                      :saved,
                      :table_id,
                      :filter_item_id
                    )
                    ';
                    $this->db->query($sql, array(
                      'saved' 					=> 1,
                      'table_id' 					=> $id,
                      'filter_item_id' 			=> $item['filter_item_id']
                    ));
                  $arr_filters[] = $item['filter_item_id'];
                }
              }		
        }
      }
    
    $controller = "products";
    $sql2 = '
    SELECT `filter_item`.* 
    FROM `filter`, `filter_item`, `filter_heading` 
    WHERE `filter_item`.filter_heading_id = `filter_heading`.filter_heading_id 
    AND `filter`.filter_id = `filter_heading`.filter_id 
    AND `filter_heading`.language_id = '.$language_id.'
    AND `filter`.controller = "'.$controller.'"';
    
    $return = $this->db->query($sql2);
    
    if(isset($return))
    foreach($return as $item){
      if(!in_array($item['filter_item_id'], $arr_filters)){
        
        $sql = 'INSERT INTO `filter_item_saved`
        (
          `filter_item_saved`.saved,
          `filter_item_saved`.table_id,
          `filter_item_saved`.filter_item_id
        )
        VALUES
        (
          :saved,
          :table_id,
          :filter_item_id
        )
        ';
        $this->db->query($sql, array(
          'saved' 					=> 0,
          'table_id' 					=> $id,
          'filter_item_id' 			=> $item['filter_item_id']
        ));
      }
    }
    
    $sql = 'DELETE FROM `product_options_item_saved` WHERE `product_options_item_saved`.table_id=? AND `product_options_item_saved`.saved=1';
    $this->db->query($sql, array($id));
    
    $sql = 'SELECT * FROM `product_options_item_saved`, `product_options_item`, `product_options_heading`
        WHERE `product_options_item_saved`.table_id=? 
        AND `product_options_item_saved`.product_options_heading_id = `product_options_heading`.product_options_heading_id
        AND `product_options_heading`.language_id = ?
        AND `product_options_item_saved`.saved=0';
    
    $res = $this->db->query($sql, array($id, $language_id));
    if(isset($res) && count($res) > 0){
      foreach($res as $obj){
        $sql = 'DELETE FROM `product_options_item_saved` WHERE `product_options_item_saved`.table_id=? AND `product_options_item_saved`.product_options_heading_id=?';
        $this->db->query($sql, array($id, $obj['product_options_heading_id']));
      }
    }
    
    if(isset($post['product']['products_options']) && $post['product']['products_options'] && count($post['product']['products_options']) > 0 )
      foreach($post['product']['products_options'] as $k => $product_options)
      {
        if(is_array($product_options)){
          foreach($product_options as $l => $product_options_subelements){
            $sql2 = '
              SELECT DISTINCT `product_options`.* 
              FROM `product_options`, `product_options_item`, `product_options_heading`
              WHERE `product_options`.product_options_id = `product_options_heading`.product_options_id
              AND `product_options_item`.product_options_heading_id = `product_options_heading`.product_options_heading_id
              AND `product_options_item`.product_options_item_id = "'.$product_options_subelements.'"';
            
            $elem = $this->db->query($sql2);
            if(isset($elem) && count($elem) > 0){
              if($elem[0]['type'] == 1){ // input
                $sql = 'INSERT INTO `product_options_item_saved`
                (
                  `product_options_item_saved`.saved,
                  `product_options_item_saved`.table_id,
                  `product_options_item_saved`.value,
                  `product_options_item_saved`.product_options_item_id,
                  `product_options_item_saved`.product_options_heading_id
                )
                VALUES
                (
                  :saved,
                  :table_id,
                  :value,
                  :product_options_item_id,
                  :product_options_heading_id
                )
                ';
                $this->db->query($sql, array(
                  'saved' 					=> 0,
                  'table_id' 					=> $id,
                  'value' 					=> $product_options_subelements,
                  'product_options_item_id' 	=> $k,
                  'product_options_heading_id' => $elem[0]['product_options_heading_id']
                ));
                
              }
              else{
                $sql = 'INSERT INTO `product_options_item_saved`
                (
                  `product_options_item_saved`.saved,
                  `product_options_item_saved`.table_id,
                  `product_options_item_saved`.product_options_item_id,
                  `product_options_item_saved`.product_options_heading_id
                )
                VALUES
                (
                  :saved,
                  :table_id,
                  :product_options_item_id,
                  :product_options_heading_id
                )
                ';
                $this->db->query($sql, array(
                  'saved' 					=> 1,
                  'table_id' 					=> $id,
                  'product_options_item_id' 	=> $product_options_subelements,
                  'product_options_heading_id' 	=> $k
                ));
                
                
                $sql2 = '
                SELECT `product_options_item`.* 
                FROM `product_options`, `product_options_item`
                WHERE `product_options_item`.product_options_item_id = "'.$product_options_subelements.'"';
                
                $elem = $this->db->query($sql2);
                
                if(isset($elem) && count($elem) > 0){
                  $sql2 = '
                      SELECT `product_options_item`.* 
                      FROM `product_options`, `product_options_item`, `product_options_heading` 
                      WHERE `product_options_item`.product_options_heading_id = `product_options_heading`.product_options_heading_id 
                      AND `product_options_item`.product_options_item_identify = "'.$elem[0]['product_options_item_identify'].'"
                      AND `product_options_item`.product_options_item_id <> "'.$product_options_subelements.'" 
                      AND `product_options`.product_options_id = `product_options_heading`.product_options_id 
                      ';
                      
                  $return = $this->db->query($sql2);
                      
                  foreach($return as $item){
                    $sql = 'INSERT INTO `product_options_item_saved`
                      (
                        `product_options_item_saved`.saved,
                        `product_options_item_saved`.table_id,
                        `product_options_item_saved`.product_options_item_id,
                        `product_options_item_saved`.product_options_heading_id
                      )
                      VALUES
                      (
                        :saved,
                        :table_id,
                        :product_options_item_id,
                        :product_options_heading_id
                      )
                      ';
                      $this->db->query($sql, array(
                        'saved' 					=> 1,
                        'table_id' 					=> $id,
                        'product_options_item_id' 	=> $item['product_options_item_id'],
                        'product_options_heading_id' 	=> $k
                      ));
                
                  }
                }		
              }
            }
          }
        }
        else
        { 
          
          $sql2 = '
              SELECT DISTINCT `product_options`.*, `product_options_heading`.* 
              FROM `product_options`, `product_options_heading`
              WHERE `product_options`.product_options_id = `product_options_heading`.product_options_id
              AND `product_options`.product_options_id = "'.$k.'"
              AND `product_options_heading`.language_id = "'.$language_id.'"
              ';
            
            $elem = $this->db->query($sql2);
            
            if(isset($elem) && count($elem) > 0){
              if($elem[0]['type'] == 1){ // input
                $sql = 'INSERT INTO `product_options_item_saved`
                (
                  `product_options_item_saved`.saved,
                  `product_options_item_saved`.table_id,
                  `product_options_item_saved`.value,
                  `product_options_item_saved`.product_options_item_id,
                  `product_options_item_saved`.product_options_heading_id
                )
                VALUES
                (
                  :saved,
                  :table_id,
                  :value,
                  :product_options_item_id,
                  :product_options_heading_id
                )
                ';
                $this->db->query($sql, array(
                  'saved' 					=> 0,
                  'table_id' 					=> $id,
                  'value' 					=> $product_options,
                  'product_options_item_id' 	=> $k,
                  'product_options_heading_id' => $elem[0]['product_options_heading_id']
                ));
              }
              else{
                $sql = 'INSERT INTO `product_options_item_saved`
                (
                  `product_options_item_saved`.saved,
                  `product_options_item_saved`.table_id,
                  `product_options_item_saved`.product_options_item_id,
                  `product_options_item_saved`.product_options_heading_id
                )
                VALUES
                (
                  :saved,
                  :table_id,
                  :product_options_item_id,
                  :product_options_heading_id
                )
                ';
                $this->db->query($sql, array(
                  'saved' 					=> 1,
                  'table_id' 					=> $id,
                  'product_options_item_id' 	=> $product_options_subelements,
                  'product_options_heading_id' 	=> $k
                ));
                
                
                $sql2 = '
                SELECT `product_options_item`.* 
                FROM `product_options`, `product_options_item`
                WHERE `product_options_item`.product_options_item_id = "'.$product_options_subelements.'"';
                
                $elem = $this->db->query($sql2);
                
                if(isset($elem) && count($elem) > 0){
                  $sql2 = '
                      SELECT `product_options_item`.* 
                      FROM `product_options`, `product_options_item`, `product_options_heading` 
                      WHERE `product_options_item`.product_options_heading_id = `product_options_heading`.product_options_heading_id 
                      AND `product_options_item`.product_options_item_identify = "'.$elem[0]['product_options_item_identify'].'"
                      AND `product_options_item`.product_options_item_id <> "'.$product_options_subelements.'" 
                      AND `product_options`.product_options_id = `product_options_heading`.product_options_id 
                      ';
                      
                  $return = $this->db->query($sql2);
                      
                  foreach($return as $item){
                    $sql = 'INSERT INTO `product_options_item_saved`
                      (
                        `product_options_item_saved`.saved,
                        `product_options_item_saved`.table_id,
                        `product_options_item_saved`.product_options_item_id,
                        `product_options_item_saved`.product_options_heading_id
                      )
                      VALUES
                      (
                        :saved,
                        :table_id,
                        :product_options_item_id,
                        :product_options_heading_id
                      )
                      ';
                      $this->db->query($sql, array(
                        'saved' 					=> 1,
                        'table_id' 					=> $id,
                        'product_options_item_id' 	=> $item['product_options_item_id'],
                        'product_options_heading_id' 	=> $k
                      ));
                  }
                }
              }
            }
        }
      }
    
    if(isset($post['media']))
      foreach($post['media'] as $media)
      {
        $sql = '
          UPDATE `media`, `media_content`
          SET
            `media`.album_thumb 		= :album_thumb
          WHERE `media`.media_id = :media_id
          AND `media_content`.media_id = :media_id
          AND `media_content`.language_id = :language_id
        ';
        
        $this->db->query($sql, array(
          'album_thumb' 	=> (isset($_POST['set_thumbnail']) && $_POST['set_thumbnail'] == $media['media_id'] ? 1 : 0),
          'media_id'		=> $media['media_id'],
          'language_id'	=> $language_id
        ));
      }
  
    $this->members_model->attach_caregories($post['product']['category_id'], $id, CONTROLLER);
    
    $last_update = time();
    foreach($post['prices']['pieces'] as $k => $pieces){
      if($pieces != ''){
        
        $sql = 'SELECT * FROM `product_prices` WHERE `product_prices`.product_prices_id = ? AND `product_prices`.product_id = ?	';
        $r = $this->db->query($sql, array($post['prices']['id'][$k], $id));
        
        if(isset($r) && $r && count($r) > 0){
          $sql = 'UPDATE `product_prices` SET
                `product_prices`.pieces 		= :pieces,
                `product_prices`.price 			= :price,
                `product_prices`.last_update 	= :last_update
              WHERE `product_prices`.product_prices_id 	= :product_prices	
              AND `product_prices`.product_id 	= :product_id	
              ';
          $this->db->query($sql, array(
              'price' 			=> $post['prices']['price'][$k],
              'pieces' 			=> $pieces,
              'last_update' 		=> $last_update,
              'product_id' 		=> $id,
              'product_prices' 	=> $post['prices']['id'][$k] 
          ));
        }
        else{
          $sql = 'INSERT INTO `product_prices`
              (
                `product_prices`.product_id,
                `product_prices`.pieces,
                `product_prices`.last_update,
                `product_prices`.price
              )
              VALUES
              (
                :product_id,
                :pieces,
                :last_update,
                :price
              )
              ';
          $this->db->query($sql, array(
              'product_id' 		=> $id,
              'pieces' 			=> $pieces,
              'last_update' 		=> $last_update,
              'price' 			=> $post['prices']['price'][$k] 
          ));
        }
      }
    }
    $sql = 'DELETE FROM `product_prices` WHERE `product_prices`.last_update <> ? AND `product_prices`.product_id = ?';
    $r = $this->db->query($sql, array($last_update, $id));

  }
  
  function product_categories()
  {
    $sql = '
      SELECT * FROM `category` 
      INNER JOIN `category_content` 
        ON `category_content`.category_id = `category`.category_id
      WHERE `category_content`.language_id = ? 
        AND `category`.controller = ? 
        AND `category`.active = ? 
        AND `category_content`.sub_active = ? 
      ORDER BY `category`.order ASC
    ';

    $r = $this->db->query($sql, array($this->config->item('default_language'), 'products', 1, 1));

    if (empty($r) || !isset($r) || !count($r)) {
        return false;
    }
    
    $categories = array();

    foreach($r as $k => $v)
    {
      $categories[$v['category_id']] = $v['title'];
    }
    
    return $categories;
  }
  
  
  function fetch_all_products() 
  {
      $categories = $this->product_categories();
      
      if (!$categories) {
          return false;
      }
      
      $products = array();
      
      foreach ($categories as $category_id => $title) 
      {
          $sql = '
            SELECT * FROM `product`
              INNER JOIN `product_content` ON `product`.product_id = `product_content`.product_id
            WHERE `product_content`.language_id = ? 
              AND `product`.category_id = ? 
            ORDER BY `product`.order ASC
          ';
          $products[$category_id]['title'] = $title;
          $products[$category_id]['products'] = $this->db->query($sql, array($this->config->item('default_language'), $category_id));
      }

      return $products;
  }
  

  function update_overview($post)
  {
    foreach($post['active'] as $k => $v)
    {
      $this->db->query('UPDATE `product` SET `product`.active = ? WHERE `product`.product_id = ? LIMIT 1', array($v, $k));
    }
    foreach($post['highlight'] as $k => $v)
    {
      $this->db->query('UPDATE `product` SET `product`.highlight = ? WHERE `product`.product_id = ? LIMIT 1', array($v, $k));
    }
    foreach($post['action'] as $k => $v)
    {
      $this->db->query('UPDATE `product` SET `product`.action = ? WHERE `product`.product_id = ? LIMIT 1', array($v, $k));
    }
  }
  
  function order_media($direction, $table_id, $language_id, $current_order)
  {
    switch($direction)
    {
      case 'left':
        $from = $this->db->query('SELECT `media`.order, `media`.media_id FROM `media` WHERE `media`.table_id = ? AND `media`.order = ?', array($table_id, $current_order));
        $to = $this->db->query('SELECT `media`.order, `media`.media_id FROM `media` WHERE `media`.order < ? AND `media`.table_id = ? ORDER BY `media`.order DESC', array($current_order, $table_id));
        
        if(!empty($to))
        {
          $this->db->query('UPDATE `media` SET `media`.order = ? WHERE `media`.media_id = ?', array($to[0]['order'], $from[0]['media_id']));
          $this->db->query('UPDATE `media` SET `media`.order = ? WHERE `media`.media_id = ?', array($from[0]['order'], $to[0]['media_id']));
        }
      break;
      
      case 'right':
        $from = $this->db->query('SELECT `media`.order, `media`.media_id FROM `media` WHERE `media`.table_id = ? AND `media`.order = ?', array($table_id, $current_order));
        $to = $this->db->query('SELECT `media`.order, `media`.media_id FROM `media` WHERE `media`.order > ? AND `media`.table_id = ? ORDER BY `media`.order ASC', array($current_order, $table_id));
        
        if(!empty($to))
        {
          $this->db->query('UPDATE `media` SET `media`.order = ? WHERE `media`.media_id = ?', array($to[0]['order'], $from[0]['media_id']));
          $this->db->query('UPDATE `media` SET `media`.order = ? WHERE `media`.media_id = ?', array($from[0]['order'], $to[0]['media_id']));
        }
      break;
    }
  }
  
  function delete($id)
  {
    $sql = 'SELECT * FROM `product` WHERE `product`.product_id = ?';
    $r = $this->db->query($sql, array($id));

    $this->db->query('DELETE FROM `product` WHERE `product`.product_id = ?', array($id));
    $this->db->query('DELETE FROM `product_content` WHERE `product_content`.product_id = ?', array($id));
    $this->db->query('DELETE FROM `product_options_item_saved` WHERE `product_options_item_saved`.table_id = ?', array($id));
    $this->db->query('DELETE FROM `filter_item_saved` WHERE `filter_item_saved`.table_id = ?', array($id));
    $this->db->query('DELETE FROM `product_prices` WHERE `product_prices`.product_id = ?', array($id));

    $this->members_model->delete_categories($id, CONTROLLER);
    
    $sql = '
    SELECT *
    FROM `media`
    WHERE `media`.table_id = ?
    AND `media`.controller = ?
    ';
    
    $media_ar = $this->db->query($sql, array($id, CONTROLLER));
    
    foreach($media_ar as $media)
    {
      $this->delete_media($media['media_id']);
    }			

  }
  
  function delete_media($media_id)
  {
      $sql  = 'SELECT `media`.filename FROM `media` WHERE `media`.media_id = ?';
      $data = $this->db->query($sql, array($media_id));
      
      $filename = $data[0]['filename'];

      //$dirs = glob(BASE_PATH . MEDIA_DIR . CONTROLLER . '/*', GLOB_ONLYDIR);
      $dirs = glob(MEDIA_DIR . CONTROLLER . '/*', GLOB_ONLYDIR);
      
      foreach ($dirs as $dir) {
          if (is_dir($dir)) {
              if (file_exists($dir . '/' . $filename)) {
                  unlink($dir . '/' . $filename);
              }
          }
      }

      $this->db->query('DELETE FROM `media` WHERE `media`.media_id = ?', array($media_id));
      $this->db->query('DELETE FROM `media_content` WHERE `media_content`.media_id = ?', array($media_id));
  }

  
  function updateTwitter($message)
  {
    $this->twitter->statusesUpdate($message);
  }

  function updateFacebook($message)
  {
    //accestoken uit db halen
    $accestoken2 = $this->facebook->accestoken_db;
    
    $attachment = array(
      'access_token' => $accestoken2,
      'message'=> $message
    );
    
    $this->facebook->api('/me/feed','POST', $attachment);
  }

  function fetch_drop_down($controller, $parent_id = 0)
  {
    $return = array();
    
    $sql = '
    SELECT `category`.category_id, `category`.parent_id, `category_content`.title
    FROM `category`, `category_content` 
    WHERE `category`.category_id = `category_content`.category_id 
    AND `category_content`.language_id = :lang 
    AND `category`.parent_id = :parent_id
    AND `category`.controller = :controller
    ORDER BY `category`.order ASC
    ';
    
    $data = $this->db->query($sql, array('lang' => $this->config->item('default_language'), 'parent_id' => $parent_id, 'controller' => $controller));
    
    $i = 0;
    
    foreach($data as $category)
    {
      $return[$i] = $category;
    
      $return[$i]['children'] = $this->fetch_drop_down($controller,$category['category_id']);
    
      $i++;
    }
    return $return;
  }
  
  function count_children($category_id = 0)
  {		
    $sql = 'SELECT `category`.category_id FROM `category` WHERE `category`.parent_id = ?';
    $categorys = $this->db->query($sql, array($category_id));
    
    $sub_sub = 0;
    
    foreach($categorys as $category)
    {
      $sql = 'SELECT `category`.category_id FROM `category` WHERE `category`.parent_id = ?';
      $this->db->query($sql, array($category['category_id']));
      if($this->db->num_rows > 0)
      {
        $sub_sub = $sub_sub + $this->db->num_rows;
      }
    }
    
    $count['sub'] = count($categorys);
    $count['sub_sub'] = $sub_sub;
    
    return $count;
  }
  
  function fetch_category_product_options($language_id, $prod_id = 0, $controller = '')
  {
    if($prod_id == 0) return false;
    
    $extra_sql = '';
    $extra_sql .= " AND `product_options_item_category`.category_id IN (
                    SELECT category_id FROM `category_selected` WHERE `category_selected`.table_id = ".$prod_id." AND `category_selected`.controller = '".$controller."' 
                    ) ";
    $return = array();
    $parent_id = 0;

    $sql = '
    SELECT * 
    FROM `product_options`, `product_options_heading`, `product_options_item_category`
    WHERE `product_options`.product_options_id = `product_options_heading`.product_options_id 
    AND `product_options_heading`.language_id = :lang 
    AND `product_options_item_category`.product_options_item_id = `product_options`.product_options_id
    '.$extra_sql.'
    ORDER BY `product_options`.order ASC
    ';
    

    $data = $this->db->query($sql, array('lang' => $language_id)); 
    
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
        
        $sql = '
        SELECT *, `product_options_item`.title as option_title
        FROM `product_options_item`, `product_options_heading`
        WHERE `product_options_item`.product_options_heading_id 	= `product_options_heading`.product_options_heading_id 
        AND `product_options_heading`.language_id 					= :lang 
        AND `product_options_heading`.product_options_id 			= :product_options_id 
        ORDER BY `product_options_item`.product_options_item_id
        ';
        
        
        $return[$i]['subelements2'] = $this->db->query($sql, array('lang' => $language_id, 'product_options_id' => $product_options['product_options_id']));
        
        $return[$i]['selected'] =  array(array('value' => '','product_options_item_id' => '' ));
        $return[$i]['selected_value'] =  array(array('value' => '','product_options_item_id' => '' ));
        
        if($type != 1){
          
          foreach($return[$i]['subelements2'] as $sub_element){
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
                $return[$i]['selected_value'][] = $option['product_options_item_id'];
              }
            }
            else $return[$i]['selected'] = array(array('product_options_item_id' => ''));
            
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
    return $return;
  }
  
  function fetch_category_filters($language_id, $controller, $prod_id = 0)
  {
    if($prod_id == 0) return false;
    
    $return = array();
    
    $extra_sql = '';
    $extra_sql .= " `filter_item_category`.category_id IN (
                    SELECT category_id FROM `category_selected` WHERE `category_selected`.table_id = ".$prod_id." AND `category_selected`.controller = '".$controller."' 
                    ) ";
    $sql = '
    SELECT * 
    FROM `filter`, `filter_heading`
    WHERE `filter`.filter_id = `filter_heading`.filter_id
    AND `filter_heading`.language_id = :lang
    AND `filter`.controller = :controller
    ';
    
    $data = $this->db->query($sql, array('lang' => $this->config->item('default_language'),'controller' => $controller));
    
    $i = 0;
        
    if(!empty($data))
    {
      foreach($data as $filter)
      {
        $return[$i] = $filter;
        $return[$i]['selected'] = array();
        
        $sql = '
        SELECT *, `filter_item`.filter_item_id as filter_item_id_number, `filter_item`.title as filter_item_title		
        
        FROM `filter`
        LEFT JOIN `filter_heading` ON `filter_heading`.filter_id = `filter`.filter_id
        LEFT JOIN `filter_item` ON `filter_item`.filter_heading_id = `filter_heading`.filter_heading_id
        LEFT JOIN `filter_item_category` ON `filter_item_category`.filter_item_id = `filter_item`.filter_item_id
        LEFT JOIN `filter_item_saved` ON `filter_item_saved`.filter_item_id = `filter_item`.filter_item_id
        
        WHERE '.$extra_sql.'
        AND `filter_item_category`.saved = 1
        AND `filter_heading`.language_id = :language_id
        AND `filter`.filter_id = :filter_id
        
        ';
        
        $subelements2 = $this->db->query($sql, array('language_id' => $language_id, 'filter_id' => $filter['filter_id']));
        //debug($subelements2);
        
        if(isset($subelements2))
          foreach($subelements2 as $j => $sub_element){
            $return[$i]['subelements'][$sub_element['filter_item_id']] = $sub_element;

            $sql = '
            SELECT *
            FROM `filter_item_saved`
            WHERE `filter_item_saved`.filter_item_id = :filter_item_id
            AND `filter_item_saved`.table_id = :table_id
            AND `filter_item_saved`.saved = 1
            ';
            
            $selected = $this->db->query($sql, array('filter_item_id' => $sub_element['filter_item_id'], 'table_id' => $prod_id));
            if($selected && count($selected) >= 1){
              foreach($selected as $option){
                $return[$i]['selected'][] = $option['filter_item_id'];
              }
            }
            //else $return[$i]['selected'] = array();
          }
        $i++;
      }
    }
    return $return;
  }
  
  function fetch_projects(){
    
    $sql = '
      SELECT * FROM `project` 
      INNER JOIN `project_content` 
        ON `project_content`.project_id = `project`.project_id
      WHERE `project_content`.language_id = :language_id
      ORDER BY `project_content`.title ASC
    ';
    
    $r = $this->db->query($sql, array('language_id' => $this->config->item('default_language')));
    $data = $r;
    
    foreach($data as $k => $v)
    {
      $data[$k]['selected'] = 0;
    }
    
    return $data;
  }
  

  function fetch_contacts(){
    
    $sql = '
      SELECT * FROM `contacts` 
      INNER JOIN `contacts_content` 
        ON `contacts_content`.contacts_id = `contacts`.contacts_id
      WHERE `contacts_content`.language_id = :language_id
      ORDER BY `contacts_content`.title ASC
    ';
    
    $r = $this->db->query($sql, array('language_id' => $this->config->item('default_language')));
    $data = $r;
    
    if(!empty($data))
    {
      foreach($data as $k => $v)
      {
        $data[$k]['selected'] = 0;
      }
      
      return $data;
    }		
  }	
  
  function fetch_referenties(){
    
    $sql = '
      SELECT * FROM `referenties` 
      INNER JOIN `referenties_content` 
        ON `referenties_content`.referenties_id = `referenties`.referenties_id
      WHERE `referenties_content`.language_id = :language_id
      ORDER BY `referenties_content`.title ASC
    ';
    
    $r = $this->db->query($sql, array('language_id' => $this->config->item('default_language')));
    $data = $r;
    
    if(isset($data) && count($data) > 0)
      foreach($data as $k => $v)
      {
        $sql = '
          SELECT * FROM `types_selected` 
          WHERE `types_selected`.product_id = :product_id
        ';
        
        $data[$k]['types'] = $this->db->query($sql, array('product_id' => $v['referenties_id']));
        
        $data[$k]['selected'] = 0;
      }
      
    return $data;
  }	
  
  function delete_price($product_prices_id){
    $this->db->query('DELETE FROM `product_prices` WHERE `product_prices`.product_prices_id = ? LIMIT 1', array($product_prices_id));
  }
    
  function order($direction, $current_order, $product_id)
  {
    switch($direction)
    {
      case 'up':
        $from = $this->db->query('SELECT `product`.order, `product`.product_id FROM `product` WHERE `product`.product_id = ?', array($product_id));
        $to = $this->db->query('SELECT `product`.order, `product`.product_id FROM `product` WHERE `product`.order < ? ORDER BY `product`.order DESC', array($current_order));
        
        if(!empty($to))
        {
          $this->db->query('UPDATE `product` SET `product`.order = ? WHERE `product`.product_id = ?', array($to[0]['order'], $from[0]['product_id']));
          $this->db->query('UPDATE `product` SET `product`.order = ? WHERE `product`.product_id = ?', array($from[0]['order'], $to[0]['product_id']));
        }
      break;
        
      case 'down':
        $from = $this->db->query('SELECT `product`.order, `product`.product_id FROM `product` WHERE `product`.product_id = ?', array($product_id));
        $to = $this->db->query('SELECT `product`.order, `product`.product_id FROM `product` WHERE `product`.order > ? ORDER BY `product`.order ASC', array($current_order));
        
        if(!empty($to))
        {
          $this->db->query('UPDATE `product` SET `product`.order = ? WHERE `product`.product_id = ?', array($to[0]['order'], $from[0]['product_id']));
          $this->db->query('UPDATE `product` SET `product`.order = ? WHERE `product`.product_id = ?', array($from[0]['order'], $to[0]['product_id']));
        }
      break;
    }
  }
  
}

?>
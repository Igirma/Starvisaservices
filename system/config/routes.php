<?php 

if ($this->url->segment(0) == '') {
    $sql = '
      SELECT `page`.controller FROM `page`, `page_content` 
      WHERE `page_content`.page_id = `page`.page_id 
      AND `page`.controller = ? 
      AND `page_content`.language_id = ?
    ';
    $page_route = 'home';
} else {
    $sql = '
      SELECT `page`.controller FROM `page`, `page_content` 
      WHERE `page_content`.page_id = `page`.page_id 
      AND `page_content`.slug = ? 
      AND `page_content`.language_id = ?
    ';
    $page_route = $this->url->segment(0);
}
$route = $this->db->query($sql, array($page_route, CUR_LANG));
if(!isset($route) || count($route) == 0) unset($route);

$sql = '
  SELECT * FROM `category`, `category_content` 
  WHERE `category`.controller = ? 
    AND `category`.category_id = `category_content`.category_id 
    AND `category`.active = ? 
    AND `category_content`.slug = ?
    AND `category_content`.language_id = ? 
    AND `category_content`.sub_active = ? 
  ORDER BY `category`.order ASC
';
$category_route = $this->db->query($sql, array('products', 1, $this->url->segment(0), CUR_LANG, 1));
if(isset($category_route) && count($category_route) > 0) $category_route[0]['controller'] = 'categories';

if(!isset($route) || count($route) == 0){
  $sql = '
    SELECT `landingspage`.controller FROM `landingspage`, `landingspage_content`
    WHERE `landingspage_content`.landingspage_id = `landingspage`.landingspage_id AND slug = ? AND language_id = ?
  ';
  $route = $this->db->query($sql, array($this->url->segment(0), CUR_LANG));
  if(!isset($route) || count($route) == 0) unset($route);
}

?>

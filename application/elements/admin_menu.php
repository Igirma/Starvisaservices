<?php

$controller_payment = getPage('payment');
$controller_delivery = getPage('delivery');

$array['menu'] = array(
    'dashboard' => array(
        'title' => $this->lang->line('module_dashboard'),
        'slug' => '',
        'icon' => 'dashboard',
        'children' => array()
    ),
    'pages' => array(
        'title' => $this->lang->line('module_pages'),
        'slug' => 'pages',
        'icon' => 'pages',
        'children' => array()
    ),
    'landingspages' => array(
        'title' => $this->lang->line('module_landingspages'),
        'slug' => 'landingspages',
        'icon' => 'landingspages',
        'children' => array()
    ),
    'news' => array(
        'title' => $this->lang->line('module_news'),
        'slug' => 'news',
        'icon' => 'news',
        'children' => array()
    ),
    'events' => array(
        'title' => $this->lang->line('module_events'),
        'slug' => 'events',
        'icon' => 'info',
        'children' => array()
    ),
    'newsletter' => array(
        'title' => $this->lang->line('module_newsletter'),
        'slug' => 'newsletter',
        'icon' => 'newsletter',
        'children' => array()
    ),
    'photoalbums' => array(
        'title' => $this->lang->line('module_photoalbums'),
        'slug' => 'photoalbums',
        'icon' => 'photoalbums',
        'children' => array()
    ),
    'products' => array(
        'title' => $this->lang->line('module_products'),
        'slug' => 'products',
        'icon' => 'products',
        'children' => array()
    ),
    'projects' => array(
        'title' => $this->lang->line('module_projects'),
        'slug' => 'projects',
        'icon' => 'projects',
        'children' => array()
    ),
    'categories' => array(
        'title' => $this->lang->line('module_categories'),
        'slug' => 'categories',
        'icon' => 'orange',
        'children' => array(
            'projects_categories' => array(
                'title' => $this->lang->line('module_projects_categories'),
                'slug' => 'categories/projects',
                'icon' => 'products'
            ),
            'products_categories' => array(
                'title' => $this->lang->line('module_products_categories'),
                'slug' => 'categories/products',
                'icon' => 'projects'
            )
            /*
            'landingspages_categories' => array(
                'title' => $this->lang->line('module_landingspages_categories'),
                'slug' => 'categories/landingspages',
                'icon' => 'orange'
            ),
            'members_categories' => array(
                'title' => $this->lang->line('module_members_categories'),
                'slug' => 'categories/members',
                'icon' => 'orange'
            )
            */
        )
    ),
    'stores' => array(
        'title' => $this->lang->line('module_stores'),
        'slug' => 'stores',
        'icon' => 'orange',
        'children' => array()
    ),
    'brands' => array(
        'title' => $this->lang->line('module_brands'),
        'slug' => 'brands',
        'icon' => 'orange',
        'children' => array()
    ),
    'videos' => array(
        'title' => $this->lang->line('module_videos'),
        'slug' => 'videos',
        'icon' => 'bekijk',
        'children' => array()
    ),
    'courses' => array(
        'title' => $this->lang->line('module_courses'),
        'slug' => 'courses',
        'icon' => 'references',
        'children' => array()
    ),
    'sliders' => array(
        'title' => $this->lang->line('module_sliders'),
        'slug' => 'sliders',
        'icon' => 'sliders',
        'children' => array()
    ),
    'forms' => array(
        'title' => $this->lang->line('module_forms'),
        'slug' => 'forms',
        'icon' => 'forms',
        'children' => array()
    ),
    'formular' => array(
        'title' => $this->lang->line('module_formular'),
        'slug' => 'formular',
        'icon' => 'formular',
        'children' => array()
    ),
    'blog' => array(
        'title' => $this->lang->line('module_blog'),
        'slug' => 'blog',
        'icon' => 'blog',
        'children' => array()
    ),
    'references' => array(
        'title' => $this->lang->line('module_references'),
        'slug' => 'references',
        'icon' => 'references',
        'children' => array()
    ),
    'client' => array(
        'title' => $this->lang->line('module_client'),
        'slug' => 'client',
        'icon' => 'client',
        'children' => array()
    ),
    'order' => array(
        'title' => $this->lang->line('module_order'),
        'slug' => 'order',
        'icon' => 'order',
        'children' => array()
    ),
    'order_mails' => array(
        'title' => $this->lang->line('module_order_mails'),
        'slug' => 'order_mails',
        'icon' => 'forms',
        'children' => array()
    ),
    'members' => array(
        'title' => $this->lang->line('module_members'),
        'slug' => 'members',
        'icon' => 'members',
        'children' => array()
    ),
    'filters' => array(
        'title' => $this->lang->line('module_filters'),
        'slug' => 'filters',
        'icon' => 'filters',
        'children' => array(
            'products_filters' => array(
                'title' => $this->lang->line('module_products_filters'),
                'slug' => 'filters/products',
                'icon' => 'filters'
            ),
            'projects_filters' => array(
                'title' => $this->lang->line('module_projects_filters'),
                'slug' => 'filters/projects',
                'icon' => 'filters'
            ),
            'news_filters' => array(
                'title' => $this->lang->line('module_news_filters'),
                'slug' => 'filters/news',
                'icon' => 'filters'
            ),
            'pages_filters' => array(
                'title' => $this->lang->line('module_pages_filters'),
                'slug' => 'filters/pages',
                'icon' => 'filters'
            ),
            'landingspages_filters' => array(
                'title' => $this->lang->line('module_landingspages_filters'),
                'slug' => 'filters/landingspages',
                'icon' => 'filters'
            ),
            'brands_filters' => array(
                'title' => $this->lang->line('module_brands_filters'),
                'slug' => 'filters/brands',
                'icon' => 'filters'
            )
        )
    ),
    'colors' => array(
        'title' => $this->lang->line('module_colors'),
        'slug' => 'colors',
        'icon' => 'orange',
        'children' => array()
    ),
    'discountcodes' => array(
        'title' => $this->lang->line('module_discountcodes'),
        'slug' => 'discountcodes',
        'icon' => 'orange',
        'children' => array()
    ),
    'destinations' => array(
        'title' => 'Add applications',
        'slug' => 'destinations',
        'icon' => 'orange',
        'children' => array()
    ),
    'country' => array(
        'title' => $this->lang->line('module_country'),
        'slug' => 'country',
        'icon' => 'orange',
        'children' => array()
    ),
    'country_groups' => array(
        'title' => 'Countries groups',
        'slug' => 'country_groups',
        'icon' => 'orange',
        'children' => array()
    ),
    'nationality_groups' => array(
        'title' => 'Nationalities groups',
        'slug' => 'nationality_groups',
        'icon' => 'orange',
        'children' => array()
    ),
    'country_holidays' => array(
        'title' => 'Countries holidays',
        'slug' => 'country_holidays',
        'icon' => 'orange',
        'children' => array()
    ),
    'type' => array(
        'title' => 'Visa types',
        'slug' => 'type',
        'icon' => 'orange',
        'children' => array()
    ),
    'entries' => array(
        'title' => 'Visa types entries',
        'slug' => 'entries',
        'icon' => 'orange',
        'children' => array()
    ),
    'entries_options' => array(
        'title' => 'Entry options',
        'slug' => 'entries_options',
        'icon' => 'orange',
        'children' => array()
    ),
    'services' => array(
        'title' => 'Entry services',
        'slug' => 'services',
        'icon' => 'orange',
        'children' => array()
    ),
    'prices' => array(
        'title' => 'Prices types',
        'slug' => 'prices',
        'icon' => 'orange',
        'children' => array()
    ),
    'documents' => array(
        'title' => 'Documents required',
        'slug' => 'documents',
        'icon' => 'pages',
        'children' => array()
    ),
    'notes' => array(
        'title' => 'Important notes',
        'slug' => 'notes',
        'icon' => 'pages',
        'children' => array()
    ),
    'controller_payment' => array(
        'title' => (isset($controller_payment['title']) ? $controller_payment['title'] : ''),
        'slug' => (isset($controller_payment['id']) ? 'pages/edit/' . $controller_payment['id'] . '/' . CUR_LANG : ''),
        'icon' => 'pages',
        'children' => array()
    ),
    'controller_delivery' => array(
        'title' => (isset($controller_delivery['title']) ? $controller_delivery['title'] : ''),
        'slug' => (isset($controller_delivery['id']) ? 'pages/edit/' . $controller_delivery['id'] . '/' . CUR_LANG : ''),
        'icon' => 'pages',
        'children' => array()
    ),
    'delivery' => array(
        'title' => 'Delivery methods',
        'slug' => 'delivery',
        'icon' => 'orange',
        'children' => array()
    ),
    'sendingcosts' => array(
        'title' => $this->lang->line('module_sendingcosts'),
        'slug' => 'sendingcosts',
        'icon' => 'orange',
        'children' => array()
    ),
    'review' => array(
        'title' => $this->lang->line('module_review'),
        'slug' => 'review',
        'icon' => 'review',
        'children' => array()
    ),
    'webshop' => array(
        'title' => $this->lang->line('module_webshop'),
        'slug' => 'webshop',
        'icon' => 'orange',
        'children' => array()
    ),
    /*
    'translations' => array(
        'title' => $this->lang->line('module_translations'),
        'slug' => 'translations',
        'icon' => 'orange',
        'children' => array()
    ),
    */
    'permissions' => array(
        'title' => $this->lang->line('module_permissions'),
        'slug' => 'permissions',
        'icon' => 'orange',
        'children' => array()
    ),
    'rights' => array(
        'title' => $this->lang->line('module_rights'),
        'slug' => 'rights',
        'icon' => 'orange',
        'children' => array()
    ),
    'module' => array(
        'title' => $this->lang->line('module_module'),
        'slug' => 'module',
        'icon' => 'orange',
        'children' => array()
    ),
    'settings' => array(
        'title' => $this->lang->line('module_settings'),
        'slug' => 'settings',
        'icon' => 'settings',
        'children' => array()
    )
);


?>

<?php if(isset($_SESSION['login_salt']) && $this->url->segment(1) != 'login'): ?>
  
  <div id="menu" class="clear" style="overflow-y: scroll">
  
    <div id="logo"></div>
    
    <ul>
    
      <?php foreach($array['menu'] as $menu): ?>
      
        <?php if(permission_overall($menu['slug'])): ?>
        
          <?php if(!empty($menu['children'])): ?>
          
          <li>
          
            <a href="javascript: void(0)" class="toggle_menu">
            
              <div style="position: absolute; left: 10px; top: 8px;" class="sprite sprite-<?=$menu['icon'];?>"></div>
              
              <span class="item"><?=$menu['title'];?></span>
              
            </a>
            
            <ul style="display: none; width: 200px; margin-left:-35px;">
            
            <?php foreach($menu['children'] as $child): ?>
            
              <li class="menu_child<?=((CONTROLLER == $menu['slug'] && ($this->url->segment(2) == @end(explode('/', $child['slug'])) OR $this->url->segment(3) == @end(explode('/', $child['slug'])))) ? ' active' : '');?>" style="padding-left: 50px;">
                
                <a href="<?=SITE_URL . LANG_CODE . '/admin/' . $child['slug'];?>">
                  
                  <div style="position: absolute; left: 30px; top: 7px;" class="sprite sprite-<?=$child['icon'];?>"></div>
                  
                  <span class="item"><?=$child['title'];?></span>
                
                </a>
              
              </li>
            
            <?php endforeach; ?>
            
            </ul>
          
          </li>
          
          <?php else: ?>
          
          <li <?=($this->url->segment(1) == $menu['slug'] ? 'class="active"' : '');?>>
            
            <a href="<?=SITE_URL . LANG_CODE . '/admin/' . $menu['slug'];?>">
              
              <div style="position: absolute; left: 10px; top: 7px;" class="sprite sprite-<?=$menu['icon'];?>"></div>
              
              <span class="item"><?=$menu['title'];?></span>
            
            </a>
          
          </li>
          
          <?php endif; ?>
        
        <?php endif; ?>
      
      <?php endforeach; ?>
    
    </ul>
    
   
    <div id="login_details">
      
      <p>
        
        <?=$this->lang->line('logged_in_as');?> <?=$_SESSION['username'];?><br>
        
        <a href="<?=SITE_URL;?>admin/logout"><?=$this->lang->line('logout');?></a>
      
      </p>
    
    </div>
    
  </div>
  
  <div id="main_container">
  
  <div id="scroll_fix">
<?php endif; ?>
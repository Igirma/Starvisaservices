<?php

class admin extends controller
{
    function __construct()
    {
        parent::__construct();
        
        $this->lang->load('admin');
        
        $this->load->model('admin_model');
        $this->load->model('login_model');
        $this->load->model('module_model');
        $this->load->model('rights_model');
        $this->load->model('languages_model', 'languages');
        $this->load->model('news_model', 'news');
        $this->load->model('pages_model', 'pages');
        $this->load->model('products_model', 'products');
        $this->load->model('landingspages_model', 'landingspages');
        $this->load->model('users_model', 'users');
        $this->load->model('forms_model', 'forms');
        $this->load->model('photoalbums_model', 'photoalbums');
        $this->load->model('brands_model', 'brands');
        $this->load->model('projects_model', 'projects');
        $this->load->model('stores_model', 'stores');
        $this->load->model('videos_model', 'videos');
        $this->load->model('courses_model', 'courses');
        $this->load->model('references_model', 'references');
        $this->load->model('sliders_model', 'sliders');
        $this->load->model('settings_model', 'settings');
        $this->load->model('categories_model', 'categories');
        $this->load->model('filters_model', 'filters');
        $this->load->model('client_model', 'client');
        $this->load->model('order_model', 'order');
        $this->load->model('order_mails_model', 'order_mails');
        $this->load->model('country_model', 'country');
        $this->load->model('country_groups_model', 'country_groups');
        $this->load->model('nationality_groups_model', 'nationality_groups');
        $this->load->model('country_holidays_model', 'country_holidays');
        $this->load->model('review_model', 'review');
        $this->load->model('webshop_model', 'webshop');
        $this->load->model('sendingcosts_model', 'sendingcosts');
        $this->load->model('members_model', 'members');
        $this->load->model('events_model', 'events');
        $this->load->model('newsletter_model', 'newsletter');
        $this->load->model('blog_model', 'blog');
        $this->load->model('formular_model', 'formular');
        $this->load->model('discountcodes_model', 'discountcodes');
        $this->load->model('colors_model', 'colors');
        $this->load->model('delivery_model', 'delivery');
        $this->load->model('type_model', 'type');
        $this->load->model('entries_model', 'entries');
        $this->load->model('entries_options_model', 'entries_options');
        $this->load->model('services_model', 'services');
        $this->load->model('prices_model', 'prices');
        $this->load->model('documents_model', 'documents');
        $this->load->model('notes_model', 'notes');
        $this->load->model('destinations_model', 'destinations');

        if($_SERVER['SERVER_NAME'] == DEV_URL) {
            $i = 0;
            
            if(isset($_SESSION['alert'])) {
                foreach($_SESSION['alert'] as $alert) {
                    if($alert['message'] == $this->lang->line('ondevserver')) {
                        $i++;
                    }
                }
                if($i == 0) {
                    $this->alert->add($this->lang->line('ondevserver'), 'information', 'bottomRight', 'false');
                }
            } else {
                $this->alert->add($this->lang->line('ondevserver'), 'information', 'bottomRight', 'false');
            }
        }
        
        $login_exceptions = array(
            SITE_URL . 'admin/forgot_password',
            SITE_URL . 'admin/reset_password/' . $this->url->segment(2)
        );
        
        if(!$this->login_model->check_login() AND !in_array($this->url->current, $login_exceptions)) {
            $this->url->redirect(SITE_URL . LANG_CODE . '/admin/login');
        }
        
        if(!$this->module_active()) {
            $this->url->redirect(SITE_URL . LANG_CODE . '/admin');
        }
        
        if(!$this->check_permission()) {
            $this->url->redirect(SITE_URL . LANG_CODE . '/admin');
        }
    }
    
    function index()
    {
        $data['pages'] = $this->pages_model->fetch_dash();
        $data['references'] = $this->references_model->fetch_dash();
        $data['news'] = $this->news_model->fetch_dash();
        $data['photoalbums'] = $this->photoalbums_model->fetch_dash();
        $this->load->view('admin', $data);
    }
    
    function module_active()
    {
        if($this->admin_model->module_active($this->url->segment(1))) {
            return true;
        } else {
            return false;
        }
    }
    
    function check_permission()
    {
        if($this->admin_model->check_permission_overall($this->url->segment(1))) {
            if($this->admin_model->check_permission($this->url->segment(2))) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    
    function set_site()
    {
        $_SESSION['site'] = $this->url->segment(2);
        $this->url->redirect(SITE_URL . 'admin');
    }
    
    function forgot_password()
    {
        $this->form->set_rules('username', $this->lang->line('username'), 'required');
        
        if($this->form->run()) {
            $email = $this->admin_model->fetch_email($_POST['username']);
            
            if($this->db->num_rows > 0) {
                $salt = $this->admin_model->set_forgot_salt($_POST['username'], $email[0]['email']);
                $this->send_email($email[0]['email'], $salt);
            } else {
                // $this->alert->clear();
                $this->alert->add('Gebruikersnaam is niet gevonden', 'error', 'topRight', 5000);
            }
        }
        
        $this->load->view('forgot_password');
    }
    
    function reset_password()
    {
        if($this->admin_model->fetch_forgot_salt($this->url->segment(2))) {
            $this->form->set_rules('password', $this->lang->line('password'), 'required');
            $this->form->set_rules('password_repeat', $this->lang->line('password_repeat'), 'required|matches[password]');
            
            if($this->form->run()) {
                $this->admin_model->update_password($_POST, $this->url->segment(2));
                
                if(!$this->db->error) {
                    // $this->alert->clear();
                    $this->alert->add($this->lang->line('password_change_success'), 'success', 'topRight', 5000);
                    $this->url->redirect(SITE_URL . LANG_CODE . '/admin');
                }
            }
            
            $this->load->view('reset_password');
        } else {
            die('Invalid URL');
        }
    }
    
    function send_email($to, $salt)
    {
        $subject = $this->lang->line('forgot_password_subject');
        $message = $this->lang->line('forgot_password_message') . "\r\n\r\n" . SITE_URL . 'admin/reset_password/' . $salt;
        $headers = 'From: ' . ADMIN_EMAIL . "\r\n" . 'Reply-To: ' . ADMIN_EMAIL . "\r\n" . 'X-Mailer: PHP/' . phpversion();
        
        if(mail($to, $subject, $message, $headers)) {
            // $this->alert->clear();
            $this->alert->add('U wachtwoord kan worden gewijzigd, bekijk uw mail voor instructies.', 'success', 'topRight', 5000);
            $this->url->redirect(SITE_URL . LANG_CODE . '/admin');
        }
    }
    
    function logout()
    {
        session_destroy();
        $this->url->redirect(SITE_URL . LANG_CODE . '/admin/login');
    }
    
    function permissions()
    {
        $this->form->set_rules('set_rules_hack', $this->lang->line('active'), 'numeric');
        
        if($this->form->run()) {
            $this->admin_model->update_overview($_POST);
            
            if(!$this->db->error) {
                // $this->alert->clear();
                $this->alert->add($this->lang->line('success'), 'success');
                $this->url->redirect(SITE_URL . LANG_CODE . '/admin/permissions');
            }
        }
        
        $data['rights'] = $this->users_model->fetch_rights();
        
        foreach($data['rights'] as $k => $v) {
            $prim_key = $k;
            
            $data['rights'][$prim_key]['mods'] = $this->users_model->get_modules_by_rights_id($v['rights_id']);
            
            foreach($data['rights'][$prim_key]['mods'] as $key => $value) {
                $data['rights'][$prim_key]['mods'][$key]['info'] = $this->users_model->get_module_by_module_id($value['module_id']);
                $data['rights'][$prim_key]['mods'][$key]['permissions'] = $this->users_model->get_permissions_by_rights_module_id($value['rights_module_id']);
            }
        }
        
        $this->load->view('permissions_view', $data);
    }
    
    function module()
    {
        $this->form->set_rules('dirname', $this->lang->line('module_dirname'), 'required');
        $this->form->set_rules('name', $this->lang->line('module_name'), 'required');
        
        $rights = $this->users_model->fetch_rights();
        
        if($this->form->run()) {
            $this->module_model->add_module($_POST, $rights);
            
            if(!$this->db->error) {
                // $this->alert->clear();
                $this->alert->add($this->lang->line('success'), 'success');
                $this->url->redirect(SITE_URL . LANG_CODE . '/admin');
            }
            
        }
        
        $data = array();
        
        $this->load->view('add_module', $data);
    }
    
    function rights()
    {
        $this->form->set_rules('name', $this->lang->line('right_name'), 'required');
        
        if($this->form->run()) {
            $this->rights_model->add_right($_POST);
            
            if(!$this->db->error) {
                // $this->alert->clear();
                $this->alert->add($this->lang->line('success'), 'success');
                $this->url->redirect(SITE_URL . LANG_CODE . '/admin');
            }
        }
        
        $data = $this->rights_model->fetch_rights();
        
        $this->load->view('add_right', $data);
    }
    
    function delete_rights()
    {
        $rights_id = $this->url->segment(2);
        
        if(permission('rights', 'delete')) {
            $this->db->query('DELETE FROM `rights` WHERE `rights`.rights_id = ?', array(
                $rights_id
            ));
            
            $rights_module_id = $this->db->query('SELECT `rights_module`.rights_module_id FROM `rights_module` WHERE `rights_module`.rights_id = ?', array(
                $rights_id
            ));
            
            foreach($rights_module_id as $id) {
                $this->db->query('DELETE FROM `permission` WHERE `permission`.rights_module_id = ?', array(
                    $id['rights_module_id']
                ));
            }
            
            $this->db->query('DELETE FROM `rights_module` WHERE `rights_module`.rights_id = ?', array(
                $rights_id
            ));
        }
        
        if(!$this->db->error) {
            // $this->alert->clear();
            $this->alert->add($this->lang->line('success'), 'success');
            $this->url->redirect(SITE_URL . LANG_CODE . '/admin');
        }
    }
    
    function getToken()
    {
        //Eerst ingelogd zijn op facebook voordat je deze informatie (*alle pagina's waar je kunt gaan posten) kunt verkrijgen!!!
        $this->admin_model->getFacebookAccesToken();
    }
    
    function fetch_pages()
    {
        $sql = '
        SELECT `page_content`.slug, `page_content`.menu_title, `page_content`.page_id
        FROM `page_content`
        WHERE `page_content`.language_id = ' . $_POST['language_id'] . ' 
        AND `page_content`.sub_active = 1 
        ';
        $data = $this->db->query($sql);
        echo '<optgroup label="Pagina\'s">';
        foreach($data as $page) {
            if($page['slug'] != '') {
                echo '<option name="internal_page" value="' . SITE_URL . subSlug($page['page_id']) . '">' . $page['menu_title'] . '</option>';
            }
        }
        echo '</optgroup>';
        
        if($this->config->item('landingspages_links_in_ckeditor') == 1) {
            
            $sql = '
            SELECT `landingspage_content`.slug, `landingspage_content`.menu_title
            FROM `landingspage_content`
            WHERE `landingspage_content`.language_id = ' . $_POST['language_id'] . ' 
            AND `landingspage_content`.sub_active = 1 
            ';
            $data = $this->db->query($sql);
            echo '<optgroup label="Landingspagina\'s">';
            foreach($data as $landingspage) {
                if($landingspage['slug'] != '') {
                    echo '<option name="internal_page" value="' . SITE_URL . $landingspage['slug'] . '">' . $landingspage['menu_title'] . '</option>';
                }
            }
            echo '</optgroup>';
        }
    }
    
    function fetch_media_ajax()
    {
        $data['controller'] = $this->url->segment(2);
        $media_id = $this->url->segment(3);
        $language_id = $this->url->segment(4);
        
        $data = $this->admin_model->fetch_media_ajax($media_id, $language_id);
        $this->load->view('fetch_media_ajax', $data);
    }
    
    function save_media_info()
    {
        $this->admin_model->save_media_info($_POST);
    }
    
    function add_formular_item()
    {
        $formular_id = $this->url->segment(2);
        $language_id = $this->url->segment(3);
        $formular_item_id = 0;
        if($this->url->segment(4) != '')
            $formular_item_id = $this->url->segment(4);
        
        $data = $this->admin_model->fetch_formular_item($formular_id, $language_id, $formular_item_id);
        $this->load->view('add_formular_item', $data);
    }
    
    function save_item_info()
    {
        $this->admin_model->save_item_info($_POST);
    }
    
}

?>
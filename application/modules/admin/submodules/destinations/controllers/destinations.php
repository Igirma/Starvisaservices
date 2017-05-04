<?php
require_once APP_PATH . 'modules/admin/controllers/admin.php';

class destinations extends admin
{
  var $destination = array();
  
  function __construct()
  {
    parent::__construct();
    $this->lang->load('country');

    $this->destination['types'] = $this->destinations_model->fetch_types();
    $this->destination['entries'] = $this->destinations_model->fetch_entries();
    $this->destination['entries_options'] = $this->destinations_model->fetch_entries_options();
    $this->destination['services'] = $this->destinations_model->fetch_services();
    $this->destination['prices'] = $this->destinations_model->fetch_prices();
    $this->destination['documents'] = $this->destinations_model->fetch_documents();
    $this->destination['notes'] = $this->destinations_model->fetch_notes();
  }

  function index()
  {
    if (preg_match('/ajax_add_new_type/', $this->url->segment(2))) {
        $this->ajax_add_new_type();
        exit;
    }
    if (preg_match('/ajax_add_type/', $this->url->segment(2))) {
        $this->ajax_add_type();
        exit;
    }
    if (preg_match('/ajax_remove_type/', $this->url->segment(2))) {
        $this->ajax_remove_type();
        exit;
    }
    if (preg_match('/ajax_add_entry/', $this->url->segment(2))) {
        $this->ajax_add_entry();
        exit;
    }
    if (preg_match('/ajax_remove_entry/', $this->url->segment(2))) {
        $this->ajax_remove_entry();
        exit;
    }
    if (preg_match('/update_entry_option/', $this->url->segment(2))) {
        $this->update_entry_option();
        exit;
    }
    if (preg_match('/ajax_add_service/', $this->url->segment(2))) {
        $this->ajax_add_service();
        exit;
    }
    if (preg_match('/ajax_remove_service/', $this->url->segment(2))) {
        $this->ajax_remove_service();
        exit;
    }
    if (preg_match('/ajax_update_prices/', $this->url->segment(2))) {
        $this->ajax_update_prices();
        exit;
    }
    if (preg_match('/ajax_add_document/', $this->url->segment(2))) {
        $this->ajax_add_document();
        exit;
    }
    if (preg_match('/ajax_remove_document/', $this->url->segment(2))) {
        $this->ajax_remove_document();
        exit;
    }
    if (preg_match('/ajax_add_note/', $this->url->segment(2))) {
        $this->ajax_add_note();
        exit;
    }
    if (preg_match('/ajax_remove_note/', $this->url->segment(2))) {
        $this->ajax_remove_note();
        exit;
    }
	if (preg_match('/preview_application/', $this->url->segment(2))) {
        $this->preview_application();
        exit;
    }
	if (preg_match('/exit_preview/', $this->url->segment(2))) {
        $this->exit_preview();
        exit;
    }

    $this->form->set_rules('active[]', 'Show online?', 'numeric');
    
    if($this->form->run())
    {
      $this->country_model->update_overview($_POST);
      
      if(!$this->db->error)
      {
        $this->alert->add($this->lang->line('success'), 'success');
        $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
      }
    }
    //$this->country_model->resetCountries();
    
    $data['destinations'] = $this->destinations_model->fetch_all();
    $data['groupped_destinations'] = $this->destinations_model->fetch_all_group($data['destinations']);
    $data['languages'] = $this->languages_model->fetch_all();

    $this->load->view('destinations_overview', $data);
  }
  
  function add()
  {
    if (isset($_POST['destination']) && !$post = $this->destinations_model->prepare_group_type($_POST['destination'])) {
        $this->alert->add('Please select the destination country or group', 'error');
        $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/add/');
        return false;
    }

    //$this->form->set_rules('destination[nationality]', 'Nationality', 'required');

    if ($this->form->run())
    {
      $id = $this->destinations_model->add($_POST['destination']);

      if (!$this->db->error)
      {
        $this->alert->add($this->lang->line('success'), 'success');
        if(isset($_POST['save_and_back']))
        {
          $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
        }
        else
        {
          $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $id);
        }
      }
    }

    $data['countries'] = $this->destinations_model->fetch_countries();
    $data['groups'] = $this->destinations_model->fetch_groups();
    $data['nationalities_groups'] = $this->destinations_model->fetch_nationalities_groups();
    $data['types'] = $this->destination['types'];
    $data['entries'] = $this->destination['entries'];
    $data['entries_options'] = $this->destination['entries_options'];
    $data['services'] = $this->destination['services'];
    $data['prices'] = $this->destination['prices'];
    $data['documents'] = $this->destination['documents'];
    $data['notes'] = $this->destination['notes'];
    $data['languages'] = $this->languages_model->fetch_all();

    $this->load->view('destinations_add_edit', $data);
  }
  
  function edit()
  {
    $id = $this->url->segment(3);

    if (isset($_POST['destination'])) {
        if (!$post = $this->destinations_model->prepare_group_type($_POST['destination'])) {
            $this->alert->add('Please select the destination country or group', 'error');
            $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $id);
            return false;
        } elseif (($destination = $this->destinations_model->destination_exists($post, $id)) !== false) {
            $this->alert->add('The destination ' . $destination['group_type'] . ' (' . $destination['name'] . ' / ' . $destination['nationality'] . ') already exists.', 'error');
            $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $id);
            return false;
        }
    }

    //$this->form->set_rules('destination[nationality]', 'Nationality', 'required');
    
    if ($this->form->run())
    {
      $this->destinations_model->edit($_POST['destination'], $id);

      if(!$this->db->error)
      {
        $this->alert->add($this->lang->line('success'), 'success');
          
        if (isset($_POST['save_and_back']))
        {
          $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
        }
        else
        {
          $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $id);
        }
      }
    }

    $data['destination'] = $this->destinations_model->fetch($id);
    $data['countries'] = $this->destinations_model->fetch_countries();
    $data['groups'] = $this->destinations_model->fetch_groups();
    $data['nationalities_groups'] = $this->destinations_model->fetch_nationalities_groups();
    $data['types'] = $this->destination['types'];
    $data['entries'] = $this->destination['entries'];
    $data['entries_options'] = $this->destination['entries_options'];
    $data['services'] = $this->destination['services'];
    $data['prices'] = $this->destination['prices'];
    $data['documents'] = $this->destination['documents'];
    $data['notes'] = $this->destination['notes'];
    $data['languages'] = $this->languages_model->fetch_all();
    
    $data['format_types'] = $this->format_types(array(
        'destination' => $data['destination']
    ));
    $data['format_type_items'] = $this->format_type_items(array(
        'selected_types' => $data['destination']['types']
    ));
    /*
    $data['format_documents'] = $this->format_documents(array(
        'destination' => $data['destination']
    ));
    $data['format_notes'] = $this->format_notes(array(
        'destination' => $data['destination']
    ));
    */

    $this->load->view('destinations_add_edit', $data);
  }

  function format_types($data)
  {
      if (!$this->destination['types']) {
          return '';
      }

      $types_exist = (isset($data['destination']['types']) && $data['destination']['types'] !== false);

      $html = '<div class="column visa-types">';
      $html .= '<div class="subheader"><h2>Visa types</h2></div>';
      
      $html .= '<div class="subcolumn full-width pull-left">';
      $html .= '<div class="section">';

      $html .= '<div class="col left">';
      $html .= '<div class="inner">';
      $html .= '<div class="source">';

      if (isset($data['destination']['types_selected'])) 
      {
          $found = array();
          foreach ($this->destination['types'] as $type) 
          {
              if (!in_array($type['users_type_id'], $data['destination']['types_selected'])) 
              {
                  $html .= Phery::link_to($type['users_type_name'], 'ajax_add_type', array(
                      'class' => 'type-item add', 
                      'phery-type' => 'json', 
                      'encoding' => 'UTF-8', 
                      'args' => array(
                          'users_nationality_id' => $data['destination']['users_nationality_id'], 
                          'users_nationality_group_type' => $data['destination']['users_nationality_group_type'], 
                          'users_country_group_type' => $data['destination']['users_country_group_type'], 
                          'users_country_group_id' => $data['destination']['users_destination_id'], 
                          'users_destination_id' => $data['destination']['users_destination_id'], 
                          'users_type_id' => $type['users_type_id']
                      ), 
                      'href' => SITE_URL . 'en/admin/destinations/ajax_add_type'
                  ));
                  array_push($found, $type['users_type_id']);
              }
          }
          if (!$found) {
              $html .= '<div class="none">No source data found.</div>';
          }
      } else {
          foreach ($this->destination['types'] as $type) 
          {
              $html .= Phery::link_to($type['users_type_name'], 'ajax_add_type', array(
                  'class' => 'type-item add', 
                  'phery-type' => 'json', 
                  'encoding' => 'UTF-8', 
                  'args' => array(
                      'users_nationality_id' => $data['destination']['users_nationality_id'], 
                      'users_nationality_group_type' => $data['destination']['users_nationality_group_type'], 
                      'users_country_group_type' => $data['destination']['users_country_group_type'], 
                      'users_country_group_id' => $data['destination']['users_destination_id'], 
                      'users_destination_id' => $data['destination']['users_destination_id'], 
                      'users_type_id' => $type['users_type_id']
                  ), 
                  'href' => SITE_URL . 'en/admin/destinations/ajax_add_type'
              ));
          }
      }

      $html .= '</div>';

      // new form
      $html .= '<div class="form full-width">';
      $html .= Phery::form_for(SITE_URL . 'en/admin/destinations/ajax_add_new_type', 'ajax_add_new_type', array(
          'class' => 'phery-form add-new-type',
          'id' => 'add-new-form',
          'role' => 'form',
          'data-type' => 'json',
          'encoding' => 'UTF-8',
          'args' => array(
              'users_nationality_id' => $data['destination']['users_nationality_id'], 
              'users_nationality_group_type' => $data['destination']['users_nationality_group_type'], 
              'users_country_group_type' => $data['destination']['users_country_group_type'], 
              'users_country_group_id' => $data['destination']['users_destination_id'], 
              'users_destination_id' => $data['destination']['users_destination_id'] 
          ), 
          'submit' => array('disabled' => true, 'all' => true)
      ));
      $html .= '<div class="inline-form-elements">';
      $html .= '<input type="text" placeholder="New visa type" name="users_type_name">';
      $html .= '<input type="submit" name="submit" value="OK">';
      $html .= '</div>';
      $html .= '</form>';
      $html .= '</div>';
      // new form

      
      $html .= '</div>';
      $html .= '</div>';

      $html .= '<div class="col right">';
      $html .= '<div class="inner">';
      $html .= '<div class="target">';

      if ($types_exist) 
      {
          foreach ($data['destination']['types'] as $type) 
          {
              $html .= Phery::link_to($type['users_type_name'], 'ajax_remove_type', array(
                  'class' => 'type-item remove', 
                  'phery-type' => 'json', 
                  'encoding' => 'UTF-8', 
                  'args' => array(
                      'users_nationality_id' => $data['destination']['users_nationality_id'], 
                      'users_nationality_group_type' => $data['destination']['users_nationality_group_type'], 
                      'users_country_group_type' => $data['destination']['users_country_group_type'], 
                      'users_country_group_id' => $data['destination']['users_destination_id'], 
                      'users_destination_id' => $data['destination']['users_destination_id'], 
                      'users_type_id' => $type['users_type_id']
                  ), 
                  'href' => SITE_URL . 'en/admin/destinations/ajax_remove_type'
              ));
          }
      } else {
          $html .= '<div class="none">No target data found.</div>';
      }

      $html .= '</div>';
      $html .= '</div>';
      $html .= '</div>';
      $html .= '</div>';

      $html .= '</div>';
      $html .= '</div>';

      return $html;
  }
  
  function format_target_types($types)
  {
      $html = '';
      if (!$types) {
          $html .= '<div class="none">No target data found.</div>';
      } else {
          foreach ($types as $type) 
          {
              $html .= Phery::link_to($type['users_type_name'], 'ajax_remove_type', array(
                  'class' => 'type-item remove', 
                  'phery-type' => 'json', 
                  'encoding' => 'UTF-8', 
                  'args' => array(
                      'users_nationality_id' => $type['users_nationality_id'], 
                      'users_nationality_group_type' => $type['users_nationality_group_type'], 
                      'users_country_group_type' => $type['users_country_group_type'], 
                      'users_country_group_id' => $type['users_country_group_id'], 
                      'users_destination_id' => $type['users_country_group_id'], 
                      'users_type_id' => $type['users_type_id']
                  ), 
                  'href' => SITE_URL . 'en/admin/destinations/ajax_remove_type'
              ));
          }
      }
      return $html;
  }
  
  function format_source_types($data)
  {
      $html = '';
      if (!$this->destination['types']) {
          $html .= '<div class="none">No source data found.</div>';
      } else {
          $selected = array();
          if (isset($data['types_selected']) && is_array($data['types_selected']) && count($data['types_selected']) > 0) 
          {
              foreach ($data['types_selected'] as $type) 
              {
                  array_push($selected, $type['users_type_id']);
              }
          }
          $found = array();
          foreach ($this->destination['types'] as $type) 
          {
              if (count($selected) > 0 && !in_array($type['users_type_id'], $selected) || !count($selected)) 
              {
                  $html .= Phery::link_to($type['users_type_name'], 'ajax_add_type', array(
                      'class' => 'type-item add', 
                      'phery-type' => 'json', 
                      'encoding' => 'UTF-8', 
                      'args' => array(
                          'users_nationality_id' => $data['data']['users_nationality_id'], 
                          'users_nationality_group_type' => $data['data']['users_nationality_group_type'], 
                          'users_country_group_type' => $data['data']['users_country_group_type'], 
                          'users_country_group_id' => $data['data']['users_country_group_id'], 
                          'users_destination_id' => $data['data']['users_country_group_id'], 
                          'users_type_id' => $type['users_type_id']
                      ), 
                      'href' => SITE_URL . 'en/admin/destinations/ajax_add_type'
                  ));
                  array_push($found, $type['users_type_id']);
              }
          }
          if (!$found) {
              $html .= '<div class="none">No source data found.</div>';
          }
      }
      return $html;
  }
  
  function format_type_items($data) 
  {
      if (!$data['selected_types']) {
          return '';
      }
      $html = '';
      foreach ($data['selected_types'] as $type) 
      {
          $html .= $this->format_type_item(array(
              'type' => $type
          ));
      }
      return $html;
  }

  function format_type_item($data)
  {
      $html = '<div class="column visa-type-items">';
      $html .= '<div class="subheader"><h2><b>Visa type:</b> ' . $data['type']['users_type_name'] . '</h2></div>';
      
      $html .= '<div class="subcolumn full-width pull-left">';
      $html .= '<div class="section">';

      $html .= '<div class="col left">';
      $html .= '<div class="inner">';
      $html .= '<div class="source">';
      $html .= $this->format_source_entries($data);
      $html .= '</div>';
      $html .= '</div>';
      $html .= '</div>';


      $html .= '<div class="col right">';
      $html .= '<div class="inner">';
      $html .= '<div class="target">';
      $html .= $this->format_target_entries($data);
      $html .= '</div>';
      $html .= '</div>';
      $html .= '</div>';

      $html .= '</div>';

      $html .= '<div class="entries">';
      $html .= $this->format_entry_items($data);
      $html .= '</div>';

      $html .= '</div>';

      // documents
      $html .= $this->format_documents($data);

      // notes
      $html .= $this->format_notes($data);

      $html .= '</div>';

      return $html;
  }
  
  function format_entry_items($data) 
  {
      if (!$data['type']['entries']) {
          return '';
      }
      $html = '';
      foreach ($data['type']['entries'] as $entry) 
      {
          $html .= $this->format_entry_item($entry);
      }
      return $html;
  }

  function format_entry_item($data)
  {
      $html = '<div class="entry-section">';
      $html .= '<div class="section heading">';

      $html .= '<div class="col left">';
      $html .= '<div class="inner">';
      $html .= '<div class="source">';
      $html .= '<b>Entry name:</b> ' . $data['user_entry_name'];
      $html .= '</div>';
      $html .= '</div>';
      $html .= '</div>';

      $html .= '<div class="col right">';
      $html .= '<div class="inner">';
      $html .= '<div class="target">';

      if ($this->destination['entries_options'] !== false) 
      {
          $options[0] = '-- select entry option --';
          foreach ($this->destination['entries_options'] as $option) {
              extract($option);
              $options[$entry_option_id] = $entry_option_name;
          }
          $html .= Phery::select_for('ajax_select', $options, array(
              'phery-type' => 'json',
              'target' => SITE_URL . 'en/admin/destinations/update_entry_option',
              'encoding' => 'UTF-8',
              'method' => 'POST',
              'args' => array(
                  'users_nationality_id' => $data['users_nationality_id'], 
                  'users_nationality_group_type' => $data['users_nationality_group_type'], 
                  'users_country_group_type' => $data['users_country_group_type'], 
                  'users_country_group_id' => $data['users_country_group_id'], 
                  'users_type_id' => $data['users_type_id'],
                  'user_entry_id' => $data['user_entry_id']
              ), 
              'selected' => $data['entry_option_id'],
              'name' => 'entry_option_id',
              'class' => 'entry_option'
          ));
      }

      $html .= '</div>';
      $html .= '</div>';
      $html .= '</div>';

      $html .= '</div>';

      $html .= '<div class="section">';

      $html .= '<div class="col left">';
      $html .= '<div class="inner">';
      $html .= '<div class="source">';
      $html .= $this->format_source_services($data);
      $html .= '</div>';
      $html .= '</div>';
      $html .= '</div>';
      
      $html .= '<div class="col right">';
      $html .= '<div class="inner">';
      $html .= '<div class="target">';
      $html .= $this->format_target_services($data);
      $html .= '</div>';
      $html .= '</div>';
      $html .= '</div>';

      $html .= '</div>';
      $html .= '<div class="section services">';
      $html .= $this->format_service_items($data);
      $html .= '</div>';
      $html .= '</div>';

      return $html;
  }
  
  function format_source_entries($data)
  {
      $html = '';
      if (!$this->destination['entries']) {
          $html .= '<div class="none">No source data found.</div>';
      } else {
          $selected = array();
          if (isset($data['type']['entries_selected']) && is_array($data['type']['entries_selected']) && count($data['type']['entries_selected']) > 0) 
          {
              foreach ($data['type']['entries_selected'] as $entry) 
              {
                  if (is_array($entry)) {
                      array_push($selected, $entry['user_entry_id']);
                  } else {
                      array_push($selected, $entry);
                  }
              }
          }
          $found = array();
          foreach ($this->destination['entries'] as $entry) 
          {
              if (count($selected) > 0 && !in_array($entry['user_entry_id'], $selected) || !count($selected)) 
              {
                  $html .= Phery::link_to($entry['user_entry_name'], 'ajax_add_entry', array(
                      'class' => 'entry-item add', 
                      'phery-type' => 'json', 
                      'encoding' => 'UTF-8', 
                      'args' => array(
                          'users_nationality_id' => $data['type']['users_nationality_id'], 
                          'users_nationality_group_type' => $data['type']['users_nationality_group_type'], 
                          'users_country_group_type' => $data['type']['users_country_group_type'], 
                          'users_country_group_id' => $data['type']['users_country_group_id'], 
                          'users_type_id' => $data['type']['users_type_id'],
                          'user_entry_id' => $entry['user_entry_id']
                      ), 
                      'href' => SITE_URL . 'en/admin/destinations/ajax_add_entry'
                  ));
                  array_push($found, $entry['user_entry_id']);
              }
          }
          if (!$found) {
              $html .= '<div class="none">No source data found.</div>';
          }
      }
      return $html;
  }

  function format_target_entries($data)
  {
      $html = '';
      if (!$data['type']['entries']) {
          $html .= '<div class="none">No target data found.</div>';
      } else {
          foreach ($data['type']['entries'] as $entry) 
          {
              $html .= Phery::link_to($entry['user_entry_name'], 'ajax_remove_entry', array(
                  'class' => 'entry-item remove', 
                  'phery-type' => 'json', 
                  'encoding' => 'UTF-8', 
                  'args' => array(
                      'users_nationality_id' => $entry['users_nationality_id'], 
                      'users_nationality_group_type' => $entry['users_nationality_group_type'], 
                      'users_country_group_type' => $entry['users_country_group_type'], 
                      'users_country_group_id' => $entry['users_country_group_id'], 
                      'users_type_id' => $entry['users_type_id'],
                      'user_entry_id' => $entry['user_entry_id']
                  ), 
                  'href' => SITE_URL . 'en/admin/destinations/ajax_remove_entry'
              ));
          }
      }
      return $html;
  }

  function format_source_services($data)
  {
      $html = '';
      if (!$this->destination['services']) {
          $html .= '<div class="none">No source data found.</div>';
      } else {
          $selected = array();
          if (isset($data['services_selected']) && is_array($data['services_selected']) && count($data['services_selected']) > 0) 
          {
              foreach ($data['services_selected'] as $service) 
              {
                  if (is_array($service)) {
                      array_push($selected, $service['users_services_id']);
                  } else {
                      array_push($selected, $service);
                  }
              }
          }
          $found = array();
          foreach ($this->destination['services'] as $service) 
          {
              if (count($selected) > 0 && !in_array($service['users_services_id'], $selected) || !count($selected)) 
              {
                  $html .= Phery::link_to($service['users_services_name'], 'ajax_add_service', array(
                      'class' => 'service-item add', 
                      'phery-type' => 'json', 
                      'encoding' => 'UTF-8', 
                      'args' => array(
                          'users_nationality_id' => $data['users_nationality_id'], 
                          'users_nationality_group_type' => $data['users_nationality_group_type'], 
                          'users_country_group_type' => $data['users_country_group_type'], 
                          'users_country_group_id' => $data['users_country_group_id'], 
                          'users_destination_id' => $data['users_country_group_id'], 
                          'users_type_id' => $data['users_type_id'],
                          'user_entry_id' => $data['user_entry_id'],
                          'users_services_id' => $service['users_services_id']
                      ), 
                      'href' => SITE_URL . 'en/admin/destinations/ajax_add_service'
                  ));
                  array_push($found, $service['users_services_id']);
              }
          }
          if (!$found) {
              $html .= '<div class="none">No source data found.</div>';
          }
      }
      return $html;
  }

  function format_target_services($data)
  {
      $html = '';
      if (!isset($data['services']) || !$data['services']) {
          $html .= '<div class="none">No target data found.</div>';
      } else {
          foreach ($data['services'] as $service) 
          {
              $html .= Phery::link_to($service['users_services_name'], 'ajax_remove_service', array(
                  'class' => 'service-item remove', 
                  'phery-type' => 'json', 
                  'encoding' => 'UTF-8', 
                  'args' => array(
                      'users_nationality_id' => $service['users_nationality_id'], 
                      'users_nationality_group_type' => $service['users_nationality_group_type'], 
                      'users_country_group_type' => $service['users_country_group_type'], 
                      'users_country_group_id' => $service['users_country_group_id'], 
                      'users_destination_id' => $service['users_country_group_id'], 
                      'users_type_id' => $service['users_type_id'], 
                      'user_entry_id' => $service['user_entry_id'], 
                      'users_services_id' => $service['users_services_id']
                  ), 
                  'href' => SITE_URL . 'en/admin/destinations/ajax_remove_service'
              ));
          }
      }
      return $html;
  }

  function format_service_items($data) 
  {
      if (!$data['services']) {
          return '';
      }
      $html = Phery::form_for(SITE_URL . 'en/admin/destinations/ajax_update_prices', 'ajax_update_prices', array(
          'class' => 'prices-form',
          'role' => 'form',
          'data-type' => 'json',
          'encoding' => 'UTF-8',
          'args' => array(
              'users_nationality_id' => $data['users_nationality_id'], 
              'users_nationality_group_type' => $data['users_nationality_group_type'], 
              'users_country_group_type' => $data['users_country_group_type'], 
              'users_country_group_id' => $data['users_country_group_id'], 
              'users_destination_id' => $data['users_country_group_id'], 
              'users_type_id' => $data['users_type_id'], 
              'user_entry_id' => $data['user_entry_id']
          ), 
          'submit' => array('disabled' => true, 'all' => true)
      ));
      foreach ($data['services'] as $service) 
      {
          $html .= $this->format_service_item($service);
      }
      $html .= '<div class="col-full">';
      $html .= '<button type="submit" data-loading-text="Please wait...">Save prices</button>';
      $html .= '</div>';
      $html .= '</form>';

      return $html;
  }

  function format_service_item($data)
  {
      if (!$this->destination['prices']) {
          return '';
      }

      $html = '<div class="service">';
      $html .= '<div class="col-full">';
      $html .= '<div class="service-name full-width pull-left"><h3>' . $data['users_services_name'] . '</h3></div>';
      
      $html .= '<div class="prices full-width pull-left">';
      $html .= '<div class="row price heading">';
      $html .= '<div class="row-col first"><b>Fee name</b></div>';
      $html .= '<div class="row-col"><b><span>Subtotal</span></b></div>';
      $html .= '<div class="row-col"><b><span>VAT</span></b></div>';
      $html .= '<div class="row-col"><b><span>Total</span></b></div>';
      $html .= '</div>';

      $total = 0;
	  $note = '';
      foreach ($this->destination['prices'] as $price) 
      {  
          $prices = array('total' => '', 'subtotal' => '', 'vat' => '');
          if (isset($data['prices']) && is_array($data['prices']) && count($data['prices']) > 0) 
          {
              foreach ($data['prices'] as $p) 
              {
                  if ($p['users_price_id'] == $price['users_price_id']) 
                  {
                    $prices['total'] = $p['total'];
                    $prices['subtotal'] = $p['subtotal'];
                    $prices['vat'] = $p['vat'];
                    $total += $p['total'];
					$note = $p['users_free_note'];
                  }
              }
          }
          $html .= '<div class="row price price-row">';
          $html .= '<div class="row-col text-right first"><b>' . $price['users_price_name'] . '</b></div>';

          $html .= '<div class="row-col">';
          $html .= '<span><input style="width: 100%" type="text" name="prices[' . $data['users_services_id'] . '][' . $price['users_price_id'] . '][subtotal]" class="price subprice subtotal' . ($price['users_price_vat'] == 0 ? ' readonly' : '') . '" data-type="subtotal"' . ($price['users_price_vat'] == 0 ? ' readonly="readonly"' : '') . ' value="' . $prices['subtotal'] . '"></span>';
          $html .= '</div>';
          $html .= '<div class="row-col">';
          $html .= '<span><input type="text" name="prices[' . $data['users_services_id'] . '][' . $price['users_price_id'] . '][vat]" class="price subprice vat' . ($price['users_price_vat'] == 0 ? ' readonly' : '') . '" style="width: 80%"  data-type="vat"' . ($price['users_price_vat'] == 0 ? ' readonly="readonly"' : '') . ' value="' . $prices['vat'] . '">' . ($price['users_price_vat'] != 0 ? ' <input class="togglevat" type="checkbox" onClick="_prices_vat_toggle(this)"' . (($prices['vat'] != 0 || ($price['users_price_vat'] == 1 && $price['users_price_id'] != 1)) ? ' checked' : '') . '>' : '') . '</span>';
          $html .= '</div>';
          $html .= '<div class="row-col">';
          $html .= '<span><input style="width: 100%" type="text" name="prices[' . $data['users_services_id'] . '][' . $price['users_price_id'] . '][total]" class="price subprice total" data-type="total" value="' . $prices['total'] . '"></span>';
          $html .= '</div>';
          $html .= '</div>';
      }
	  
	  $html .= '<div class="row">';
	  $html .= '<div class="row-col text-right"><b>Visa Note</b></div>';
	  $html .= '<div class="row-col" style="width: 75%"><span><input style="width: 100%" type="text" name="prices[' . $data['users_services_id'] . '][users_free_note]" class="note" data-type="note" placeholder="Note" value="' . $note . '"></span></div>';
	  $html .= '</div>';
	  
      $html .= '<div class="row price last">';
      $html .= '<div class="row-col">';
      $html .= '<span><input style="width: 100%" type="text" name="prices[' . $data['users_services_id'] . '][grand_total]" readonly="readonly" class="grand_total" data-type="grand_total" placeholder="Total" value="' . formatPriceDecimals($total) . '">';
	  $html .= '<div style="margin-top: 3px; width: 100%; text-align: right"><input class="toggleprice" type="checkbox" onClick="_prices_set_free(this)"' . (formatPriceDecimals($total) == 0 ? ' checked' : '') . ' title="Free Visa"><em>Enable Free Visa</em></span></div>';
      $html .= '</div>';
      $html .= '</div>';

      $html .= '</div>';
      $html .= '</div>';
      $html .= '</div>';
 
      return $html;
  }
  
  function format_documents($data)
  {
      if (!$this->destination['documents']) {
          return '';
      }

      $documents_exist = (isset($data['type']['documents']) && $data['type']['documents'] !== false);

      //$html = '<div class="column documents">';
      //$html .= '<div class="subheader"><h2>Documents</h2></div>';
      
      $html = '<div class="subcolumn full-width pull-left documents">';
      $html .= '<div class="subheader"><h2>Documents required</h2></div>';
      $html .= '<div class="section">';

      $html .= '<div class="col left">';
      $html .= '<div class="inner">';
      $html .= '<div class="source">';

      if (isset($data['type']['documents_selected'])) 
      {
          $found = array();
          foreach ($this->destination['documents'] as $document) 
          {
			  if(strlen($document['users_document_subtitle']) > 0)
			  {
				$document_display_title = $document['users_document_title'] . " (<em>" . $document['users_document_subtitle'] . "</em>)";
			  }
			  else
			  {
				$document_display_title = $document['users_document_title'];
			  }
			  
              if (!in_array($document['users_document_id'], $data['type']['documents_selected'])) 
              {
                  $html .= Phery::link_to($document_display_title, 'ajax_add_document', array(
                      'class' => 'document-item add', 
                      'phery-type' => 'json', 
                      'encoding' => 'UTF-8', 
                      'args' => array(
                          'users_nationality_id' => $data['type']['users_nationality_id'], 
                          'users_nationality_group_type' => $data['type']['users_nationality_group_type'], 
                          'users_country_group_type' => $data['type']['users_country_group_type'], 
                          'users_country_group_id' => $data['type']['users_country_group_id'], 
                          'users_destination_id' => $data['type']['users_country_group_id'], 
                          'users_type_id' => $data['type']['users_type_id'], 
                          'users_documents_id' => $document['users_document_id']
                      ), 
                      'href' => SITE_URL . 'en/admin/destinations/ajax_add_document'
                  ));
                  array_push($found, $document['users_document_id']);
              }
          }
          if (!$found) {
              $html .= '<div class="none">No source data found.</div>';
          }
      } else {
          foreach ($this->destination['documents'] as $document) 
          {
			  if(strlen($document['users_document_subtitle']) > 0)
			  {
				$document_display_title = $document['users_document_title'] . " (<em>" . $document['users_document_subtitle'] . "</em>)";
			  }
			  else
			  {
				$document_display_title = $document['users_document_title'];
			  }
			  
              $html .= Phery::link_to($document_display_title, 'ajax_add_document', array(
                  'class' => 'document-item add', 
                  'phery-type' => 'json', 
                  'encoding' => 'UTF-8', 
                  'args' => array(
                      'users_nationality_id' => $data['type']['users_nationality_id'], 
                      'users_nationality_group_type' => $data['type']['users_nationality_group_type'], 
                      'users_country_group_type' => $data['type']['users_country_group_type'], 
                      'users_country_group_id' => $data['type']['users_country_group_id'], 
                      'users_destination_id' => $data['type']['users_country_group_id'], 
                      'users_type_id' => $data['type']['users_type_id'], 
                      'users_documents_id' => $document['users_document_id']
                  ), 
                  'href' => SITE_URL . 'en/admin/destinations/ajax_add_document'
              ));
          }
      }

      $html .= '</div>';

      $html .= '</div>';
      $html .= '</div>';

      $html .= '<div class="col right">';
      $html .= '<div class="inner">';
      $html .= '<div class="target">';

      if ($documents_exist) 
      {
          foreach ($data['type']['documents'] as $document) 
          {
			  if(strlen($document['users_document_subtitle']) > 0)
			  {
				$document_display_title = $document['users_document_title'] . " (<em>" . $document['users_document_subtitle'] . "</em>)";
			  }
			  else
			  {
				$document_display_title = $document['users_document_title'];
			  }
			  
              $html .= Phery::link_to($document_display_title, 'ajax_remove_document', array(
                  'class' => 'document-item remove', 
                  'phery-type' => 'json', 
                  'encoding' => 'UTF-8', 
                  'args' => array(
                      'users_nationality_id' => $data['type']['users_nationality_id'], 
                      'users_nationality_group_type' => $data['type']['users_nationality_group_type'], 
                      'users_country_group_type' => $data['type']['users_country_group_type'], 
                      'users_country_group_id' => $data['type']['users_country_group_id'], 
                      'users_destination_id' => $data['type']['users_country_group_id'], 
                      'users_type_id' => $data['type']['users_type_id'], 
                      'users_documents_id' => $document['users_document_id']
                  ), 
                  'href' => SITE_URL . 'en/admin/destinations/ajax_remove_document'
              ));
          }
      } else {
          $html .= '<div class="none">No target data found.</div>';
      }

      $html .= '</div>';
      $html .= '</div>';
      $html .= '</div>';
      $html .= '</div>';

      $html .= '</div>';
      //$html .= '</div>';

      return $html;
  }

  function format_source_documents($data)
  {
      $html = '';
      if (!$this->destination['documents']) {
          $html .= '<div class="none">No source data found.</div>';
      } else {
          $selected = array();
          if (isset($data['documents_selected']) && is_array($data['documents_selected']) && count($data['documents_selected']) > 0) 
          {
              foreach ($data['documents_selected'] as $document) 
              {
                  if (is_array($document)) {
                      array_push($selected, $document['users_documents_id']);
                  } else {
                      array_push($selected, $document);
                  }
              }
          }
          $found = array();
          foreach ($this->destination['documents'] as $document) 
          {
              if (count($selected) > 0 && !in_array($document['users_document_id'], $selected) || !count($selected)) 
              {
				  if(strlen($document['users_document_subtitle']) > 0)
				  {
					$document_display_title = $document['users_document_title'] . " (<em>" . $document['users_document_subtitle'] . "</em>)";
				  }
				  else
				  {
					$document_display_title = $document['users_document_title'];
				  }
                  $html .= Phery::link_to($document_display_title, 'ajax_add_document', array(
                      'class' => 'document-item add', 
                      'phery-type' => 'json', 
                      'encoding' => 'UTF-8', 
                      'args' => array(
                          'users_nationality_id' => $data['users_nationality_id'], 
                          'users_nationality_group_type' => $data['users_nationality_group_type'], 
                          'users_country_group_type' => $data['users_country_group_type'], 
                          'users_country_group_id' => $data['users_country_group_id'], 
                          'users_destination_id' => $data['users_country_group_id'], 
                          'users_type_id' => $data['users_type_id'], 
                          'users_documents_id' => $document['users_document_id']
                      ), 
                      'href' => SITE_URL . 'en/admin/destinations/ajax_add_document'
                  ));
                  array_push($found, $document['users_document_id']);
              }
          }
          if (!$found) {
              $html .= '<div class="none">No source data found.</div>';
          }
      }
      return $html;
  }

  function format_target_documents($data)
  {
      $html = '';
      if (!isset($data['documents']) || !$data['documents']) {
          $html .= '<div class="none">No target data found.</div>';
      } else {
          foreach ($data['documents'] as $document) 
          {
			  if(strlen($document['users_document_subtitle']) > 0)
			  {
				$document_display_title = $document['users_document_title'] . " (<em>" . $document['users_document_subtitle'] . "</em>)";
			  }
			  else
			  {
				$document_display_title = $document['users_document_title'];
			  }
              $html .= Phery::link_to($document_display_title, 'ajax_remove_document', array(
                  'class' => 'document-item remove', 
                  'phery-type' => 'json', 
                  'encoding' => 'UTF-8', 
                  'args' => array(
                      'users_nationality_id' => $document['users_nationality_id'], 
                      'users_nationality_group_type' => $document['users_nationality_group_type'], 
                      'users_country_group_type' => $document['users_country_group_type'], 
                      'users_country_group_id' => $document['users_country_group_id'], 
                      'users_destination_id' => $document['users_country_group_id'], 
                      'users_type_id' => $document['users_type_id'], 
                      'users_documents_id' => $document['users_document_id']
                  ), 
                  'href' => SITE_URL . 'en/admin/destinations/ajax_remove_document'
              ));
          }
      }
      return $html;
  }

  function format_notes($data)
  {
      if (!$this->destination['notes']) {
          return '';
      }

      $notes_exist = (isset($data['type']['notes']) && $data['type']['notes'] !== false);

      //$html = '<div class="column notes">';
      //$html .= '<div class="subheader"><h2>Notes</h2></div>';
      
      $html = '<div class="subcolumn full-width pull-left notes">';
      $html .= '<div class="subheader"><h2>Notes</h2></div>';
      $html .= '<div class="section">';

      $html .= '<div class="col left">';
      $html .= '<div class="inner">';
      $html .= '<div class="source">';

      if (isset($data['type']['notes_selected'])) 
      {
          $found = array();
          foreach ($this->destination['notes'] as $note) 
          {
              if (!in_array($note['users_notes_id'], $data['type']['documents_selected'])) 
              {
				  if(strlen($note['users_notes_subtitle']) > 0)
				  {
					$note_display_title = $note['users_notes_title'] . " (<em>" . $note['users_notes_subtitle'] . "</em>)";
				  }
				  else
				  {
					$note_display_title = $note['users_notes_title'];
				  }
				  
                  $html .= Phery::link_to($note_display_title, 'ajax_add_note', array(
                      'class' => 'note-item add', 
                      'phery-type' => 'json', 
                      'encoding' => 'UTF-8', 
                      'args' => array(
                          'users_nationality_id' => $data['type']['users_nationality_id'], 
                          'users_nationality_group_type' => $data['type']['users_nationality_group_type'], 
                          'users_country_group_type' => $data['type']['users_country_group_type'], 
                          'users_country_group_id' => $data['type']['users_country_group_id'], 
                          'users_destination_id' => $data['type']['users_country_group_id'], 
                          'users_type_id' => $data['type']['users_type_id'], 
                          'users_notes_id' => $note['users_notes_id']
                      ), 
                      'href' => SITE_URL . 'en/admin/destinations/ajax_add_note'
                  ));
                  array_push($found, $note['users_notes_id']);
              }
          }
          if (!$found) {
              $html .= '<div class="none">No source data found.</div>';
          }
      } else {
          foreach ($this->destination['notes'] as $note) 
          {
			  if(strlen($note['users_notes_subtitle']) > 0)
			  {
				$note_display_title = $note['users_notes_title'] . " (<em>" . $note['users_notes_subtitle'] . "</em>)";
			  }
			  else
			  {
				$note_display_title = $note['users_notes_title'];
			  }
			  
              $html .= Phery::link_to($note_display_title, 'ajax_add_note', array(
                  'class' => 'note-item add', 
                  'phery-type' => 'json', 
                  'encoding' => 'UTF-8', 
                  'args' => array(
                      'users_nationality_id' => $data['type']['users_nationality_id'], 
                      'users_nationality_group_type' => $data['type']['users_nationality_group_type'], 
                      'users_country_group_type' => $data['type']['users_country_group_type'], 
                      'users_country_group_id' => $data['type']['users_country_group_id'], 
                      'users_destination_id' => $data['type']['users_country_group_id'], 
                      'users_type_id' => $data['type']['users_type_id'], 
                      'users_notes_id' => $note['users_notes_id']
                  ), 
                  'href' => SITE_URL . 'en/admin/destinations/ajax_add_note'
              ));
          }
      }

      $html .= '</div>';

      $html .= '</div>';
      $html .= '</div>';

      $html .= '<div class="col right">';
      $html .= '<div class="inner">';
      $html .= '<div class="target">';

      if ($notes_exist) 
      {
          foreach ($data['type']['notes'] as $note) 
          {
			  if(strlen($note['users_notes_subtitle']) > 0)
			  {
				$note_display_title = $note['users_notes_title'] . " (<em>" . $note['users_notes_subtitle'] . "</em>)";
			  }
			  else
			  {
				$note_display_title = $note['users_notes_title'];
			  }
			  
              $html .= Phery::link_to($note_display_title, 'ajax_remove_note', array(
                  'class' => 'note-item remove', 
                  'phery-type' => 'json', 
                  'encoding' => 'UTF-8', 
                  'args' => array(
                      'users_nationality_id' => $data['type']['users_nationality_id'], 
                      'users_nationality_group_type' => $data['type']['users_nationality_group_type'], 
                      'users_country_group_type' => $data['type']['users_country_group_type'], 
                      'users_country_group_id' => $data['type']['users_country_group_id'], 
                      'users_destination_id' => $data['type']['users_country_group_id'], 
                      'users_type_id' => $data['type']['users_type_id'], 
                      'users_notes_id' => $note['users_notes_id']
                  ), 
                  'href' => SITE_URL . 'en/admin/destinations/ajax_remove_note'
              ));
          }
      } else {
          $html .= '<div class="none">No target data found.</div>';
      }

      $html .= '</div>';
      $html .= '</div>';
      $html .= '</div>';
      $html .= '</div>';

      $html .= '</div>';
      //$html .= '</div>';

      return $html;
  }
  
  function format_source_notes($data)
  {
      $html = '';
      if (!$this->destination['notes']) {
          $html .= '<div class="none">No source data found.</div>';
      } else {
          $selected = array();
          if (isset($data['notes_selected']) && is_array($data['notes_selected']) && count($data['notes_selected']) > 0) 
          {
              foreach ($data['notes_selected'] as $note) 
              {
                  if (is_array($note)) {
                      array_push($selected, $note['users_notes_id']);
                  } else {
                      array_push($selected, $note);
                  }
              }
          }
          $found = array();
          foreach ($this->destination['notes'] as $note) 
          {
              if (count($selected) > 0 && !in_array($note['users_notes_id'], $selected) || !count($selected)) 
              {
				  if(strlen($note['users_notes_subtitle']) > 0)
				  {
					$note_display_title = $note['users_notes_title'] . " (<em>" . $note['users_notes_subtitle'] . "</em>)";
				  }
				  else
				  {
					$note_display_title = $note['users_notes_title'];
				  }
				  
                  $html .= Phery::link_to($note_display_title, 'ajax_add_note', array(
                      'class' => 'note-item add', 
                      'phery-type' => 'json', 
                      'encoding' => 'UTF-8', 
                      'args' => array(
                          'users_nationality_id' => $data['users_nationality_id'], 
                          'users_nationality_group_type' => $data['users_nationality_group_type'], 
                          'users_country_group_type' => $data['users_country_group_type'], 
                          'users_country_group_id' => $data['users_country_group_id'], 
                          'users_destination_id' => $data['users_country_group_id'], 
                          'users_type_id' => $data['users_type_id'], 
                          'users_notes_id' => $note['users_notes_id']
                      ), 
                      'href' => SITE_URL . 'en/admin/destinations/ajax_add_note'
                  ));
                  array_push($found, $note['users_notes_id']);
              }
          }
          if (!$found) {
              $html .= '<div class="none">No source data found.</div>';
          }
      }
      return $html;
  }
  
  function format_target_notes($data)
  {
      $html = '';
      if (!isset($data['notes']) || !$data['notes']) {
          $html .= '<div class="none">No target data found.</div>';
      } else {
          foreach ($data['notes'] as $note) 
          {
			  if(strlen($note['users_notes_subtitle']) > 0)
			  {
				$note_display_title = $note['users_notes_title'] . " (<em>" . $note['users_notes_subtitle'] . "</em>)";
			  }
			  else
			  {
				$note_display_title = $note['users_notes_title'];
			  }
			  
              $html .= Phery::link_to($note_display_title, 'ajax_remove_note', array(
                  'class' => 'note-item remove', 
                  'phery-type' => 'json', 
                  'encoding' => 'UTF-8', 
                  'args' => array(
                      'users_nationality_id' => $note['users_nationality_id'], 
                      'users_nationality_group_type' => $note['users_nationality_group_type'], 
                      'users_country_group_type' => $note['users_country_group_type'], 
                      'users_country_group_id' => $note['users_country_group_id'], 
                      'users_destination_id' => $note['users_country_group_id'], 
                      'users_type_id' => $note['users_type_id'], 
                      'users_notes_id' => $note['users_notes_id']
                  ), 
                  'href' => SITE_URL . 'en/admin/destinations/ajax_remove_note'
              ));
          }
      }
      return $html;
  }

  private function ajax_add_new_type() 
  {
      if (!isset($_POST['args']) || !is_array($_POST['args'])) {
          echo result('Missing params', 'error');
          exit;
      }
      if (!isset($_POST['args']['users_type_name'])) {
          echo result('Missing field name', 'error');
          exit;
      }
      if (strlen($_POST['args']['users_type_name']) < 1) {
          echo result('Empty data sent', 'error');
          exit;
      }

      $this->destinations_model->insert_type($_POST['args']);
      $types = $this->destinations_model->fetch_selected_types($_POST['args']);

      $data['target'] = $this->format_target_types($types);
      $data['content'] = $this->format_type_items(array(
          'selected_types' => $types
      ));
      
      echo result($data, 'success');
      exit;
  }

  private function ajax_add_type() 
  {
      if (!isset($_POST['args']) || !is_array($_POST['args'])) {
          echo result('Missing params', 'error');
          exit;
      }
      if (!isset($_POST['args']['users_country_group_id']) || !isset($_POST['args']['users_country_group_type'])) {
          echo result('Missing params', 'error');
          exit;
      }

      $this->destinations_model->insert_type($_POST['args']);
      $types = $this->destinations_model->fetch_selected_types($_POST['args']);

      $data['target'] = $this->format_target_types($types);
      $data['content'] = $this->format_type_items(array(
          'selected_types' => $types
      ));
      
      echo result($data, 'success');
      exit;
  }
  
  private function ajax_remove_type() 
  {
      if (!isset($_POST['args']) || !is_array($_POST['args'])) {
          echo result('Missing params', 'error');
          exit;
      }
      if (!isset($_POST['args']['users_type_id']) || !isset($_POST['args']['users_country_group_id'])) {
          echo result('Missing params', 'error');
          exit;
      }

      $this->destinations_model->delete_type($_POST['args']);

      $type['data'] = $_POST['args'];
      $type['types'] = $this->destinations_model->fetch_types();
      $type['types_selected'] = $this->destinations_model->fetch_selected_types($_POST['args']);

      $data['source'] = $this->format_source_types($type);
      $data['content'] = $this->format_type_items(array(
          'selected_types' => $type['types_selected']
      ));

      echo result($data, 'success');
      exit;
  }
  
  private function ajax_add_entry() 
  {
      if (!isset($_POST['args']) || !is_array($_POST['args'])) {
          echo result('Missing params', 'error');
          exit;
      }
      if (!isset($_POST['args']['user_entry_id']) || !isset($_POST['args']['users_type_id'])) {
          echo result('Missing params', 'error');
          exit;
      }
      
      $this->destinations_model->insert_entry($_POST['args']);

      $entry['type']['entries'] = $this->destinations_model->fetch_selected_entries($_POST['args']);

      $data['target'] = $this->format_target_entries($entry);
      $data['content'] = $this->format_entry_items($entry);

      echo result($data, 'success');
      exit;
  }

  private function ajax_remove_entry() 
  {
      if (!isset($_POST['args']) || !is_array($_POST['args'])) {
          echo result('Missing params', 'error');
          exit;
      }
      if (!isset($_POST['args']['user_entry_id']) || !isset($_POST['args']['users_type_id'])) {
          echo result('Missing params', 'error');
          exit;
      }

      $this->destinations_model->delete_entry($_POST['args']);

      $entry['type'] = $_POST['args'];
      $entry['type']['entries_selected'] = $this->destinations_model->fetch_selected_entries($_POST['args']);
      $entry['type']['entries'] = $entry['type']['entries_selected'];

      $data['source'] = $this->format_source_entries($entry);
      $data['content'] = $this->format_entry_items($entry);

      echo result($data, 'success');
      exit;
  }
  
  private function update_entry_option() 
  {
      if (!isset($_POST['args']) || !is_array($_POST['args'])) {
          echo result('Missing params', 'error');
          exit;
      }
      if (!isset($_POST['args']['user_entry_id']) || !isset($_POST['args']['users_type_id'])) {
          echo result('Missing params', 'error');
          exit;
      }

      if (!$this->destinations_model->update_entry_option($_POST['args'])) {
          echo result('Entry update error', 'error');
          exit;
      }

      echo result('Entry successfully updated.', 'success');
      exit;
  }

  private function ajax_add_service() 
  {
      if (!isset($_POST['args']) || !is_array($_POST['args'])) {
          echo result('Missing params', 'error');
          exit;
      }
      if (!isset($_POST['args']['users_services_id']) || !isset($_POST['args']['user_entry_id'])) {
          echo result('Missing params', 'error');
          exit;
      }

      $this->destinations_model->insert_service($_POST['args']);

      $service = $_POST['args'];
      $service['services'] = $this->destinations_model->fetch_selected_services($_POST['args']);

      $data['target'] = $this->format_target_services($service);
      $data['content'] = $this->format_service_items($service);

      echo result($data, 'success');
      exit;
  }

  private function ajax_remove_service() 
  {
      if (!isset($_POST['args']) || !is_array($_POST['args'])) {
          echo result('Missing params', 'error');
          exit;
      }
      if (!isset($_POST['args']['user_entry_id']) || !isset($_POST['args']['users_type_id'])) {
          echo result('Missing params', 'error');
          exit;
      }

      $this->destinations_model->delete_service($_POST['args']);

      $service = $_POST['args'];
      $service['services_selected'] = $this->destinations_model->fetch_selected_services($_POST['args']);
      $service['services'] = $service['services_selected'];

      $data['source'] = $this->format_source_services($service);
      $data['content'] = $this->format_service_items($service);

      echo result($data, 'success');
      exit;
  }

  private function ajax_update_prices() 
  {
      if (!isset($_POST['args']) || !is_array($_POST['args'])) {
          echo result('Missing params', 'error');
          exit;
      }

      if (!$this->destinations_model->insert_prices($_POST['args'])) {
          echo result('Prices update error', 'error');
          exit;
      }

      echo result('Prices successfully updated.', 'success');
      exit;
  }

  private function ajax_add_document() 
  {
      if (!isset($_POST['args']) || !is_array($_POST['args'])) {
          echo result('Missing params', 'error');
          exit;
      }
      if (!isset($_POST['args']['users_documents_id'])) {
          echo result('Missing params', 'error');
          exit;
      }
      
      $this->destinations_model->insert_document($_POST['args']);

      $docs['documents'] = $this->destinations_model->fetch_selected_documents($_POST['args']);

      $data['target'] = $this->format_target_documents($docs);

      echo result($data, 'success');
      exit;
  }

  private function ajax_remove_document() 
  {
      if (!isset($_POST['args']) || !is_array($_POST['args'])) {
          echo result('Missing params', 'error');
          exit;
      }
      if (!isset($_POST['args']['users_documents_id'])) {
          echo result('Missing params', 'error');
          exit;
      }

      $this->destinations_model->delete_document($_POST['args']);

      $document = $_POST['args'];
      $document['documents_selected'] = $this->destinations_model->fetch_selected_documents($_POST['args']);
      $document['documents'] = $document['documents_selected'];

      $data['source'] = $this->format_source_documents($document);

      echo result($data, 'success');
      exit;
  }
  
  private function ajax_add_note() 
  {
      if (!isset($_POST['args']) || !is_array($_POST['args'])) {
          echo result('Missing params', 'error');
          exit;
      }
      if (!isset($_POST['args']['users_notes_id'])) {
          echo result('Missing params', 'error');
          exit;
      }

      $this->destinations_model->insert_note($_POST['args']);

      $notes['notes'] = $this->destinations_model->fetch_selected_notes($_POST['args']);

      $data['target'] = $this->format_target_notes($notes);

      echo result($data, 'success');
      exit;
  }

  private function ajax_remove_note() 
  {
      if (!isset($_POST['args']) || !is_array($_POST['args'])) {
          echo result('Missing params', 'error');
          exit;
      }
      if (!isset($_POST['args']['users_notes_id'])) {
          echo result('Missing params', 'error');
          exit;
      }

      $this->destinations_model->delete_note($_POST['args']);

      $notes = $_POST['args'];
      $notes['notes_selected'] = $this->destinations_model->fetch_selected_notes($_POST['args']);
      $notes['notes'] = $notes['notes_selected'];

      $data['source'] = $this->format_source_notes($notes);

      echo result($data, 'success');
      exit;
  }

  function preview_application() {
	  if($id = $this->url->segment(3) && $data = $this->destinations_model->fetch_item($this->url->segment(3))) {
		  $preview_session = array();
		  $preview_session['users_destinations_selected_id'] = $this->url->segment(3);
		  $preview_session['users_country_group_type'] = $data['users_country_group_type'];
		  $preview_session['users_nationality_group_type'] = $data['users_nationality_group_type'];
		  $preview_session['users_destination_id'] = $data['users_destination_id'];
		  $preview_session['users_nationality_id'] = $data['users_nationality_id'];
		  $preview_session['users_country_group_id'] = $data['users_destination_id'];
      $tmp1 = $this->destinations_model->fetch_country_name($data['users_destination_id']);
		  $preview_session['destination_name'] = $tmp1[0]['users_country_name'];
		  
		  $_SESSION['preview'] = $preview_session;

		  $this->url->redirect(SITE_URL . '/visa-costs/');
	  }
	  else {
		  $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
	  }
  }
  
  function exit_preview() {
	  unset($_SESSION['preview']);
	  $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
  }
  
  function edit_type()
  {
    $id = $this->url->segment(3);
    $users_type_id = $this->url->segment(4);

    if (!isset($_POST['types'])) {
        $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $id . '/anchor_' . $users_type_id);
        return false;
    }

    $data = array();
    $data['users_nationality_id'] = $_POST['types']['users_nationality_id'];
    $data['users_country_group_id'] = $_POST['types']['users_country_group_id'];
    $data['users_country_group_type'] = $_POST['types']['users_country_group_type'];
    $data['users_type_id'] = $_POST['types']['users_type_id'];
    $data['entries'] = $_POST['types']['entries'];
    
    if (count($data) < 1) {
        $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $id . '/anchor_' . $users_type_id);
        return false;
    }
    $this->destinations_model->insert_entries($data);

    $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $id . '/anchor_' . $users_type_id);
  }
  
  function edit_entry()
  {
    $id = $this->url->segment(3);
    $users_type_id = $this->url->segment(4);
    $user_entry_id = $this->url->segment(5);

    if (!isset($_POST['entries'])) {
        $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $id . '/anchor_' . $users_type_id);
        return false;
    }

    $data = array();
    $data['users_nationality_id'] = $_POST['entries']['users_nationality_id'];
    $data['users_country_group_id'] = $_POST['entries']['users_country_group_id'];
    $data['users_country_group_type'] = $_POST['entries']['users_country_group_type'];
    $data['entry_option_id'] = $_POST['entries']['entry_option_id'];
    $data['users_type_id'] = $_POST['entries']['users_type_id'];
    $data['user_entry_id'] = $_POST['entries']['user_entry_id'];
    $data['services'] = $_POST['entries']['services'];
    
    if (count($data) < 1) {
        $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $id . '/anchor_' . $users_type_id);
        return false;
    }
    $this->destinations_model->insert_services($data);

    $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $id . '/entry_' . $users_type_id . '_' . $user_entry_id);
  }

  function edit_prices()
  {
    $id = $this->url->segment(3);
    $users_type_id = $this->url->segment(4);
    $user_entry_id = $this->url->segment(5);
    
    if (!isset($_POST['services'])) {
        $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $id . '/anchor_' . $users_type_id);
        return false;
    }

    $data = array();
    $data['users_nationality_id'] = $_POST['services']['users_nationality_id'];
    $data['users_country_group_id'] = $_POST['services']['users_country_group_id'];
    $data['users_country_group_type'] = $_POST['services']['users_country_group_type'];
    $data['users_type_id'] = $_POST['services']['users_type_id'];
    $data['user_entry_id'] = $_POST['services']['user_entry_id'];
    $data['prices'] = $_POST['services']['prices'];

    if (count($data) < 1) {
        $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $id . '/anchor_' . $users_type_id);
        return false;
    }
    $this->destinations_model->insert_prices($data);

    $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $id . '/price_' . $users_type_id . '_' . $user_entry_id);
  }

  function edit_documents()
  {
    $id = $this->url->segment(3);

    if (!isset($_POST['documents'])) {
        $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $id);
        return false;
    }

    $data = array();
    $data['users_nationality_id'] = $_POST['documents']['users_nationality_id'];
    $data['users_country_group_id'] = $_POST['documents']['users_country_group_id'];
    $data['users_country_group_type'] = $_POST['documents']['users_country_group_type'];
    $data['documents'] = $_POST['documents']['documents'];
    
    if (count($data) < 1) {
        $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $id);
        return false;
    }
    $this->destinations_model->insert_documents($data);

    $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $id . '/documents_anchor');
  }

  function edit_notes()
  {
    $id = $this->url->segment(3);

    if (!isset($_POST['notes'])) {
        $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $id);
        return false;
    }

    $data = array();
    $data['users_nationality_id'] = $_POST['notes']['users_nationality_id'];
    $data['users_country_group_id'] = $_POST['notes']['users_country_group_id'];
    $data['users_country_group_type'] = $_POST['notes']['users_country_group_type'];
    $data['notes'] = $_POST['notes']['notes'];
    
    if (count($data) < 1) {
        $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $id);
        return false;
    }
    $this->destinations_model->insert_notes($data);

    $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $id . '/notes_anchor');
  }

  function delete_type()
  {
    $id = $this->url->segment(3);
    $users_type_id = $this->url->segment(4);
    $this->destinations_model->delete_type($id, $users_type_id);

    if (!$this->db->error)
    {
      $this->alert->add($this->lang->line('success'), 'success');
    }
    $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $id);
  }

  function delete()
  {
    $id = $this->url->segment(3);
    $this->destinations_model->delete($id);

    if (!$this->db->error)
    {
      $this->alert->add($this->lang->line('success'), 'success');
    }
    $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
  }

}
?>
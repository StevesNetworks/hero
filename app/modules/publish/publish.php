<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
* Publish Module Definition
*
* Declares the module, update code, etc.
*
* @author Electric Function, Inc.
* @copyright Electric Function, Inc.
* @package Electric Publisher
*
*/

class Publish extends Module {
	var $version = '1.09';
	var $name = 'publish';

	function __construct () {
		// set the active module
		$this->active_module = $this->name;	
		
		parent::__construct();
	}
	
	/*
	* Pre-admin function
	*
	* Initiate navigation in control panel
	*/
	function admin_preload ()
	{
		$this->CI->navigation->child_link('publish',10,'Publish New Content',site_url('admincp/publish/create'));
		
		// alows for 20 possible content types in the menu - more than enough
		$weight = 11;
		
		$this->CI->db->where('content_type_is_module','0');
		$this->CI->db->order_by('content_type_friendly_name','ASC');
		$result = $this->CI->db->get('content_types');
		foreach ($result->result_array() as $type) {
			$this->CI->navigation->child_link('publish',$weight,'Manage ' . $type['content_type_friendly_name'],site_url('admincp/publish/manage/' . $type['content_type_id']));
			$weight++;
		}
		
		$this->CI->navigation->child_link('publish',50,'Topics',site_url('admincp/publish/topics'));
		$this->CI->navigation->child_link('publish',60,'Content Types',site_url('admincp/publish/types'));
	}
	
	/*
	* Pre-front Method
	*
	* Triggered prior to loading the frontend
	*/
	function front_preload () {
		$this->CI->smarty->addPluginsDir(APPPATH . 'modules/publish/template_plugins/');
	}
	
	/*
	* Module update
	*
	* @param int $db_version The current DB version
	*
	* @return int The current software version, to update the database
	*/
	function update ($db_version) {
		if ($db_version < 1.09) {
			$this->CI->db->query('DROP TABLE IF EXISTS `content`');
			
			$this->CI->db->query('CREATE TABLE `content` (
 								 `content_id` int(11) NOT NULL auto_increment,
 								 `link_id` int(11) NOT NULL,
 								 `content_type_id` int(11) NOT NULL,
 								 `user_id` int(11) NOT NULL,
 								 `content_date` DATETIME NOT NULL,
 								 `content_modified` DATETIME NOT NULL,
 								 `content_topics` VARCHAR(255) NOT NULL,
 								 `content_is_standard` tinyint(1) NOT NULL,
 								 `content_title` varchar(255),
 								 `content_privileges` varchar(255),
   								 PRIMARY KEY  (`content_id`)
								 ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;');
		}
		if ($db_version < 1.03) {
			$this->CI->db->query('CREATE TABLE `topic_maps` (
 								 `topic_map_id` int(11) NOT NULL auto_increment,
 								 `topic_id` int(11) NOT NULL,
 								 `content_id` int(11) NOT NULL,
   								 PRIMARY KEY  (`topic_map_id`)
								 ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;');
		
			$this->CI->db->query('CREATE TABLE `topics` (
 								 `topic_id` int(11) NOT NULL auto_increment,
 								 `topic_parent_id` int(11) NOT NULL,
 								 `topic_name` varchar(250) NOT NULL,
  								 `topic_description` text NOT NULL,
  								 `topic_deleted` tinyint(1) NOT NULL,
   								 PRIMARY KEY  (`topic_id`)
								 ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1000 ;');
		}
								 
		if ($db_version < 1.02) {	
			$this->CI->db->query('DROP TABLE IF EXISTS `content_types`');
										 
			$this->CI->db->query('CREATE TABLE `content_types` (
 								 `content_type_id` int(11) NOT NULL auto_increment,
 								 `content_type_is_module` tinyint(1) NOT NULL,
 								 `content_type_is_standard` tinyint(1) NOT NULL,
 								 `content_type_is_privileged` tinyint(1) NOT NULL,
 								 `custom_field_group_id` int(11) NOT NULL,
 								 `content_type_friendly_name` varchar(100) NOT NULL,
 								 `content_type_system_name` varchar(50) NOT NULL,
 								 `content_type_template` varchar(255) NOT NULL,
 								 PRIMARY KEY  (`content_type_id`)
								) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;');
		}
		
		if ($db_version < 1.0) {								 
			$this->CI->settings_model->make_writeable_folder(setting('path_editor_uploads'));
		}
	
		return $this->version;
	}
}
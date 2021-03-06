<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}

class Phdawarded_menu_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    function getMenu() {
        $menu = array();

        $menu['exam_dr'] = array();
        $menu['exam_dr']['PHD'] = array();
        $menu['exam_dr']['PHD']['Ongoing'] = site_url('phdawarded/phdcurrent');
	$menu['exam_dr']['PHD']['Awarded'] = site_url('phdawarded/phdawardedlist');
        $menu['exam_dr']['PHD']['Status Update'] = site_url('phdawarded/phdawarded');
        
        $menu['acad_ar'] = array();
        $menu['acad_ar']['PHD'] = array();
	$menu['acad_ar']['PHD']['Ongoing'] = site_url('phdawarded/phdcurrent');
	$menu['acad_ar']['PHD']['Awarded'] = site_url('phdawarded/phdawardedlist');
        $menu['acad_ar']['PHD']['Status Update'] = site_url('phdawarded/phdawarded');
      
        $menu['adug'] = array();
        $menu['adug']['PHD'] = array();
	$menu['adug']['PHD']['Ongoing'] = site_url('phdawarded/phdcurrent');
	$menu['adug']['PHD']['Awarded'] = site_url('phdawarded/phdawardedlist');
        $menu['adug']['PHD']['Status Update'] = site_url('phdawarded/phdawarded');
		
		$menu['adpg'] = array();
        $menu['adpg']['PHD'] = array();
	$menu['adpg']['PHD']['Ongoing'] = site_url('phdawarded/phdcurrent');
	$menu['adpg']['PHD']['Awarded'] = site_url('phdawarded/phdawardedlist');
        $menu['adpg']['PHD']['Status Update'] = site_url('phdawarded/phdawarded');
		
		$menu['dean_acad'] = array();
        $menu['dean_acad']['PHD'] = array();
	$menu['dean_acad']['PHD']['Ongoing'] = site_url('phdawarded/phdcurrent');
	$menu['dean_acad']['PHD']['Awarded'] = site_url('phdawarded/phdawardedlist');
        $menu['dean_acad']['PHD']['Status Update'] = site_url('phdawarded/phdawarded');
		
		
        $menu['acad_exam'] = array();
        $menu['acad_exam']['PHD'] = array();
	$menu['acad_exam']['PHD']['Ongoing'] = site_url('phdawarded/phdcurrent');
	$menu['acad_exam']['PHD']['Awarded'] = site_url('phdawarded/phdawardedlist');
        $menu['acad_exam']['PHD']['Status Update'] = site_url('phdawarded/phdawarded');
	
	$menu['acad_dr'] = array();
        $menu['acad_dr']['PHD'] = array();
	$menu['acad_dr']['PHD']['Ongoing'] = site_url('phdawarded/phdcurrent');
	$menu['acad_dr']['PHD']['Awarded'] = site_url('phdawarded/phdawardedlist');
        $menu['acad_dr']['PHD']['Status Update'] = site_url('phdawarded/phdawarded');
        



        return $menu;
    }

}

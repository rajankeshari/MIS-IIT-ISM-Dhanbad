<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}

class Hs_reg_menu_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    function getMenu() {
        $menu = array();
        
        
        $menu['admin'] = array();
        $menu['admin']['Hostel Booking'] = array();
       // $menu['hos_stu_admin']['Hostel Booking']['Manage Hostel Name'] = site_url('hs_reg/hostel/manage_hostel_name');
        $menu['admin']['Hostel Booking']['Manage Hostel'] = site_url('hs_reg/hostel/manage_hostel');
        $menu['admin']['Hostel Booking']['Block/Unblock Room'] = site_url('hs_reg/hostel/block_unblock_room');
        $menu['admin']['Hostel Booking']['Manage student contact'] = site_url('hs_reg/hostel/manage_contact_student');
        $menu['admin']['Hostel Booking']['Bulk upload student contact'] = site_url('hs_reg/hostel/bulk_upload_contact_student');
        $menu['admin']['Hostel Booking']['Search Assigned Student'] = site_url('hs_reg/hostel/search_student_allot');
        $menu['admin']['Hostel Booking']['Manage Warden'] = site_url('hs_reg/hostel/manage_hostel_warden');
        $menu['admin']['Hostel Booking']['Show Room Status'] = site_url('hs_reg/hostel/show_hostel_room');
        $menu['admin']['Report']['Show Students'] = site_url('hs_reg/hostel/filter_assigned_student');
        $menu['admin']['Report']['Show Rooms'] = site_url('hs_reg/hostel/show_rooms_hostel');
      //$menu['admin']['Report']['History of Student'] = site_url('hs_reg/hostel/history_student');

        //$menu['admin'] = array();
        $menu['admin']['Hostel Booking']['Manage guest contact'] = site_url('hs_reg/hostel/manage_guest_contact');
        $menu['admin']['Hostel Booking']['Show Guest Room Status'] = site_url('hs_reg/hostel/show_guest_hostel_room');
        $menu['admin']['Hostel Booking']['Search Assigned Guest'] = site_url('hs_reg/hostel/search_guest');
        //$menu['hos_guest_admin']['Report']['Show Assigned Guest'] = site_url('hs_reg/hostel/filter_assigned_guest');
	    $menu['admin']['Report']['Show Assigned Guest'] = site_url('hs_reg/hostel/search_guest');
	    $menu['admin']['Report']['Download Student Details'] = site_url('hs_reg/hostel/download_student_contact');
        
        

        $menu['hos_stu_admin'] = array();
        $menu['hos_stu_admin']['Hostel Booking'] = array();
       // $menu['hos_stu_admin']['Hostel Booking']['Manage Hostel Name'] = site_url('hs_reg/hostel/manage_hostel_name');
        $menu['hos_stu_admin']['Hostel Booking']['Manage Hostel'] = site_url('hs_reg/hostel/manage_hostel');
        $menu['hos_stu_admin']['Hostel Booking']['Block/Unblock Room'] = site_url('hs_reg/hostel/block_unblock_room');
        $menu['hos_stu_admin']['Hostel Booking']['Manage student contact'] = site_url('hs_reg/hostel/manage_contact_student');
        $menu['hos_stu_admin']['Hostel Booking']['Bulk upload student contact'] = site_url('hs_reg/hostel/bulk_upload_contact_student');
        $menu['hos_stu_admin']['Hostel Booking']['Search Assigned Student'] = site_url('hs_reg/hostel/search_student_allot');
        $menu['hos_stu_admin']['Hostel Booking']['Manage Warden'] = site_url('hs_reg/hostel/manage_hostel_warden');
        $menu['hos_stu_admin']['Hostel Booking']['Show Room Status'] = site_url('hs_reg/hostel/show_hostel_room');
        $menu['hos_stu_admin']['Hostel Booking']['Vacant Hostel'] = site_url('hs_reg/hostel/vacant_hostel');
        $menu['hos_stu_admin']['Hostel Booking']['Vacant Temp Hostel'] = site_url('hs_reg/hostel/vacant_temp_hostel');
        $menu['hos_stu_admin']['Hostel Booking']['Manual Room Allotment'] = site_url('hs_reg/hostel/manual_room_allotment');
        $menu['hos_stu_admin']['Report']['Show Students'] = site_url('hs_reg/hostel/filter_assigned_student');
        $menu['hos_stu_admin']['Report']['Show Rooms'] = site_url('hs_reg/hostel/show_rooms_hostel');
        $menu['hos_stu_admin']['Report']['History of Student'] = site_url('hs_reg/hostel/history_student');
        $menu['hos_stu_admin']['Report']['Download Student Details'] = site_url('hs_reg/hostel/download_student_contact');
        //$menu['hos_stu_admin']['Hostel Booking']['Hostel No Dues Inventory List'] = site_url('hs_reg/hostel_no_dues_inventory_list');
        // $menu['hos_stu_admin']['Hostel No Dues']['Manage Inventory List'] = site_url('hs_reg/hostel_no_dues_inventory_list');
        // $menu['hos_stu_admin']['Hostel No Dues']['Assign Individual No Dues'] = site_url('hs_reg/assign_individual_no_dues');
        // //$menu['hos_stu_admin']['Hostel No Dues']['Bulk Upload No Dues'] = site_url('hs_reg/bulk_upload_no_dues');
        // //$menu['hos_stu_admin']['Hostel No Dues']['Bulk Assign Hostel No Dues'] = site_url('hs_reg/bulk_upload_no_dues/assign_bulk_upload_no_dues');
        // //$menu['hos_stu_admin']['Hostel No Dues']['Bulk Upload No Dues'] = array();
        // $menu['hos_stu_admin']['Hostel No Dues']['Bulk Upload No Dues']['Download Sample CSV'] = site_url('hs_reg/bulk_upload_no_dues/sample_csv_download');
        // $menu['hos_stu_admin']['Hostel No Dues']['Bulk Upload No Dues']['Upload Sample CSV'] = site_url('hs_reg/bulk_upload_no_dues/sample_csv_upload');
        // $menu['hos_stu_admin']['Hostel No Dues']['Edit/Delete Hostel No Dues'] = site_url('hs_reg/bulk_upload_no_dues/edit_delete_assigned_hs_no_dues');
        // $menu['hos_stu_admin']['Hostel No Dues']['View Hostel No Dues List'] = site_url('hs_reg/assign_individual_no_dues/view_hs_no_dues_list');
        // $menu['hos_stu_admin']['Hostel No Dues']['View Comprehensive No Dues List'] = site_url('hs_reg/hostel/comprehensive_no_dues_list');
        // $menu['hos_stu_admin']['Hostel No Dues']['Report']['General'] = site_url('hs_reg/hostel_no_dues_report/general_report');
        // $menu['hos_stu_admin']['Hostel No Dues']['Report']['Student-wise'] = site_url('hs_reg/hostel_no_dues_report/student_wise_report');
        
        
        //$menu['hos_stu_admin']['Report']['History of Student'] = site_url('hs_reg/hostel/history_student');
        //$menu['hos_stu_admin']['Report']['Download Student Details'] = site_url('hs_reg/hostel/download_student_contact');

        //$menu['hos_stu_admin']['Report']['Vacant Hostel'] = site_url('hs_reg/hostel/vacant_hostel');
        //$menu['hos_stu_admin']['Report']['Vacant Temp Hostel'] = site_url('hs_reg/hostel/vacant_temp_hostel');
        //$menu['hos_stu_admin']['Report']['Manual Room Allotment'] = site_url('hs_reg/hostel/manual_room_allotment');
   
        $menu['hos_guest_admin'] = array();
        $menu['hos_guest_admin']['Hostel Booking']['Manage guest contact'] = site_url('hs_reg/hostel/manage_guest_contact');
        $menu['hos_guest_admin']['Hostel Booking']['Show Guest Room Status'] = site_url('hs_reg/hostel/show_guest_hostel_room');
        $menu['hos_guest_admin']['Hostel Booking']['Search Assigned Guest'] = site_url('hs_reg/hostel/search_guest');
        //$menu['hos_guest_admin']['Report']['Show Assigned Guest'] = site_url('hs_reg/hostel/filter_assigned_guest');
		$menu['hos_guest_admin']['Report']['Show Assigned Guest'] = site_url('hs_reg/hostel/search_guest');

        $menu['hostel_cwd'] = array();
        $menu['hostel_cwd']['Hostel Booking'] = array();
        $menu['hostel_cwd']['Hostel Booking']['Manage Hostel'] = site_url('hs_reg/hostel/manage_hostel');
        $menu['hostel_cwd']['Hostel Booking']['Block/Unblock Room'] = site_url('hs_reg/hostel/block_unblock_room');
        $menu['hostel_cwd']['Hostel Booking']['Manage student contact'] = site_url('hs_reg/hostel/manage_contact_student');
        $menu['hostel_cwd']['Hostel Booking']['Bulk upload student contact'] = site_url('hs_reg/hostel/bulk_upload_contact_student');
        $menu['hostel_cwd']['Hostel Booking']['Search Assigned Student'] = site_url('hs_reg/hostel/search_student_allot');
        $menu['hostel_cwd']['Hostel Booking']['Show Room Status'] = site_url('hs_reg/hostel/show_hostel_room');
        $menu['hostel_cwd']['Hostel Booking']['Vacant Hostel'] = site_url('hs_reg/hostel/vacant_hostel');
        $menu['hostel_cwd']['Hostel Booking']['Manual Room Allotment'] = site_url('hs_reg/hostel/manual_room_allotment');
        $menu['hostel_cwd']['Report']['Show Students'] = site_url('hs_reg/hostel/filter_assigned_student');
        $menu['hostel_cwd']['Report']['Show Rooms'] = site_url('hs_reg/hostel/show_rooms_hostel');
		$menu['hostel_cwd']['Report']['History of Student'] = site_url('hs_reg/hostel/history_student');
		$menu['hostel_cwd']['Report']['Download Student Details'] = site_url('hs_reg/hostel/download_student_contact');
                
        //$menu['hostel_cwd']['Hostel No Dues']['Manage Inventory List'] = site_url('hs_reg/hostel_no_dues_inventory_list');
        $menu['hostel_cwd']['Hostel Dues']['Assign Dues']['Individual'] = site_url('hs_reg/assign_individual_no_dues');
        //$menu['hostel_cwd']['Hostel No Dues']['Bulk Upload No Dues'] = site_url('hs_reg/bulk_upload_no_dues');
        $menu['hostel_cwd']['Hostel Dues']['Assign Dues']['Bulk']['Download Sample CSV'] = site_url('hs_reg/bulk_upload_no_dues/sample_csv_download');
        $menu['hostel_cwd']['Hostel Dues']['Assign Dues']['Bulk']['Upload Sample CSV'] = site_url('hs_reg/bulk_upload_no_dues/sample_csv_upload');
        $menu['hostel_cwd']['Hostel Dues']['Manage Vacanted Hostel Rooms'] = site_url('hs_reg/assign_individual_no_dues/manage_vacanted_hostel_rooms');
        //$menu['hostel_cwd']['Hostel No Dues']['Bulk Assign Hostel No Dues'] = site_url('hs_reg/bulk_upload_no_dues/assign_bulk_upload_no_dues');
        $menu['hostel_cwd']['Hostel Dues']['Assign Dues']['Approve/Reject Dues'] = site_url('hs_reg/assign_individual_no_dues/view_hs_no_dues_list');
        $menu['hostel_cwd']['Hostel Dues']['Edit/Delete Dues'] = site_url('hs_reg/bulk_upload_no_dues/edit_delete_assigned_hs_no_dues');
        
        //$menu['hostel_cwd']['Hostel No Dues']['View Comprehensive No Dues List'] = site_url('hs_reg/hostel/comprehensive_no_dues_list');
        //$menu['hostel_cwd']['Report']['Vacant Hostel'] = site_url('hs_reg/hostel/vacant_hostel');
        $menu['hostel_cwd']['Hostel Dues']['Report']['General'] = site_url('hs_reg/hostel_no_dues_report/general_report');
        $menu['hostel_cwd']['Hostel Dues']['Report']['Student-wise'] = site_url('hs_reg/hostel_no_dues_report/student_wise_report');

        $menu['hostel_wd'] = array();
        $menu['hostel_wd']['Hostel Booking'] = array();
        $menu['hostel_wd']['Hostel Booking']['Manage Hostel'] = site_url('hs_reg/hostel/manage_hostel');
        $menu['hostel_wd']['Hostel Booking']['Block/Unblock Room'] = site_url('hs_reg/hostel/block_unblock_room');
        $menu['hostel_wd']['Hostel Booking']['Manage student contact'] = site_url('hs_reg/hostel/manage_contact_student');
        $menu['hostel_wd']['Hostel Booking']['Bulk upload student contact'] = site_url('hs_reg/hostel/bulk_upload_contact_student');
        $menu['hostel_wd']['Hostel Booking']['Search Assigned Student'] = site_url('hs_reg/hostel/search_student_allot');
        $menu['hostel_wd']['Hostel Booking']['Show Room Status'] = site_url('hs_reg/hostel/show_hostel_room');
        $menu['hostel_wd']['Hostel Booking']['Vacant Hostel'] = site_url('hs_reg/hostel/vacant_hostel');
        $menu['hostel_wd']['Hostel Booking']['Manual Room Allotment'] = site_url('hs_reg/hostel/manual_room_allotment');
        $menu['hostel_wd']['Report']['Show Students'] = site_url('hs_reg/hostel/filter_assigned_student');
		$menu['hostel_wd']['Report']['Show Rooms'] = site_url('hs_reg/hostel/show_rooms_hostel');
		$menu['hostel_wd']['Report']['History of Student'] = site_url('hs_reg/hostel/history_student');
		$menu['hostel_wd']['Report']['Download Student Details'] = site_url('hs_reg/hostel/download_student_contact');
        //$menu['hostel_wd']['Report']['Vacant Hostel'] = site_url('hs_reg/hostel/vacant_hostel');
        //$menu['hostel_wd']['Report']['Manual Room Allotment'] = site_url('hs_reg/hostel/manual_room_allotment');
       //$menu['hostel_cwd']['Hostel No Dues']['Manage Inventory List'] = site_url('hs_reg/hostel_no_dues_inventory_list');
       $menu['hostel_wd']['Hostel Dues']['Assign Dues']['Individual'] = site_url('hs_reg/assign_individual_no_dues');
       //$menu['hostel_cwd']['Hostel No Dues']['Bulk Upload No Dues'] = site_url('hs_reg/bulk_upload_no_dues');
       $menu['hostel_wd']['Hostel Dues']['Assign Dues']['Bulk']['Download Sample CSV'] = site_url('hs_reg/bulk_upload_no_dues/sample_csv_download');
       $menu['hostel_wd']['Hostel Dues']['Assign Dues']['Bulk']['Upload Sample CSV'] = site_url('hs_reg/bulk_upload_no_dues/sample_csv_upload');
       //$menu['hostel_wd']['Hostel Dues']['Assign Dues']['Bulk']['Upload Sample CSV'] = site_url('hs_reg/bulk_upload_no_dues/sample_csv_upload');
       $menu['hostel_wd']['Hostel Dues']['Manage Vacanted Hostel Rooms'] = site_url('hs_reg/assign_individual_no_dues/manage_vacanted_hostel_rooms');
       //$menu['hostel_cwd']['Hostel No Dues']['Bulk Assign Hostel No Dues'] = site_url('hs_reg/bulk_upload_no_dues/assign_bulk_upload_no_dues');
       $menu['hostel_wd']['Hostel Dues']['Assign Dues']['Approve/Reject Dues'] = site_url('hs_reg/assign_individual_no_dues/view_hs_no_dues_list');
       $menu['hostel_wd']['Hostel Dues']['Edit/Delete Dues'] = site_url('hs_reg/bulk_upload_no_dues/edit_delete_assigned_hs_no_dues');
       
       //$menu['hostel_cwd']['Hostel No Dues']['View Comprehensive No Dues List'] = site_url('hs_reg/hostel/comprehensive_no_dues_list');
       //$menu['hostel_cwd']['Report']['Vacant Hostel'] = site_url('hs_reg/hostel/vacant_hostel');
       $menu['hostel_wd']['Hostel Dues']['Report']['General'] = site_url('hs_reg/hostel_no_dues_report/general_report');
       $menu['hostel_wd']['Hostel Dues']['Report']['Student-wise'] = site_url('hs_reg/hostel_no_dues_report/student_wise_report');
        //$menu['hostel_wd']['Hostel No Dues']['Bulk Assign Hostel No Dues'] = site_url('hs_reg/bulk_upload_no_dues/assign_bulk_upload_no_dues');
        
       // $menu['hostel_wd']['Hostel No Dues']['View Comprehensive No Dues List'] = site_url('hs_reg/hostel/comprehensive_no_dues_list');
                
                

        $menu['hostel_wd_asst'] = array();
        $menu['hostel_wd_asst']['Hostel Booking'] = array();
        $menu['hostel_wd_asst']['Hostel Booking']['Manage Hostel'] = site_url('hs_reg/hostel/manage_hostel');
        $menu['hostel_wd_asst']['Hostel Booking']['Manage student contact'] = site_url('hs_reg/hostel/manage_contact_student');
        $menu['hostel_wd_asst']['Hostel Booking']['Bulk upload student contact'] = site_url('hs_reg/hostel/bulk_upload_contact_student');
        $menu['hostel_wd_asst']['Hostel Booking']['Search Assigned Student'] = site_url('hs_reg/hostel/search_student_allot');
        $menu['hostel_wd_asst']['Hostel Booking']['Show Room Status'] = site_url('hs_reg/hostel/show_hostel_room');
        $menu['hostel_wd_asst']['Report']['Show Students'] = site_url('hs_reg/hostel/filter_assigned_student');
		$menu['hostel_wd_asst']['Report']['Show Rooms'] = site_url('hs_reg/hostel/show_rooms_hostel');
		$menu['hostel_wd_asst']['Report']['History of Student'] = site_url('hs_reg/hostel/history_student');
        //$menu['hostel_cwd']['Hostel No Dues']['Manage Inventory List'] = site_url('hs_reg/hostel_no_dues_inventory_list');
        $menu['hostel_wd_asst']['Hostel Dues']['Assign Dues']['Individual'] = site_url('hs_reg/assign_individual_no_dues');
        //$menu['hostel_cwd']['Hostel No Dues']['Bulk Upload No Dues'] = site_url('hs_reg/bulk_upload_no_dues');
        $menu['hostel_wd_asst']['Hostel Dues']['Assign Dues']['Bulk']['Download Sample CSV'] = site_url('hs_reg/bulk_upload_no_dues/sample_csv_download');
        $menu['hostel_wd_asst']['Hostel Dues']['Assign Dues']['Bulk']['Upload Sample CSV'] = site_url('hs_reg/bulk_upload_no_dues/sample_csv_upload');
        //$menu['hostel_wd_asst']['Hostel Dues']['Assign Dues']['Bulk']['Upload Sample CSV'] = site_url('hs_reg/bulk_upload_no_dues/sample_csv_upload');
        $menu['hostel_wd_asst']['Hostel Dues']['Manage Vacanted Hostel Rooms'] = site_url('hs_reg/assign_individual_no_dues/manage_vacanted_hostel_rooms');
        //$menu['hostel_cwd']['Hostel No Dues']['Bulk Assign Hostel No Dues'] = site_url('hs_reg/bulk_upload_no_dues/assign_bulk_upload_no_dues');
        $menu['hostel_wd_asst']['Hostel Dues']['Assign Dues']['Approve/Reject Dues'] = site_url('hs_reg/assign_individual_no_dues/view_hs_no_dues_list');
        $menu['hostel_wd_asst']['Hostel Dues']['Edit/Delete Dues'] = site_url('hs_reg/bulk_upload_no_dues/edit_delete_assigned_hs_no_dues');
        
        //$menu['hostel_cwd']['Hostel No Dues']['View Comprehensive No Dues List'] = site_url('hs_reg/hostel/comprehensive_no_dues_list');
        //$menu['hostel_cwd']['Report']['Vacant Hostel'] = site_url('hs_reg/hostel/vacant_hostel');
        $menu['hostel_wd_asst']['Hostel Dues']['Report']['General'] = site_url('hs_reg/hostel_no_dues_report/general_report');
        $menu['hostel_wd_asst']['Hostel Dues']['Report']['Student-wise'] = site_url('hs_reg/hostel_no_dues_report/student_wise_report');
        //$menu['hostel_wd_asst']['Hostel No Dues']['Bulk Assign Hostel No Dues'] = site_url('hs_reg/bulk_upload_no_dues/assign_bulk_upload_no_dues');
        
        //$menu['hostel_wd_asst']['Hostel No Dues']['View Comprehensive No Dues List'] = site_url('hs_reg/hostel/comprehensive_no_dues_list');


        $menu['stu'] = array();
        $menu['stu']['Hostel Booking'] = array();
        $menu['stu']['Hostel Booking']['Received OTP'] = site_url('hs_reg/hostel_booking/received_otp');
        $menu['stu']['Hostel Booking']['Choose Room Partner'] = site_url('hs_reg/hostel_booking/choose_room_partner');
        $menu['stu']['Hostel Booking']['Enter OTP'] = site_url('hs_reg/hostel_booking/enter_otp');
        $menu['stu']['Hostel Booking']['Room booking'] = site_url('hs_reg/hostel_booking/book_room');
        $menu['stu']['Hostel Booking']['View Booked Room'] = site_url('hs_reg/hostel_booking/completed_room_booking');
        $menu['stu']['Hostel Booking']['Sent Request'] = site_url('hs_reg/hostel_booking/success_otp');
        $menu['stu']['Hostel Booking']['View Hostel No Dues List'] = site_url('hs_reg/assign_individual_no_dues/view_hs_no_dues_list');


        $menu['dsw'] = array();
        $menu['dsw']['No Dues Admin'] = array();
        $menu['dsw']['No Dues Admin']['Open/Close Portal (Specific Student)']['Start No Dues'] = site_url('hs_reg/hostel_admin_portal/specific_student_start');
        $menu['dsw']['No Dues Admin']['Open/Close Portal (Specific Student)']['Stop No Dues']['Admin'] = site_url('hs_reg/hostel_admin_portal/specific_student_stop_admin');
        $menu['dsw']['No Dues Admin']['Open/Close Portal (Specific Student)']['Stop No Dues']['Student'] = site_url('hs_reg/hostel_admin_portal/specific_student_stop_student');
        $menu['dsw']['No Dues Admin']['Open/Close Portal (Specific Student)']['Edit No Dues']['Admin'] = site_url('hs_reg/hostel_admin_portal/specific_student_stop');
        $menu['dsw']['No Dues Admin']['Open/Close Portal (Specific Student)']['Edit No Dues']['Student'] = site_url('hs_reg/hostel_admin_portal/specific_student_stop');
        // $menu['adhm']['Hostel No Dues Admin']['Open / Close Portal']['Specific Student'] = site_url('hs_reg/hostel_admin_portal/specific_student');
        // $menu['adhm']['Hostel No Dues Admin']['Open / Close Portal']['Specific Student'] = site_url('hs_reg/hostel_admin_portal/specific_student');
        // $menu['adhm']['Hostel No Dues Admin']['Open / Close Portal']['Specific Student'] = site_url('hs_reg/hostel_admin_portal/specific_student');
        $menu['dsw']['No Dues Admin']['Open/Close Portal (All Student)']['Start No Dues']['Start for admin'] = site_url('no_dues/no_dues_admin_edit/specific_student_edit_admin');
        $menu['dsw']['No Dues Admin']['Open/Close Portal (All Student)']['Start No Dues']['Start for student'] = site_url('no_dues/no_dues_admin_edit/specific_student_edit_student');
        $menu['dsw']['No Dues Admin']['Open/Close Portal (All Student)']['Stop No Dues']['Stop for admin'] = site_url('no_dues/no_dues_admin_edit/stop_dues_admin');
        $menu['dsw']['No Dues Admin']['Open/Close Portal (All Student)']['Stop No Dues']['Stop for student'] = site_url('no_dues/no_dues_admin_edit/stop_dues_student');
        $menu['dsw']['No Dues Admin']['Open/Close Portal (All Student)']['Edit No Dues Time']['Edit for admin'] = site_url('no_dues/no_dues_admin_edit/edit_no_dues_start_admin');
        $menu['dsw']['No Dues Admin']['Open/Close Portal (All Student)']['Edit No Dues Time']['Edit for student'] = site_url('no_dues/no_dues_admin_edit/edit_no_dues_start_student');
        $menu['dsw']['No Dues Admin']['Manage Due Types'] = site_url('no_dues/no_dues_manage_due_type');
        $menu['dsw']['No Dues Admin']['Dues Clearance'] = site_url('no_dues/dues_clearance_by_no_dues_admin');
        $menu['dsw']['No Dues Admin']['Manage Inventory'] = site_url('hs_reg/hostel_no_dues_inventory_list');
        $menu['dsw']['No Dues Admin']['Report']['General'] = site_url('hs_reg/hostel_admin_portal/general_report');
        $menu['dsw']['No Dues Admin']['Report']['Hostel-wise'] = site_url('hs_reg/hostel_admin_portal/hostel_wise_report');
        $menu['dsw']['No Dues Admin']['Report']['Student-wise'] = site_url('hs_reg/hostel_admin_portal/student_wise_report');

        $menu['adsw'] = array();
        $menu['adsw']['No Dues Admin'] = array();
        $menu['adsw']['No Dues Admin']['Open/Close Portal (Specific Student)']['Start No Dues'] = site_url('hs_reg/hostel_admin_portal/specific_student_start');
        $menu['adsw']['No Dues Admin']['Open/Close Portal (Specific Student)']['Stop No Dues']['Admin'] = site_url('hs_reg/hostel_admin_portal/specific_student_stop_admin');
        $menu['adsw']['No Dues Admin']['Open/Close Portal (Specific Student)']['Stop No Dues']['Student'] = site_url('hs_reg/hostel_admin_portal/specific_student_stop_student');
        $menu['adsw']['No Dues Admin']['Open/Close Portal (Specific Student)']['Edit No Dues']['Admin'] = site_url('hs_reg/hostel_admin_portal/specific_student_stop');
        $menu['adsw']['No Dues Admin']['Open/Close Portal (Specific Student)']['Edit No Dues']['Student'] = site_url('hs_reg/hostel_admin_portal/specific_student_stop');
        // $menu['adhm']['Hostel No Dues Admin']['Open / Close Portal']['Specific Student'] = site_url('hs_reg/hostel_admin_portal/specific_student');
        // $menu['adhm']['Hostel No Dues Admin']['Open / Close Portal']['Specific Student'] = site_url('hs_reg/hostel_admin_portal/specific_student');
        // $menu['adhm']['Hostel No Dues Admin']['Open / Close Portal']['Specific Student'] = site_url('hs_reg/hostel_admin_portal/specific_student');
        $menu['adsw']['No Dues Admin']['Open/Close Portal (All Student)']['Start No Dues']['Start for admin'] = site_url('no_dues/no_dues_admin_edit/specific_student_edit_admin');
        $menu['adsw']['No Dues Admin']['Open/Close Portal (All Student)']['Start No Dues']['Start for student'] = site_url('no_dues/no_dues_admin_edit/specific_student_edit_student');
        $menu['adsw']['No Dues Admin']['Open/Close Portal (All Student)']['Stop No Dues']['Stop for admin'] = site_url('no_dues/no_dues_admin_edit/stop_dues_admin');
        $menu['adsw']['No Dues Admin']['Open/Close Portal (All Student)']['Stop No Dues']['Stop for student'] = site_url('no_dues/no_dues_admin_edit/stop_dues_student');
        $menu['adsw']['No Dues Admin']['Open/Close Portal (All Student)']['Edit No Dues Time']['Edit for admin'] = site_url('no_dues/no_dues_admin_edit/edit_no_dues_start_admin');
        $menu['adsw']['No Dues Admin']['Open/Close Portal (All Student)']['Edit No Dues Time']['Edit for student'] = site_url('no_dues/no_dues_admin_edit/edit_no_dues_start_student');
        $menu['adsw']['No Dues Admin']['Manage Due Types'] = site_url('no_dues/no_dues_manage_due_type');
        $menu['adsw']['No Dues Admin']['Dues Clearance'] = site_url('no_dues/dues_clearance_by_no_dues_admin');
        $menu['adsw']['No Dues Admin']['Manage Inventory'] = site_url('hs_reg/hostel_no_dues_inventory_list');
        $menu['adsw']['No Dues Admin']['Report']['General'] = site_url('hs_reg/hostel_admin_portal/general_report');
        $menu['adsw']['No Dues Admin']['Report']['Hostel-wise'] = site_url('hs_reg/hostel_admin_portal/hostel_wise_report');
        $menu['adsw']['No Dues Admin']['Report']['Student-wise'] = site_url('hs_reg/hostel_admin_portal/student_wise_report');

        $menu['adhm'] = array();
        $menu['adhm']['No Dues Admin'] = array();
        $menu['adhm']['No Dues Admin']['Open/Close Portal (Specific Student)']['Start No Dues'] = site_url('hs_reg/hostel_admin_portal/specific_student_start');
        $menu['adhm']['No Dues Admin']['Open/Close Portal (Specific Student)']['Stop No Dues']['Admin'] = site_url('hs_reg/hostel_admin_portal/specific_student_stop_admin');
        $menu['adhm']['No Dues Admin']['Open/Close Portal (Specific Student)']['Stop No Dues']['Student'] = site_url('hs_reg/hostel_admin_portal/specific_student_stop_student');
        $menu['adhm']['No Dues Admin']['Open/Close Portal (Specific Student)']['Edit No Dues']['Admin'] = site_url('hs_reg/hostel_admin_portal/specific_student_stop');
        $menu['adhm']['No Dues Admin']['Open/Close Portal (Specific Student)']['Edit No Dues']['Student'] = site_url('hs_reg/hostel_admin_portal/specific_student_stop');
        // $menu['adhm']['Hostel No Dues Admin']['Open / Close Portal']['Specific Student'] = site_url('hs_reg/hostel_admin_portal/specific_student');
        // $menu['adhm']['Hostel No Dues Admin']['Open / Close Portal']['Specific Student'] = site_url('hs_reg/hostel_admin_portal/specific_student');
        // $menu['adhm']['Hostel No Dues Admin']['Open / Close Portal']['Specific Student'] = site_url('hs_reg/hostel_admin_portal/specific_student');
        $menu['adhm']['No Dues Admin']['Open/Close Portal (All Student)']['Start No Dues']['Start for admin'] = site_url('no_dues/no_dues_admin_edit/specific_student_edit_admin');
        $menu['adhm']['No Dues Admin']['Open/Close Portal (All Student)']['Start No Dues']['Start for student'] = site_url('no_dues/no_dues_admin_edit/specific_student_edit_student');
        $menu['adhm']['No Dues Admin']['Open/Close Portal (All Student)']['Stop No Dues']['Stop for admin'] = site_url('no_dues/no_dues_admin_edit/stop_dues_admin');
        $menu['adhm']['No Dues Admin']['Open/Close Portal (All Student)']['Stop No Dues']['Stop for student'] = site_url('no_dues/no_dues_admin_edit/stop_dues_student');
        $menu['adhm']['No Dues Admin']['Open/Close Portal (All Student)']['Edit No Dues Time']['Edit for admin'] = site_url('no_dues/no_dues_admin_edit/edit_no_dues_start_admin');
        $menu['adhm']['No Dues Admin']['Open/Close Portal (All Student)']['Edit No Dues Time']['Edit for student'] = site_url('no_dues/no_dues_admin_edit/edit_no_dues_start_student');
        $menu['adhm']['No Dues Admin']['Manage Due Types'] = site_url('no_dues/no_dues_manage_due_type');
        $menu['adhm']['No Dues Admin']['Dues Clearance'] = site_url('no_dues/dues_clearance_by_no_dues_admin');
        $menu['adhm']['No Dues Admin']['Manage Inventory'] = site_url('hs_reg/hostel_no_dues_inventory_list');
        $menu['adhm']['No Dues Admin']['Report']['General'] = site_url('hs_reg/hostel_admin_portal/general_report');
        $menu['adhm']['No Dues Admin']['Report']['Hostel-wise'] = site_url('hs_reg/hostel_admin_portal/hostel_wise_report');
        $menu['adhm']['No Dues Admin']['Report']['Student-wise'] = site_url('hs_reg/hostel_admin_portal/student_wise_report');


        $menu['ndva'] = array();
        $menu['ndva']['No Dues View Admin'] = array();
        $menu['ndva']['No Dues View Admin'] = site_url('hs_reg/hostel_admin_portal/general_report');





        return $menu;
    }

}

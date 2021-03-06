<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
pce_da5_action ($app_num)
room_plans ($building, $check_in, $check_out)
insert_edc_allotment ($app_num)
*/

class Room_allotment extends MY_Controller
{
	function __construct() {
		parent::__construct(array('pce_da5', 'pce'));
		date_default_timezone_set('Asia/Kolkata');
		$this->addJS("edc_booking/booking.js");

		$this->load->model ('edc_booking/edc_allotment_model');
		$this->load->model ('edc_booking/edc_booking_model');
		$this->load->model ('user_model');
		$this->initialize_buildings();
	}

	function initialize_buildings() {
		$initializer_id = array('old' => 0,
								'extension' => 1
		);
		$this->initialize_building('old', $initializer_id);
		$this->initialize_building('extension', $initializer_id);
	}

	function initialize_building($building = '', $initializer_id) {
		if($this->edc_allotment_model->no_of_rooms($building) === 0) {
			$this->edc_allotment_model->initialize_building($building, $initializer_id[$building]);
		}
	}

	function auth_is($auth) {
		foreach($this->session->userdata('auth') as $a){
			if($a == $auth)
				return;
		}
		$this->session->set_flashdata('flashWarning', 'You do not have access to that page!');
		redirect('home');
	}

	function pce_da5_action($app_num) {
		$this->auth_is('pce_da5');
		$today=date("Y-m-d H:i:s");
		$data = $this->edc_booking_model->registration_details($app_num);
		if($data['hod_status'] === 'Cancel' ||
			$data['hod_status'] === 'Cancelled' ||
			$data['dsw_status'] === 'Cancel' ||
			$data['dsw_status'] === 'Cancelled' ||
			$data['pce_status'] === 'Cancelled') {
				$this->session->set_flashdata('flashError','Cannot allot room! Applicant has cancelled booking request.');
				redirect('edc_booking/booking_request/pce_da5_app_list');
			}
		/*else if($data['check_in']<$today)
		{
			$this->edc_booking_model->cancel_request($app_num);
			$reason = "Application expired before verification process was completed .";
			$this->edc_booking_model->set_cancel_reason($app_num,$reason);
			$this->session->set_flashdata('flashError','The application has expired . ');
			redirect('sah_booking/booking_request/est_da4_app_list');
		}*/
		$this->drawHeader ("Room Allotment");
		$this->load->view('edc_booking/edc_allotment_view',$data);
		$this->drawFooter();
	}

	function room_plans($building, $check_in = '', $check_out = '')	{
		$result_floor_wise=array();
		$building="old";
		if($this->edc_allotment_model->no_of_rooms($building) === 1) {
			$this->load->view('edc_booking/no_room_data.php');
		}
		else {
			$result_uavail_rooms = $this->edc_allotment_model->check_unavail($check_in, $check_out);
			//print_r($result_uavail_rooms);
			$floor_array = $this->edc_allotment_model->floors($building);

			$flr = 1;
			foreach($floor_array as $floor) {
				$temp_query = $this->edc_allotment_model->rooms($building,$floor['floor']);
				$result_floor_wise[$flr][0] = $temp_query;
				$result_floor_wise[$flr++][1] = $floor['floor'];
			}

			$data_array = array();
			$i = 0;
			foreach($result_floor_wise as $floor) {
				$sno=1;
				$data_array[$i][0] = $floor[1];
				foreach($floor[0] as $row) {
					$flag=0;
					foreach($result_uavail_rooms as $room_unavailable) {
						if($row['id']==$room_unavailable['room_id'])
							$flag = 1;
					}
					$data_array[$i][$sno][0] = $row['id'];
					$data_array[$i][$sno][1] = $row['room_no'];
					$data_array[$i][$sno][2] = $row['room_type'];
					if($flag==0) {
						$data_array[$i][$sno][3] = 1;
					}
					else {
						$data_array[$i][$sno][3] = 0;
					}
					$data_array[$i][$sno][4] = $row['blocked'];
					$data_array[$i][$sno++][5] = $row['remark'];
				}
				$i++;
			}
			$data['floor_room_array'] = $data_array;
			$data['room_array'] = $this->edc_allotment_model->room_types();
			$data['building'] = $building;
			$this->load->view('edc_booking/edc_rooms',$data);
		$building="extension";
	if($this->edc_allotment_model->no_of_rooms($building) === 1) {
			$this->load->view('edc_booking/no_room_data.php');
		}
		else {
			$result_uavail_rooms = $this->edc_allotment_model->check_unavail($check_in, $check_out);
			
			$floor_array = $this->edc_allotment_model->floors($building);

			$flr = 1;
			foreach($floor_array as $floor) {
				$temp_query = $this->edc_allotment_model->rooms($building,$floor['floor']);
				$result_floor_wise[$flr][0] = $temp_query;
				$result_floor_wise[$flr++][1] = $floor['floor'];
			}

			$data_array = array();
			$i = 0;
			foreach($result_floor_wise as $floor) {
				$sno=1;
				$data_array[$i][0] = $floor[1];
				foreach($floor[0] as $row) {
					$flag=0;
					foreach($result_uavail_rooms as $room_unavailable) {
						if($row['id']==$room_unavailable['room_id'])
							$flag = 1;
					}
					$data_array[$i][$sno][0] = $row['id'];
					$data_array[$i][$sno][1] = $row['room_no'];
					$data_array[$i][$sno][2] = $row['room_type'];
					if($flag==0) {
						$data_array[$i][$sno][3] = 1;
					}
					else {
						$data_array[$i][$sno][3] = 0;
					}
					$data_array[$i][$sno][4] = $row['blocked'];
					$data_array[$i][$sno++][5] = $row['remark'];
				}
				$i++;
			}
			$data['floor_room_array'] = $data_array;
			$data['room_array'] = $this->edc_allotment_model->room_types();
			$data['building'] = $building;
			$this->load->view('edc_booking/edc_rooms',$data);
		}
	}
}

	function insert_edc_allotment($app_num) {
		$this->auth_is('pce_da5');

		$b_detail = $this->edc_booking_model->registration_details($app_num);
		if($b_detail['ctk_allotment_status'] === 'Approved') {
			$this->session->set_flashdata('flashError','Invalid attempt to allot room. Room Allotment has already been done.');
			redirect('edc_booking/booking_request/pce_da5_app_list');
		}
		else if($b_detail['hod_status'] === 'Cancel' ||
			$b_detail['hod_status'] === 'Cancelled' ||
			$b_detail['dsw_status'] === 'Cancel' ||
			$b_detail['dsw_status'] === 'Cancelled' ||
			$b_detail['pce_status'] === 'Cancelled') {
				$this->session->set_flashdata('flashError','Cannot allot room! Applicant has cancelled booking request.');
				redirect('edc_booking/booking_request/pce_da5_app_list');
		}

		$double_bedded_ac = $this->input->post('checkbox_double_bedded_ac');
		$ac_suite = $this->input->post('checkbox_ac_suite');

		if(gettype($double_bedded_ac) == 'array' && gettype($ac_suite) == 'array')
			$room_list = array_merge($double_bedded_ac, $ac_suite);
		else if(gettype($double_bedded_ac) == 'array')
			$room_list = $double_bedded_ac;
		else $room_list = $ac_suite;

		$this->edc_allotment_model->set_ctk_status("Approved", $app_num);

		foreach($room_list as $room) {
			$input_data = array(
				'app_num' => $app_num,
				'room_id'	=> $room,
			);
			$this->edc_allotment_model->insert_booking_details ($input_data);
		}
		$res = $this->user_model->getUsersByDeptAuth('all', 'pce');
		$pce = '';
		foreach ($res as $row) {
			$pce = $row->id;
			$this->notification->notify ($pce, "pce", "Approve/Reject Pending Request", "EDC Room Booking Request (Application No. : ".$app_num." ) is Pending for your approval.", "edc_booking/booking_request/details/".$app_num."/pce", "");
		}

		if ($this->input->post("submit") == "Rooms Unavailable") 
			$this->session->set_flashdata('flashSuccess', 'Rooms were not allotted due to unavailability');
		else 
			$this->session->set_flashdata('flashSuccess','Room Allotment has been done successfully.');
		redirect('edc_booking/booking_request/pce_da5_app_list');
	}
}

<?php

	$ui = new UI();

	$row = $ui->row()->open();

	$col1 = $ui->col()
				 ->width(1)
	             ->open();
	$col1->close();

	$col2 = $ui->col()
				 ->width(10)
	             ->open();

	$box = $ui->box()
			 ->uiType('primary')
			 ->title('EDC Room Allotment Form')
			 ->solid()
			 ->open();

		$form = $ui->form()
		   ->multipart()
		   ->action('edc_booking/booking/insert_other_booking_details')
		   ->open();

			echo '<input type="hidden" name="purpose" value="official"></input>';

		$ui->textarea()->label('Purpose of Visit')->name('purpose_of_visit')->placeholder("Enter the purpose of visit")->required()->show();

		$check_in_row = $ui->row()->open();
		$ui->datePicker()
			 ->width(6)
			 ->label ('Check-In-Date')
			 ->id('checkin')	
			 ->name('checkin')
		   	 ->placeholder("yyyy-mm-dd")
			 ->addonLeft($ui->icon("calendar"))
			 ->dateFormat('yyyy-mm-dd')
			 ->required()
			 ->show();

		$ui->timePicker()->width(6)->label("Check-In-Time")->name('checkin_time')->addonLeft($ui->icon("clock-o"))->show();
		$check_in_row->close();

		$check_out_row = $ui->row()->open();
		$ui->datePicker()
			 ->width(6)
			 ->label ('Check-Out-Date')
			 ->id('checkout')
			 ->name('checkout')
		   	 ->placeholder("yyyy-mm-dd")
			 ->addonLeft($ui->icon("calendar"))
			 ->dateFormat('yyyy-mm-dd')
			 ->required()
			 ->show();

		$ui->timePicker()->width(6)->label("Check-Out-Time")->name('checkout_time')->addonLeft($ui->icon("clock-o"))->show();
		$check_out_row->close();

		$no_of_guests_row = $ui->row()->open();
		echo '<input type="hidden" name="no_of_guests" ></input>';
			
			$radio_col = $ui->col()->width(3)->id('school_guest_row')->open();
				echo '<label for="school_guest_radio">Whether School Guest</label>
				<div id = "school_guest_radio">';
					$yes_col = $ui->col()->width(1)->open(); 
					echo '<input type="radio" name="school_guest" value="1">Yes';
					$yes_col->close();
					$dump_col = $ui->col()->width(1)->open()->close();
					$no_col = $ui->col()->width(1)->open();
					echo '<input type="radio" name="school_guest" value="0" checked="checked">No';
					$no_col->close();
				echo '</div>';
			$radio_col->close();

			$radio_col = $ui->col()->width(3)->open();
				echo '<label for="boarding_required_radio">Boarding Required</label>
				<div id = "boarding_required_radio">';
					$yes_col = $ui->col()->width(1)->open(); 
					echo '<input type="radio" name="boarding_required" value="1" checked="checked">Yes';
					$yes_col->close();
					$dump_col = $ui->col()->width(1)->open()->close();
					$no_col = $ui->col()->width(1)->open();
					echo '<input type="radio" name="boarding_required" value="0">No';
					$no_col->close();
				echo '</div>';
			$radio_col->close(); 
				
			$ui->input()
				->width(3)
				->id('double_AC')
			   ->label('Double AC Rooms Required')
			   ->name('double_AC')
			   ->required()
			   ->show();

			$ui->input()
				->width(3)
				->id('suite_AC')
			   ->label('Suite AC Rooms Required')
			   ->name('suite_AC')
			   ->required()
			   ->show();
		$no_of_guests_row->close();
		$row_room_type = $ui->row()->open();
			$col1 = $ui->col()->id('approval_letter_col')->width(12)->open();
			 $ui->input()
				->type('file')
				->label('Approval Letter')
				->name('approval_letter')
				->id('approval_letter')
				->required()
				->show();
			$col1->close();
		$row_room_type->close();
		$ui->textarea()->label('Remark')->name('Remark')->placeholder("Enter the remark")->required()->show();
?>
<center>
<?
		$ui->button()
		   ->id('booking_form')
		   ->value('Submit')
		   ->uiType('primary')
		   ->submit()
		   ->name('submit')
		   ->show();
?>
</center>
<?
		$form->close();

	$box->close();

	$col2->close();

	$row->close();
?>

<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<script>
	//$(document).tooltip();
	$(document).ready(function(){
		$('#booking_form').click(function(){
			$('[name="no_of_guests"]').val(parseInt($('#double_AC').val()) * 2 + parseInt($('#suite_AC').val()));
		});
	});
</script>

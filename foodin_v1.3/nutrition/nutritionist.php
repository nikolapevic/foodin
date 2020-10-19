<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

//Display nutritionist dashboard view
function nutritionist_content(){

	if(is_user_logged_in()){

		//Get this nutritionists id
		$user_id = get_current_user_id();
		
		$current_user = get_userdata($user_id);
		$c_first_name = $current_user->first_name;
		$c_last_name = $current_user->last_name;
		$c_email = $current_user->user_email;
		$homeurl = get_home_url();
		
		$today = date("Ymd");
		
		//Get requests 
		$requested_clients = get_the_author_meta( 'client_ids', $user_id );
		
		//Get active clients
		$active_clients = get_the_author_meta( 'active_ids', $user_id );
		
		//Get active clients record when activated, when cancelled
		$active_client_records = get_the_author_meta( 'active_clients_records', $user_id );
		
		
		//Post for activating clients
		if($_POST['active_ids']){
			
			//Get active nutritionist from activated id 
			$active_nutritionist = get_the_author_meta( 'active_nutritionist', $_POST['active_ids'] );

			//Get data of activated user
			$activated_user = get_userdata($_POST['active_ids']);
			$user_email = $activated_user->user_email; 
			$msg = 'Congrats! You finally have a nutritionist. His name is ' . $c_first_name . ' ' . $c_last_name . '. Go and Bite the Future together! Log in to your <a href="'.$homeurl.'/profile/schedule">Schedule</a> and contact him away';
			$subject = "Your Food Journey Begins!";
			$headers = "From: Foodin <noreply@foodin.io> \r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			
			//If he doesnt have a nutritionist active
			if(! is_array($active_nutritionist) || is_array($active_nutritionist) && empty($active_nutritionist)){

				//Remove requested id from array and update
				array_splice($requested_clients, array_search($_POST['active_ids'], $requested_clients ), 1);
				update_user_meta( $user_id, 'client_ids', $requested_clients);

				//Insert nutritionist id in array and update for that user
				$active_nutritionist = array($user_id);
				update_user_meta( $_POST['active_ids'], 'active_nutritionist', $active_nutritionist);
				
				//Update active clients it with post id
				if (! is_array($active_clients)){

					$active_clients = array($_POST['active_ids']);
					update_user_meta( $user_id, 'active_ids', $active_clients);
					mail($nutri_email,$subject ,$msg, $headers);

				} elseif (! in_array($_POST['active_ids'], $active_clients)){

					array_push ($active_clients, $_POST['active_ids']);
					update_user_meta( $user_id, 'active_ids', $active_clients);
					mail($nutri_email,$subject ,$msg, $headers);

				}
				
				if(! is_array($active_client_records)){
					
					//Record timeframe
					$active_client_records = array(array(
						'id' => $_POST['active_ids'],
						'start_time' => $today,
						'end_time' => '',
					));
					update_user_meta( $user_id, 'active_clients_records', $active_client_records);
					
				} else {
					
					array_push ($active_client_records, array(
						'id' => $_POST['active_ids'],
						'start_time' => $today,
						'end_time' => '',
					));
					update_user_meta( $user_id, 'active_clients_records', $active_client_records);
					
				}

				
			
			//If that user has a nutritionist active print a notice
			} else {
				$client = get_userdata($_POST['active_ids']);
				$first_name = $client->first_name;
				$last_name = $client->last_name;
				$notice = '<div class="white-container">Unfortunately, ' . $first_name . ' ' . $last_name. ' already has a nutritionist assigned. </div>';
			}
		}
		
		//Post for canceling clients
		if($_POST['client_ids']){

			array_splice($active_clients, array_search($_POST['client_ids'], $active_clients ), 1);
			update_user_meta( $user_id, 'active_ids', $active_clients);
			
			
			foreach($active_client_records as $k => $active_client_record){
				if($active_client_records[$k]['id'] == $_POST['client_ids'] && $active_client_records[$k]['end_time'] == ''){
					$active_client_records[$k]['end_time'] = $today;
				}
			}
			update_user_meta( $user_id, 'active_clients_records', $active_client_records);
			
			array_splice($active_nutritionist, array_search($user_id, $active_nutritionist ), 1);
			update_user_meta( $_POST['client_ids'], 'active_nutritionist', $active_nutritionist);

			if (! is_array($requested_clients)){

				$requested_clients = array($_POST['client_ids']);
				update_user_meta( $user_id, 'client_ids', $requested_clients);

			} elseif (! in_array($_POST['client_ids'], $requested_clients)){

				array_push ($requested_clients, $_POST['client_ids']);
				update_user_meta( $user_id, 'client_ids', $requested_clients);
				
			}
		}
								
		$active_client_records = get_the_author_meta( 'active_clients_records', $user_id );
		//$active_clients = [3,9,97,169];
		//update_user_meta( $user_id, 'active_ids', $active_clients);
		//$active_client_records = update_user_meta( $user_id, 'active_clients_records', 0);
		
		//Get for viewing clients
		
		$date = getdate();
        $day = $date['weekday'];
		$today = strtolower($day);
		
		if(in_array($_GET['client'],$active_clients)){

			$client_id = $_GET['client'];
			$user_data = get_user_meta($client_id);
			$user = get_userdata($client_id);
			$first_name = $user->first_name;
			$last_name = $user->last_name;
			$assinged_diet_id = get_user_meta($client_id, 'assigned_diet', true);
			$habits = get_user_meta($client_id, 'habits', true);
			$vegetables = get_user_meta($client_id, 'vegetables', true);
			$products = get_user_meta($client_id, 'products', true);
			$todays_meals = get_user_meta($client_id, $today, true);
			$goal = get_user_meta($client_id, 'goal', true);
			$goals = ['Gain Weight','Lose Weight', 'Be Healthy'];
			$meat = get_user_meta($client_id, 'meat', true);
			$weight = get_user_meta($client_id, 'weight', true);
			$desired_weight = get_user_meta($client_id, 'desired_weight', true);
			$starting_weight = get_user_meta($client_id, 'starting_weight', true);
			$custom = get_user_meta($client_id, 'custom_diet', true);
			$progress = round((($desired_weight - $weight)/($starting_weight - $desired_weight)+1)*100,1);
			if (is_nan($progress)){
				$progress = 0;
			}
			$weight_diff = abs($desired_weight - $weight);
			$client_view = '';
			$client_view .= '<h3 class="ddwc-inline inline">'.$first_name. ' ' .$last_name.'</h3>';
			$client_view .= '<a href="'.get_home_url().'/profile/edit-client/?client='.$client_id.'" class="button btn btn-white inline float-right">Edit client</a>';
			$client_view .= '<h5 class="ddwc-inline subtitle-font">Day | '.$date['mday'] .'.'. $date['mon'] .'.'. $date['year'].'.</h4>';
			$client_view .= '<div class="nutritionist-dashboard">';
			$client_view .= '<div class="col-lg-9 col-md-12 inline"><div class="white-container">';
			$client_view .= '<div class="nd-lg-4 col-sm-12 inline"><h4>Today&apos;s Schedule</h4>';
			if ($custom == 'Yes'){
				$client_view .= client_meals($todays_meals,$user);
			} else {
				$client_view .= client_meals(assigned_diet_meals($assinged_diet_id, $today),$user);
			}
			$client_view .= '</div>';
			$client_view .= '<div class="nd-lg-4 col-sm-12 inline">';
			$client_view .= client_select($goal, 'Goal', $goals);
			$client_view .= '<div class="middle-cont"><div class="nutri-graph" data-percent="'.$progress.'"><h6>Progress</h6><div class="graph-font">' . $progress . '&percnt;</div><h5 class="subtitle-font">'.$weight.' kg</h5></div></div>';
			$client_view .= '<p class="text-center">' . $first_name . ' ' . $last_name . ' has '.$weight_diff.' kg to go to reach their goal.</p>';
			$client_view .= client_checks($habits, 'Habits');
			$client_view .= '</div>';
			$client_view .= '<div class="nd-lg-4 col-sm-12 inline">';
			$client_view .= client_checks($vegetables ,'Vegetables');
			$client_view .= client_checks($products, 'Products');
			$client_view .= client_checks($meat, 'Meat');
			$client_view .= '</div>';
			$client_view .= '</div></div>';
			$client_view .= '<div class="col-lg-3 col-md-12 inline"><div class="white-container">';
			$client_view .= '<h4>Information</h4>';
			$client_view .= '<table class="information-table">';
			$client_view .= '<tr><td>Name</td><td><h6>' . $first_name . ' ' . $last_name . '</h6></td></tr>';
			$client_view .= client_info('Address', 'shipping_address_1', $client_id);
			$client_view .= client_info('Phone', 'billing_phone', $client_id);
			$client_view .= client_info('City', 'billing_city', $client_id);
			$client_view .= client_info('Gender', 'gender', $client_id);
			$client_view .= client_info('Age', 'age', $client_id);
			$client_view .= client_info('Height', 'height', $client_id);
			$client_view .= client_info('Starting Weight', 'starting_weight', $client_id);
			$client_view .= client_info('Weight', 'weight', $client_id);
			$client_view .= client_info('Desired Weight', 'desired_weight', $client_id);
			$client_view .= client_info('Body', 'body_type', $client_id);
			$client_view .= client_info('Activity', 'activity', $client_id);
			$client_view .= client_info('Sleep', 'sleep', $client_id);
			$client_view .= client_info('Drink', 'drink', $client_id);
			$client_view .= client_info('Motivation', 'motivation', $client_id);
			$client_view .= client_info('Behavior', 'behavior', $client_id);
			$client_view .= '</table>';
			$client_view .= '</div></div>';
			$client_view .= '</div>';
		}
		
		
		
		//Post for declining clients
		if($_POST['remove']){
			
			array_splice($requested_clients, array_search($_POST['remove'], $requested_clients ), 1);
			update_user_meta( $user_id, 'client_ids', $requested_clients);
			
			$requested_nutritionists = get_the_author_meta( 'request_id', $_POST['remove'] );
			array_splice($requested_nutritionists, array_search($user_id, $requested_nutritionists ), 1);
			update_user_meta( $_POST['remove'], 'request_id', $requested_nutritionists);
			
		}

		$clients = '';
		$clients .= '<h3 class="ddwc-inline">Clients</h3>';
		$clients .= '<div class="nutritionist-dashboard"><div class="col-lg-12">';
		$clients .= '<table class="white-table clients"><thead><tr>';
		$clients .= '<th>Name</th><th>City</th><th>Telephone</th><th>Action</th>';
		$clients .= '</tr><thead>';

		$a = 0;
		foreach($active_clients as $key => $id){
			$a++;
			$user_data = get_user_meta($id);
			$user = get_userdata($id);
			$first_name = $user->first_name;
			$last_name = $user->last_name;
			$email = $user->user_email;
			$city = $user_data['billing_city'][0];
			$country = $user_data['billing_country'][0];
			$telephone = $user_data['billing_phone'][0];
			$assinged_diet_id = get_user_meta($id, 'assigned_diet', true);
			$view = '<form class="inline" method="get" action=""><button name="client" value="'.$id.'" class="button btn btn-white inline">View</button></form>';
			$decline = '<form class="inline" method="post" action=""><button class="button btn btn-cancel " name="client_ids" value="'.$id.'">Cancel</button></form>';
			if ($assinged_diet_id){
				$diet = wc_get_product( $assinged_diet_id );
				if($diet){
				$diet_name = $diet->get_name();
				}
			}
			$custom = get_user_meta($id, 'custom_diet', true);
			$avatarurl = get_user_meta( $id, 'simple_local_avatar', true);
			$avatarresize = groci_resize( $avatarurl['full'], 350, 350, true, true, true );
			if($avatarresize){
				$img = '<img width=50 class="inline" src="'.$avatarresize.'">';
			} else {
				$img = '<img width=50 class="inline" src="'. get_avatar_url($id) .'">';
			}

?>

<?php
			$clients .= '<tr class="woocommerce-orders-table__row" data-number>';
			$clients .= '<td clas="woocommerce-orders-table__cell" data-title="Name">'.$img.'<div class="inline client-name"><h5>' . $first_name . ' ' . $last_name . '</h5>' . $email . '</div></td>';
			$clients .= '<td clas="woocommerce-orders-table__cell" data-title="City">';
			if($city){
				$clients .= $city . ', ';
			}
			$clients .= $country . '</td>';
			$clients .= '<td clas="woocommerce-orders-table__cell" data-title="Telephone">'.$telephone.'</td>';
			$clients .= '<td clas="woocommerce-orders-table__cell" data-title="Action">'.$view.' '.$decline.'</td>';
			$clients .= '</tr>';
		}

		$clients .= '</table>';
		$clients .= '</div></div>';

		$requests = '';

		$requests .= '<h3 class="ddwc-inline">Requests</h3>';
		$requests .= '<div class="nutritionist-dashboard"><div class="col-lg-12">';
		$requests .= '<table class="white-table clients"><thead><tr>';
		$requests .= '<th>Name</th><th>City</th><th>Telephone</th><th>Action</th>';
		$requests .= '</tr><thead>';

		$r = 0;
		foreach($requested_clients as $key => $id){
			$r++;
			$user_data = get_user_meta($id);
			$user = get_userdata($id);
			$first_name = $user->first_name;
			$last_name = $user->last_name;
			$email = $user->user_email;
			$city = $user_data['billing_city'][0];
			$country = $user_data['billing_country'][0];
			$telephone = $user_data['billing_phone'][0];
			$assinged_diet_id = get_user_meta($id, 'assigned_diet', true);
			$accept = '<form class="inline" method="post" action=""><button class="button btn" name="active_ids" value="'.$id.'">Accept</button></form>';
			$remove = '<form class="inline" method="post" action=""><button class="button btn btn-cancel" name="remove" value="'.$id.'">Decline</button></form>';
			
			$custom = get_user_meta($id, 'custom_diet', true);
			$avatarurl = get_user_meta( $id, 'simple_local_avatar', true);
			$avatarresize = groci_resize( $avatarurl['full'], 350, 350, true, true, true );
			if($avatarresize){
				$img = '<img width=50 class="inline" src="'.$avatarresize.'">';
			} else {
				$img = '<img width=50 class="inline" src="'. get_avatar_url($id) .'">';
			}
			$requests .= '<tr class="woocommerce-orders-table__row">';
			$requests .= '<td clas="woocommerce-orders-table__cell" data-title="Name">'.$img.'<div class="inline client-name"><h5>' . $first_name . ' ' . $last_name . '</h5>' . $email . '</div></td>';
			$requests .= '<td clas="woocommerce-orders-table__cell" data-title="City">';
			if($city){
				$requests .= $city . ', ';
			}
			$requests .= $country . '</td>';
			$requests .= '<td clas="woocommerce-orders-table__cell" data-title="Telephone">'.$telephone.'</td>';
			$requests .= '<td clas="woocommerce-orders-table__cell" data-title="Action">'.$accept.' '.$remove.'</td>';
			$requests .= '</tr>';
		}

		$requests .= '</table>';
		$requests .= '</div></div>';

	}
	
	$days_result = sum_clients_subscription_days($active_client_records);
	
	$commission = round($days_result*(4.99/30),2);
	
	update_user_meta( $user_id, 'earnings', $commission);

	
	$withdraw = get_the_author_meta( 'withdraw', $user_id );
	
	if($_POST['withdraw']){
		$withdraw = $withdraw + $_POST['withdraw'] ;
		update_user_meta( $user_id, 'withdraw', $withdraw);
		
		update_user_meta( $user_id, 'active_withdraw', $_POST['withdraw']);
		// Always set content-type when sending HTML email
		$headers = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

		// Message to foodin
		// 
		$headers = 'From: '.$c_first_name.' '.$c_last_name.' <noreply@foodin.io>' . PHP_EOL .
			'X-Mailer: PHP/' . phpversion();
		$message .= $c_first_name . ' ' . $c_last_name . ' has made a request to withdraw $' . $_POST['withdraw'] . ' to his PayPal account.';
		//Add the PayPal account email. 
		$to = "accounts@foodin.io";
		$subject = "💸 Withdrawal Request";

		mail($to,$subject,$message,$headers);

	}

	$total = $commission;
	$available = $total - $withdraw;
	
	$earnings = '<h3 class="ddwc-inline">Earnings</h3><div class="col-lg-12 white-container black-bg edit-plans">';
	$earnings .= '<div class="schedule-left">';
	$earnings .= '<h4>Available to withdraw</h4>';
	$earnings .= $paramount;
	$earnings .= '<p>$' . $available . '</p>';
	$earnings .= '<h5 class="mt-4">All time commission</h5>';
	$earnings .= '<p>$' . $total . '</p>';
	$earnings .= '</div>';
	$earnings .= '<div class="schedule-right">';
	$earnings .= '<form method="post" action=""><button class="btn btn-primary button" name="withdraw" value="'.$available.'" type="submit">Withdraw</button></form>';
	$earnings .= '</div">';
	$earnings .= '</div>';
	

	if($a == 0){
		$clients = '';
		$clients .= '<h3 class="ddwc-inline">Clients</h3><div class="white-container">You haven&apos;t activated any clients yet.</div>';
	}
	if($r == 0){
		$requests = '<h3 class="ddwc-inline">Requests</h3><div class="white-container">You don&apos;t have any requests yet.</div>';
	}
	
	//If get method isn't selected, nutritionist is shown two buttons that toggle the helper
	if(! $client_view ){
		$client_view = '<button class="ddwc-inline btn-white button" id="nutri-help">Help</button>
			<div id="help" class="text-center white-container disappear">
			<h2 class="text-center">Welcome to your dashboard</h2>
			<p >Here you can accept or decline requests, activate and deactivate your clients, 
			<br>view and track their progress and edit their nutritional plans.</p>
			<img width=600 src="https://foodin.io/wp-content/uploads/2020/08/nutritiondashempty.jpg">


			<div class="m-4">

			<div class="col-lg-4 col-sm-12 float-left">
			<h5>How can clients find me?</h5>
			<p class="text-justify">The better rating you have, the higher chances will be of you landing a client. You can also post <span class="black">Free to try Plans</span> and get noticed on our Plans page.</p>
			<h5>Can I find clients?</h5>
			<p class="text-justify">Not for now. We are still in Beta and we haven&apos;t added all of the features we want to add. You will get notified soon enough.</p>
			</div>

			<div class="col-lg-4 col-sm-12 float-left">
			<h5>How do I create Plans?</h5>
			<p class="text-justify">Press the <span class="black dash-cont preference">Add a plan</span> button. Under Product Data select <span class="black">Meal Plan</span>. Click on <span class="black">Plan Meals</span> Tab
			and add recipes you seem fit for your plan.</p>
			<a href="/wp-admin/post-new.php?post_type=product"><button class="m-3 btn btn-primary button">Add a plan</button></a>
			</div>

			<div class="col-lg-4 col-sm-12 float-left">
			<h5>Can I add Recipes?</h5>
			<p class="text-justify">You can add recipes by pressing the <span class="black dash-cont preference">Add a recipe</span> button. 
			<br><br>Under Product Data select <span class="black">Product Bundle</span>. Go to Bundle Products, and press <span class="black dash-cont preference">Add Product</span> to search for Groceries. 
			<br><br>When you add the grocery, choose between <span class="black">Set Quantity in Pieces</span> or <span class="black">Set Quantity in Grams</span>.</p>
			<a href="/wp-admin/post-new.php?post_type=product"><button class="m-3 btn btn-white button">Add a recipe</button></a>
			</div></div>

			</div>';
	}
	$output = $notice . $client_view . $clients . $requests . $earnings;

	return $output;
}

//Sum earnings while clients are active
function sum_clients_subscription_days($active_client_records){
	//Get earnings of the nutritionist
	$sum_days = get_the_author_meta( 'earnings', $user_id );
	
	global $plan_subscription;
	
	//Sum all days user is active
	$sum_days = 0;
	$paramount= '';
	foreach($active_client_records as $active_client_record){
		//Run trough clients subscriptions 
		$total_sub = 0;
		$subscriptions = wcs_get_users_subscriptions( $active_client_record['id'] );
		foreach ($subscriptions as $sub){
			$subscription    = wcs_get_subscription( $sub->ID );
			$related_orders_ids_array = $subscription->get_related_orders();
			foreach($related_orders_ids_array as $order_id){
				$i=0;
				if(get_post_type($order_id) == "shop_order"){
					$order = new WC_Order( $order_id );
					$items = $order->get_items();
					$check = false;
					foreach ( $items as $product ) {
						if ($product['product_id'] == $plan_subscription){
							$check = true;
							$this_id = $order_id;
							$i++;
						}
					}
				}
			}
			
			if($check){
				$start = $subscription->get_date( 'start' );
				$end = $subscription->get_date( 'end' );
				if (!$end){
					$end = date("Ymd");
				}
				$secondssubscribed = strtotime($end) - strtotime($start);
				$one_period_subscribed = $secondssubscribed/60/60/24;
				$total_sub += round($one_period_subscribed,0);
				
			}
		}

		$starttime = strtotime($active_client_record['start_time']);
		if ($active_client_record['end_time']){
			$endtime = strtotime($active_client_record['end_time']);
		} else {
			$endtime = strtotime(date("Ymd"));
		}
		$days = ($endtime - $starttime)/60/60/24;
		$sum_days += $days;
		
			if($total_sub > $sum_days){
				$days_result = $sum_days;
			} else {
				$days_result = $total_sub;
			}
		
	}
	return $days_result;
}


//Functions for edit profile... should be moved to edit-account.php

function edit_row($name, $title, $desc, $user){
	$row .= '';
	$row .= '<label for="'.$name.'">' . $title . '</label>';
	$row .= '<td><input type="text" name="'.$name.'" id="'.$name.'" value="' . esc_attr( get_the_author_meta( $name, $user->ID ) ) . '" class="regular-text"><br/>';
	if ($desc){
		$row .= '<span class="description">' . $desc . '</span>';
	}

	echo $row;
}

function edit_disabled_row($name, $title, $desc, $user){
	$value = get_the_author_meta( $name, $user->ID );
	if (is_array($value)){
		$value = count($value);
	}
	$row .= '';
	$row .= '<label for="'.$name.'">' . $title . '</label>';
	$row .= '<td><input type="text" name="'.$name.'" id="'.$name.'" value="' . $value . '" class="regular-text" disabled><br/>';
	if ($desc){
		$row .= '<span class="description">' . $desc . '</span>';
	}

	echo $row;
}

function edit_radio($name, $title, $array, $desc, $user){
	$row .= '';
	$row .= '<br><label for="'.$name.'">' . $title . '</label>';
	$value = get_the_author_meta( $name, $user->ID );
	foreach($array as $key => $v){
		$checked = '';
		if($v == $value){
			$row .= '<div class="box-shadow edit"><input class="regular-text" type="radio" name="'.$name.'" value="'.$v.'" checked>'.$v.'</div>';
			$checked = true;
		};
		if (! $checked){
			$row .= '<div class="box-shadow edit"><input class="regular-text" type="radio" name="'.$name.'" value="'.$v.'">'.$v.'</div>';
		}
	}
	if ($desc){
		$row .= '<div class="description">' . $desc . '</div>';
	}
	
	echo $row;
}

function edit_checkbox($name, $title, $array, $desc, $user){
	$row .= '';
	$row .= '<br><label for="'.$name.'">' . $title . '</label><br>';
	$values = get_the_author_meta( $name, $user->ID );
	foreach($array as $key => $value){
		$checked = '';
		foreach($values as $k => $v){
			if($v == $value){
				$row .= '<label class="bundled_product_optional_checkbox"><input type="checkbox" class="ddwc-left bundled_product_checkbox" value="'.$value.'" name="'.$name.'[]" checked><span class="checkmark"></span>'.$v.'</label><br>';
				$checked = true;
			};
		}
		if (! $checked){
			$row .= '<label class="bundled_product_optional_checkbox"><input type="checkbox" class="ddwc-left bundled_product_checkbox" value="'.$value.'" name="'.$name.'[]"><span class="checkmark"></span>'.$value.'</label><br>';
		}
	}
	if ($desc){
		$row .= '<div class="description">' . $desc . '</div>';
	}
	echo $row;
}

// Add the custom field "favorite_color"
add_action( 'woocommerce_edit_account_form', 'add_paypal_to_edit_account_form' );
function add_paypal_to_edit_account_form() {
    $user = wp_get_current_user();
	$user_id = get_current_user_id();
	$paypal = get_the_author_meta( 'paypal', $user_id );
	
	$genders = ['Male','Female'];
	$goal = ['Gain Weight','Lose Weight', 'Be healthy'];
	$habits = ['I eat late at night','I dont sleep enough','I like sweets','I love soft drinks', 'I consume a lot of salt', 'None of the above'];
	$vegetables = ['Broccoli', 'Sweet potato', 'Mushrooms', 'Tomato', 'Peas', 'Spinach', 'Zucchini', 'Pepper'];
	$products = ['Avocado', 'Eggs', 'Yoghurt', 'Cottage cheese', 'Tofu', 'Olives', 'Peanut butter', 'Nuts, Mozzarella', 'Milk'];
	$meat = ['Turkey','Fish','Beef','Chicken','Pork','None'];
	$age = ['20-29','30-39','40-49','50+'];
	$body_type = ['Ectomorph','Mesomorph','Endomorph'];
	$typical_day = ['At the office','Daily Long Walks','Physical Work','Mostly at Home'];
	$activity = ['Barely Active', '1-2 times a week', '3-5 times', '5-7 times', 'More than once a day'];
	$sleep = ['5 hours', '5-6 hours', '7-8 hours', 'more than 8 hours'];
	$drink = ['Coffee or tea', 'Less than 2 glasses - 0,5l','2-6 glasses 0,5-1,5 l','More than 6 glasses'];
	$motivation = ['I need motivation', 'I can motivate myself'];
	$behavior = ['Yes','No'];
	$cooking = ['< 30','30- 60 min','More than one hour'];
	$bool = ['Yes','No'];
    ?>
	<legend>Payment Details</legend>
    <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
        <label for="paypal"><?php _e( 'PayPal Email', 'woocommerce' ); ?></label>
        <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="paypal" id="paypal" value="<?php echo esc_attr( $paypal ); ?>" />
    </p>
	<legend>Nutritonal Info</legend>
<div class="">
	<?php edit_radio('gender', 'Gender', $genders,'', $user);?>
	<?php edit_radio('goal', 'Goal', $goal,'', $user);?>
	<?php edit_row('height', 'Height', '', $user);?>
	<?php edit_row('starting_weight', 'Starting Weight', '', $user);?>
	<?php edit_row('weight', 'Weight', '', $user);?>
	<?php edit_row('desired_weight', 'Desired Weight', '', $user);?>
	<?php edit_row('age', 'Age', '', $user);?>
	<?php edit_radio('body_type', 'Body Type', $body_type,'', $user);?>
	<?php edit_radio('typical_day', 'Typical Day', $typical_day,'', $user);?>
	<?php edit_checkbox('habits', 'Habits', $habits,'', $user);?>
	<?php edit_radio('activity', 'Activity', $activity,'', $user);?>
	<?php edit_radio('sleep', 'Sleep', $sleep,'', $user);?>
	<?php edit_radio('drink', 'Drink', $drink,'', $user);?>
	<?php edit_radio('motivation', 'Motivation', $motivation,'', $user);?>
	<?php edit_radio('behavior', 'Behavior', $behavior,'', $user);?>
	<?php edit_checkbox('vegetables', 'Vegetables', $vegetables, '', $user);?>
	<?php edit_checkbox('products', 'Products', $products, '', $user);?>
	<?php edit_checkbox('meat', 'Meat', $meat, '', $user);?>
	<?php edit_radio('cooking', 'Cooking', $cooking, '', $user);?>
</div>
	<legend>Nutritional Stats</legend>
	<?php edit_disabled_row('bmi', 'BMR', '', $user);?>
	<?php edit_disabled_row('amr', 'AMR', 'Optimal number of calories per day', $user);?>
	<?php edit_disabled_row('needed_fat', 'Needed Fat', '', $user);?>
	<?php edit_disabled_row('needed_carbs', 'Needed Carbs', '', $user);?>
	<?php edit_disabled_row('needed_protein', 'Needed Protein', '', $user);?>
	<?php edit_disabled_row('needed_fiber', 'Needed Fiber', '', $user);?>
	<br><br>
    <?php
}

// Save the custom field 
add_action( 'woocommerce_save_account_details', 'save_paypal_account_details', 12, 1 );
function save_paypal_account_details( $user_id ) {
    // For Favorite color
    foodin_form_update('paypal', $user_id);
	foodin_form_update('gender', $user_id);
	foodin_form_update('goal', $user_id);
	foodin_form_update('height', $user_id);
	foodin_form_update('weight', $user_id);

	foodin_form_update('starting_weight', $user_id);
	foodin_form_update('weight', $user_id);
	if($_POST['weight']){
		update_calculate_bmr($_POST['gender'], $_POST['weight'], $_POST['height'], $_POST['age'], $_POST['activity'], $user_id);
	}

	foodin_form_update('desired_weight', $user_id);
	foodin_form_update('age', $user_id);
	foodin_form_update('body_type', $user_id);
	foodin_form_update('typical_day', $user_id);
	foodin_form_update('habits', $user_id);//Checkbox
	foodin_form_update('activity', $user_id);

	foodin_form_update('sleep', $user_id);
	foodin_form_update('drink', $user_id);
	foodin_form_update('motivation', $user_id);
	foodin_form_update('behavior', $user_id);
	foodin_form_update('vegetables', $user_id);//Checkbox
	foodin_form_update('products', $user_id);//Checkbox
	foodin_form_update('meat', $user_id);//Checkbox
	foodin_form_update('cooking', $user_id);
}

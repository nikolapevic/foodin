<?php

//Check what subscriptions user has and return matching answer
function check_subscriptions(){
	$client = wp_get_current_user();
	$client_id = $client->ID;

	global $plan_subscription;
	global $nutritional_subscription;

	if(! active_subscription($plan_subscription,$client_id) && ! active_subscription($nutritional_subscription,$client_id)){
		$output .= '<div class="white-container">';
		$output .= 'Subscribe if you want to gain access to all Foodin features. <a class="blue" href="/profile/schedule">Go to schedule.</a>';
		$output .= '</div>';
	}

	if(! active_subscription($nutritional_subscription,$client_id)){
		$output .= '<div class="white-container">';
		$output .= 'Subscribe if you want to gain access to all Foodin features. <a class="blue" href="/profile/schedule">Go to schedule.</a>';
		$output .= '</div>';
	}
	echo $output;
}

add_action( 'woocommerce_account_dashboard','client_dashboard' );
add_action( 'woocommerce_account_dashboard','check_subscriptions' );

remove_action( 'woocommerce_account_dashboard', 'action_woocommerce_account_dashboard', 10, 0 );

//Ordinal numbers function
function ordinal($number) {
    $ends = array('th','st','nd','rd','th','th','th','th','th','th');
    if ((($number % 100) >= 11) && (($number%100) <= 13))
        return $number. 'th';
    else
        return $number. $ends[$number % 10];
}

//Dashboard for clients side in profile
function client_dashboard(){
	//Get for viewing clients

	$date = getdate();
	$day = $date['weekday'];
	$shortweek = ['Monday' => 'Mon','Tuesday' => 'Tue','Wednesday' => 'Wed','Thursday' => 'Thu','Friday' => 'Fri','Saturday' => 'Sat','Sunday' => 'Sun'];
	$today = strtolower($day);

	if(is_user_logged_in()){

		$client = wp_get_current_user();
		$client_id = $client->ID;
		$first_name = $client->first_name;
		$last_name = $client->last_name;
		
		if($_POST['custom_diet']){
			update_user_meta($client_id, 'custom_diet',$_POST['custom_diet']);
		}

		$activity = get_user_meta($client_id, 'activity', true);
		$age = get_user_meta($client_id, 'age', true);
		$height = get_user_meta($client_id, 'height', true);
		$gender = get_user_meta($client_id, 'gender', true);
		$assinged_diet_id = get_user_meta($client_id, 'assigned_diet', true);
		$custom_diet = get_user_meta($client_id, 'custom_diet', true);
		$habits = get_user_meta($client_id, 'habits', true);
		$vegetables = get_user_meta($client_id, 'vegetables', true);
		$products = get_user_meta($client_id, 'products', true);
		$todays_meals = get_user_meta($client_id, $today, true);
		$goal = get_user_meta($client_id, 'goal', true);
		$goals = ['Gain Weight','Lose Weight', 'Be Healthy'];
		$bool = ['Yes','No'];
		$meat = get_user_meta($client_id, 'meat', true);

		if($_POST['weight']){
			update_user_meta($client_id, 'weight',$_POST['weight']);
			update_calculate_bmr($gender, $_POST['weight'], $height, $age, $activity, $client_id);
		}

		$weight = get_user_meta($client_id, 'weight', true);
		$desired_weight = get_user_meta($client_id, 'desired_weight', true);
		$starting_weight = get_user_meta($client_id, 'starting_weight', true);
		$progress = round((($desired_weight - $weight)/($starting_weight - $desired_weight)+1)*100,1);
		if (is_nan($progress) || empty($desired_weight) || empty($weight) || empty($starting_weight)){
			$progress = 0;
		}
		$weight_diff = abs($desired_weight - $weight);
		$nodate = $date['mday'];
		$month = $date['month'];
		$client_view = '';
		
		$client_view .= '<h3 class="ddwc-inline header-profile"><b>'. $shortweek[$day] . ',</b> '.  ordinal($nodate) .' '. $month. '</h3>';
		$client_view .= '<h2 class="ddwc-inline header-profile">Hi, '.$first_name.'</h2>';
		
		$client_view .= '<div class="nutritionist-dashboard">';
		$client_view .= '<div class="col-lg-9 col-md-12 inline"><div class="white-container">';

		$client_view .= '<div class="col-lg-12 col-sm-12 inline">';
		if ($custom_diet == 'Yes'){
			$client_view .= my_meals($todays_meals,$client,'Today&apos;s Schedule',null);
		} else {
			$client_view .= my_meals(assigned_diet_meals($assinged_diet_id, $today),$client,'Today&apos;s Schedule',null);
		}
		$client_view .= '</div>';
		$client_view .= '</div>';
		$client_view .= assigned_nutritionist();
		$client_view .= '</div>';


		$client_view .= '<div class="col-lg-3 col-md-12 inline">';
		$client_view .= '<div class="white-container">';
		$client_view .= client_select($goal, 'Goal', $goals);
		$client_view .= '<div class="fridge"><div class="dash-cont preference float-left">Start: ' . $starting_weight . ' kg</div>';
		$client_view .= '<div class="dash-cont preference float-right">Goal: ' . $desired_weight . ' kg</div></div>';
		$client_view .= '<div class="middle-cont"><div class="nutri-graph" data-percent="'.$progress.'"><h6>Progress</h6><div class="graph-font">' . $progress . '&percnt;</div><h5 class="subtitle-font">'.$weight.' kg</h5></div></div>';
		$client_view .= '<p class="text-center">You have '.$weight_diff.' kg to go to reach their goal.</p>';
		$client_view .= weight_input($weight);
		$client_view .= '</div>';
		$client_view .= '<div id="dash-settings">';
		$client_view .= '<div class="white-container"><h4><ion-icon class="nav__icon" name="hammer-outline"></ion-icon>  Preferences</h4></div>';
		$client_view .= '<div class="dash-settings disappear">';
		$client_view .= '<div class="white-container">';
		$client_view .= '<h4>Custom diet</h4>';
		$client_view .= '<p class="print-servings">Switch between your custom diet and a generic diet.</p>';
		$client_view .= '<form method="post" class="client-select" action=""><select name="custom_diet"  onchange="changeSelect(this)">';
		$cd = ['Yes' => 'Custom Diet', 'No' => 'Generic Diet'];
		if($custom_diet){
			$client_view .= '<option value='.$custom_diet.'>'.$cd[$custom_diet].'</option>';
		}
		foreach ($bool as $v){
			if($custom_diet != $v){
				$client_view .= '<option value='.$v.'>'.$cd[$v].'</option>';
			}
		}
		$client_view .= '</select></form>';
		$client_view .= '</div>';
		$client_view .= '<div class="white-container">';
		$client_view .= '</br><h4>Change store</h4>';
		$client_view .= '<p class="print-servings">Pick a store you want your groceries delivered from</p>';
		$client_view .= pick_store($client_id);
		$client_view .= '</div>';
		
		$client_view .= '</div>';
		$client_view .= '</div>';
		$client_view .= '</div>';
		
		$client_view .= '</div>';
		
		$client_view .= '<div class="col-lg-12 col-sm-12 inline recommended-plans">';

		$client_view .= recommended_meals(meal_array('healthy',4),$client,'Recommended Meals','/product-category/recipes');
		
		$client_view .= '</div>';
		
		$client_view .= '<div class="col-lg-12 col-sm-12 inline recommended-plans">';

		$client_view .= recommended_diets(meal_array('plans',4),$client,'Recommended Plans','/product-category/plans');
		$client_view .= '</div>';
		$client_view .= '</div>';

		echo $client_view;
	}
}

//Pick from which store will variable products be displayed
//Stores are based on shop tag in Attributes
function pick_store($user_id){
	
	$user_shop = get_user_meta($user_id, 'shop', true);
	
	if (empty($user_shop)){
		setcookie("shop", 'Any Shop', time() + (86400 * 30), "/");
		update_user_meta($user_id, 'shop', 'Any Shop');
		$_COOKIE['shop'] = 'Any Shop';
	} else {
		setcookie("shop", $user_shop, time() + (86400 * 30), "/");
		$_COOKIE['shop'] = $user_shop;
	}
	if($_POST['shop']){
		setcookie("shop", $_POST['shop'], time() + (86400 * 30), "/");
		$_COOKIE['shop'] = $_POST['shop'];
		update_user_meta($user_id, 'shop',  $_POST['shop']);
	} 
	
	global $wp_query;
	$term = $wp_query->queried_object;

	$args_cat = array(
		'number'     => "",
		'orderby'    => "menu_order",
		'order'      => 'ASC',
		'hide_empty' => false,
		'parent'     => '',
		'include'    => $ids
	);


	$attributes = get_terms( 'pa_shop', $args_cat );
	
	$output = '<form method="post" action="" class="client-select">';
	$output .= '<select name="shop" onchange="changeSelect(this)">';
	if($_COOKIE['shop']){
		$output .= '<option value="' .$_COOKIE['shop']. '">' . $_COOKIE['shop'] . '</option>';
	}
	foreach($attributes as $attribute){
		$shop_name = $attribute->name;
		if($shop_name != $_COOKIE['shop']){
			$output .= '<option value="' . $shop_name. '">' . $shop_name . '</option>';
		}
	}
	$output .= '</select>';
	$output .= '</form>';
	
	return $output;
}

//Input weight function
function weight_input($weight){
	$output = '';
	$output .= '<h4>Update weight</h4>';
	$output .= '<form method="post" action="" class="weight-form"><input class="inline" type="text" name="weight" value="' . $weight . '"><button type="submit" class="inline button btn-primary">Update</button></form>';
	return $output;
}

//Display stars function
function stars($array){
	$output = '<form name="rating[]" method="post"><div class="nutrition-stars" onchange="dissapear()">';
	foreach ($array as $k => $grade){
		$output .= '<label class="m-1 stars-rating" for="'.$grade.'">'.$grade;
		$output .= '<input type="radio" id="'.$grade.'" name="rating" value="'.$grade.'">';
		$output .= '<span class="checkradio"></span>';
		$output .= '</label>';
	}
	$output .= '</div>';
	$output .= '<button id="submitRatings" style="display:none" class="button btn-primary text-center" type="submit">Submit</button>';
	$output .= '</form>';
	return $output;
}

//Display average values
function avg_values($array, $value){
	$output = '';
	$sum = array_sum(array_column($array, $value));
	$count = count($array);
	$avg = round($sum/$count,1);
	return $avg;
}

//Confirm removal
function confirmation($nutri_id){
	$output = '';
	$output .= '<div id="confirmation" style="display:none">';
	$output .= '<h4>Are you sure?</h4><p>You will have to request and wait for activation again.</p>';
	$output .= '<div class="clearfix" id="confirmBtns">';
	$output .= '<form method="post" action="" class="inline"><button type="submit" class="button btn-primary" name="remove_nutritionist" value="'.$nutri_id.'">Yes, Im Sure</button></form>';
	$output .= '<button type="button" onclick="revert()" class="button btn-white" >No</button>';
	$output .= '</div></div>';
	return $output;	
}

//Display assigned nutritionist 
function assigned_nutritionist(){

	$date = getdate();
	$day = $date['weekday'];
	$today = strtolower($day);


	$home_url = get_home_url();
	$client = wp_get_current_user();
	$client_id = $client->ID;
	$active_nutritionist = get_user_meta($client_id, 'active_nutritionist', true);
	$nutri_id = $active_nutritionist[0];
	$nutri = get_userdata($nutri_id);

	if ($_POST['remove_nutritionist']){

		// Remove the nutritionists from user requested nutritionists and active nutritionists
		$nutritionists = get_the_author_meta( 'request_id', $user_id );
		array_splice($active_nutritionist, array_search($_POST['remove_nutritionist'], $active_nutritionist ), 1);
		array_splice($nutritionists, array_search($_POST['remove_nutritionist'], $nutritionists ), 1);

		// Remove the client from nutritionist active clients
		$active_clients = get_the_author_meta( 'active_ids', $nutri_id );
		array_splice($active_clients, array_search($client_id, $active_clients ), 1);

		// Update arrays
		update_user_meta( $client_id, 'active_nutritionist', $active_nutritionist);
		update_user_meta( $client_id, 'request_id', $nutritionists);
		update_user_meta( $nutri_id, 'active_ids', $active_clients);

		$nutri_id = '';
	} 

	$ratings = get_user_meta($nutri_id, 'rating', true);

	if ($_POST['rating']){

		$rating = $_POST['rating'];

		if(! is_array($ratings)){
			$ratings = array(array(
				'client_id' => $client_id,
				'rating' => $rating));
			update_user_meta($nutri_id, 'rating', $ratings);
		} elseif (is_array($ratings)){

			$client_ids = array_column($ratings, 'client_id');

			$key = array_search($client_id, $client_ids);

			if (is_numeric($key)){
				foreach ($ratings as $k => $v) {
					if ($v['client_id'] == $client_id) {
						$ratings[$k]['rating'] = $rating;
					}
				}
			} else {
				array_push ($ratings, array(
					'client_id' => $client_id,
					'rating' => $rating));
			}

			update_user_meta( $nutri_id, 'rating', $ratings);
		}
	}

	$grades = [1,2,3,4,5];


	$first_name = $nutri->first_name;
	$last_name = $nutri->last_name;
	$avatarurl = get_user_meta( $id, 'simple_local_avatar', true);
	$avatarresize = groci_resize( $avatarurl['full'], 350, 350, true, true, true );
	if($avatarresize){
		$img = '<img width=100 src="'.$avatarresize.'">';
	} else {
		$img = '<img width=100 src="'. get_avatar_url($id) .'">';
	}

	$assigned_view .= '';

	if(! $nutri_id){
		$assigned_view .= '<div class="white-container text-center assigned-nutritionist"><img width=400 src="https://foodin.io/wp-content/uploads/2020/08/onlyyou.jpg">';
		$assigned_view .= '<h4>You don&apos;t have an active nutritionist.</h4><p>Request someone to advise you <a class="blue" href="'.$home_url.'/nutritionists">here</a>.</p></div>';
	} else {
		$assigned_view .= '<div class="white-container assigned-nutritionist"><div class="img-wrapper">'.$img.'</div><div class="nutritionist-info">';
		$assigned_view .= '<h6><ion-icon name="star"></ion-icon> '.avg_values($ratings, 'rating').'</h6>';
		$assigned_view .= '<h3 class="inline">' . $first_name . ' ' . $last_name . '</h3>';
		$assigned_view .= stars($grades);
		$assigned_view .= '</div>';
		$assigned_view .= '<div class="cancel-wrapper">';
		$assigned_view .= '<div id="cancellation"><button id="cancel" onclick="confirm()" class="button btn-cancel" value="'.$nutri_id.'">Deactivate</button></div>';
		$assigned_view .= confirmation($nutri_id);
		$assigned_view .= '</div>';
		$assigned_view .= '</div>';
	}
	return $assigned_view;

}

add_action( 'woocommerce_account_dashboard','assigned_nutritionist' );

?>
<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

//Select options function for frontend
function client_select_options($name, $title, $options_arr, $desc, $user){
	$set_option = esc_attr( get_the_author_meta( $name, $user->ID ) );
	$row .= '';
	$row .= '<tr><th><label for="'.$name.'">' . $title . '</label></th>';
	$row .= '<td><select name="'.$name.'" id="'.$name.'" class="regular-text">';
	if ($set_option){
		$row .= '<option value="'.$set_option.'">'.$set_option.'</option>';
	}
	foreach($options_arr as $key => $option){
		if ($option != $set_option){
			$row .= '<option value="'.$option.'">'.$option.'</option>';
		}
	}
	$row .= '</select>';
	if ($desc){
		$row .= '<div class="description">' . $desc . '</div>';
	}
	$row .= '</td></tr>';

	return $row;
}

//Run options for frontend
function client_run_options($options_arr, $assigned_id){

	$output = '';
	$asssigned_product = wc_get_product( $assigned_id );
	if ($asssigned_product){
		$assigned_name = $asssigned_product->get_name();
		$assigned_img_url = wp_get_attachment_image_url($asssigned_product->get_image_id(),'large');
		$a_calories = get_post_meta($assigned_id,'calories',true);
		$output .= '<option value="'.$assigned_id.'" src="'.$assigned_img_url.'" calories="'.$a_calories.'">'.$assigned_name.'</option>';
	} else {
		$output .= '<option value="'.$assigned_id.'" src="'.$assigned_img_url.'" calories="'.$a_calories.'">'.$assigned_id.'</option>';
	}

	foreach($options_arr as $option){
		$option_id = $option->get_id();
		$option_name = $option->get_name();
		$o_calories = get_post_meta($option_id,'calories',true);
		$option_img_url = wp_get_attachment_image_url($option->get_image_id(),'large');
		if ($option_id != $assigned_id){
			$output .= '<option value="'.$option_id.'" src="'.$option_img_url.'" calories="'.$o_calories.'">'.$option_name.'</option>';
		}
	}

	if ($asssigned_product){
		$output .= '<option value="Nothing">Nothing</option>';
	}
	return $output;
}

//Get image by product ID
function img_url($id){
	$product = wc_get_product($id);
	if ($product){
		$img_url = wp_get_attachment_image_url($product->get_image_id(), 'large');
		return $img_url;
	} else {
		$img_url = wc_placeholder_img_src( 'large' );
		return $img_url;
	}
}

//Sum all calories for meals in that day
function dayCalories($array){
	$calories = 0;
	foreach ($array as $val){
		$product = wc_get_product($val);
		if ($product) {
			$calories += get_post_meta($val,'calories',true);
		}
	}
	return $calories;
}

//Return clients meals per day
function client_day_meals($name, $title, $breakfast, $snack_one, $lunch, $snack_two, $dinner, $user){
	$values = get_the_author_meta( $name, $user->ID );
	if(empty($values)){
		$values = ['Select Breakfast', 'Select Snack', 'Select Lunch', 'Select Snack', 'Select Dinner'];
	}
	
	$row = '';
	$row = '<div class="edit-plans">';
	$row .= '<div class="schedule-left"><h4>'. $title . '</h4></div>';
	$row .= '<div class="schedule-right"><span class="macro"><p>calories</p><h4>'.dayCalories($values).'</h4></span></div>';
	$row .= '</div>';
	$row .= '<div class="day-meals__container">';
	$row .= '<div class="select client-edit"><img src="'.img_url($values[0]).'"><select onchange="changeMeal(this)" name="'.$name.'[]">' . client_run_options($breakfast, $values[0]) . '</select></div>';
	$row .= '<div class="select client-edit"><img src="'.img_url($values[1]).'"><select onchange="changeMeal(this)" name="'.$name.'[]">' . client_run_options($snack_one, $values[1]) . '</select></div>';
	$row .= '<div class="select client-edit"><img src="'.img_url($values[2]).'"><select onchange="changeMeal(this)" name="'.$name.'[]">' . client_run_options($lunch, $values[2]) . '</select></div>';
	$row .= '<div class="select client-edit"><img src="'.img_url($values[3]).'"><select onchange="changeMeal(this)" name="'.$name.'[]">' . client_run_options($snack_two, $values[3]) . '</select></div>';
	$row .= '<div class="select client-edit"><img src="'.img_url($values[4]).'"><select onchange="changeMeal(this)" name="'.$name.'[]">' . client_run_options($dinner, $values[4]) . '</select></div>';
	$row .= '</div>';

	return $row;
}

//Get meals of a category and number of posts
function get_meals($name,$no_posts){
	$array = wc_get_products(array(
		'category' => array($name),
		'posts_per_page' => $no_posts,
		'status' => 'publish'
	));
	return $array;
}

//Edit client view
function edit_client(){
	$user_id = get_current_user_id();
	$client_id = $_GET['client'];
	$active_clients = get_the_author_meta( 'active_ids', $user_id );

	if(in_array($client_id,$active_clients)){
		$bool = ['Yes','No'];

		$client = get_userdata($client_id);

		$breakfast = get_meals('breakfast-recipes',100);
		$italian = get_meals('italian',100);
		$healthy = get_meals('healthy',100);
		$recipes = get_meals('recipes',1000);
		$snacks = get_meals('snacks-recipes',100);

		if($_POST['monday']){
			update_user_meta( $client_id, 'monday', $_POST['monday'] );
			update_user_meta( $client_id, 'tuesday', $_POST['tuesday'] );
			update_user_meta( $client_id, 'wednesday', $_POST['wednesday'] );
			update_user_meta( $client_id, 'thursday', $_POST['thursday'] );
			update_user_meta( $client_id, 'friday', $_POST['friday'] );
			update_user_meta( $client_id, 'saturday', $_POST['saturday'] );
			update_user_meta( $client_id, 'sunday', $_POST['sunday'] );
		}

		$output .= '<h2>Editing ' .  $client->first_name . ' ' . $client->last_name . '</h2>';
		$output .= '<div class="white-container">';
		$output .= '<form method="post" action="">';
		$output .= client_day_meals('monday', 'Monday', $breakfast, $snacks, $recipes, $snacks, $recipes, $client);
		$output .= client_day_meals('tuesday', 'Tuesday', $breakfast, $snacks, $recipes, $snacks, $recipes, $client);
		$output .= client_day_meals('wednesday', 'Wednesday', $breakfast, $snacks, $recipes, $snacks, $recipes, $client);
		$output .= client_day_meals('thursday', 'Thursday', $breakfast, $snacks, $recipes, $snacks, $recipes,$client);
		$output .= client_day_meals('friday', 'Friday', $breakfast, $snacks, $recipes, $snacks, $recipes, $client);
		$output .= client_day_meals('saturday', 'Saturday', $breakfast, $snacks, $recipes, $snacks, $recipes, $client);
		$output .= client_day_meals('sunday', 'Sunday', $breakfast, $snacks, $recipes, $snacks, $recipes, $client);
		$output .= '<button class="button btn-primary" type="submit">Submit</button>';
		$output .= '</form>';
		$output .= '</div>';
		return $output;
	} else {
		$notactive = '<div class="white-container">';
		$notactive .= 'This is not your client';
		$notactive .= '</div>';
		return $notactive;
	}
	
	
}
?>
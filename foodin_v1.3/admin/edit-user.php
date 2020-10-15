<?php

global $current_user;

$user_role = $current_user->role[0];


function nutrition_select($name, $title, $options_arr, $desc, $user){

	$assigned_id = esc_attr( get_the_author_meta( $name, $user->ID ) );
	$asssigned_product = wc_get_product( $assigned_id );
	if ($asssigned_product){
		$assigned_name = $asssigned_product->get_name();
	}

	$row .= '';
	$row .= '<tr><th><label for="'.$name.'">' . $title . '</label></th>';
	$row .= '<td><select name="'.$name.'" id="'.$name.'" class="regular-text">';
	if($assigned_id){
		$row .= '<option value="'.$assigned_id.'">'.$assigned_name.'</option>';
	} else {
		$row .= '<option value="">None</option>';
	}
	foreach($options_arr as $option){
		$option_id = $option->get_id();
		$option_name = $option->get_name();
		if ($option_id != $assigned_id){
			$row .= '<option value="'.$option_id.'">'.$option_name.'</option>';
		}
	}
	$row .= '</select>';
	if ($desc){
		$row .= '<div class="description">' . $desc . '</div>';
	}
	$row .= '</td></tr>';

	echo $row;
}

function nutrition_row($name, $title, $desc, $user){
	$row .= '';
	$row .= '<tr><th><label for="'.$name.'">' . $title . '</label></th>';
	$row .= '<td><input type="text" name="'.$name.'" id="'.$name.'" value="' . esc_attr( get_the_author_meta( $name, $user->ID ) ) . '" class="regular-text"><br/>';
	if ($desc){
		$row .= '<span class="description">' . $desc . '</span>';
	}
	$row .= '</td></tr>';

	echo $row;
}

function disabled_row($name, $title, $desc, $user){
	$value = get_the_author_meta( $name, $user->ID );
	if (is_array($value)){
		$value = count($value);
	}
	$row .= '';
	$row .= '<tr><th><label for="'.$name.'">' . $title . '</label></th>';
	$row .= '<td><input type="text" name="'.$name.'" id="'.$name.'" value="' . $value . '" class="regular-text disabled" disabled><br/>';
	if ($desc){
		$row .= '<span class="description">' . $desc . '</span>';
	}
	$row .= '</td></tr>';

	echo $row;
}


//Checkbox arrays
function nutrition_checkbox($name, $title, $array, $desc, $user){
	$row .= '';
	$row .= '<tr><th><label for="'.$name.'">' . $title . '</label></th><td>';
	$values = get_the_author_meta( $name, $user->ID );
	foreach($array as $key => $value){
		$checked = '';
		foreach($values as $k => $v){
			if($v == $value){
				$row .= '<label><input class="regular-text" type="checkbox" name="'.$name.'[]" value="'.$value.'" checked>'.$value.'</label><br>';
				$checked = true;
			};
		}
		if (! $checked){
			$row .= '<label><input class="regular-text" type="checkbox" name="'.$name.'[]" value="'.$value.'">'.$value.'</label><br>';
		}
	}
	if ($desc){
		$row .= '<div class="description">' . $desc . '</div>';
	}
	$row .= '</td></tr>';
	echo $row;
}

function nutrition_radio($name, $title, $array, $desc, $user){
	$row .= '';
	$row .= '<tr><th><label for="'.$name.'">' . $title . '</label></th><td>';
	$value = get_the_author_meta( $name, $user->ID );
	foreach($array as $key => $v){
		$checked = '';
		if($v == $value){
			$row .= '<label><input class="regular-text" type="radio" name="'.$name.'" value="'.$v.'" checked>'.$v.'</label><br>';
			$checked = true;
		};
		if (! $checked){
			$row .= '<label><input class="regular-text" type="radio" name="'.$name.'" value="'.$v.'">'.$v.'</label><br>';
		}
	}
	if ($desc){
		$row .= '<div class="description">' . $desc . '</div>';
	}
	$row .= '</td></tr>';
	echo $row;
}

function select_options($name, $title, $options_arr, $desc, $user){
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

	echo $row;
}

function run_options($options_arr, $assigned_id){

	$output = '';
	$asssigned_product = wc_get_product( $assigned_id );
	if ($asssigned_product){
		$assigned_name = $asssigned_product->get_name();
		$output .= '<option value="'.$assigned_id.'">'.$assigned_name.'</option>';
	} else {
		$output .= '<option value="'.$assigned_id.'">'.$assigned_id.'</option>';
	}

	foreach($options_arr as $option){
		$option_id = $option->get_id();
		$option_name = $option->get_name();
		if ($option_id != $assigned_id){
			$output .= '<option value="'.$option_id.'">'.$option_name.'</option>';
		}
	}

	if ($asssigned_product){
		$output .= '<option value="Nothing">Nothing</option>';
	}
	return $output;
}


function day_meals($name, $title, $breakfast, $snack_one, $lunch, $snack_two, $dinner, $user){
	$values = get_the_author_meta( $name, $user->ID );
	if(empty($values)){
		$values = ['Select Breakfast', 'Select Snack', 'Select Lunch', 'Select Snack', 'Select Dinner'];
	}
	$row = '';
	$row .= '<tr><th><label for="'.$name.'">' . $title . '</label></th><td>';
	$row .= '<select name="'.$name.'[]">' . run_options($breakfast, $values[0]) . '</select><br>';
	$row .= '<select name="'.$name.'[]">' . run_options($snack_one, $values[1]) . '</select><br>';
	$row .= '<select name="'.$name.'[]">' . run_options($lunch, $values[2]) . '</select><br>';
	$row .= '<select name="'.$name.'[]">' . run_options($snack_two, $values[3]) . '</select><br>';
	$row .= '<select name="'.$name.'[]">' . run_options($dinner, $values[4]) . '</select><br>';
	$row .= '</td></tr>';

	echo $row;
}

add_action( 'show_user_profile', 'extra_user_profile_fields' );
add_action( 'edit_user_profile', 'extra_user_profile_fields' );

function extra_user_profile_fields( $user ) {

	$current_id = get_current_user_id();
	$active_clients = get_the_author_meta( 'active_ids', $current_id );
	$user_id = $user->ID;
	if(is_super_admin() || $current_id == $user_id){
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

		$breakfast = get_meals('breakfast-recipes',100);
		$italian = get_meals('italian',100);
		$healthy = get_meals('healthy',100);
		$recipes = get_meals('recipes',1000);
		$snacks = get_meals('snacks-recipes',100);

?>

<?php echo '<h2>Payment Settings</h2>'; ?>
<table class="form-table">
	<?php nutrition_row('paypal', 'PayPal', 'Email for PayPal payments', $user);?>
</table>

<?php echo '<h2>Diet information</h2>'; ?>
<table class="form-table">
	<?php select_options('custom_diet', 'Custom diet', $bool, 'Do you want to create a custom diet or you want assign an already existing?', $user);?>
	<?php nutrition_select('assigned_diet', 'Assigned diet', $diets, 'Pick a diet you want to assign', $user);?>
	<?php day_meals('monday', 'Monday', $breakfast, $snack, $recipes, $snack, $recipes, $user);?>
	<?php day_meals('tuesday', 'Tuesday', $breakfast, $snack, $recipes, $snack, $recipes, $user);?>
	<?php day_meals('wednesday', 'Wednesday', $breakfast, $snack, $recipes, $snack, $recipes, $user);?>
	<?php day_meals('thursday', 'Thursday', $breakfast, $snack, $recipes, $snack, $recipes, $user);?>
	<?php day_meals('friday', 'Friday', $breakfast, $snack, $recipes, $snack, $recipes, $user);?>
	<?php day_meals('saturday', 'Saturday', $breakfast, $snack, $recipes, $snack, $recipes, $user);?>
	<?php day_meals('sunday', 'Sunday', $breakfast, $snack, $recipes, $snack, $recipes, $user);?>
</table>

<?php echo '<h2>Nutritional information</h2>'; ?>

<table class="form-table">
	
	<?php nutrition_radio('gender', 'Gender', $genders,'', $user);?>
	<?php nutrition_radio('goal', 'Goal', $goal,'', $user);?>
	<?php nutrition_row('height', 'Height', '', $user);?>
	<?php nutrition_row('starting_weight', 'Starting Weight', '', $user);?>
	<?php nutrition_row('weight', 'Weight', '', $user);?>
	<?php nutrition_row('desired_weight', 'Desired Weight', '', $user);?>
	<?php nutrition_row('age', 'Age', '', $user);?>
	<?php nutrition_radio('body_type', 'Body Type', $body_type,'', $user);?>
	<?php nutrition_radio('typical_day', 'Typical Day', $typical_day,'', $user);?>
	<?php nutrition_checkbox('habits', 'Habits', $habits,'', $user);?>
	<?php nutrition_radio('activity', 'Activity', $activity,'', $user);?>
	<?php nutrition_radio('sleep', 'Sleep', $sleep,'', $user);?>
	<?php nutrition_radio('drink', 'Drink', $drink,'', $user);?>
	<?php nutrition_radio('motivation', 'Motivation', $motivation,'', $user);?>
	<?php nutrition_radio('behavior', 'Behavior', $behavior,'', $user);?>

	<?php nutrition_checkbox('vegetables', 'Vegetables', $vegetables, '', $user);?>
	<?php nutrition_checkbox('products', 'Products', $products, '', $user);?>
	<?php nutrition_checkbox('meat', 'Meat', $meat, '', $user);?>
	<?php nutrition_radio('cooking', 'Cooking', $cooking, '', $user);?>
	<?php disabled_row('bmi', 'BMR', '', $user);?>
	<?php disabled_row('amr', 'AMR', 'Optimal number of calories per day', $user);?>
	<?php disabled_row('needed_fat', 'Needed Fat', '', $user);?>
	<?php disabled_row('needed_carbs', 'Needed Carbs', '', $user);?>
	<?php disabled_row('needed_protein', 'Needed Protein', '', $user);?>
	<?php disabled_row('needed_fiber', 'Needed Fiber', '', $user);?>
	
	<?php if(is_super_admin()){
	disabled_row('withdraw', 'Withdrawn amount', '', $user);
	disabled_row('active_withdraw', 'Recent amount withdrawn', '', $user);
	disabled_row('earnings', 'All time made commission', '', $user);
	}
	disabled_row('active_ids', 'Active Clients', '', $user);?>

</table>
<?php }}

add_action( 'personal_options_update', 'save_extra_user_profile_fields' );
add_action( 'edit_user_profile_update', 'save_extra_user_profile_fields' );


function update_calculate_bmr($gender, $weight, $height, $age, $activity, $user_id){
	$activities = array(['factor'=>'Barely Active','value'=>1.2], ['factor'=>'1-2 times a week','value'=>1.375], ['factor'=>'3-5 times','value'=>1.55], ['factor'=>'5-7 times','value'=>1.725], ['factor'=>'More than once a day','value'=>1.9]);
	
	foreach($activities as $option){
		if($option['factor'] == $activity){
			$factor = $option['value'];
		}
	}
	if($gender = 'Male'){
		$bmi = (10 * $weight) + (6.25 * $height) - (5 * $age) + 5;
	} else {
		$bmi = (10 * $weight) + (6.25 * $height) - (5 * $age) - 161;
	}
	
	$amr = $bmi * $factor;
	
	$needed_protein = $weight * 0.8;
	$needed_carbs = $amr * 0.125;
	$needed_fat = $amr * 0.27 / 9;
	
	if($gender = 'Male'){
		if(intval($age) < 50){
			$needed_fiber = $amr * 0.017;
		} else {
			$needed_fiber = $amr * 0.01;
		}
	} else {
		if (intval($age) < 50){
			$needed_fiber = $amr * 0.0115;
		} else {
			$needed_fiber = $amr * 0.01;
		}
	}
	
	update_user_meta($user_id, 'needed_fat', $needed_fat);
	update_user_meta($user_id, 'needed_carbs', $needed_carbs);
	update_user_meta($user_id, 'needed_protein', $needed_protein);
	update_user_meta($user_id, 'needed_fiber', $needed_fiber);
	update_user_meta($user_id, 'bmi', $bmi);
	update_user_meta($user_id, 'amr', $amr);
}

function save_extra_user_profile_fields( $user_id ) {
	if ( !current_user_can( 'edit_user', $user_id ) ) {
		return false;
	}
	update_user_meta( $user_id, 'paypal', $_POST['paypal'] );
	update_user_meta( $user_id, 'withdraw', $_POST['withdraw'] );
	update_user_meta( $user_id, 'monday', $_POST['monday'] );
	update_user_meta( $user_id, 'tuesday', $_POST['tuesday'] );
	update_user_meta( $user_id, 'wednesday', $_POST['wednesday'] );
	update_user_meta( $user_id, 'thursday', $_POST['thursday'] );
	update_user_meta( $user_id, 'friday', $_POST['friday'] );
	update_user_meta( $user_id, 'saturday', $_POST['saturday'] );
	update_user_meta( $user_id, 'sunday', $_POST['sunday'] );
	update_user_meta( $user_id, 'custom_diet', $_POST['custom_diet'] );
	update_user_meta( $user_id, 'assigned_diet', $_POST['assigned_diet'] );
	update_user_meta( $user_id, 'gender', $_POST['gender'] );
	update_user_meta( $user_id, 'goal', $_POST['goal'] );
	update_user_meta( $user_id, 'height', $_POST['height'] );
	update_user_meta( $user_id, 'starting_weight', $_POST['starting_weight'] );
	update_user_meta( $user_id, 'weight', $_POST['weight'] );
	update_user_meta( $user_id, 'desired_weight', $_POST['desired_weight'] );
	update_user_meta( $user_id, 'age', $_POST['age'] );
	update_user_meta( $user_id, 'body_type', $_POST['body_type'] );
	update_user_meta( $user_id, 'typical_day', $_POST['typical_day'] );
	update_user_meta( $user_id, 'habits', $_POST['habits'] );
	update_user_meta( $user_id, 'activity', $_POST['activity'] );
	update_user_meta( $user_id, 'sleep', $_POST['sleep'] );
	update_user_meta( $user_id, 'drink', $_POST['drink'] );
	update_user_meta( $user_id, 'motivation', $_POST['motivation'] );
	update_user_meta( $user_id, 'behavior', $_POST['behavior'] );
	update_user_meta( $user_id, 'vegetables', $_POST['vegetables'] );
	update_user_meta( $user_id, 'products', $_POST['products'] );
	update_user_meta( $user_id, 'meat', $_POST['meat'] );
	update_user_meta( $user_id, 'cooking', $_POST['cooking'] );
	update_calculate_bmr($_POST['gender'], $_POST['weight'], $_POST['height'], $_POST['age'], $_POST['activity'], $user_id);
}


add_role('nutritionist', 'Nutritionist', array(
	'edit_posts' => true,
	'edit_product' => true,
	'edit_product_terms' => true,
	'edit_products' => true,
	'edit_users' => true,
	'list_users' => true,
	'publish_products' => true,
	'read' => true,
	'read_product' => true,
	'assign_product_terms' => true,
)
		);
?>
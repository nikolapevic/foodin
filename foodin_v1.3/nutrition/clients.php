<?php
/**
 * Foodin Follow Diet
 *
 * @version 1.0.0.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
?>

<?php

function add_product(){
	$word = 'Hello';
	?>
	<?php ob_start(); ?>
	<div class="first--block landing">
		<div class="left__block">
			<img src="https://foodin.io/wp-content/uploads/2020/09/foodin_logo-03.png" width="120">
			<h3>Put your <span id="changeText">nutrition</span> on schedule</h3>
			<button id="portfolio-posts-btn" class="button btn-secondary btn">Press here</button>
		</div>
		<div class="right__block"></div>
	</div>
	<div class="second--block landing">
		<div class="admin-quick-add">
			<input type="text" name="title" placeholder="Title">
			<textarea type="text" name="content" placeholder="Type text"></textarea>
			<button id="quick-add-button" class="button btn-secondary btn">Quick Add</button>
		</div>
		<div id="portfolio-posts-container"></div>
	</div>
	<?php $output = ob_get_clean(); ?>

	<?php
	return $output;
}

add_shortcode('add_product','add_product');

?>
	
<?php
/*
if(is_user_logged_in()){

	$user_id = get_current_user_id();
	$current_user = get_userdata($user_id);
	$c_first_name = $current_user->first_name;
	$c_last_name = $current_user->last_name;
	$homeurl = get_home_url();
	
	
	if($_POST['request_id']){
		
		//Get array of requests asked from user
		$nutritionists = get_the_author_meta( 'request_id', $user_id );
		
		//Get array of user requests from a nutritionist
		$clients = get_the_author_meta( 'client_ids', $_POST['request_id'] );
		
		$nutritionist_data = get_userdata($_POST['request_id']);
		$nutri_email = $nutritionist_data->user_email; 
		$msg = 'You have a new diet request from ' . $c_first_name . ' ' . $c_last_name . '. Help them on the way to success! Log in to your Nutritionist Dashboard <a href="'.$homeurl.'/profile/nutritionist-dashboard">here</a>';
		$subject = "New Client Request";
		$headers = "From: Foodin <noreply@foodin.io> \r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		
		if (! is_array($clients)){
			
			$clients = array($user_id);
			update_user_meta( $_POST['request_id'], 'client_ids', $clients);
			
			mail($nutri_email,$subject ,$msg, $headers);
			
		} elseif (! in_array($user_id, $clients)){
			
			array_push ($clients, $user_id);
			update_user_meta( $_POST['request_id'], 'client_ids', $clients);
			mail($nutri_email,$subject ,$msg, $headers);
			
		}
		
		if (! is_array($nutritionists)){
			
			$nutritionists = array($_POST['request_id']);
			update_user_meta( $user_id, 'request_id', $nutritionists);
			
		} elseif (! in_array($_POST['request_id'], $nutritionists)){
			
			array_push ($nutritionists, $_POST['request_id']);
			update_user_meta( $user_id, 'request_id', $nutritionists);
			
		}
	}
	
	$nutritionist_arr = get_the_author_meta( 'request_id', $user_id );
	
} 

$args = array(
    'role'    => 'Nutritionist',
    'orderby' => 'last_name',
    'order'   => 'ASC'
);
$users = get_users( $args );


?>

<script>
	/*
function searchName() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("searchInput");
  filter = input.value.toUpperCase();
  table = document.getElementById("nutritionists");
  tr = table.getElementsByClassName("nutritionist");
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("h3")[0];
    if (td) {
      txtValue = td.textContent || td.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }       
  }
}


</script>
<?php

$i=0;

$outputall = '';
$ouputcity = '';
$cities = ['World'];
foreach ( $users as $user ){
	$user_data = get_user_meta( $user->ID );
	$id = $user->ID;
	$first_name = $user->first_name;
	$last_name = $user->last_name;
	$full_name = $first_name . ' ' . $last_name;
	$city = $user_data['billing_city'][0];
	$country = $user_data['billing_country'][0];
	$avatarurl = get_user_meta( $id, 'simple_local_avatar', true);
	$avatarresize = groci_resize( $avatarurl['full'], 350, 350, true, true, true );
	$active_clients = get_the_author_meta( 'active_ids', $id );
	$ratings = get_the_author_meta( 'rating', $id );
	if(is_array($active_clients)){
		$no_clients = count($active_clients);
	} else {
		$no_clients = 0;
	}
	if(! in_array($city, $cities)){
		array_push($cities, $city);
	}
	
	if($avatarresize){
		$img = '<img width=100 src="'.$avatarresize.'">';
	} else {
		$img = '<img width=100 src="'. get_avatar_url($id) .'">';
	}
	
	$request = '<form method="post"><button class="btn button" name="request_id" type="submit" value="'.$id.'">Request advice</button></form>';
	$requested = '<form method="post"><button class="btn button" name="request_id" type="submit" value="'.$id.'" disabled>Requested</button></form>';
	$guest = '<form method="get" action="/profile"><button class="btn button" name="request_id" type="submit" value="'.$id.'">Request advice</button></form>';
	$active = '<form method="post"><button class="btn button btn-primary" name="request_id" type="submit" value="'.$id.'" disabled>Active</button></form>';
	
	$output = '';
	
	$output .= '<div class="nutritionist">';
	$output .= '<div class="white-container">';
	$output .= '<div>' . $img . '</div>';
	$output .= '<h3 class="nutritionist-name">' . $first_name . ' ' . $last_name . '</h3>';
	$output .= '<p>';
	if ($city){
		$output .= '<span class="nutritionist-city">' . $city . '</span>, ';
	} else {
		$output .= '<span class="nutritionist-city">Unavailable</span>, ';
	}
	if ($country){
		$output .= $country;
	} else {
		$output .= 'N/A';
	}
	$output .= '</p>';
	$output .= '<div class="inline m-1"><h5>Clients</h5>';
	$output .= '<h6>' . $no_clients . '</h6></div>';
	$output .= '<div class="inline m-1"><h5>Rating</h5>';
	$output .= '<h6>' . avg_values($ratings, 'rating') . '</h6></div>';
	if(is_user_logged_in()){
		
		if(in_array($user_id, $active_clients)){
			$output .= '<p>' . $active . '</p>';
		} else {
			if(in_array($id, $nutritionist_arr)){
				$output .= '<p>' . $requested . '</p>';
			} else {
				$output .= '<p>' . $request . '</p>';
			}
		}
	} else {
		$output .= '<p>' . $guest . '</p>';
	}
	$output .= '</div>';
	$output .= '</div>';
	$c=0;
	$match = strpos(strtolower($full_name), strtolower($_GET['n']));
	if (empty($_GET['city']) || $_GET['city'] == "World"){
		if ($_GET['n']){
			if($match !== false){
				$i++;
				$outputall .= $output;
				if($i==50) break;
			}
		} else {
			$i++;
			$outputall .= $output;
			if($i==50) break;
		}
	} elseif ($_GET['city'] == $city){
		if ($_GET['n']){
			if($match !== false){
				$i++;
				$outputcity .= $output;
				if($i==50) break;
			}
		} else {
			$i++;
			$outputcity .= $output;
			if($i==50) break;
		}
	}
  }

$error = '<div class="white-container"><h5>Unfortunately, no nutritionists were found.</h5></div>';
if($i == 0){
	$outputcity = $error;
	$outputall = $error;
}

$content = '';
$content .= '<div class="nutri-header">';
$content .= '<h2>Nutritionists</h2>';
$content .= '<p>Send a request to a nutritionist you would like to hire. You will get notified when they accept your request.</p>';
$content .= '<form class="nutritionist-form" id="nutrForm" method="get" action=""><select name="city" onchange="changeSelect(this)" class="nutritionist-select">';
if ($_GET['city']){
		$content .= '<option class="city-option" value="'.$_GET['city'].'">'.$_GET['city'].'</option>';
}
foreach($cities as $key => $city){
	if ($_GET['city'] != $city && !empty($city)){
		$content .= '<option class="city-option" value="'.$city.'">'.$city.'</option>';
	}
}
$content .= '</select>';
$content .= '<div class="nutritionist-searchform"><input  class="nutritionist-search" name="n" type="text" id="searchInput" value="'.$_GET['n'].'" onkeyup="searchName()" placeholder="Search by name..." title="Search">';
$content .= '<button class="button btn btn-primary" type="submit"><i class="mdi mdi-magnify"></i></button></div>';
$content .= '</form>';
$content .= '</div>';
$content .= '<div id="nutritionists" class="nutritionist-container">';
if(empty($_GET['city']) || $_GET['city'] == "World"){
	$content .= $outputall;
} else {
	$content .= $outputcity;
}

$content .= '</div>';

echo $content;
*/

?>

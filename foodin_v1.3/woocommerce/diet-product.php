<?php 
function follow_diet_function(){
    global $post;
    $terms = wp_get_post_terms( $post->ID, 'product_cat' );
    
    foreach ( $terms as $term ) $categories[] = $term->slug;

    if ( in_array( 'plans', $categories ) ) {
        echo do_shortcode('[follow_diet id='.$post->ID.']');
    }

}

add_action('woocommerce_product_meta_start', 'follow_diet_function');

remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );

function woocommerce_template_single_excerpt() {
        return;
	}

add_action('woocommerce_single_product_summary', 'customizing_single_product_summary_hooks', 2  );

function customizing_single_product_summary_hooks(){
    global $post;
    $id = $post->ID;
    $product = wc_get_product($id);
    $type = $product->get_type();
    if($type == 'bundle' || $type == 'plan' ){
        remove_action('woocommerce_single_product_summary','woocommerce_template_single_price',10  );
    }

}

              
function recipe_description($id){
    
    $steps = [];
    for($i = 1; $i < 15; $i++){
        $step_key = 'step_' . $i;
        $step = get_post_meta($id, $step_key, true);
        $img_key = 'step_image_' . $i;
        $img = get_post_meta($id, $img_key, true);
        array_push($steps, array(
            'step' => $step,
            'img' => $img,
        ));
    }
	$a=0;
    $stepimages = get_post_meta($id, 'step_image', false);
    if (! empty($steps)){
        foreach ($steps as $step){
            if(!empty($step['step'])){
				$a++;
                $output .= '<div class="step">';
                $output .= '<h4>Step '. $a .'</h4>';
                $output .= '<img class="rect_pic" loading="lazy" src="'.$step['img'].'" onerror=this.style.display="none">';
                $output .= '<div class="step_desc">'.$step['step'].'</div>';
                $output .= '</div>';
            }
        }
    }

    return $output;
    
}

// Echo steps description

function steps_description(){
    global $post;
    $id = $post->ID;
    $product = wc_get_product($id);
    $type = $product->get_type();
    
    $terms = wp_get_post_terms( $post->ID, 'product_cat' );
    
    foreach ( $terms as $term ) $categories[] = $term->slug;

    if($type == 'bundle'){
        if(!empty($product->get_description())){
            echo '<div class="col-lg-12 col-md-12 col-sm-12">';
            echo '<div class="white-wrapper">';
            echo '<h3>Description</h3>';
            echo $product->get_description();
            echo '</div>';
            echo '</div>';
        } else {
            echo '<div class="col-lg-12">';
            echo '<div class="white-wrapper">';
			echo '<div class="recipe-left">';
			echo ingredients_needed($id);
			echo '</div>';
			echo '<div class="recipe-right">';
			echo '<h3>Cooking instructions</h3>' . print_servings($id, 'Instructions');
        	echo '<div class="steps">';
            echo recipe_description($id);
			echo '</div>';
			echo '</div>';
            echo '</div>';
            echo '</div>';
        }
        
    } elseif ($type == 'plan'){
        echo '<div class="col-lg-12">';
        echo display_diet($id);
        echo '</div>';
    }
}

function construct_diet_day($post_id, $name, $day_name) {

    $values = get_post_meta($post_id, $name, true);
	$valArr = explode(',',$values);
	$productArr = [];
	foreach($valArr as $val){
		$product = wc_get_product($val);
		if($product){
			array_push($productArr,$val);
		}
	}
	$i=0;
	$values = '';
	foreach($productArr as $v){
		$i++;
		if(count($productArr) == $i){
			$values .= $v;
		} else {
			$values .= $v . ',';
		}
	}
    if ($values == ''){
        return null;
    } else {
		return do_shortcode('[diet_meals ids='. $values . ' day_name=' . $day_name. ']');
    }
}

function display_diet($post_id){
    $output .= construct_diet_day($post_id, 'monday', 'Monday');
    $output .= construct_diet_day($post_id, 'tuesday', 'Tuesday');
    $output .= construct_diet_day($post_id, 'wednesday', 'Wednesday');
    $output .= construct_diet_day($post_id, 'thursday', 'Thursday');
    $output .= construct_diet_day($post_id, 'friday', 'Friday');
    $output .= construct_diet_day($post_id, 'saturday', 'Saturday');
    $output .= construct_diet_day($post_id, 'sunday', 'Sunday');
    
    return $output;
}

add_action( 'woocommerce_after_single_product_summary', 'steps_description' );


function print_servings($postid, $name){
    $servings = get_post_meta($postid, 'servings', true);
    $half = $servings/2;
    $double = $servings*2;
    if ($servings == 1){
		$output = '<div class="print-servings">';
        $output .= $name.' for '.$servings.' servings(double for '.$double.')';
		$output .= '</div>';
    } elseif($servings > 1) {
		$output = '<div class="print-servings">';
        $output .= $name.' for '.$servings.' servings(divide for '.$half.')';
		$output .= '</div>';
    }
    return $output;
}
function decToFraction($float) {
    // 1/2, 1/4, 1/8, 1/16, 1/3 ,2/3, 3/4, 3/8, 5/8, 7/8, 3/16, 5/16, 7/16,
    // 9/16, 11/16, 13/16, 15/16
    $whole = floor ( $float );
    $decimal = $float - $whole;
    $leastCommonDenom = 48; // 16 * 3;
    $denominators = array (2, 3, 4, 8, 16, 24, 48 );
    $roundedDecimal = round ( $decimal * $leastCommonDenom ) / $leastCommonDenom;
    if ($roundedDecimal == 0)
        return $whole;
    if ($roundedDecimal == 1)
        return $whole + 1;
    foreach ( $denominators as $d ) {
        if ($roundedDecimal * $d == floor ( $roundedDecimal * $d )) {
            $denom = $d;
            break;
        }
    }
    return ($whole == 0 ? '' : $whole) . " " . ($roundedDecimal * $denom) . "/" . $denom;
}

function convert_units($post_id,$description,$weight,$need){
	$cupconvert = get_post_meta($post_id, 'cup', true);
	$tableconvert = get_post_meta($post_id, 'tbsp', true);
	$teaconvert = get_post_meta($post_id, 'tsp', true);
	if ($description < $weight){
		$output = round($description/$weight,1);
	}
	if($description > 40){
		if($cupconvert){
			$output = decToFraction(round($description/$cupconvert,2)) . ' cup';
		}
	} elseif($description < 40 && $description > 10) {
		if($tableconvert){
			$output = decToFraction(round($description/$tableconvert,1)) . ' tbsp';
		}
	} else {
		if($teaconvert){
			$output = decToFraction(round($description/$teaconvert,1)) . ' tsp';
		}
	}
	if ($output){
		return $output;
	} else {
		return $need;
	}
}

function ingredients_needed($id){
	
	$ingredients .= '<h3>Ingredients</h3>';
	$ingredients .= print_servings($id, 'Ingredients');
	$results = WC_PB_DB::query_bundled_items( array(
		'return'    => 'id=>product_id',
		'bundle_id' => $id,
	) );
	$output = '';
	$ingredients .= '<div class="bundled-container">';
	foreach($results as $key => $id){
		$cats = array();
		$title =  WC_PB_DB::get_bundled_item_meta( $key, 'title' );
		$quantity = WC_PB_DB::get_bundled_item_meta( $key, 'quantity_min' );
		$override = WC_PB_DB::get_bundled_item_meta($key, 'override_description');
		$description = WC_PB_DB::get_bundled_item_meta($key, 'description');

		$bproduct = wc_get_product( $id );
		$b_id = $id;
		$bname = $bproduct->get_name();
		if ($title){
			$bname = $title;
		}
		
		$terms = wp_get_post_terms( $id, 'product_cat' );
		foreach ( $terms as $term ) {
			array_push($cats,$term->slug);
		}
			
		
		$mweight = $bproduct->get_weight() * 1000;
		$bprice = $bproduct->get_price_html();

		$weight = $mweight;

		$varoutput = '';
		$variations = $bproduct->get_children();
		$brand_name = '';
		$n = 0;
		if($variations){
			foreach($variations as $k => $v){
				$n++;
				$vproduct = wc_get_product($v);
				$vname = $vproduct->get_name();
				$vprice = $vproduct->get_price_html();
				$vweight = $vproduct->get_weight() * 1000;

				$picked_shop = 'Any Shop';
				if($_COOKIE['shop']){
					$picked_shop = $_COOKIE['shop'];
				}

				$shop = $vproduct->get_attribute('shop');
				$brand = $vproduct->get_attribute('brand');
				$default = $bproduct->get_default_attributes();
				$default_brand = $default['brand'];
				if ($shop == $picked_shop){
					if($brand == $default_brand){
						$brand_name = $default_brand;
						$id = $v;
						$bprice = $vprice;
						$weight = $vweight;
					} elseif ($n == 1) {
						$brand_name = $brand;
						$id = $v;
						$bprice = $vprice;
						$weight = $vweight;
					}
				}
			}
		}

		if ($description){
			$need = $description . 'g';
			if(in_array('drinks',$cats)||in_array('vinegar',$cats)||in_array('sauces',$cats)||in_array('oil',$cats)||in_array('milk',$cats)){
				$need = $description . 'ml';
			}
		
			$need = convert_units($b_id,$description,$weight,$need);
			
			
			if($weight > 0){
				if ($description < $weight){
					$quantity = 1;
				} else {
					$quantity = round($description/$weight,1);
					if ($quantity > 1.2 && $quantity < 1.5){
						$quantity = 2;
					} else {
						$quantity = round($quantity,0);
					}
				}
			}
		} elseif (! $description){
			$need = $quantity;
			if($mweight > 0){
				if($quantity * $mweight < $weight){
					$quantity = 1;
				} else {
					$quantity = round($quantity*$mweight/$weight,0);
				}
			}
		}

		if($weight < 1000){
			$title_weight = $weight;
			$unit = 'g';
			if(in_array('drinks',$cats)||in_array('vinegar',$cats)||in_array('sauces',$cats)||in_array('oil',$cats)||in_array('milk',$cats)){
				$unit = 'ml';
			}
		} else {
			$title_weight = $weight/1000;
			$unit = 'kg';
			if(in_array('drinks',$cats)||in_array('vinegar',$cats)||in_array('sauces',$cats)||in_array('oil',$cats)||in_array('milk',$cats)){
				$unit = 'l';
			}
		}

		$add_to_cart = '<a href="?add-to-cart='.$id.'" data-quantity="'.$quantity.'" class="btn btn-secondary btn-sm button add_to_cart_button ajax_add_to_cart" data-product_id="'.$id.'" data-product_sku="" aria-label="Read more about “'.$bpname.'”" rel="nofollow"><i class="mdi mdi-cart-outline"></i> Add</a>';

		if ($bproduct->get_image_id()){
			$image = '<img class="full" src="' . wp_get_attachment_url($bproduct->get_image_id()). '">';
		} else {
			$image = '<img class="full" src="' .  wc_placeholder_img_src(). '">';
		}
		$output .= '<div class="bundled_product">';
		$output .= $image;
		$output .= '<div class="product-body">';
		$output .= '<h5>' . $need . ' ' . $bname . '</h5>';
		$output .= 'Add '. $quantity . ' x ' . $title_weight . $unit . ' ' . $brand_name;
		$output .= '<p class="offer-price mb-0">' . $bprice . '</p>';
		$output .= '</div>';
		$output .= '<div class="product-footer">';
		$output .= $add_to_cart;
		$output .= '</div>';
		$output .= '</div>';
	}
	$ingredients .= $output;
	$ingredients .= '</div>';
	
	return $ingredients;
}

function display_macros($product_id){
	
	$user_id = get_current_user_id();
	$needed_kcal = get_user_meta($user_id, 'amr', true);
	$needed_fat = get_user_meta($user_id, 'needed_fat', true);
	$needed_protein = get_user_meta($user_id, 'needed_protein', true);
	$needed_fiber = get_user_meta($user_id, 'needed_fiber', true);
	$needed_carbs = get_user_meta($user_id, 'needed_carbs', true);
	$results = WC_PB_DB::query_bundled_items( array(
		'return'    => 'id=>product_id',
		'bundle_id' => $product_id,
	) );
	$servings = number_format(get_post_meta($product_id, 'servings', true));
	
	foreach ($results as $key => $id){
        $quantity = WC_PB_DB::get_bundled_item_meta( $key, 'quantity_min' );
        $override = WC_PB_DB::get_bundled_item_meta($key, 'override_description');
        $description = number_format(WC_PB_DB::get_bundled_item_meta($key, 'description'));
		$product = wc_get_product( $id );
		$weight = $product->get_weight() * 1000;
		
		$calories = number_format(nutritional_info($override,$description,$weight,get_post_meta( $id, 'calories', true ),$servings),1);
		$proteins = number_format(nutritional_info($override,$description,$weight,get_post_meta( $id, 'proteins', true ),$servings),1);
		$carbs = number_format(nutritional_info($override,$description,$weight,get_post_meta( $id, 'carbs', true ),$servings),1);
		$fats = number_format(nutritional_info($override,$description,$weight,get_post_meta( $id, 'fats', true ),$servings),1);
		$fiber = number_format(nutritional_info($override,$description,$weight,get_post_meta( $id, 'fiber', true ),$servings),1);

		$meal_kcal += $calories;
		$meal_proteins += $proteins;
		$meal_carbs += $carbs;
		$meal_fats += $fats;
		$meal_fiber += $fiber;
	}
	
	$output .= '<h3>Nutritional info</h3><p class="print-servings text-left">Nutritional values per 1 serving</p><div class="nutrition">';
    $output .= '<div class="macro kcal" data-percent="'.percentage($meal_kcal, $needed_kcal).'"><p>kcal</p><h4>'.$meal_kcal.'</h4></div>';
    $output .= '<div class="macro protein" data-percent="'.percentage($meal_proteins, $needed_protein).'"><p>protein</p><h4>'.$meal_proteins.' g</h4></div>';
    $output .= '<div class="macro carbs" data-percent="'.percentage($meal_carbs, $needed_carbs).'"><p>carbs</p><h4>'.$meal_carbs.' g</h4></div>';
    $output .= '<div class="macro fats" data-percent="'.percentage($meal_fats, $needed_fat).'"><p>fats</p><h4>'.$meal_fats.' g</h4></div>';
    $output .= '<div class="macro fiber" data-percent="'.percentage($meal_fiber, $needed_fiber).'"><p>fiber</p><h4>'.$meal_fiber.' g</h4></div>';
    $output .= '</div>';
	
	return $output;
}




// Print description
function product_description(){
	global $post;
	$id = $post->ID;
    $terms = wp_get_post_terms( $id, 'product_cat' );
    $product = wc_get_product($id);
    foreach ( $terms as $term ) $categories[] = $term->slug;
    
    if($product){
        $type = $product->get_type();
        $category= $product->get_categories();
        if($type == 'bundle'){
            $duration = get_post_meta($id, 'duration', true);
            $excerpt = get_the_excerpt($id);

            $product_description = '<div class="stat"><ion-icon class="nav__icon" name="heart-outline"></ion-icon> '.$category.'</div>';
            $product_description .= '<div class="stat"><ion-icon class="nav__icon" name="time-outline"></ion-icon> '.$duration.'</div>';
            $product_description .= '<p>' . $excerpt . '</p>';
			$product_description .= display_macros($id);
        } elseif ($type == 'plan'){
			
            $user = wp_get_current_user();
            $date = getdate();
            $day = $date['weekday'];
            $weekdays = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];

            $date = time(); //Current date
            $weekDay = date('w', strtotime('+1 day',$date));
            
            $today = strtolower($day);
            $tomorrow = $weekdays[$weekDay];

            $servings = get_post_meta($id, 'servings', true);
            $duration = get_post_meta($id, 'duration', true);
            $excerpt = get_the_excerpt($id);
            $half = $servings/2;
            $product_description = '<div class="stat">'.$category.'</div>';
            $product_description .= '<div class="stat">'.$duration.'</div>';
            $product_description .= '<p>' . $excerpt . '</p>';
            
            $product_description .= '<h3><a class="black" href="#'.$day.'">'.$day.'</a>&apos;s meals</h3>';
            $product_description .= client_meals(assigned_diet_meals($id, $today),$user);
            $product_description .= '<a href="#'.$tomorrow.'">See what&apos;s Tomorrow</a>';
        }
    }
    echo $product_description;
}

//Plan category
add_action( 'save_post_product', 'update_product_category' );
function update_product_category( $post_id ) {
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return $post_id;
    }

    if ( ! current_user_can( 'edit_product', $post_id ) ) {
        return $post_id;
    }

	$post = get_post($post_id);
	if( get_post_status( $post_id ) == 'publish' && has_term('plan', 'product_type',$post_id ) && has_term( 'Recipes', 'product_cat', $post_id )) {
		wp_remove_object_terms($post_id, 'Recipes', 'product_cat');
		wp_set_object_terms($post_id, 'Plans', 'product_cat', true );
	}
	
	if( get_post_status( $post_id ) == 'publish' && has_term('variable', 'product_type',$post_id ) && has_term( 'Recipes', 'product_cat', $post_id )) {
		wp_remove_object_terms($post_id, 'Recipes', 'product_cat');
		wp_set_object_terms($post_id, 'Groceries', 'product_cat', true );
	}
}


add_action('woocommerce_product_meta_start', 'product_description');


// Remove additional info tab and availability map
add_filter( 'woocommerce_product_tabs', 'bbloomer_remove_product_tabs', 9999 );
  
function bbloomer_remove_product_tabs( $tabs ) {
    unset( $tabs['additional_information'] );
    unset( $tabs['avalibility_map'] );
    unset( $tabs['description'] );
    return $tabs;
}
              


?>

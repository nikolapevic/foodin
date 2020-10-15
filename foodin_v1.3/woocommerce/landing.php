<?php /* Template Name: CustomPageT1 */ ?>
<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}



function landing_page(){
	$freelink = 'https://foodin.io/';
	$planslink = 'https://foodin.io/product-category/plans';
	$mealslink = 'https://foodin.io/product-category/recipes';
	$startlink = 'https://foodin.io/profile/schedule/';
	$nutrilink = 'https://foodin.io/profile/nutritionist-dashboard/';
	$driverlink = 'https://foodin.io/register-as-driver';
	global $plan_subscription;
	global $nutritional_subscription;
	$product = wc_get_product($plan_subscription);
	$n_product = wc_get_product($nutritional_subscription);
	$price = $product->get_price();
	$n_price = $n_product->get_price();
?>

<?php ob_start(); ?>
<div class="first--block landing">
	<div class="left__block">
		<img src="https://foodin.io/wp-content/uploads/2020/09/foodin_logo-03.png" width="120">
		<h1>Do More</h1>
		<h3>Put your <span id="changeText">nutrition</span> on schedule</h3>
		<a href="<?php echo $startlink?>"><button class="button btn-secondary btn">Get Started</button></a>
		<a href="<?php echo $freelink?>"><button class="button btn-secondary btn btn-empty">Try for Free</button></a>
	</div>
	<div class="right__block"></div>
</div>
<div class="second--block landing">
	<h2>Who are we for?</h2>
	<h3>That depends on who you are.</h3>
	<ul class="categories-wrapper" id="nav-tab">
		<li class="inline"><a id="foodie-tab" class="curr-cat selected" href="#foodie">I'm a Foodie</a></li>
		<li class="inline"><a id="nutritionist-tab" class="curr-cat" href="#nutritionist">I'm Nutritionist</a></li>
		<li class="inline"><a id="nutritionist-tab" class="curr-cat" href="#driver">I'm a Driver</a></li>
	</ul>
	<div class="tab-content">
		<div class="tab-pane selected" id="foodie-content">
			<div class="left col-lg-6 col-xs-12">
				<h3>Pick or create your own diet</h3>
				<p>Select a diet you want to follow, or better yet, create one for yourself.</p>
				<h3>View recipes and diets</h3>
				<p>Try our service for free and see delicious recipes and diets we offer.</p>
				<h3>Get groceries delivered</h3>
				<p>See what has your feed prepared, select groceries you need and get them the next day.</p>
				<a href="<?php echo $freelink?>"><button class="button btn-secondary btn mb-2">Be a Foodie</button></a>
				<p class="fine-print">Subscribe for $<?php echo $price?>. Free for the first month. You can cancel anytime</p>
			</div>
			<div class="right col-lg-6 col-xs-12">
				<img src="https://foodin.io/wp-content/uploads/2020/08/foodies.jpg">
			</div>
		</div>
		<div class="tab-pane" id="nutritionist-content">
			<div class="left col-lg-6 col-xs-12">
				<h3>Create or assign diets</h3>
				<p>Create a generic diet that everyone can follow, or create a custom diet, tailored to your clients needs.</p>
				<h3>Lead your clients to success</h3>
				<p>You can easily update and change their meal schedule.</p>
				<h3>Save your time</h3>
				<p>Do not waste your time on calculating nutrition or looking for recipe combinations, we have it all on one spot.</p>
				<a href="<?php echo $nutrilink?>"><button class="button btn-secondary btn mb-2">Be a Nutritionist</button></a>
				<p class="fine-print">Subscribe for $<?php echo $n_price?>. Free for the first month. You can cancel anytime</p>
			</div>
			<div class="right col-lg-6 col-xs-12">
				<img src="https://foodin.io/wp-content/uploads/2020/08/nutritionist.jpg">
			</div>
		</div>
		<div class="tab-pane" id="driver-content">
			<div class="left col-lg-6 col-xs-12">
				<h3>Make money on your own terms</h3>
				<p>You’re the boss. You can drive with Foodin whenever grocery stores are opened. More groceries, more cash.</p>
				<h3>Deliver during timeslots</h3>
				<p>No need to rush, your deliveries are scheduled always for tomorrow in timeslots.</p>
				<h3>Let our dashboard guide you trough the city</h3>
				<p>Our dashboard categorizes order items so you don't lose too much time on shopping.</p>
				<a href="<?php echo $driverlink?>"><button class="button btn-secondary btn">Be a Driver</button></a>
			</div>
			<div class="right col-lg-6 col-xs-12">
				<img src="https://foodin.io/wp-content/uploads/2020/08/deliver.jpg">
			</div>
		</div>
	</div>
</div>
<div class="second--block landing basic--principles">
	<h2>Basic principles</h2>
	<div class="basic--principles--wrapper">
		<div class="third">
			<img src="https://foodin.io/wp-content/uploads/2020/09/schedule-1.png">
			<h3>Schedule your food</h3>
		</div>
		<div class="third">
			<img src="https://foodin.io/wp-content/uploads/2020/09/groceries-2.png">
			<h3>Get groceries delivered whenever you please</h3>
		</div>
		<div  class="third">
			<img src="https://foodin.io/wp-content/uploads/2020/09/shape-1.png">
			<h3>Cook and get in shape</h3>
		</div>
	</div>
</div>
<div class="landing first--block third--block text-center">
	<div class="third--wrapper">
		<h1>Taste your future</h1>
		<h3>Let our <span>schedule</span> take you beyond.</h3>
		<a href="<?php echo $startlink?>"><button class="button btn-secondary btn">Get Started</button></a>
		<a href="<?php echo $freelink?>"><button class="button btn-secondary btn btn-empty">Try for Free</button></a>
	</div>
</div>
<div class="landing fourth--block">
	<div class="text-center">
		<h2>Foodin for Nutritionists</h2>
		<div class="left text-left">
			<h2>Schedule</h2>
			<h3>Best way to monitor habits and health.</h3>
			<ul class="landing-list">
				<li>Accept clients, check their habits, preferences and track their progress.</li>
				<li>We guarantee $4.99 a month for every client that subscribes to our Plan Subscription</li>
			</ul>
			
			<a href="<?php echo $nutrilink?>"><button class="button btn-secondary btn mb-2">Get Started</button></a>
			<a href="<?php echo $mealslink?>"><button class="button btn-secondary btn btn-empty mb-2">See our meals</button></a>
			<p class="fine-print">Subscribe for $<?php echo $n_price?>. Free for the first month. You can cancel anytime</p>
		</div>
		<div class="right">
			<img src="https://foodin.io/wp-content/uploads/2020/09/ipad1-1.jpeg">
		</div>
	</div>
</div>
<div class="landing fourth--block">
	<div class="text-center">
		<div class="left text-left">
			<h2>Simple plans,<br>easy gains.</h2>
			<h3>Create nutrition plans for your clients</h3>
			<ul class="landing-list">
				<li>Easily schedule your clients nutrition plan. Just select meals that fit their profile.</li>
				<li>Change it on the go and gradually, lead them to success.</li>
			</ul>
			<a href="<?php echo $nutrilink?>"><button class="button btn-secondary btn mb-2">Get Started</button></a>
			<a href="<?php echo $planslink?>"><button class="button btn-secondary btn btn-empty mb-2">See our plans</button></a>
			<p class="fine-print">Subscribe for $<?php echo $n_price?>. Free for the first month. You can cancel anytime</p>
		</div>
		<div class="right">
			<img src="https://foodin.io/wp-content/uploads/2020/09/ipadplan-1.jpg">
		</div>
	</div>
</div>

<style>

	.tab-content>.tab-pane {
		display:table-column;
		opacity:0;
	}

	.tab-pane.selected { 
		opacity: 1;
		transition:1s;
		display:block;
	}
</style>
<script>
	
	var i = 0;
	var a = 0;
	var groceries = 'groceries';
	var health = 'health';
	var speed = 60;

	$(document).ready(
		setTimeout(function(){
			var changeText = document.getElementById("changeText");
			changeText.innerHTML = '';
			typeGroceries()
			setTimeout(function(){
				changeText.innerHTML = '';
				typeHealth()
			},2500)
		},2000)
	);


	function typeGroceries() {
		if (i < groceries.length) {
			var changeText = document.getElementById("changeText");
			changeText.innerHTML += groceries.charAt(i);
			i++;
			setTimeout(typeGroceries, speed);
		}
	}

	function typeHealth() {
		if (a < health.length) {
			var changeText = document.getElementById("changeText");
			changeText.innerHTML += health.charAt(a);
			a++;
			setTimeout(typeHealth, speed);
		}
	}
	
	function onTabClick(event) {
		let activeTabs = document.querySelectorAll('.selected');

		// deactivate existing active tab and panel
		// for( let i = 0; i < activeTabs.length; i++) {
		//   activeTabs[i].className = activeTabs[i].className.replace('active', '');
		// }

		// activate new tab and panel
		if(event.target.parentElement.parentElement.className == "categories-wrapper"){
			activeTabs.forEach(function(tab) {
				tab.className = tab.className.replace('selected', '');
			});
			event.target.className += ' selected';
			document.getElementById(event.target.href.split('#')[1]+"-content").className += ' selected';
		}

	}

	const element = document.getElementById('nav-tab');
	$(document).ready(function() { 
		document.getElementById("searchform").action = "/product-category/recipes/?product_cat=recipes";
	});

	element.addEventListener('click', onTabClick, false);
</script>
<?php $output = ob_get_clean(); ?>

<?php	
	return $output;
}
add_shortcode( 'landing_page', 'landing_page' );
?>
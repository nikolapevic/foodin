<?php
/**
 * Foodin Diet Template
 *
 * @version 1.0.0.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

function register_form($user_id){

?>
<style>
	* {
		box-sizing: border-box;
	}

	body {
		background-color: #F5F7FA;
	}

	#regForm {
		background-color: #ffffff;
		margin: 5vh auto 30vh;
		font-family: Montserrat;
		padding: 40px;
		width: 50%;
		min-width: 300px;
		border-radius:10px;
		box-shadow:0 8px 20px rgba(203, 223, 255,0.5);
	}

	input {
		padding: 10px;
		width: 100%;
		font-size: 17px;
		font-family: Raleway;
		border: 1px solid #aaaaaa;
	}

	/* Mark input boxes that gets an error on validation: */
	input.invalid {
		background-color: #ffdddd;
	}

	/* Hide all steps by default: */
	.tab {
		display: none;
		height:380px;
	}
	
	.tab label{
		font-size: 14px;
		font-family:Comfortaa;
		display:inline-block;
		color:#000;
		width: 90%;
	}

	/* Make circles that indicate the steps of the form: */
	.step {
		height: 10px;
		width: 10px;
		margin: 0 2px;
		background-color: rgba(203, 223, 255);
		border: none;
		border-radius: 50%;
		display: inline-block;
		opacity: 0.5;
	}
	
	.step.active {
		opacity: 1;
	}
	
	.select2.select2-container{
		width: 100% !important;
		margin-bottom: 15px;
	}

	/* Mark the steps that are finished and valid: */
	.step.finish {
		background-color: #91e094;
	}

	.woocommerce-account input[type="checkbox"]{
		width: initial;
		margin-right: 15px;
	}

	#regForm input[type="radio"] {
		width: 24px !important;
	}

	#desiredWeight input, .woocommerce-account input {
		display: inline-block;
		width: 100px;
		font-family:Montserrat;
	}
	
	#desiredWeight kg{
		font-size: 14px;
		font-family:Montserrat;
		display: inline-block;
		width: 38px;
		margin-left:-40px;
	}
	
</style>
<?php

	$genders = ['Male','Female'];
	$goal = ['Gain Weight','Lose Weight', 'Be healthy'];
	$body_type = ['Ectomorph','Mesomorph','Endomorph'];
	$typical_day = ['At the office','Daily Long Walks','Physical Work','Mostly at Home'];
	$habits = ['I eat late at night','I dont sleep enough','I like sweets','I love soft drinks', 'I consume a lot of salt', 'None of the above'];
	$activity = ['Barely Active', '1-2 times a week', '3-5 times', '5-7 times', 'More than once a day'];
	$sleep = ['5 hours', '5-6 hours', '7-8 hours', 'more than 8 hours'];
	$drink = ['Coffee or tea', 'Less than 2 glasses - 0,5l','2-6 glasses 0,5-1,5 l','More than 6 glasses'];
	$motivation = ['I need motivation', 'I can motivate myself'];
	$behavior = ['Yes','No'];
	$vegetables = ['Broccoli', 'Sweet potato', 'Mushrooms', 'Tomato', 'Peas', 'Spinach', 'Zucchini', 'Pepper'];
	$products = ['Avocado', 'Eggs', 'Yoghurt', 'Cottage cheese', 'Tofu', 'Olives', 'Peanut butter', 'Nuts, Mozzarella', 'Milk'];
	$meat = ['Turkey','Fish','Beef','Chicken','Pork','None'];
	$cooking = ['< 30','30- 60 min','More than one hour'];


	function checkbox_options($array, $name){
		foreach($array as $key => $value){
			echo '<label class="bundled_product_optional_checkbox"><input type="checkbox" class="ddwc-left bundled_product_checkbox" value="'.$value.'" name="'.$name.'[]"><span class="checkmark"></span>'.$value.'</label><br>';
		}
	}

	function radio_options($array, $name){
		foreach($array as $key => $value){
			echo '<div class="box-shadow"><input type="radio" oninput="this.className" id="'.$value.'" name="'.$name.'" value="'.$value.'">';
			echo '<label for="'.$value.'">'.$value.'</label></div>';
		}
	}
?>
<h3 class="text-center m-4">Let's Create<br>Your Body Profile</h3>

<form id="regForm" method="post" action="">
	
	<div class="tab"><h4 class="text-center">Select Your Gender</h4><br>
		<?php radio_options($genders, 'gender'); ?>
	</div>
	<div class="tab">
		<h4 class="text-center">Goal</h4>
		<p class="text-center">What is Your Primary Goal?</p>
		<?php radio_options($goal, 'goal'); ?>
	</div>
	<div id="desiredWeight" class="tab text-center">
		<h4 class="text-center">Desired Weight</h4>
		<p class="text-center">What is the Ideal Weight You Want to Reach?</p>
		<div style="display:inline-block"><input type="text" name="desired_weight"><kg>kg</kg></div>
	</div>
	<div class="tab">
		<h4 class="text-center">Body Measurements</h4>
		<p class="text-center">What About Your Current Height and Weight?</p>
		<select name="height">
			<?php
	for($i = 130; $i < 230; $i++){
		echo '<option value="'.$i.'">'. $i .' cm'.'</options>';
	}
			?>
		</select>
		<select name="weight">
			<?php
	for($i = 30; $i < 200; $i++){
		echo '<option value="'.$i.'">'. $i .' kg'.'</options>';
	}
			?>
		</select>
	</div>
	<div id="age" class="tab text-center">
		<h4 class="text-center">Age</h4>
		<p class="text-center">What is Your Age?</p>
		<div style="display:inline-block"><input width=300 type="text" name="age"></div>
	</div>
	<div class="tab">
		<h4 class="text-center">Body Type</h4>
		<p class="text-center">Various body types have their own specific metabolism properties.</p>
		<?php radio_options($body_type, 'body_type'); ?>
	</div>
	<div class="tab">
		<h4 class="text-center">Habits</h4>
		<p class="text-center">Select the Habits that are True for You.</p>
		<?php checkbox_options($habits, 'habits');?>
	</div>
	<div class="tab">
		<h4 class="text-center">Typical Day</h4>
		<p class="text-center">Please Describe Your Typical Day</p>
		<?php radio_options($typical_day, 'typical_day'); ?>
	</div>
	<div class="tab">
		<h4 class="text-center">Fitness</h4>
		<p class="text-center">How Physically Active are You?</p>
		<?php radio_options($activity, 'activity'); ?>
	</div>
	<div class="tab">
		<h4 class="text-center">Sleep</h4>
		<p class="text-center">How Much Do You Normally Sleep?</p>
		<?php radio_options($sleep, 'sleep'); ?>
	</div>
	<div class="tab">
		<h4 class="text-center">Water Intake</h4>
		<p class="text-center">How Much Water Do You Drink Daily?</p>
		<?php radio_options($drink, 'drink'); ?>
	</div>
	<div class="tab">
		<h4 class="text-center">Motivation</h4>
		<p class="text-center">Do You Relate to the Statement Below?</p>
		<p class="text-center grey">I often require external motivation to keep going. I can easily give up when I feel stressed</p>
		<?php radio_options($motivation, 'motivation'); ?>
	</div>
	<div class="tab">
		<h4 class="text-center">Behavior</h4>
		<p class="text-center">Do You Relate to the Statement Below?</p>
		<p class="text-center grey">I’m afraid I won’t have time to do the other things I love because I’ll be so busy exercising and planning meals</p>
		<?php radio_options($behavior, 'behavior'); ?>
	</div>
	<div class="tab">
		<h4 class="text-center">Vegetables</h4>
		<p class="text-center grey">You're almost there! Now let's customize your plan for good</p>
		<p class="text-center">Mark the Vegetables You Want to Include in Your Meal Plan.</p>
		<?php checkbox_options($vegetables, 'vegetables');?>
	</div>
	<div class="tab">
		<h4 class="text-center">Products</h4>
		<p class="text-center">Mark the Products You Want to Include in Your Meal Plan.</p>
		<?php checkbox_options($products, 'products');?>
	</div>
	<div class="tab">
		<h4 class="text-center">Meat</h4>
		<p class="text-center">Mark the Kinds of Meat You Want to Include in Your Meal Plan.</p>
		<?php checkbox_options($meat, 'meat');?>
	</div>
	<div class="tab">
		<h4 class="text-center">Cooking</h4>
		<p class="text-center">One Last Thing. How Much Time You Have for Meal Preparation Every Day?</p>
		<?php radio_options($cooking, 'cooking');?>
	</div>
	<div class="tab">
		<h3 class="text-center">Thank you</h3>
		<p class="text-center">Congratulations, You have arrived to the end. Press Submit to and lets get started</p>
	</div>
	<div id="error-message"></div>
	<div style="overflow:auto;" class="p-3">
		<div style="float:right;">
			<button class="btn-empty button" type="button" id="prevBtn" onclick="nextPrev(-1)">Previous</button>
			<button class="btn-secondary button" type="button" id="nextBtn" onclick="nextPrev(1)">Next</button>
			<button class="btn-secondary button" type="submit" id="submitBtn">Submit</button>
		</div>
	</div>
	<!-- Circles which indicates the steps of the form: -->
	<div style="text-align:center;margin-top:40px;">
		<span class="step"></span>
		<span class="step"></span>
		<span class="step"></span>
		<span class="step"></span>
		<span class="step"></span>
		<span class="step"></span>
		<span class="step"></span>
		<span class="step"></span>
		<span class="step"></span>
		<span class="step"></span>
		<span class="step"></span>
		<span class="step"></span>
		<span class="step"></span>
		<span class="step"></span>
		<span class="step"></span>
		<span class="step"></span>
		<span class="step"></span>
	</div>
</form>
<script>
    var currentTab = 0; // Current tab is set to be the first tab (0)
    showTab(currentTab); // Display the current tab

    function showTab(n) {
        // This function will display the specified tab of the form...
        var x = document.getElementsByClassName("tab");
        x[n].style.display = "block";
        //... and fix the Previous/Next buttons:
        if (n == 0) {
            document.getElementById("prevBtn").style.display = "none";
        } else {
            document.getElementById("prevBtn").style.display = "inline";
        }
        if (n == (x.length - 1)) {
            document.getElementById("nextBtn").style.display = "none";
            document.getElementById("submitBtn").style.display = "inline";
        } else {
            document.getElementById("nextBtn").innerHTML = "Next";
            document.getElementById("nextBtn").style.display = "inline";
            document.getElementById("submitBtn").style.display = "none";
        }
        //... and run a function that will display the correct step indicator:
        fixStepIndicator(n)
    }

    function nextPrev(n) {
        // This function will figure out which tab to display
        var x = document.getElementsByClassName("tab");
        // Exit the function if any field in the current tab is invalid:
        if (n == 1 && !validateForm()) return false;
        // Hide the current tab:
        x[currentTab].style.display = "none";
        // Increase or decrease the current tab by 1:
        currentTab = currentTab + n;
        // Otherwise, display the correct tab:
        showTab(currentTab);
    }

    function validateForm() {
        // This function deals with validation of the form fields
        var x, y, i, valid = true;
        x = document.getElementsByClassName("tab");
        y = x[currentTab].getElementsByTagName("input");
        // A loop that checks every input field in the current tab:
        var unchecked = 0;
        for (i = 0; i < y.length; i++) {
            if (y[i].checked == false && (y[i].type =='radio' || y[i].type =='checkbox')){
                unchecked++;
            }
            // If a field is empty...
            if (y[i].value == "") {
                // add an "invalid" class to the field:
                y[i].className += " invalid";
                // and set the current valid status to false
                valid = false;
            }

        }

        var error = document.getElementById("error-message");
        if (y[0]){
            if (unchecked == y.length){
                valid = false;
                error.innerHTML = '<ul class="woocommerce-error">You have to pick one option</ul></ul>';
            }
        }
        // If the valid status is true, mark the step as finished and valid:
        if (valid) {
            document.getElementsByClassName("step")[currentTab].className += " finish";
            error.innerHTML = "";
        }
        return valid; // return the valid status
    }

    function fixStepIndicator(n) {
        // This function removes the "active" class of all steps...
        var i, x = document.getElementsByClassName("step");
        for (i = 0; i < x.length; i++) {
            x[i].className = x[i].className.replace(" active", "");
        }
    }
</script>
<?php

}   

?>
var cancellation = document.getElementById("cancellation");

function changeSelect(item){
	item.form.submit()
}

function confirm(){
	document.getElementById("confirmation").style.display = "initial";
	document.getElementById("cancel").style.display = "none";
}
	
function revert(){
	document.getElementById("cancel").style.display = "initial";
	document.getElementById("confirmation").style.display = "none";
}

function deactivateNutritionist(){
	document.getElementById("deactivateForm").submit();
	console.log("deactivateForm");
}

function dissapear(){
	document.getElementById("submitRatings").style.display = "inline-block";
}

function disappear(item){
    var parent = item.parentElement.parentElement.parentElement;
    var recipes = parent.getElementsByClassName("white-container");
	var y = item.getBoundingClientRect().bottom + window.scrollY - 140;
	a=0;
    for(i=0;i<recipes.length;i++){
        var recipe = recipes[i];
        if(recipe.classList.contains("meal-"+item.id) && a<1){
			a++;
            if(recipe.classList.contains("disappear")){
                recipe.classList += " appear";
                recipe.classList.remove("disappear");
				window.scroll({
					top: y,
					behavior: 'smooth'
				});
            } else {
                recipe.classList.remove("appear");
                recipe.classList += " disappear";
            }
        } else {
            if(recipe.classList.contains("appear")){
               recipe.classList.remove("appear");
                recipe.classList += " disappear";
            }
        }
    }
}

const scrollButtons = document.querySelectorAll('.scrollbtn')
function moveMeals(){
	var container = this.nextSibling;
	var child = container.firstElementChild
	container.scrollTo({
		left: container.scrollLeft + child.offsetWidth + 10,
		behavior:'smooth'
	});
}
scrollButtons.forEach(l=> l.addEventListener('click', moveMeals))

const previewWrapper = document.querySelectorAll('.preview-wrapper')
function showScrollButton(){
	var button = this.getElementsByClassName("scrollbtn")[0];
	if (button){
		button.style.opacity = 1;
		button.style.transition = '0.3s';
	} 
}
previewWrapper.forEach(l=> l.addEventListener('mouseover', showScrollButton))

function removeScrollButton(){
	var button = this.getElementsByClassName("scrollbtn")[0];
	if (button) {
		button.style.opacity = 0;
		button.style.transition = '0.3s';
	}
}
previewWrapper.forEach(l=> l.addEventListener('mouseout', removeScrollButton))

jQuery(function($){
	$('.nutri-graph').easyPieChart({
		animate:{
			duration:1000,
			enabled:true
		},
		barColor:'#40ace1',
		trackColor:'#ecf0f5',
		scaleColor:false,
		lineWidth:8,
		lineCap:'round',
		size:200
	});
	
	$('.kcal').easyPieChart({
		animate:{
			duration:1000,
			enabled:true
		},
		barColor:'#131C28',
		trackColor:'#ecf0f5',
		scaleColor:false,
		lineWidth:4,
		lineCap:'round',
		size:70
	});

	$('.protein').easyPieChart({
		animate:{
			duration:1000,
			enabled:true
		},
		barColor:'#41ace0',
		trackColor:'#ecf0f5',
		scaleColor:false,
		lineWidth:4,
		lineCap:'round',
		size:70
	});
	
	$('.carbs').easyPieChart({
		animate:{
			duration:1000,
			enabled:true
		},
		barColor:'#5f5f5f',
		trackColor:'#ecf0f5',
		scaleColor:false,
		lineWidth:4,
		lineCap:'round',
		size:70
	});
	
	$('.fats').easyPieChart({
		animate:{
			duration:1000,
			enabled:true
		},
		barColor:'#e35d5d',
		trackColor:'#ecf0f5',
		scaleColor:false,
		lineWidth:4,
		lineCap:'round',
		size:70
	});
	
	$('.fiber').easyPieChart({
		animate:{
			duration:1000,
			enabled:true
		},
		barColor:'#91e094',
		trackColor:'#ecf0f5',
		scaleColor:false,
		lineWidth:4,
		lineCap:'round',
		size:70
	});
});
$(document).ready(function(){
	const assistant = document.getElementsByClassName("assistant")[0]
	const typing = document.getElementsByClassName("typing")[0];
	const chat = document.getElementsByClassName("chat")[0];
	const chatList = document.getElementsByClassName("chat list")[0];
	const chatLink = document.getElementsByClassName("chat link")[0];
	if (assistant){
		if(assistant.classList.contains("disappear")){
			assistant.classList.add("chat-appear")
			assistant.classList.remove("disappear")
			setTimeout(function(){
				typing.classList.add("disappear");
				chat.classList.add("chat-appear");
				chat.classList.remove("disappear");
				setTimeout(function(){
					chatList.classList.add("chat-appear");
					chatList.classList.remove("disappear");
					setTimeout(function(){
						chatLink.classList.add("chat-appear");
						chatLink.classList.remove("disappear");
					},1000)
				},1000)
			},2000)
		} 
	}
	
	const mealList = document.getElementById("meal-list")
	const mealToggle = document.getElementsByClassName("meal-toggle")[0];
	if(mealList){
		const addToList = document.querySelectorAll(".add-to-list")

		addToList.forEach(l=> l.addEventListener('click', function(){
			var mealVal = '<input name="meals[]" value="'+this.parentElement.id+'">';
			var mealCounter = mealToggle.getElementsByClassName("meal-counter")[0];
			mealCounter.innerText = Number(mealCounter.innerText) + 1;
			mealList.innerHTML = mealList.innerHTML + mealVal;
		}))
	}
	
	if (mealToggle){
		mealToggle.addEventListener('click',function(){
			var itemsInList = mealToggle.previousElementSibling.getElementsByTagName("input")
			if(itemsInList.length){
				this.previousElementSibling.firstElementChild.submit();
			} 
		})	
	}
	
	var nutriHelp = document.getElementById("nutri-help");
	if (nutriHelp){
		var help = document.getElementById("help");
		nutriHelp.addEventListener('click',function(){
			if (help.classList.contains("disappear")){
				help.classList.add("appear");
				help.classList.remove("disappear");
			} else {
				help.classList.add("disappear");
				help.classList.remove("appear");
			}
		})
	}
});

function changeMeal(item){
	//Change Image
	var mealImg = item.previousElementSibling;
	mealImg.src = item.selectedOptions[0].attributes[1].value;
	
	//Change Sum Calories
    var selects = item.parentElement.parentElement.getElementsByTagName("select");
    var mealCalories = '';
    for(i=0;i<selects.length;i++){
    var calories = Number(selects[i].selectedOptions[0].attributes[2].value);
	var oldCals = Number(mealCalories);
    mealCalories = calories + oldCals;
    }
    var dayCalories = item.parentElement.parentElement.previousElementSibling.lastElementChild.firstElementChild
    dayCalories.lastElementChild.innerHTML = Math.round(mealCalories);
}


const activeCategory = document.getElementsByClassName("curr-cat active")[0];
	if (activeCategory){
		const category = activeCategory.parentElement.parentElement.parentElement.parentElement.parentElement;
		category.scrollTo({
			left:category.scrollLeft + activeCategory.offsetLeft - 20
		})
	}

$(document).ready(function(){
	const settings = document.getElementById("dash-settings");
	if(settings){
		const settingsToggle = settings.firstElementChild;
		settingsToggle.addEventListener('click',function(){
			const dashSettings = settings.getElementsByClassName("dash-settings")[0]
			if (dashSettings.classList.contains("disappear")){
				dashSettings.classList.remove("disappear")
				dashSettings.classList.add("appear")
			} else {
				dashSettings.classList.remove("appear")
				dashSettings.classList.add("disappear")
			}

		})
	}
});



$(document).ready(function() {	
				const showMenu = (toggleId, navbarId, mainId)=>{
					const toggle = document.getElementById(toggleId),
						  navbar = document.getElementById(navbarId),
						  bodypadding = document.getElementById(mainId),
						  footerpadding = document.getElementsByClassName('footer-bottom')[0]

					if(toggle && navbar){
						toggle.addEventListener('click', ()=>{
							if(window.innerWidth < 776){
								navbar.classList.toggle('expander')
								navbar.classList.toggle('mobile')
							} else {
								navbar.classList.toggle('expander')
								

								bodypadding.classList.toggle('body-pd')
								footerpadding.classList.toggle('body-pd')
							}
							
						})
					}
				}
				showMenu('nav-toggle','navbar','mainContent')

				var mobileToggle = document.getElementById("nav-toggle-mobile");

				if(mobileToggle && navbar){
					mobileToggle.addEventListener('click', ()=>{
						navbar.classList.toggle('expander')
						navbar.classList.toggle('mobile')
					}
				)}

			});

			/*===== LINK ACTIVE  =====*/ 
			const linkColor = document.querySelectorAll('.nav__link')
			function colorLink(){
				linkColor.forEach(l=> l.classList.remove('active'))
				this.classList.add('active')
			}
			linkColor.forEach(l=> l.addEventListener('click', colorLink))
			
			/*===== CURRENT ACTIVE  =====*/ 
			var locationR = window.location.href.split('/');
			var currentLocation = locationR[locationR.length - 2];
			linkColor.forEach(l=> l.classList.remove('active'))
			
			var guidelinks = document.getElementsByClassName("nav__list")[0].getElementsByTagName("a");
			
			for(g=0;g<guidelinks.length;g++){
				var guidelinkR = guidelinks[g].href.split('/')
				var guidelink = guidelinkR[guidelinkR.length-1];
				var linkElement = guidelinks[g].classList;
				
				if(! locationR.includes("profile")){
					if(guidelinkR.includes("profile") && guidelinkR.length>4 && ! guidelinkR.includes("schedule")){
						if(linkElement.contains('n_collapse__sublink')){
							guidelinks[g].parentElement.parentElement.style.display = "none";
						} else {
							guidelinks[g].style.display = "none";
						}
					}
				}
		
				if (currentLocation == guidelink){
					if(linkElement.contains('n_collapse__sublink')){
						guidelinks[g].parentElement.parentElement.classList.add('active');
					} else {
						linkElement.add('active');
					}
				}
			}
			
			


			/*===== COLLAPSE MENU  =====*/ 
			const linkCollapse = document.getElementsByClassName('nav__link')
			var i

			for(i=0;i<linkCollapse.length;i++){
				linkCollapse[i].addEventListener('click', function(){
					const collapseMenu = this.getElementsByClassName("n_collapse__menu")[0];
					collapseMenu.classList.toggle('showCollapse')

					if(! navbar.classList.value.includes('expander')){
						bodypadding = document.getElementById('mainContent')
						navbar.classList.toggle('expander')
						bodypadding.classList.toggle('body-pd')
					}

					const rotate = collapseMenu.getElementsByClassName("n_collapse__link")[0];
					rotate.classList.toggle('rotate')
				})
			}




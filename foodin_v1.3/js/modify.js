//Add posts from frontend with REST API
var portfolioPostsButton = document.getElementById("portfolio-posts-btn");
var portfolioPostsContainer = document.getElementById("portfolio-posts-container");

if(portfolioPostsButton){
	portfolioPostsButton.addEventListener("click", function(){
        var ourRequest = new XMLHttpRequest();
        ourRequest.open('GET', magicalData.siteURL + '/wp-json/wc/v3?_jsonp=callback');
        ourRequest.onload = function() {
            if (ourRequest.status >= 200 && ourRequest.status < 400) {
                var data = JSON.parse(ourRequest.responseText);
				console.log(data);
            } else {
                console.log("We connected to the server, but it returned on error.")
            }
        }

        ourRequest.onerror = function (){
            console.log("Connection error");
        }

        ourRequest.send();
	})
}

function createHTML(postsData){
	var ourHTMLString = '';
	for(i=0;i<postsData.length;i++){
		ourHTMLString += '<h2>' + postsData[i].title.rendered + '</h2>';
		ourHTMLString += postsData[i].content.rendered;
	}
	portfolioPostsContainer.innerHTML = ourHTMLString;
}

//Quick add post AJAX

var quickAddBtn = document.getElementById("quick-add-button");
if(quickAddBtn){
	quickAddBtn.addEventListener("click",function(){
        var title = document.querySelector('.admin-quick-add [name="title"]');
        var content = document.querySelector('.admin-quick-add [name="content"]');
        if (title.value == '' || content.value == ''){
            alert("Error - Try again");
        } else {
            var ourPostData = {
                "title": title.value,
                "content": content.value,
                "status": "publish"
            }
            var createPost = new XMLHttpRequest();
            createPost.open("POST",magicalData.siteURL + "/wp-json/wp/v2/posts");
            createPost.setRequestHeader("X-WP-Nonce", magicalData.nonce);
            createPost.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
            createPost.send(JSON.stringify(ourPostData));
            createPost.onreadystatechange = function(){
                if(createPost.readyState == 4){
                    if(createPost.status == 201){
                        title.value = '';
                        content.value = '';
                        portfolioPostsContainer.innerHTML += '<h2>' + ourPostData.title + '</h2>';
                        portfolioPostsContainer.innerHTML += ourPostData.content;
                    } else {
                        alert("Error - Try again")
                    }
                }
            }
        }
    })
}

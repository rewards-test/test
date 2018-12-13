<!doctype html>
<html lang="en">

	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width,minimum-scale=1,initial-scale=1">
		<link rel="shortcut icon" href="">

		<title>Rewards Gateway Test</title>

		<style>

			body {
				font-family: -apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue","Fira Sans",Ubuntu,Oxygen,"Oxygen Sans",Cantarell,"Droid Sans","Apple Color Emoji","Segoe UI Emoji","Segoe UI Emoji","Segoe UI Symbol","Lucida Grande",Helvetica,Arial,sans-serif;
			}

			a {
				text-decoration: none;
				color: #0073b1;
			}

			ul {
				border: 1px solid black;
			}

			ul li {
				list-style: none;
				margin: 20px;
			}

			.img-wrapper {
				width: 56px;
    			height: 56px;
    			float: 	left;
    			margin-right: 20px;
			}

			.img-wrapper img {
				max-width: 100%;
			}

			.person-info {
				border-bottom: 1px solid gray;
				padding-bottom: 20px;
			}

			.name {
				font-size: 1.2em;
			}
		</style>
	</head>

	<body>
		
		<button type="button" class="btn">Search me!</button>
		
		<ul id="searchResults" class="results-list">
			Search results to show up here
		</ul>


	</body>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script>

		$(document).ready(function() {

		    $('.btn').click(function() {
  				$('#searchResults').text('loading . . .');

  				//strip html tags from the json - mainly <pre> in the bio section
  				//TO DO - if raw data has script tags - remove them altogether with the scripts, xss. if using the proxy.php - better be done there (faster)
  				function strip(html) {
					var tmp = document.createElement("DIV");
					tmp.innerHTML = html;
					return tmp.textContent || tmp.innerText;
				}

				function replaceImg(url, result, item) {
					if (result == "success") {
						$(item).attr("src", url);
					} else {
						//we keep HELLO there
					}
				}   

				//testImage taken from stack overflow - https://stackoverflow.com/questions/9714525/javascript-image-url-verify
				//no time to write it properly, modified a bit, could be done better, sorry
				function testImage(url, callback, item, timeout) {
				    timeout = timeout || 5000;
				    var timedOut = false, timer;
				    var img = new Image();
				    img.onerror = img.onabort = function() {
				        if (!timedOut) {
				            clearTimeout(timer);
				            callback(url, "error");
				        }
				    };
				    img.onload = function() {
				        if (!timedOut) {
				            clearTimeout(timer);
				            callback(url, "success", item);
				        }
				    };
				    img.src = url;
				    timer = setTimeout(function() {
				        timedOut = true;
				        // reset .src to invalid URL so it stops previous
				        // loading, but doesn't trigger new load
				        img.src = "//!!!!/test.jpg";
				        callback(url, "timeout");
				    }, timeout); 
				}

				//check if the avatar actually returns something useful that resembles and image
				function imgReplace() {
					$( "#searchResults img" ).each(function(index) {
						var $this 	= $(this);
						var $src 	= $this.data("src");
						testImage($src, replaceImg, $this);
					});
				}

  				$.ajax({
                    url: 'proxy.php',
                    dataType: 'json',
                    success: function(data){
                        var resultsHtml = '';
                        var defaultAvatar = 'http://quickaskips.com/wp-content/uploads/2018/04/funny-missing-person-poster-best-ampquotlostampquot-sign-yet-best-ideas-of-funny-missing-person-poster-of-funny-missing-person-poster.jpg';
                        //var len = data.length; 
                        //TODO - PAGINATION
                        var len = 10;
                        for(var i=0; i<len; i++) {
                        	listItem = data[i];

                        	var titleEl;
                        	var bioEl;

                        	titleEl = '<div class="title">';
                        	//if job title or comany is missing, still show info that makes semantic sense
                        	if (listItem.title) {
                        		titleEl += strip(listItem.title);
                        		if (listItem.company) {
                        			titleEl += ' at ' + strip(listItem.company);
                        		} 
                        	} else {
                        		if (listItem.company) {
                        			titleEl += 'Works at ' + strip(listItem.company);
                        		} 
                        	}
                        	titleEl += '</div>';

                        	if (listItem.bio && listItem.bio != "0") {
                        		bioEl = '<div class="bio">' + strip(listItem.bio) + '</div>';
                        	}
                            
                            resultsHtml += 	'<li class="search-result" id="' +listItem.uuid +'">';
                            resultsHtml += 	'	<div class="search-result__wrapper">';
                            resultsHtml += 	'		<div class="img-wrapper">';
                            resultsHtml += 	'			<img data-src="' + listItem.avatar + '" src="'+ defaultAvatar +'" />';
                            resultsHtml += 	'		</div>';
                            resultsHtml += 	'		<div class="person-info">';
                            resultsHtml += 	'			<div class="name"><a href="#">' + strip(listItem.name) + '</a></div>';
                            resultsHtml += 	titleEl; 	// <div class="title">Technician at Johnson</div>
                            resultsHtml += 	bioEl;		// <div class="bio">Sit ipsum...</div>
                            resultsHtml += 	'		</div>';
                            resultsHtml += 	'	</div>';
                            resultsHtml += 	'</li>';
                        }

                        $('#searchResults').html(resultsHtml);

                        imgReplace(); //check if the image placeholder can be replaced with an actual image
                    }
                });
			}); 
		});

		
	</script>
</html>




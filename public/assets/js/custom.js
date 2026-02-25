(function($) {

	$(document).ready(function() {
	  $('body').addClass('js');
	  var $menu = $('#menu'),
	    $menulink = $('.menu-link');
	  
	$menulink.click(function() {
	  $menulink.toggleClass('active');
	  $menu.toggleClass('active');
	  return false;
	});});


	videoPopup();


	$('.owl-carousel').owlCarousel({
	    loop:true,
	    margin:30,
	    nav:true,
	    autoplay:true,
		autoplayTimeout:5000,
		autoplayHoverPause:true,
	    responsive:{
	        0:{
	            items:1
	        },
	        550:{
	            items:2
	        },
	        750:{
	            items:3
	        },
	        1000:{
	            items:4
	        },
	        1200:{
	            items:5
	        }
	    }
	})


	$(".Modern-Slider").slick({
	    autoplay:true,
	    autoplaySpeed:10000,
	    speed:600,
	    slidesToShow:1,
	    slidesToScroll:1,
	    pauseOnHover:false,
	    dots:true,
	    pauseOnDotsHover:true,
	    cssEase:'fade',
	   // fade:true,
	    draggable:false,
	    prevArrow:'<button class="PrevArrow"></button>',
	    nextArrow:'<button class="NextArrow"></button>', 
	});


	$("div.features-post").on("click", function () {
		// Close all other content-hide sections
		$("div.content-hide").not($(this).find("div.content-hide")).slideUp("medium");
	
		// Toggle the clicked one
		$(this).find("div.content-hide").slideToggle("medium");
	});
	
	$(document).ready(function () {
		// Event listener for submenu links
		$('.sub-menu a').on('click', function (e) {
			e.preventDefault(); // Prevent default anchor behavior (e.g., scrolling)
	
			const targetId = $(this).attr('href'); // Get the target section ID
			const targetSection = $(targetId); // Find the corresponding section
	
			// Hide all content sections
			$('.content-section').hide();
	
			// Show the specific section
			targetSection.fadeIn("medium");
		});
	});	

	$(document).ready(function() {
        // Add a click event listener for the login link
        $('#login-link').on('click', function(event) {
            event.preventDefault(); // Prevent default behavior of the link
            window.location.href = '/login'; // Redirect to the login page
        });

        // Add a click event listener for the register link
        $('#register-link').on('click', function(event) {
            event.preventDefault(); // Prevent default behavior of the link
            window.location.href = '/register'; // Redirect to the register page
        });
    });
	
	$( "#tabs" ).tabs();


	(function init() {
	  function getTimeRemaining(endtime) {
	    var t = Date.parse(endtime) - Date.parse(new Date());
	    var seconds = Math.floor((t / 1000) % 60);
	    var minutes = Math.floor((t / 1000 / 60) % 60);
	    var hours = Math.floor((t / (1000 * 60 * 60)) % 24);
	    var days = Math.floor(t / (1000 * 60 * 60 * 24));
	    return {
	      'total': t,
	      'days': days,
	      'hours': hours,
	      'minutes': minutes,
	      'seconds': seconds
	    };
	  }
	  
	  function initializeClock(endtime){
	  var timeinterval = setInterval(function(){
	    var t = getTimeRemaining(endtime);
	    document.querySelector(".days > .value").innerText=t.days;
	    document.querySelector(".hours > .value").innerText=t.hours;
	    document.querySelector(".minutes > .value").innerText=t.minutes;
	    document.querySelector(".seconds > .value").innerText=t.seconds;
	    if(t.total<=0){
	      clearInterval(timeinterval);
	    }
	  },1000);
	}
	initializeClock(((new Date()).getFullYear()+1) + "/1/1")
	})()

  // Get the modal and the "Contacts" link
	var modal = document.getElementById("contactModal");
	var contactLink = document.getElementById("contact-link");
	var closeModal = document.getElementById("closeModal");

	// When the user clicks the "Contacts" link, show the modal
	contactLink.onclick = function(event) {
		event.preventDefault(); // Prevents default action (link navigation)
		modal.style.display = "block";
	}

	// When the user clicks the close button, close the modal
	closeModal.onclick = function() {
		modal.style.display = "none";
	}

	// When the user clicks outside of the modal, close it
	window.onclick = function(event) {
		if (event.target == modal) {
			modal.style.display = "none";
		}
	}

})
(jQuery);


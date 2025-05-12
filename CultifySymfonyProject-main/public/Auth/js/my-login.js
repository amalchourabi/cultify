// Background Slideshow
const images = [
    'Auth/img/image1.jpg',
    'Auth/img/image2.jpg',
    'Auth/img/image3.jpg',
    'Auth/img/image4.jpg',
    'Auth/img/image5.jpg',
    'Auth/img/image6.jpg',
    'Auth/img/image7.jpg',
    'Auth/img/image8.jpg'
];

const background = document.querySelector('.background-slideshow');
let currentIndex = 0;

function changeBackground() {
    background.style.backgroundImage = `url('${images[currentIndex]}')`;
    background.style.opacity = 1;

    currentIndex = (currentIndex + 1) % images.length; // Move to the next image

    setTimeout(() => {
        background.style.opacity = 0; // Fade out the current image
    }, 5000); // Adjust timing as needed
}

// Change the background every 6 seconds (5000ms fade + 1000ms delay)
setInterval(changeBackground, 6000);

// Initialize the first background
background.style.backgroundImage = `url('${images[currentIndex]}')`;
background.style.opacity = 1;
currentIndex = (currentIndex + 1) % images.length;

// Login Page Functionality
$(function() {
    // Password toggle
    $("input[type='password'][data-eye]").each(function(i) {
        var $this = $(this),
            id = 'eye-password-' + i,
            el = $('#' + id);

        $this.wrap($("<div/>", {
            style: 'position:relative',
            id: id
        }));

        $this.css({
            paddingRight: 60
        });
        $this.after($("<div/>", {
            html: '<i class="fas fa-eye"></i>', // Use FontAwesome eye icon
            class: 'btn btn-primary btn-sm passeye-toggle',
            id: 'passeye-toggle-'+i,
        }).css({
            position: 'absolute',
            right: 10,
            top: ($this.outerHeight() / 2) - 12,
            padding: '2px 7px',
            fontSize: 12,
            cursor: 'pointer',
        }));

        $this.after($("<input/>", {
            type: 'hidden',
            id: 'passeye-' + i
        }));

        var invalid_feedback = $this.parent().parent().find('.invalid-feedback');

        if(invalid_feedback.length) {
            $this.after(invalid_feedback.clone());
        }

        $this.on("keyup paste", function() {
            $("#passeye-"+i).val($(this).val());
        });
        $("#passeye-toggle-"+i).on("click", function() {
            if($this.hasClass("show")) {
                $this.attr('type', 'password');
                $this.removeClass("show");
                $(this).find('i').removeClass("fa-eye-slash").addClass("fa-eye");
            } else {
                $this.attr('type', 'text');
                $this.val($("#passeye-"+i).val());                
                $this.addClass("show");
                $(this).find('i').removeClass("fa-eye").addClass("fa-eye-slash");
            }
        });
    });

    // Form validation
    $(".my-login-validation").submit(function() {
        var form = $(this);
        if (form[0].checkValidity() === false) {
            event.preventDefault();
            event.stopPropagation();
        }
        form.addClass('was-validated');
    });
});
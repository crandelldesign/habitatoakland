export default {
  init() {
    // JavaScript to be fired on all pages

    var w = window,
        d = document,
        e = d.documentElement,
        g = d.getElementsByTagName('body')[0],
        x = w.innerWidth || e.clientWidth || g.clientWidth;

    $('nav.main-nav .menu-item-has-children').hover(function() {
      if (x >= 960) {
        //$(this).addClass('show');
        setTimeout( () => {
          $(this).addClass('show');
        }, 500);
      }
    }, function() {
      if (x >= 960) {
        //$(this).removeClass('show');
        setTimeout( () => {
          $(this).removeClass('show');
        }, 500);
      }
    });

    $('.navbar-toggler').click( function() {
      //$(this).toggleClass('collapsed');
      $('nav.main-nav').toggleClass('navbar-open');
    });

    // Add/Remove Class on Scroll
    let scrollpos = window.scrollY;
    let header = document.getElementById('main-nav');

    function add_class_on_scroll() {
      header.classList.add('simple');
    }

    function remove_class_on_scroll() {
      header.classList.remove('simple');
    }

    window.addEventListener('scroll', function() {
      scrollpos = window.scrollY;
      if (scrollpos > 69) {
          add_class_on_scroll();
      }
      else {
          remove_class_on_scroll();
      }
    });

    // Header Search
    $('.header-search span.icon').click( function() {
      $('.header-search #search').focus();
    });

    $('.scrollTo a[href^=\'#\'], a.scrollTo[href^=\'#\']').click( function(event) {
      // Make sure this.hash has a value before overriding default behavior
      if (this.hash !== '') {
        // Prevent default anchor click behavior
        event.preventDefault();

        // Store hash
        var hash = this.hash;

        // Using jQuery's animate() method to add smooth page scroll
        // The optional number (800) specifies the number of milliseconds it takes to scroll to the specified area
        $('html, body').animate({
          scrollTop: $(hash).offset().top - 100,
        }, 800, function(){

          // Add hash (#) to URL when done scrolling (default click behavior)
          window.location.hash = hash;
        });
      } // End if
    });

    // Custom Give WP Form Content
    var homebuyerSavingsDeposit = $('#give-form-3546-1, #give-form-6511-1');
    if (homebuyerSavingsDeposit.length > 0) {
      homebuyerSavingsDeposit.find('#give_cc_fields legend').text('Debit Card Info');
      homebuyerSavingsDeposit.find('.give-recurring-donors-choice label').html(function(index, html) {
        return html.replace('donation', 'deposit');
      });
      homebuyerSavingsDeposit.find('.give-donation-total-label').text('Deposit Total');
    }
    
  },
  finalize() {
    // JavaScript to be fired on all pages, after page specific JS is fired
  },
};

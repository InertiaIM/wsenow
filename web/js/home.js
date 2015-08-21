$(function() {
    var width = 1020;
    
    // initialize the slider functionality
    var count = $('#banner .slide').length;
    if(count < 4) {
        $('#banner .slide').each(function(index, slide) {
            $(slide).clone().css('left', ($('#banner .slide:last').position().left + width) + 'px').appendTo('#banner');
        });
    }
    $('#banner .slide:last').css('left', ($('#banner .slide:first').position().left - width) + 'px').prependTo('#banner');
    
    
    // set up event handlers
    $('#banner .slide').mouseenter(function(e) {
        if((e.relatedTarget == undefined || (e.relatedTarget.id != 'nav-left' && e.relatedTarget.id != 'nav-right')))
//           (e.fromElement == undefined || (e.fromElement.id != 'nav-left' && e.fromElement.id != 'nav-right')))
        {
            $('#banner .slide .slide-overlay').stop(true);
            $('#banner').stop(true);
            $('#banner-filter #filter-center').stop(true, true).fadeIn();
        }
    });
    
    $('#banner .slide').mouseleave(function(e) {
        if((e.relatedTarget.id != 'nav-left' && e.relatedTarget.id != 'nav-right')) 
//           (e.toElement.id != 'nav-left' && e.toElement.id != 'nav-right'))
        {
            cycle_left_auto();
            $('#banner-filter #filter-center').stop(true, true).fadeOut();
        }
    });
    
    $('#banner-filter #filter-center').mouseenter(function(e) {
        if((e.relatedTarget.parentElement.className != 'slide'))
//           (e.fromElement.parentElement.className != 'slide'))
        {
            $('#banner .slide .slide-overlay').stop(true);
            $('#banner').stop(true);
            $('#banner-filter #filter-center').stop(true, true).fadeIn();
        }
    });
    
    $('#banner-filter #filter-center').mouseleave(function(e) {
        if((e.relatedTarget.parentElement.className != 'slide'))
//           (e.toElement.parentElement.className != 'slide'))
        {
            cycle_left_auto();
            $('#banner-filter #filter-center').stop(true, true).fadeOut();
        }
    });
    
    $('#banner-filter #filter-center #nav-left').click(function(e) {
        e.preventDefault();
        cycle_right();
    });
    
    $('#banner-filter #filter-center #nav-right').click(function(e) {
        e.preventDefault();
        cycle_left();
    });
    
    $('#banner .slide').click(function(e){
        e.preventDefault();
        var href = $(this).find('a.learn-more').attr('href');
        location.href = href;
    });
    
    
    // set the slider in motion
    cycle_left_auto();
    
    
    function cycle_left_auto() {
        var activeOverlay = $('#banner .slide:eq(2) .slide-overlay');
        $(activeOverlay).stop(true, true).fadeIn();
        $(activeOverlay).delay(10000).fadeOut('normal', function() {
            $('#banner').animate({'left': '-=1020'}, 'normal', 'easeOutQuad', function() {
                $('#banner').css('left', '+=1020');
                $('#banner .slide').css('left', '-=1020');
                $('#banner .slide:first').css('left', ($('#banner .slide:last').position().left + width) + 'px').appendTo('#banner');
                cycle_left_auto();
            });
        });
    }
    
    function cycle_left() {
        var currentOverlay = $('#banner .slide:eq(2) .slide-overlay');
        var newOverlay = $('#banner .slide:eq(3) .slide-overlay');
        $(currentOverlay).stop(true, true).hide();
        $('#banner').animate({'left': '-=1020'}, 'normal', 'easeOutQuad', function() {
            $(newOverlay).stop(true, true).fadeIn();
            $('#banner').css('left', '+=1020');
            $('#banner .slide').css('left', '-=1020');
            $('#banner .slide:first').css('left', ($('#banner .slide:last').position().left + width) + 'px').appendTo('#banner');
        });
    }
    
    function cycle_right() {
        var currentOverlay = $('#banner .slide:eq(2) .slide-overlay');
        var newOverlay = $('#banner .slide:eq(1) .slide-overlay');
        $(currentOverlay).stop(true, true).hide();
        $('#banner').animate({'left': '+=1020'}, 'normal', 'easeOutQuad', function() {
            $(newOverlay).stop(true, true).fadeIn();
            $('#banner').css('left', '-=1020');
            $('#banner .slide').css('left', '+=1020');
            $('#banner .slide:last').css('left', ($('#banner .slide:first').position().left - width) + 'px').prependTo('#banner');
        });
    }
});
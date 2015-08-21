$(function() {
    var width = 1020;
    var scrollElement = 'html,body';
    
    // initialize the slider functionality
    if($('#banner .slide').length < 5 && $('#banner .slide').length > 1) {
        $('#banner .slide').each(function(index, slide) {
            $(slide).clone().css('left', ($('#banner .slide:last').position().left + width) + 'px').appendTo('#banner');
        });
    }
    if($('#banner .slide').length < 5 && $('#banner .slide').length > 1) {
        $('#banner .slide').each(function(index, slide) {
            $(slide).clone().css('left', ($('#banner .slide:last').position().left + width) + 'px').appendTo('#banner');
        });
    }
    if($('#banner .slide').length < 5 && $('#banner .slide').length > 1) {
        $('#banner .slide').each(function(index, slide) {
            $(slide).clone().css('left', ($('#banner .slide:last').position().left + width) + 'px').appendTo('#banner');
        });
    }
    
    if($('#banner .slide').length > 1) {
        $('#banner .slide:last').css('left', ($('#banner .slide:first').position().left - width) + 'px').prependTo('#banner');
        $('#banner .slide:last').css('left', ($('#banner .slide:first').position().left - width) + 'px').prependTo('#banner');
    }
    
    
    $('#banner').hide().css({'visibility': 'visible', 'display': 'none;'});
    $('#banner').fadeIn('fast', function() {
        $('#banner .slide:eq(2) .slide-overlay').fadeIn('fast');
    });
    
    
    // initialize the select menus
    $('#select-overlay select').selectmenu();
    
    
    // set up tooltips
    $('a[title]').tooltip({
        effect: 'slide',
        offset: [-26, 60],
        tipClass: 'tooltip_grey'
    });
    
    // we'll need access to the tooltip to manually hide it
    var tooltip1 = $('a#phone').data('tooltip');
    
    // set up overlay
    $('a#phone, a.contact-link').overlay({
        effect: 'apple',
        target: '#contact1',
        top: 154,
        fixed: false,
        mask: {
            color:'#000000',
            loadSpeed:0,
            opacity:0.8
        },
        onBeforeLoad: function(e) {
            tooltip1.hide();
        }
    });
    var overlay = $('a#phone, a.contact-link').data('overlay');
    
    
    // set up labels
    $('label').inFieldLabels();

    
    $('#contact1').on('click', 'a.button2', function(e) {
        e.preventDefault();
        overlay.close();
    });
    
    // set up ajax form handler
    $('#contact1').on('submit', 'form', function(e) {
        e.preventDefault();
        $.ajax({
            type: 'POST',
            url: '/ajax-contact',
            data: $(this).serialize(),
            success: function(data) {
                $('#contact1 form').replaceWith(data);
                $('#contact1 form label').inFieldLabels();
            },
            dataType: 'html'
        });
    });
    
    
    $.address.strict(false);
    $.address.change(function(e) {
        path = e.value;
        if(path != '') {
            $('#experience-select').selectmenu('value', path);
            pullUp(path);
        }
        else
        {
            if($('#banner .slide').length > 1) {
                $.address.value($('#banner .slide:eq(2)').attr('data-slug'));
            }
            else {
                $.address.value($('#banner .slide:eq(0)').attr('data-slug'));
            }
        }
    });
    
    
    // set up event handlers
    $('#select-overlay').mouseenter(function(e) {
        $('#banner-filter #filter-center').stop(true, true).fadeIn();
    });
    
    $('#banner .slide').mouseenter(function(e) {
        if((e.relatedTarget == undefined || (e.relatedTarget.id != 'nav-left' && e.relatedTarget.id != 'nav-right' && e.relatedTarget.id != 'select-overlay')))
//           (e.fromElement == undefined || (e.fromElement.id != 'nav-left' && e.fromElement.id != 'nav-right')))
        {
            $('#banner-filter #filter-center').stop(true, true).fadeIn();
        }
    });
    
    $('#banner .slide').mouseleave(function(e) {
        if((e.relatedTarget.id != 'nav-left' && e.relatedTarget.id != 'nav-right' && e.relatedTarget.id != 'select-overlay'))
//           (e.toElement.id != 'nav-left' && e.toElement.id != 'nav-right'))
        {
            $('#banner-filter #filter-center').stop(true, true).fadeOut();
        }
    });
    
//    $('#banner-filter #filter-center').mouseenter(function(e) {
//        if((e.relatedTarget.parentElement.className != 'slide'))
////           (e.fromElement.parentElement.className != 'slide'))
//        {
//            $('#banner-filter #filter-center').stop(true, true).fadeIn();
//        }
//    });
//    
//    $('#banner-filter #filter-center').mouseleave(function(e) {
//        if((e.relatedTarget.parentElement.className != 'slide'))
//           (e.toElement.parentElement.className != 'slide'))
//        {
//            $('#banner-filter #filter-center').stop(true, true).fadeOut();
//        }
//    });
    
    $('#banner-filter #filter-center #nav-left').click(function(e) {
        e.preventDefault();
        cycleRight();
    });
    
    $('#banner-filter #filter-center #nav-right').click(function(e) {
        e.preventDefault();
        cycleLeft();
    });
    
    $('#banner .slide .slide-overlay a.close').click(function(e) {
        e.preventDefault();
        
        if($(this).parent().css('paddingLeft') == '0px')
        {
            $(this).parent().animate(
                {'width': '468px'},
                {
                    'complete': function() {
                        $(this).children().show();
                        $(this).css('padding', '16px');
                        $(this).css('height', 'auto');
                    },
                    'duration': 'fast'
                }
            );
            $(this).css('background-position', '0 -540px');
        }
        else
        {
            $(this).siblings().hide();
            $(this).parent().css('padding', '0');
            $(this).parent().css('height', '35px');
            $(this).parent().animate(
                {'width': '35px'},
                'fast'
            );
            $(this).css('background-position', '0 -570px');
        }
    });
    
    // click handler for "similar" Experiences
    $('#middle4').on('click', 'a', function(e) {
        var $target = $('#header-wrapper'),
            top = $target.offset().top,
            current = $('body').scrollTop();
        
        // Some browsers report the body as scolled, others use html
        if(current == 0)
        {
            current = $('html').scrollTop();
        }
        
        var scrollTime = Math.round((Math.abs(top - current) / 600) * 100);
        
        $(scrollElement).stop().animate({
            'scrollTop': top
        }, scrollTime, 'swing');
    });
    
    $('.slide-overlay').on('click', 'a.learn-more', function(e) {
        e.preventDefault();
        
        var $target = $('#middle3-wrapper'),
            top = $target.offset().top,
            current = $('body').scrollTop();
        
        // Some browsers report the body as scolled, others use html
        if(current == 0)
        {
            current = $('html').scrollTop();
        }
        
        var scrollTime = Math.round((Math.abs(top - current) / 600) * 100);
        
        $(scrollElement).stop().animate({
            'scrollTop': top
        }, scrollTime, 'swing');
    });
    
    $('#category-select').change(function(){
        location.href = $(this).val();
    });
    
    $('#experience-select').change(function(){
        $.address.value($(this).val());
    });
    
    
    function cycleLeft() {
        $('#middle3 .column1').children().stop(true, true).fadeOut('fast');
        
        var currentOverlay = $('#banner .slide:eq(2) .slide-overlay');
        var newOverlay = $('#banner .slide:eq(3) .slide-overlay');
        $(currentOverlay).stop(true, true).hide();
        $('#banner').animate({'left': '-=1020'}, 'normal', 'easeOutQuad', function() {
            $(newOverlay).stop(true, true).fadeIn();
            $('#banner').css('left', '+=1020');
            $('#banner .slide').css('left', '-=1020');
            $('#banner .slide:first').css('left', ($('#banner .slide:last').position().left + width) + 'px').appendTo('#banner');
            
            $.address.value($('#banner .slide:eq(2)').attr('data-slug'));
        });
    }
    
    function cycleRight() {
        $('#middle3 .column1').children().stop(true, true).fadeOut('fast');
        
        var currentOverlay = $('#banner .slide:eq(2) .slide-overlay');
        var newOverlay = $('#banner .slide:eq(1) .slide-overlay');
        $(currentOverlay).stop(true, true).hide();
        $('#banner').animate({'left': '+=1020'}, 'normal', 'easeOutQuad', function() {
            $(newOverlay).stop(true, true).fadeIn();
            $('#banner').css('left', '-=1020');
            $('#banner .slide').css('left', '+=1020');
            $('#banner .slide:last').css('left', ($('#banner .slide:first').position().left - width) + 'px').prependTo('#banner');
            
            $.address.value($('#banner .slide:eq(2)').attr('data-slug'));
        });
    }
    
    function pullUp(slug) {
        var count = 0;
        var index = 0;
        $('#banner .slide').each(function() {
            if(slug == $(this).attr('data-slug')) {
                index = count; 
            }
            count++;
        });
        
        if(index != 2) {
            $('#banner .slide .slide-overlay').hide();
            
            var jump = (2 - index);
            if(jump > 0) {
                for(var i = 0; i < jump; i++) {
                    $('#banner .slide').css('left', '+=1020');
                    $('#banner .slide:last').css('left', ($('#banner .slide:first').position().left - width) + 'px').prependTo('#banner');
                }
            }
            if(jump < 0) {
                for(var i = jump; i < 0; i++) {
                    $('#banner .slide').css('left', '-=1020');
                    $('#banner .slide:first').css('left', ($('#banner .slide:last').position().left + width) + 'px').appendTo('#banner');
                }
            }
        }
        
        if($('#banner .slide').length > 1) {
            $('#banner .slide:eq(2) .slide-overlay').fadeIn();
            
            $.get('detail/' + $('#banner .slide:eq(2)').attr('data-slug'), function(data) {
                $('#middle3 .column1').html(data);
                $('#middle3 .column1').children().stop(true, true).fadeIn('fast');

                // modify overlay contact form to utilize current Experience details
                $('#contact1 form #form_experience').val($('#banner .slide:eq(2)').attr('data-slug'));
                $('#contact1 #details h4').text($('#middle3 .column1 h2').text());
                $('#contact1 #details p').html($('#middle3 .column1 h3').html());
                $('#contact1 #image').empty();
                $('#banner .slide:eq(2) img:eq(0)').clone().appendTo('#contact1 #image').attr({width: '120', height: '70'});
            });
            
            $.get('similar/' + $('#banner .slide:eq(2)').attr('data-slug'), function(data) {
                $('#middle4').html(data);
            });
            
          $.get('mini/' + $('#banner .slide:eq(2)').attr('data-slug'), function(data) {
              $('#middle3 .column2 img').remove();
              $('#middle3 .column2').prepend(data);
          });
        }
        else
        {
            $('#banner .slide:eq(0) .slide-overlay').fadeIn();
            
            $.get('detail/' + $('#banner .slide:eq(0)').attr('data-slug'), function(data) {
                $('#middle3 .column1').html(data);
                $('#middle3 .column1').children().stop(true, true).fadeIn('fast');
                
                // modify overlay contact form to utilize current Experience details
                $('#contact1 form #form_experience').val($('#banner .slide:eq(0)').attr('data-slug'));
                $('#contact1 form[name="experience"]').val($('#banner .slide:eq(0)').attr('data-slug'));
                $('#contact1 #details h4').text($('#middle3 .column1 h2').text());
                $('#contact1 #details p').html($('#middle3 .column1 h3').html());
                $('#contact1 #image').empty();
                $('#banner .slide:eq(0) img:eq(0)').clone().appendTo('#contact1 #image').attr({width: '120', height: '70'});
            });
            
            $.get('similar/' + $('#banner .slide:eq(0)').attr('data-slug'), function(data) {
                $('#middle4').html(data);
            });
            
            $.get('mini/' + $('#banner .slide:eq(0)').attr('data-slug'), function(data) {
                $('#middle3 .column2 img').remove();
                $('#middle3 .column2').prepend(data);
            });
        }
    }
});
$(function() {
    // set up event handlers
    $('#grid a.experience').mouseenter(function(e) {
        e.preventDefault();
        $(this).children('span').stop(true, true).fadeIn('fast');
    });
    
    $('#grid a.experience').mouseleave(function(e) {
        e.preventDefault();
        $(this).children('span').stop(true, true).fadeOut('fast');
    });
    
    
    // set up tooltips
    $('#grid a[title]').tooltip({
        effect: 'slide',
        offset: [-26, 60],
        tipClass: 'tooltip_grey'
    });
    
    var tooltip1 = $('#grid a#phone').data('tooltip');
    
    // set up overlay
    $('#grid a#phone').overlay({
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
    
    
    // set up combobox
    $('#search-select').change(function(e){
        location.href = $(this).val();        
    });
    
    $('#search-select').kendoComboBox({
        highLightFirst: false
    });
    
    if($('.k-input').val() == '') {
        $('.k-dropdown-wrap').append('<span class="helper-text">Search or Select</span>');
    }
    
    $('.k-dropdown-wrap span.helper-text').click(function(e){
        $(this).fadeOut('fast');
        $('.k-input').trigger('focus');
    });
    
    $('.k-input').focus(function(e) {
        $('.k-dropdown-wrap span.helper-text').fadeOut('fast');
    });
    
});
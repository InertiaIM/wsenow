$(function() {
    // set up tooltips
    $('table.grid tr').tooltip({
        delay: 0, 
        effect: 'fade',
        events: {
            def:     "mouseenter,mouseleave",
            input:   "focus,blur",
            widget:  "focus mouseenter,blur mouseleave",
            tooltip: "mouseenter"
        },
        offset: [-25, -150],
        onBeforeShow: function(e, pos) {
            var title = $(e.target).attr('data-experience-title');
            var description = $(e.target).attr('data-experience-description');
            var image = $(e.target).attr('data-experience-image');
            var location = $(e.target).attr('data-experience-location');
            var date = $(e.target).attr('data-experience-date');
            $('#tooltip').html(
                '<div class="column1">' +
                '<h4>' + title + '</h4>' +
                '<p><em>' + location + '<br/>' + date + '</em></p>' +
                '</div><div class="column2">' +
                '<img src="/uploads/assets/experiences/images/' + image + '" width="120" height="70"/>' +
                '</div>' +
                '<p style="clear:both; padding-top:16px;">' + description + '</p>'
            );
        },
        predelay: 100,
        relative: true,
        tip: '#tooltip',
        tipClass: 'tooltip-grey-large'
    });
    
    
    // set up multiselect
    $('#category-select').multiselect({
        height: 'auto',
        noneSelectedText: 'Select all that apply'
    });
    
    
    // set up datepickers
    var dates = $('#datepicker1, #datepicker2').datepicker({
        defaultDate: '+1w',
        changeMonth: true,
        showOn: 'button',
        buttonImage: '/images/calendar-icon.png',
        buttonImageOnly: true,
        dateFormat: 'm/d/yy',
        onSelect: function(selectedDate, item) {
            var option = this.id == 'datepicker1' ? 'minDate' : 'maxDate',
                instance = $(this).data('datepicker'),
                date = $.datepicker.parseDate(
                    instance.settings.dateFormat ||
                    $.datepicker._defaults.dateFormat,
                    selectedDate, instance.settings);
            dates.not(this).datepicker('option', option, date);
            
            update1(selectedDate, item);
        }
    });
    
    
    // set up event handlers
    $('#calendar-wrapper').on('mouseenter', '.grid tr', function(e) {
        $(this).find('span').addClass('hover');
    });
    
    $('#calendar-wrapper').on('mouseleave', '.grid tr', function(e) {
        $(this).find('span').removeClass('hover');
    });
    
    $('#calendar-wrapper').on('click', '.grid tr', function(e) {
        e.preventDefault();
        location.href = $(this).attr('data-category-slug') + '#' + $(this).attr('data-experience-slug');
    });
    
    $('a#all').click(function(e) {
        e.preventDefault();
        
        var now = new Date();
        $('#datepicker1').val((now.getMonth() + 1) + '/' + now.getDate() + '/' + now.getFullYear());
        $('#datepicker2').val('');
        $('#category-select').multiselect('uncheckAll');
        
        $.ajax({
            url: 'calendar-data',
            success: function(data, textStatus, jqXHR) {
                $('#calendar-wrapper').html(data);
            },
            dataType: 'html'
        });
    });
    
    $('#category-select').change(function(e) {
        var categories = $(this).val();
        
        if(categories) {
            var catstr = $.map(categories, function(val,index) {
                var str = val;
                return str;
            }).join(',');
        }
        else {
            var catstr = '';
        }
        
        $.ajax({
            url: 'calendar-data',
            data: {
                'categories': catstr,
                'start': $('#datepicker1').val(),
                'end': $('#datepicker2').val()
            },
            success: function(data, textStatus, jqXHR) {
                $('#calendar-wrapper').html(data);
            },
            dataType: 'html'
        });
    });
    
    $('#datepicker1, #datepicker2').change(function(e) {
        var categories = $('#category-select').val();
        if(categories) {
            var catstr = $.map(categories, function(val,index) {
                var str = val;
                return str;
            }).join(',');
        }
        else {
            var catstr = '';
        }
        
        var start = $('#datepicker1').val();
        var end = $('#datepicker2').val();
        
        $.ajax({
            url: 'calendar-data',
            data: {
                'categories': catstr,
                'start': start,
                'end': end
            },
            success: function(data, textStatus, jqXHR) {
                $('#calendar-wrapper').html(data);
            },
            dataType: 'html'
        });
    });
    
    function update1(date, item) {
        var categories = $('#category-select').val();
        if(categories) {
            var catstr = $.map(categories, function(val,index) {
                var str = val;
                return str;
            }).join(',');
        }
        else {
            var catstr = '';
        }
        
        var start = $('#datepicker1').val();
        var end = $('#datepicker2').val();
        if(item.id == 'datepicker1') {
            start = date;
        }
        
        if(item.id == 'datepicker2') {
            end = date;
        }        
        
        $.ajax({
            url: 'calendar-data',
            data: {
                'categories': catstr,
                'start': start,
                'end': end
            },
            success: function(data, textStatus, jqXHR) {
                $('#calendar-wrapper').html(data);
            },
            dataType: 'html'
        });
    }
});

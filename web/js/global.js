$(function() {
    // set up labels
    $('#newsletter label').inFieldLabels();
    
    $('#newsletter').submit(function(e) {
        e.preventDefault();
        
        var email = $('#newsletter #email').val();
        if(email == '') {
            
        }
        else {
            $('#newsletter .result img').show();
            
            $.ajax({
                url: '/subscribe.json',
                dataType: 'json',
                data: {
                    email: email
                },
                success: function(data) {
                    $('#newsletter .result img').hide();
                    $('#newsletter .result span').show();
                    if(data.result.error) {
                        $('#newsletter .result span')
                            .css('color', '#ffce00')
                            .text('Invalid Email Address!');
                    }
                    else {
                        $('#newsletter .result span')
                            .css('color', '#ffffff')
                            .text('Thank you for your interest...');
                    }
                    $('#newsletter .result span').delay(5000).fadeOut(null, function(){
                        $('#newsletter #email').val('').trigger('blur');
                    });
                }
            });
        }
    });
});


var datepicker = require('clivia-datepicker/clivia-datepicker.jquery.js');
var ajax       = require('mandeling-ajax/mandeling-ajax.js');
var dialog     = require('baguette-dialog/baguette-dialog.jquery.js');
var validator  = require('burendo-validator/burendo-validator.jquery.js');

$(function() {
    $('#date').datepicker();
    $('#popup-dialog').dialog();
    $('#popup-dialog').dialog('dismiss', function() {
        location.reload();
    });
        var Ajax = new ajax(); 
        var form   = $('.invoice-form');

        /**
         * Validate the form
         */
        form.validate({
            message: {
                required: "必填欄位不可空白"
            },
            success: function() {
                var action = form.attr('action');
                var number = form.find('input[name=number]').val();
                var date   = form.find('input[name=date').val();
                var amount = form.find('input[name=amount').val();

                Ajax.post(action, {
                    number: number,
                    date: date,
                    amount: amount
                }).success(function(result) {console.log(result);
                    if (result.status !== 'Success')
                    {
                        $('#popup-dialog').find('.title').text('錯誤');
                        $('#popup-dialog .message h4').text(result.message);
                        $('#popup-dialog').dialog('show', function() {
                            $('#popup-dialog').find('#close-dialog').on('click', function(e) {
                                e.preventDefault();
                                $('#popup-dialog').dialog('hide', function() {
                                    location.reload();
                                });
                            });
                        });
                    } else  {
                        $('#popup-dialog').find('.title').text('成功');
                        $('#popup-dialog .message h4').text(result.message);
                        $('#popup-dialog').dialog('show', function() {
                            $('#popup-dialog').find('#close-dialog').on('click', function(e) {
                                e.preventDefault();
                                $('#popup-dialog').dialog('hide', function() {
                                    location.reload();
                                });
                            });
                        });
                    }
                });
            }
        });
});
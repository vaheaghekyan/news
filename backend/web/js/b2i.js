$(document).ready(function()
{
    $('.datepicker').datetimepicker(
    {
        format:"Y-m-d",
        timepicker:false,
        validateOnBlur: true,
        defaultDate:new Date(),
       // value: new Date()
    });

    $('.datetimepicker').datetimepicker(
    {
        format:"Y-m-d H:i:s",
        validateOnBlur:true,
        defaultDate:new Date(),
       // value: new Date()
    });

    //match height of all dics so they are inline
    $(".matchheight").matchHeight();

    //if there is file upload field make it to look like in bootstrap
   //$(":file").filestyle({buttonBefore: true, buttonName: "btn-primary"});
});


//FUNCTIONS



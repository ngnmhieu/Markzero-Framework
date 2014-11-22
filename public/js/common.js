$(document).ready(function () {
  $("#datepicker").datepicker({
    dateFormat: 'dd/mm/yy'
  });

  $('#transaction-list .transaction').on('click', function() {
    $(this).siblings('.moreinfo').hide();
    $(this).next('.moreinfo').toggle();
  });


})

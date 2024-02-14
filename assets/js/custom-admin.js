jQuery(document).ready(function($){
  
  // calendar show js 
  $('#demoEvoCalendar').evoCalendar({
    'sidebarToggler' : false,
    'eventDisplayDefault' : false,
    'eventListToggler' : false,
  })


  // caledar click to show user 
  $('#demoEvoCalendar').on('click', '.calendar-months .month, .calendar-day .day', function() {
    var get_this = $(this);
    var get_date = get_this.attr('data-date-val');

    $.ajax({
      type: 'POST',
      url: formAjax.ajaxurl,
      data: {
          action: 'user_filter_calendar',
          'get_date' : get_date,
      },
      beforeSend: function() {
        $('.user_list_wrap').addClass('processing');
        $('.user_list_wrap .spinner').addClass('is-active');
      },
      success: function(response) {

        $('.user_list_wrap').removeClass('processing');
        $('.user_list_wrap .spinner').removeClass('is-active');

        try {
          var datas = JSON.parse(response);
          $('.user_list_table tbody tr').remove();
          if(datas.length > 0) {
              datas.forEach(function(data) {
                  var dataHtml = `
                      <tr>
                          <td>${data.username}</td>
                          <td>${data.name}</td>
                          <td>${data.email}</td>
                          <td>${data.phone}</td>
                          <td>${data.registered_date}</td>
                      </tr>
                  `;
                  $('.user_list_table tbody').append(dataHtml);
              });
          } else {
              var noDataHtml = `
                  <tr>
                      <td colspan='5'>No User is found in this date</td>
                  </tr>
              `;
              $('.user_list_table tbody').append(noDataHtml);
          }
        } catch (error) {
          console.error('Error parsing JSON:', error);
        }
      },    
      error: function(xhr, status, error) {
          console.error('AJAX request failed:', status, error);
      }
    });
    

  });

});
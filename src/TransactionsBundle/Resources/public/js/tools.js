$(document).ready(function() {
  $('#daily tr').click(function() {
    // TODO Show a modal with all transactions done this day
    //alert('Sucess');
  });

  $('#myTabs a').click(function (e) {
      e.preventDefault()
      $(this).tab('show')
  });

  $('#months a').click(function (e) {
    e.preventDefault();
    // TODO ajax call to get the content for the month and reload the table :)
    $.ajax({
      url: "/",
      data: { currentMonth: $(this).attr('href') }
    }).done(function() {
      console.log('Fix this later by getting the table redesigned :)');
    });
  });
});

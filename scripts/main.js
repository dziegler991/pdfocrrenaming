$('.file-upload input[type="file"]').change(function() {
  var label = $(this).parent().find('label');
  if($(this).val() === '') {
      label.text('No File');
  } else {
      label.text($(this).val().replace('C:\\fakepath\\',''));
  }
});

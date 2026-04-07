(function ($) {
  function openMedia(button) {
    const frame = wp.media({
      title: 'Choose image',
      button: { text: 'Use image' },
      multiple: false,
    });

    frame.on('select', function () {
      const attachment = frame.state().get('selection').first().toJSON();
      const field = button.closest('.mm-media-field');
      field.find('.mm-media-id').val(attachment.id);
      field.find('.mm-media-preview').html('<img src="' + attachment.url + '" alt="">');
    });

    frame.open();
  }

  $(document).on('click', '.mm-media-upload', function (event) {
    event.preventDefault();
    openMedia($(this));
  });

  $(document).on('click', '.mm-media-clear', function (event) {
    event.preventDefault();
    const field = $(this).closest('.mm-media-field');
    field.find('.mm-media-id').val('');
    field.find('.mm-media-preview').empty();
  });
})(jQuery);


jQuery(function ($) {
    var $popup = $('#marketplace-review-popup');
    var $overlay = $popup.find('.marketplace-review-popup-overlay');
    var $closeBtn = $popup.find('.marketplace-review-popup-close');
    var $triggerButtons = $('.open-review-popup');
    var $stars = $('#popup-stars-rating .star');
    var $input = $('#popup-rating-input');
    var selected = 0;
		 var $dropArea = $('#drop-area');
  var $fileInput = $('#review_images');
  var $gallery = $('#gallery');
  var maxFiles = parseInt($fileInput.data('max'), 10) || 5;
  var fileList = [];
		// Open popup with fade-in animation
    function openPopup(productName = '') {
        if (!$popup.length) return;

  
        $popup.addClass('visible');
    

        var $nameHolder = $popup.find('.popup-product-name');
        if ($nameHolder.length && productName) {
            $nameHolder.text(productName);
        }
    }

    // Close popup with fade-out animation
    function closePopup() {
        if (!$popup.length) return;
				$popup.removeClass('visible');
      
    }

    // Manual triggers
    $triggerButtons.on('click', function (e) {
        e.preventDefault();
				  var productId = $(this).data('product-id');
    $('#popup_product_id').val(productId);
        var productName = $(this).data('product-name') || '';
        openPopup(productName);
    });

    $closeBtn.on('click', closePopup);
    $overlay.on('click', closePopup);

//    // Automatic reminder after login (once per session)
//    var hasSeenPopup = sessionStorage.getItem('marketplace_review_popup_shown');
//    var shouldRemind = typeof MarketplaceReviewsData !== 'undefined' ? MarketplaceReviewsData.shouldRemind : 'no';

//    if (!hasSeenPopup && shouldRemind === 'yes') {
//        sessionStorage.setItem('marketplace_review_popup_shown', '1');
//        setTimeout(function () {
//            openPopup();
//        }, 2000); // delay reminder by 2 seconds
//    }
 
  $stars.on('mouseenter', function() {
    var idx = $(this).index();
    $stars.removeClass('hovered');
    $stars.each(function(i) {
      if (i <= idx) $(this).addClass('hovered');
    });
  });

  $stars.on('mouseleave', function() {
    $stars.removeClass('hovered');
  });

  $stars.on('click', function() {
    selected = $(this).data('value');
    $input.val(selected);
    $stars.removeClass('selected active');
    $stars.each(function(i) {
      if (i < selected) $(this).addClass('active selected');
    });
  });

  // Восстановить выбранные звёзды при уходе мыши
  $stars.on('mouseleave', function() {
    $stars.removeClass('active selected');
    $stars.each(function(i) {
      if (i < selected) $(this).addClass('active selected');
    });
  });

  // Если уже есть рейтинг (например, при ошибке формы)
  var initSelected = parseInt($input.val());
  if (initSelected) {
    $stars.each(function(i) {
      if (i < initSelected) $(this).addClass('active selected');
    });
  }
  function updateInputFiles() {
    var dt = new DataTransfer();
    fileList.forEach(f => dt.items.add(f));
    $fileInput[0].files = dt.files;
  }

  function previewImages() {
    $gallery.empty();
    fileList.forEach((file, i) => {
      var reader = new FileReader();
      reader.onload = function(e){
        var $wrap = $('<div class="preview-img-wrap"></div>');
        var $img = $('<img>').attr('src', e.target.result);
        var $del = $('<span class="remove-img">&times;</span>').click(function(){
          fileList.splice(i,1);
          previewImages();
          updateInputFiles();
        });
        $wrap.append($img).append($del);
        $gallery.append($wrap);
      };
      reader.readAsDataURL(file);
    });
  }

  $dropArea.on('click', function(e){
    if (e.target === this || e.target.id === 'drop-label') {
      $fileInput.trigger('click');
    }
  });

  $dropArea.on('dragover', function(e){
    e.preventDefault(); e.stopPropagation();
    $dropArea.addClass('dragover');
  }).on('dragleave dragend', function(e){
    e.preventDefault(); e.stopPropagation();
    $dropArea.removeClass('dragover');
  }).on('drop', function(e){
    e.preventDefault(); e.stopPropagation();
    $dropArea.removeClass('dragover');
    var files = Array.from(e.originalEvent.dataTransfer.files);
    files = files.filter(f=>f.type.match('image.*'));
    if (fileList.length + files.length > maxFiles) {
      files = files.slice(0, maxFiles - fileList.length);
      alert('Можно загрузить не более ' + maxFiles + ' изображений!');
    }
    fileList = fileList.concat(files);
    fileList = fileList.slice(0, maxFiles);
    previewImages();
    updateInputFiles();
  });

  $fileInput.on('change', function(){
    var files = Array.from(this.files).filter(f=>f.type.match('image.*'));
    if (fileList.length + files.length > maxFiles) {
      files = files.slice(0, maxFiles - fileList.length);
      alert('Можно загрузить не более ' + maxFiles + ' изображений!');
    }
    fileList = fileList.concat(files);
    fileList = fileList.slice(0, maxFiles);
    previewImages();
    updateInputFiles();
  });

  // начальное обновление
  previewImages();
});

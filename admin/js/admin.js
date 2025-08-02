document.addEventListener('DOMContentLoaded', function () {
  const rows = document.querySelectorAll('.wp-list-table .column-moderation_status');
  rows.forEach((cell) => {
    const text = cell.textContent.trim().toLowerCase();
    if (text === 'pending') {
      cell.style.color = '#e60023';
      cell.style.fontWeight = 'bold';
    } else if (text === 'approved') {
      cell.style.color = 'green';
    } else if (text === 'rejected') {
      cell.style.color = 'gray';
    }
  });
});
jQuery(document).ready(function($) {
    $('.create-page-btn').on('click', function() {
        var $btn = $(this);
        var field = $btn.data('field');
        var pageType = $btn.data('type');
        var pageTitle = $btn.data('title');
        var pageContent = $btn.data('content');

        $btn.prop('disabled', true).text(window.marketplaceReviewsCreatingText || 'Creating...');

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'create_marketplace_page',
                field: field,
                page_type: pageType,
                page_title: pageTitle,
                page_content: pageContent,
                nonce: window.marketplaceReviewsCreateNonce || ''
            },
            success: function(response) {
                if (response.success) {
                    var $select = $('#' + field);
                    $select.append('<option value="' + response.data.page_id + '">' + response.data.page_title + '</option>');
                    $select.val(response.data.page_id);
                    $btn.closest('.page-select-wrapper').append(
                        '<span class="page-status success">✅ ' + (window.marketplaceReviewsPageCreatedText || 'Page created successfully!') + '</span>'
                    );
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    alert((window.marketplaceReviewsErrorText || 'Error creating page:') + ' ' + response.data);
                }
            },
            error: function() {
                alert(window.marketplaceReviewsAjaxErrorText || 'AJAX error occurred');
            },
            complete: function() {
                $btn.prop('disabled', false).text(window.marketplaceReviewsCreateBtnText || 'Create Page');
            }
        });
    });

    $('.nav-tab').on('click', function() {
        setTimeout(function() {
            $('html, body').animate({
                scrollTop: $('.tab-content').offset().top - 100
            }, 500);
        }, 100);
    });

    function toggleFields() {
        $('[data-mp-toggle]').each(function() {
            var $field = $(this);
            var toggleId = $field.data('mp-toggle');
            var toggleValue = $field.data('mp-toggle-value');
            var $toggle = $('#' + toggleId);
            var show = false;
            if ($toggle.length) {
                if ($toggle.is(':checkbox')) {
                    show = $toggle.is(':checked') === (toggleValue === 'yes');
                } else if ($toggle.is('select')) {
                    show = $toggle.val() === toggleValue;
                }
            }
            if (show) {
                $field.removeClass('mp-field-hidden').addClass('mp-field-visible');
            } else {
                $field.removeClass('mp-field-visible').addClass('mp-field-hidden');
            }
        });
    }

    function toggleRows() {
        $('[data-mp-toggle]').each(function() {
            var $row = $(this).closest('tr');
            var toggleId = $(this).data('mp-toggle');
            var toggleValue = $(this).data('mp-toggle-value');
            var $toggle = $('#' + toggleId);
            var show = false;
            if ($toggle.length) {
                if ($toggle.is(':checkbox')) {
                    show = $toggle.is(':checked') === (toggleValue === 'yes');
                } else if ($toggle.is('select')) {
                    show = $toggle.val() === toggleValue;
                }
            }
            if (show) {
                if (!$row.is(':visible')) $row.stop(true, true).slideDown(200);
            } else {
                if ($row.is(':visible')) $row.stop(true, true).slideUp(200);
            }
        });
    }

    toggleFields();
    toggleRows();
    $(document).on('change', 'input[type=checkbox], select', function() {
        toggleFields();
        toggleRows();
    });
});

jQuery(function($){
    $(document).on('submit', '.review-reply-form', function(e){
        e.preventDefault();
        var form = $(this);
        var textarea = form.find('textarea');
        var data = {
            action: 'submit_review_reply',
            _wpnonce: MarketplaceReviewsData.reply_nonce,
            review_id: form.data('review'),
            parent_id: form.data('parent'),
            content: textarea.val()
        };
        textarea.prop('disabled', true);
        $.post(MarketplaceReviewsData.ajax_url, data)
            .done(function(resp){
                if(resp.success){
                    location.reload();
                } else {
                    alert(resp.data && resp.data.message ? resp.data.message : 'Error');
                }
            })
            .fail(function(){
                alert('Error');
            })
            .always(function(){
                textarea.prop('disabled', false);
            });
    });
});

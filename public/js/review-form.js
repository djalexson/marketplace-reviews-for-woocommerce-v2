document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('marketplace-review-form');

    if (!form) return;

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');

        submitBtn.disabled = true;
        const originalText = submitBtn.textContent;
        const loadingText = submitBtn.getAttribute('data-loading-text') || 'Submitting...';
        submitBtn.textContent = loadingText;

        fetch(MarketplaceReviewsData.ajax_url, {
            method: 'POST',
            credentials: 'same-origin',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;

                if (data.success) {
                    alert(data.data.message);
                    form.reset();
                } else {
                    alert(data.data.message || 'Something went wrong.');
                }
            })
            .catch(error => {
                console.error('Review submit error:', error);
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
                alert('Error submitting the review.');
            });
    });
});

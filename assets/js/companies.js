/* assets/js/companies.js */

document.addEventListener('DOMContentLoaded', function() {
    const companyCards = document.querySelectorAll('.company-card');

    companyCards.forEach(card => {
        card.addEventListener('click', function(e) {
            // Prevent redirection if the user clicks on a button or specific tag inside the card
            if (e.target.closest('button') || e.target.closest('a')) {
                return;
            }

            // Get the company ID from a data attribute we will add to the PHP
            const companyId = this.getAttribute('data-id');
            
            if (companyId) {
                window.location.href = `view-company.php?id=${companyId}`;
            }
        });
    });
});
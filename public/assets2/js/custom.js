document.addEventListener('DOMContentLoaded', function() {
    // Mini cart toggle
    function toggleCartmini(open) {
        const cartminiArea = document.querySelector('.cartmini__area');
        const bodyOverlay = document.querySelector('.body-overlay');
        if (cartminiArea && bodyOverlay) {
            if (open === true) {
                cartminiArea.classList.add('cartmini-opened');
                bodyOverlay.classList.add('opened');
                console.log('Mini-cart opened');
            } else if (open === false) {
                cartminiArea.classList.remove('cartmini-opened');
                bodyOverlay.classList.remove('opened');
                console.log('Mini-cart closed');
            } else {
                cartminiArea.classList.toggle('cartmini-opened');
                bodyOverlay.classList.toggle('opened');
                console.log('Mini-cart toggled');
            }
        } else {
            console.warn('Không tìm thấy .cartmini__area hoặc .body-overlay');
        }
    }


    document.querySelectorAll('.cartmini-open-btn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            toggleCartmini(true);
        });
    });

    document.querySelectorAll('.cartmini-close-btn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            toggleCartmini(false);
        });
    });

    const bodyOverlay = document.querySelector('.body-overlay');
    if (bodyOverlay) {
        bodyOverlay.addEventListener('click', function(e) {
            e.preventDefault();
            toggleCartmini(false);
        });
    }
     // Dropdown account
     const accountWrapper = document.querySelector('.tp-account-item');
     if (accountWrapper) {
         const accountToggle = accountWrapper.querySelector('.tp-account-toggle');
         const accountMenu = accountWrapper.querySelector('.tp-account-menu');
 
         if (accountToggle && accountMenu) {
             accountToggle.addEventListener('click', function(e) {
                 e.preventDefault();
                 e.stopPropagation();
                 if (accountMenu) accountMenu.classList.toggle('show');
             });
 
             document.addEventListener('click', function(e) {
                 if (accountMenu && !accountWrapper.contains(e.target)) {
                     accountMenu.classList.remove('show');
                 }
             });
         }
     }
}); 
if (document.querySelector('a')) {
    let hasBeenPrerendered = [];

    // Use the IntersectionObserver API to only prerender links when they are visible on the screen
    let observer = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            // Only prerender the link if it is visible on the screen
            if (entry.isIntersecting) {
                let link = entry.target;
                let href = link.getAttribute('href');

                if (!hasBeenPrerendered.includes(href)) {
                    let prerenderLink = document.createElement('link');

                    prerenderLink.setAttribute('rel', 'prerender');
                    prerenderLink.setAttribute('href', href);

                    // Use requestIdleCallback to schedule the prerendering logic to run when the browser is idle
                    requestIdleCallback(() => {
                        document.head.appendChild(prerenderLink);
                    });

                    hasBeenPrerendered.push(href);

                    // Stop observing the link once it has been prerendered
                    observer.unobserve(link);
                }
            }
        });
    });

    // Observe all links on the page
    document.querySelectorAll('a').forEach(link => {
        observer.observe(link);
    });
}

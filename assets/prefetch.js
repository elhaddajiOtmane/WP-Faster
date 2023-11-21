if (document.querySelector('a')) {
    let hasBeenPrerendered = [],
        hasBeenPrefetched = [],
        prefetch_throttle = parseInt(lhf_ajax_var.prefetch_throttle, 10);

    let prefetch_throttle_delay = (prefetch_throttle > 0) ? prefetch_throttle : 65;

    // Use the IntersectionObserver API to only prefetch links when they are visible on the screen
    let observer = new IntersectionObserver((entries, observer) => {
        let start = performance.now();
        entries.forEach(entry => {
            // Only prefetch the link if it is visible on the screen
            if (entry.isIntersecting) {
                let link = entry.target;
                let href = link.getAttribute('href');

                if (!hasBeenPrefetched.includes(href)) {
                    let prerenderLink = document.createElement('link');

                    prerenderLink.setAttribute('rel', 'prerender');
                    prerenderLink.setAttribute('rel', 'prefetch');
                    prerenderLink.setAttribute('href', href);

                    // Use requestIdleCallback to schedule the prefetching logic to run when the browser is idle
                    requestIdleCallback(() => {
                        document.head.appendChild(prerenderLink);
                    }, { timeout: prefetch_throttle_delay });

                    hasBeenPrerendered.push(href);
                    hasBeenPrefetched.push(href);

                    // Stop observing the link once it has been prefetched
                    observer.unobserve(link);
                }
            }
        });
        let end = performance.now();
        let elapsed = end - start;

        // Adjust the prefetch_throttle_delay value based on the measured performance
        prefetch_throttle_delay = Math.max(prefetch_throttle_delay - elapsed, 0);
    });

    // Observe all links on the page
    document.querySelectorAll('a').forEach(link => {
        observer.observe(link);
    });
}

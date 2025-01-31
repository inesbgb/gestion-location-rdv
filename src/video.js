// videoLazyLoad.js
export function setupVideoLazyLoad(video) {
    if (video) {
        const observer = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    video.src = video.dataset.src;
                    video.load();
                    observer.unobserve(video);
                }
            });
        });

        observer.observe(video);
    }
}
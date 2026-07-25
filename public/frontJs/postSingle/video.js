
    document.addEventListener('DOMContentLoaded', function() {
    // Video mute/unmute toggle
    document.querySelectorAll('.mute-toggle-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const video = this.closest('.slide').querySelector('video');
            const mutedIcon = this.querySelector('.muted-icon');
            const unmutedIcon = this.querySelector('.unmuted-icon');

            if (video) {
                if (video.muted) {
                    video.muted = false;
                    mutedIcon.style.display = 'none';
                    unmutedIcon.style.display = 'block';
                } else {
                    video.muted = true;
                    mutedIcon.style.display = 'block';
                    unmutedIcon.style.display = 'none';
                }
            }
        });
    });

    // Image slider functionality
    document.querySelectorAll('.post-img-slider').forEach(slider => {
    const slidesContainer = slider.querySelector('.slides-container');
    const slides = slider.querySelectorAll('.slide');
    const prevBtn = slider.querySelector('.prev-btn');
    const nextBtn = slider.querySelector('.next-btn');
    const dots = slider.querySelectorAll('.dot');

    if (!slidesContainer || slides.length <= 1) return;

    let currentSlide = 0;

    function updateSlider() {
    slidesContainer.style.transform = `translateX(-${currentSlide * 100}%)`;
    dots.forEach((dot, index) => {
    dot.style.background = index === currentSlide ? '#0095f6' : '#a8a8a8';
    if (index === currentSlide) {
    dot.classList.add('active');
} else {
    dot.classList.remove('active');
}
});
}

    if (prevBtn) {
    prevBtn.addEventListener('click', (e) => {
    e.preventDefault();
    e.stopPropagation();
    currentSlide = (currentSlide - 1 + slides.length) % slides.length;
    updateSlider();
});
}

    if (nextBtn) {
    nextBtn.addEventListener('click', (e) => {
    e.preventDefault();
    e.stopPropagation();
    currentSlide = (currentSlide + 1) % slides.length;
    updateSlider();
});
}

    dots.forEach(dot => {
    dot.addEventListener('click', (e) => {
    e.preventDefault();
    e.stopPropagation();
    currentSlide = parseInt(dot.dataset.index);
    updateSlider();
});
});
});
});

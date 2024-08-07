document.addEventListener('scroll', function() {
    const products = document.querySelectorAll('.product');
    const scrollY = window.scrollY;

    products.forEach(product => {
        const offset = product.getBoundingClientRect().top + window.scrollY;
        const height = product.offsetHeight;
        const windowHeight = window.innerHeight;
        
        if (scrollY + windowHeight > offset && scrollY < offset + height) {
            const scrollPercentage = (scrollY + windowHeight - offset) / (windowHeight + height);
            const scale = 1 + (scrollPercentage * 0.2); // Scale up to 120%
            product.style.transform = `scale(${scale}) translateY(${scrollY * 0.05}px)`;
        } else {
            product.style.transform = 'scale(1)'; // Reset scale if out of view
        }
    });
});

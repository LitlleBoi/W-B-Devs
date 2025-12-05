document.addEventListener('DOMContentLoaded', function () {
    const pano = document.querySelector('main');
    const minimap = document.getElementById('panoramaMinimap');
    const viewport = document.getElementById('panoramaMinimapViewport');
 
    if (!pano || !minimap || !viewport) {
        console.warn('Panorama of minimap elementen niet gevonden.');
        return;
    }
 
    function updateMinimap() {
        const scrollWidth = pano.scrollWidth;
        const clientWidth = pano.clientWidth;
        const scrollLeft = pano.scrollLeft;
 
        if (scrollWidth <= 0) return;
 
        const minimapWidth = minimap.clientWidth;
 
        const ratioVisible = clientWidth / scrollWidth;
        const viewportWidth = minimapWidth * ratioVisible;
 
        const ratioLeft = scrollLeft / scrollWidth;
        const viewportLeft = minimapWidth * ratioLeft;
 
        viewport.style.width = viewportWidth + 'px';
        viewport.style.left = viewportLeft + 'px';
    }
 
    pano.addEventListener('scroll', updateMinimap);
    window.addEventListener('resize', updateMinimap);
 
    minimap.addEventListener('click', function (e) {
        const rect = minimap.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const percentage = x / minimap.clientWidth;
 
        const targetScroll = percentage * pano.scrollWidth - (pano.clientWidth / 2);
 
        const maxScroll = pano.scrollWidth - pano.clientWidth;
        const newScrollLeft = Math.max(0, Math.min(targetScroll, maxScroll));
 
        pano.scrollTo({
            left: newScrollLeft,
            behavior: 'smooth'
        });
    });
 
    updateMinimap();
});
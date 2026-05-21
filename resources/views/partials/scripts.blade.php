<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script>
    const slider = document.getElementById('slider');
    const slides = document.querySelectorAll('#slider > div');

    let index = 0;
    const totalSlides = slides.length;

    function showSlide(i) {
        slider.style.transform = `translateX(calc(-${i * 100}% - ${i * 16}px))`;
    }

    // Next Slide
    function nextSlide() {
        index = (index + 1) % totalSlides;
        showSlide(index);
    }

    // Previous Slide
    function prevSlide() {
        index = (index - 1 + totalSlides) % totalSlides;
        showSlide(index);
    }

    // Auto Slide
    let autoSlide = setInterval(nextSlide, 3000);

    // Button Events
    document.getElementById('nextBtn').addEventListener('click', () => {
        nextSlide();
        resetAutoSlide();
    });

    document.getElementById('prevBtn').addEventListener('click', () => {
        prevSlide();
        resetAutoSlide();
    });

    // Reset Auto Timer
    function resetAutoSlide() {
        clearInterval(autoSlide);
        autoSlide = setInterval(nextSlide, 3000);
    }
</script>
<style>
    @keyframes newsScroll {
        0% {
            transform: translateY(0);
        }

        100% {
            transform: translateY(-50%);
        }
    }

    .animate-news-scroll {
        animation: newsScroll 18s linear infinite;
    }
</style>
<style>
    @keyframes newsScroll {
        0% {
            transform: translateY(0);
        }

        100% {
            transform: translateY(-50%);
        }
    }

    .animate-news-scroll {
        animation: newsScroll 18s linear infinite;
    }

    .animate-news-scroll:hover {
        animation-play-state: paused;
    }

    a {
        text-decoration: none !important;
    }
</style>
<script>
    const logoSlider = document.getElementById('logoSlider');

    let logoIndex = 0;

    function moveLogos() {
        logoSlider.style.transform = `translateX(-${logoIndex * 140}px)`;
    }

    document.getElementById('logoNext').addEventListener('click', () => {
        const maxScroll = logoSlider.children.length - 4;

        if (logoIndex < maxScroll) {
            logoIndex++;
            moveLogos();
        }
    });

    document.getElementById('logoPrev').addEventListener('click', () => {
        if (logoIndex > 0) {
            logoIndex--;
            moveLogos();
        }
    });

    // Auto Slide
    setInterval(() => {
        const maxScroll = logoSlider.children.length - 4;

        if (logoIndex >= maxScroll) {
            logoIndex = 0;
        } else {
            logoIndex++;
        }

        moveLogos();
    }, 2500);
</script>
<script>
    $(document).ready(function() {

        if ($.fn.DataTable.isDataTable('#whoTable')) {
            $('#whoTable').DataTable().destroy();
        }

        $('#whoTable').DataTable({
            responsive: true,
            pageLength: 10,
            ordering: true,
            autoWidth: false,
            lengthMenu: [10, 25, 50, 100],

            language: {
                search: "Search Officer:",
                lengthMenu: "Show _MENU_ entries",
            }
        });

    });
</script>

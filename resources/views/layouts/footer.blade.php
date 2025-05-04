{{-- resources/views/layouts/footer.blade.php --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<footer class="bg-white text-center text-md-end py-2 border-top mt-auto" dir="rtl">
    <div class="container">
        <div class="row align-items-center">
            {{-- Right column: About (placed first in RTL) --}}
            <div class="col-md-6 mb-3 mb-md-0 text-md-end">
                <h6 class="fw-bold">سیستم رأی‌گیری</h6>
                <p class="text-muted mb-1">
                    طراحی و توسعه توسط تیم فنی دانشگاه بین‌المللی امام خمینی (ره)
                </p>
                <p class="text-muted small mb-0">
                    کانون وکلای دادگستری استان قزوین &copy; {{ date('Y') }}. تمامی حقوق محفوظ است.
                </p>
            </div>

            {{-- Left column: Contact (placed second in RTL) --}}
            <div class="col-md-6 text-md-start">
                <p class="mb-1">تماس با ما</p>
                <a href="mailto:aliyeganegi2002@gmail.com" class="text-decoration-none text-muted ms-3">
                    <i class="fas fa-envelope"></i>
                </a>
                <a href="https://instagram.com" class="text-muted ms-3" target="_blank">
                    <i class="fab fa-instagram"></i>
                </a>
                <a href="https://linkedin.com" class="text-muted ms-3" target="_blank">
                    <i class="fab fa-linkedin"></i>
                </a>
                <a href="https://github.com/AliYeganegi" class="text-muted" target="_blank">
                    <i class="fab fa-github"></i>
                </a>
            </div>
        </div>
    </div>
</footer>

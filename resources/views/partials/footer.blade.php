<footer id="footer" class="m-3">
    <p class="legal ">
        &copy;{{ date('Y') }} {{ config('site.community_long_name') }}. All rights reserved.
    </p>
    <p class="userip d-print-none float-right">{{ request()->ip() }}</p>
</footer>

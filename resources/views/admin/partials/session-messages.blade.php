@if (session('success'))
    <script>
        $(document).Toasts('create', {
            class: 'bg-success',
            title: 'สำเร็จ!',
            body: '{{ session('success') }}',
            autohide: true,
            delay: 5000,
        });
    </script>
@endif

@if (session('error'))
    <script>
        $(document).Toasts('create', {
            class: 'bg-danger',
            title: 'เกิดข้อผิดพลาด!',
            body: '{{ session('error') }}',
            autohide: true,
            delay: 7000,
        });
    </script>
@endif

@if (session('info'))
    <script>
        $(document).Toasts('create', {
            class: 'bg-info',
            title: 'แจ้งเพื่อทราบ',
            body: '{{ session('info') }}',
            autohide: true,
            delay: 5000,
        });
    </script>
@endif

@if (session('warning'))
    <script>
        $(document).Toasts('create', {
            class: 'bg-warning',
            title: 'คำเตือน',
            body: '{{ session('warning') }}',
            autohide: true,
            delay: 6000,
        });
    </script>
@endif

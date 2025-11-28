<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'White Industry ERP System')</title>

    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.8/css/line.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
      <link rel="stylesheet" type="text/css" href="{{ asset('css/style.css') }}">
      <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.3.4/css/dataTables.dataTables.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />




</head>

<body>

    
       <div class="">
        @include('partials.sidebar')

        <div class="main-content">
            @include('partials.header')

            <main class="content">

                @yield('content')
            </main>
        </div>
    </div>


    <div id="commonModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modal-title" class="modal-title">Modal Title</h3>
                <button class="close" onclick="hideModel()">&times;</button>
            </div>
            <div id="body" class="body"></div>
        </div>
    </div>

<<!-- 1. jQuery (must load first) -->


    <script>
    

          function hideModel(){
    $("#commonModal").css('display','none');
}
        // Initialize Bootstrap toast if present. Retry briefly if bootstrap isn't loaded yet.
     
    </script>


    <script>
 


        

        function showModal() {
            const o = document.getElementById('modal-overlay');
            if (!o) return;
            o.style.zIndex = 100000;
            o.style.display = 'flex';
            o.classList.add('show');
        }

        function hideModal() {
            const o = document.getElementById('modal-overlay');
            if (!o) return;
            const modalContent = o.querySelector('.modal-content');
            if (modalContent && modalContent.classList.contains('confirm')) modalContent.classList.remove('confirm');
            o.classList.remove('show');
            o.style.display = 'none';
        }

      
    </script>

    <script>
        // Sidebar collapse toggle and persistence
        (function() {
            const toggle = document.getElementById('sidebar-toggle');
            const root = document.documentElement; // add class to <html> for CSS scope
            const STORAGE_KEY = 'wi_sidebar_collapsed';

            function setCollapsed(collapsed) {
                if (collapsed) {
                    document.body.classList.add('sidebar-collapsed');
                    toggle.setAttribute('aria-pressed', 'true');
                } else {
                    document.body.classList.remove('sidebar-collapsed');
                    toggle.setAttribute('aria-pressed', 'false');
                }
            }

            // Initialize from localStorage
            const saved = localStorage.getItem(STORAGE_KEY);
            setCollapsed(saved === '1');

            if (toggle) {
                toggle.addEventListener('click', function() {
                    const collapsed = document.body.classList.toggle('sidebar-collapsed');
                    localStorage.setItem(STORAGE_KEY, collapsed ? '1' : '0');
                    toggle.setAttribute('aria-pressed', collapsed ? 'true' : 'false');
                });
            }
        })();
    </script>

   


    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
  
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

<!-- 2. Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<!-- 3. Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    @stack('scripts')

    @yield('scripts')
<!-- 4. Initialize -->
<script>
    $(document).ready(function () {
        $('.select2').select2({
            placeholder: "Select Customer",
            allowClear: true,
            width: '100%'
        });
          $('#quotesTable').DataTable({
            responsive: true,
            pageLength: 10,
            ordering: true,
            searching: true
        });
    });
</script>
</body>

</html>

<link href="{{asset('css/admin_nav.css')}}" rel="stylesheet">


<div class="nav-side-menu">
    <div class="brand">Timerly Admin Panel</div>
    <i class="fa fa-bars fa-2x toggle-btn" data-toggle="collapse" data-target="#menu-content"></i>

    <div class="menu-list">

        <ul id="menu-content" class="menu-content collapse out">
            <li>
                <a href="{{route('admin.dashboard')}}">
                    <i class="fa fa-dashboard fa-lg"></i> Dashboard
                </a>
            </li>
            <li>
                <a href="">
                    <i class="fab fa-fort-awesome"></i> Shops
                </a>
            </li>
            <li>
                <a href="">
                    <i class="fab fa-fort-awesome"></i> Trials
                </a>
            </li>

            <li>
                <a href="{{ route('admin.logout') }}" >
                    <i class=""></i> Logout
                </a>
            </li>
        </ul>
    </div>
</div>
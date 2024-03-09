<div class="nav-item dropdown">
    <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown"
        aria-label="Open user menu">
        <span class="avatar avatar-sm" style="background-image: url({{ asset(config('custom.images.avatar')) }})"></span>
        <div class="d-none d-xl-block ps-2">
            <div>{{ auth('admin')->user()->fullname }}</div>
            <div class="mt-1 small text-muted">{{ auth('admin')->user()->roles->description() }}</div>
        </div>
    </a>
    <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
        <a href="{{ route('admin.profile.index') }}" class="dropdown-item">@lang('profile')</a>
        <a href="{{ route('admin.password.index') }}" class="dropdown-item">@lang('passwordChange')</a>
        <div class="dropdown-divider"></div>
        <a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#modalLogout">@lang('logout')</a>
    </div>
</div>
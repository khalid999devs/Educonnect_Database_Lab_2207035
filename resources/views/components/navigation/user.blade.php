@props(['user'])

<div class="sidebar-user">
    <span class="sidebar-user__avatar" aria-hidden="true">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
    <span class="sidebar-user__details">
        <strong>{{ $user->name }}</strong>
        <span>{{ ucfirst(strtolower($user->role)) }}</span>
    </span>
</div>

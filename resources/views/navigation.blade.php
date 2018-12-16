<?php 
use Laravel\Nova\Nova;
use Spatie\Permission\PermissionRegistrar;
?>

@if ((Nova::resourceForModel(app(PermissionRegistrar::class)->getRoleClass()))::authorizedToViewAny(request()) || (Nova::resourceForModel(app(PermissionRegistrar::class)->getPermissionClass()))::authorizedToViewAny(request()))
    <h3 class="flex items-center font-normal text-white mb-6 text-base no-underline">
        <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
            <path fill="var(--sidebar-icon)"
                  d="M7 10V7a5 5 0 1 1 10 0v3h2a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-8c0-1.1.9-2 2-2h2zm2 0h6V7a3 3 0 0 0-6 0v3zm-4 2v8h14v-8H5zm7 2a1 1 0 0 1 1 1v2a1 1 0 0 1-2 0v-2a1 1 0 0 1 1-1z"/>
        </svg>
        <span class="sidebar-label">
                @lang('laravel-nova-permission::navigation.sidebar-label')
        </span>
    </h3>

    <ul class="list-reset mb-8">

        @if((Nova::resourceForModel(app(PermissionRegistrar::class)->getRoleClass()))::authorizedToViewAny(request()))
        <li class="leading-wide mb-4 text-sm">
            <router-link :to="{
                name: 'index',
                params: {
                    resourceName: 'roles'
                }
            }" class="text-white ml-8 no-underline dim">
                @lang('laravel-nova-permission::resources.Roles')
            </router-link>
        </li>
        @endif

        @if((Nova::resourceForModel(app(PermissionRegistrar::class)->getPermissionClass()))::authorizedToViewAny(request()))
        <li class="leading-wide mb-4 text-sm">
            <router-link :to="{
                name: 'index',
                params: {
                    resourceName: 'permissions'
                }
            }" class="text-white ml-8 no-underline dim">
                @lang('laravel-nova-permission::resources.Permissions')
            </router-link>
        </li>
        @endif

    </ul>
@endif
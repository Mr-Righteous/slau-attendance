<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @livewireStyles()
        @include('partials.head')
        @filamentStyles()
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:sidebar sticky collapsible class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.header>
                <a href="{{ route('dashboard') }}" class="me-5 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
                    <x-app-logo />
                </a>
                <flux:sidebar.collapse class="in-data-flux-sidebar-on-desktop:not-in-data-flux-sidebar-collapsed=desktop:-mr-2" />
            </flux:sidebar.header>
            

            <flux:navlist variant="outline">
                @hasrole('student')
                        <flux:navlist.group :heading="__('Platform')" class="grid">
                            <flux:navlist.item icon="home" :href="route('student.dashboard')" :current="request()->routeIs('student.dashboard')" wire:navigate>{{ __('Dashboard') }}</flux:navlist.item>
                            <flux:navlist.item icon="academic-cap" :href="route('student.courses')" :current="request()->routeIs('student.courses')" wire:navigate>{{ __('My Courses') }}</flux:navlist.item>
                            <flux:navlist.item icon="chart-bar" :href="route('student.attendance')" :current="request()->routeIs('student.attendance')" wire:navigate>{{ __('My Attendance') }}</flux:navlist.item>
                        </flux:navlist.group>
                    
                @endhasrole

                @hasrole('admin')
                    <flux:navlist.item icon="user" :href="route('admin.dashboard')" :current="request()->routeIs('admin.*')">{{ __(Auth::user()->purpose) }}</flux:navlist.item>
                    <flux:menu.separator />

                    <flux:navlist.group :heading="__('Platform - HOD')" class="grid">
                        
                        <flux:navlist.item icon="home" :href="route('admin.dashboard')" :current="request()->routeIs('admin.dashboard')" wire:navigate>{{ __('Dashboard') }}</flux:navlist.item>
                        
                        
                        <flux:navlist.item icon="academic-cap" :href="route('admin.courses')" :current="request()->routeIs('admin.courses')" wire:navigate>{{ __('Manage Courses') }}</flux:navlist.item>
                        <flux:navlist.item icon="pencil-square" :href="route('admin.manage-course-units')" :current="request()->routeIs('admin.manage-course-units')" wire:navigate>{{ __('Manage Course Units') }}</flux:navlist.item>
                        <flux:navlist.item icon="academic-cap" :href="route('admin.manage-students')" :current="request()->routeIs('admin.manage-students')" wire:navigate>{{ __('Manage Students') }}</flux:navlist.item>
                        
                        <flux:navlist.item icon="user-group" :href="route('admin.view-attendance')" :current="request()->routeIs('admin.view-attendance')" wire:navigate>{{ __('View Attendance') }}</flux:navlist.item>
                        <flux:navlist.item icon="chart-bar" :href="route('admin.reports')" :current="request()->routeIs('admin.reports')" wire:navigate>{{ __('Advanced Reports') }}</flux:navlist.item>
                        <flux:navlist.item icon="pencil-square" :href="route('admin.attendance')" :current="request()->routeIs('admin.attendance')" wire:navigate>{{ __('Mark Attendance') }}</flux:navlist.item>
                        <flux:navlist.item icon="academic-cap" :href="route('admin.attendance-reports')" :current="request()->routeIs('admin.attendance-reports')" wire:navigate>{{ __('Attendance Reports') }}</flux:navlist.item>
                    
                    </flux:navlist.group>
                @endhasrole

                @hasanyrole(['big-admin','super-admin','faculty-dean'])
                    <flux:navlist.group :heading="__('Dean')" class="grid">
                        <flux:navlist.item icon="arrow-up-tray" :href="route('admin.import')" :current="request()->routeIs('admin.import')" wire:navigate>{{ __('Import Users') }}</flux:navlist.item>
                        <flux:navlist.item icon="pencil-square" :href="route('admin.manage-departments')" :current="request()->routeIs('admin.manage-departments')" wire:navigate>{{ __('Manage Departments') }}</flux:navlist.item>
                        <flux:navlist.item icon="academic-cap" :href="route('admin.manage-lecturers')" :current="request()->routeIs('admin.manage-lecturers')" wire:navigate>{{ __('Manage Lecturers') }}</flux:navlist.item>
                        </flux:navlist.group>
                @endhasanyrole


                @hasanyrole(['big-admin','super-admin'])
                    <flux:navlist.group :heading="__('Admin Only')" class="grid">
                        <flux:navlist.item icon="academic-cap" :href="route('admin.manage-faculties')" :current="request()->routeIs('admin.manage-faculties')" wire:navigate>{{ __('Manage Faculties') }}</flux:navlist.item>        
                    </flux:navlist.group>
                @endhasanyrole
           

                @hasrole('lecturer')
                    <flux:navlist.item icon="home" :href="route('lecturer.dashboard')" :current="request()->routeIs('lecturer.dashboard')" wire:navigate>{{ __('Dashboard') }}</flux:navlist.item>
                    <flux:navlist.item icon="academic-cap" :href="route('lecturer.courses')" :current="request()->routeIs('lecturer.courses')" wire:navigate>{{ __('My Courses') }}</flux:navlist.item>
                    <flux:navlist.item icon="chart-bar" :href="route('lecturer.attendance')" :current="request()->routeIs('lecturer.attendance')" wire:navigate>{{ __('View Attendance') }}</flux:navlist.item>
                @endhasrole

       
            </flux:navlist>

            

            <flux:spacer />
            @include('partials.theme')
            <flux:navlist variant="outline">
                <flux:navlist.item icon="folder-git-2" :href="route('settings.profile')" target="_blank">
                {{ __('Settings') }}
                </flux:navlist.item>

                
            </flux:navlist>

            <!-- Desktop User Menu -->
            <flux:dropdown class="hidden lg:block" position="bottom" align="start">
                <flux:profile
                    :name="auth()->user()->name"
                    :initials="auth()->user()->initials()"
                    icon:trailing="chevrons-up-down"
                />

                <flux:menu class="w-[220px]">
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                                    >
                                        {{ auth()->user()->initials() }}
                                    </span>
                                </span>

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                    <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:sidebar>

        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                                    >
                                        {{ auth()->user()->initials() }}
                                    </span>
                                </span>

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                    <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>
        @livewireScripts()
        @livewire('notifications')

        {{ $slot }}
        
        @fluxScripts
        @filamentScripts()
    </body>
</html>

<a id="show-sidebar" class="btn btn-sm btn-dark" href="#">
    <i class="fas fa-bars"></i>
</a>
<nav id="sidebar" class="sidebar-wrapper">
    <div class="sidebar-content">
        <div class="sidebar-item sidebar-brand">
            <a href="{{route('admin.index')}}">{{config('app.project_name')}}</a>
            <a></a>
            <a></a>
            <div id="close-sidebar" class="cursor-pointer">
                <i class="fas fa-times"></i>
            </div>
        </div>
        <div class="sidebar-header d-flex flex-nowrap">
            <div class="user-pic">
                <img class="img-responsive img-rounded" src="{{ asset('images/profile.png') }}" alt="User picture">
            </div>
            <div class="user-info">
                <span class="user-name">{{ Auth::guard('admin')->user()->name }}</span>
                <span class="user-role"></span>
            </div>
        </div>
        <div class=" sidebar-item sidebar-menu">
            <ul>
                <li class="">
                    <a href="{{ route('admin.index') }}">
                        <i class="fas fa-home"></i>
                        <span class="menu-text">{{__('ui.homepage')}}</span>
                    </a>
                </li>

                <li class="header-menu">
                    <span>{{__('ui.general')}}</span>
                </li>

                @canany(['user', 'deposit', 'payment'])
                    <li class="sidebar-dropdown ">
                        <a href="#">
                            <i class="fas fa-user"></i>
                            <span class="menu-text">{{__('ui.manage') . __('ui.merchant')}}</span>
                        </a>
                        <div class="sidebar-submenu">
                            <ul>
                                @can('user')
                                    <li>
                                        <a href="{{route('admin.users.index')}}">{{__('ui.merchant') . __('ui.list')}}</a>
                                    </li>
                                @endcan
                                @can('deposit')
                                    <li>
                                        <a href="{{route('admin.deposits.index')}}">{{__('ui.deposit') . __('ui.list')}}</a>
                                    </li>
                                @endcan
                                @can('payment')
                                    <li>
                                        <a href="{{route('admin.payments.index')}}">{{__('ui.payment') . __('ui.list')}}</a>
                                    </li>
                                @endcan
                            </ul>
                        </div>
                    </li>
                @endcanany
                @canany(['wallet'])
                    <li class="sidebar-dropdown ">
                        <a href="#">
                            <i class="fas fa-user"></i>
                            <span class="menu-text">{{__('ui.manage') . __('ui.wallet')}}</span>
                        </a>
                        <div class="sidebar-submenu">
                            <ul>
                                <li>
                                    <a href="{{route('admin.wallets.index')}}">{{__('ui.wallet') . __('ui.list')}}</a>
                                </li>
                            </ul>
                        </div>
                    </li>
                @endcanany

                @can('new')
                    <li class="sidebar-dropdown ">
                        <a href="#">
                            <i class="fas fa-user"></i>
                            <span class="menu-text">{{__('ui.new_setting')}}</span>
                        </a>
                        <div class="sidebar-submenu">
                            <ul>
                                <li>
                                    <a href="{{route('admin.news.index')}}">{{__('ui.new') . __('ui.list')}}</a>
                                </li>

                            </ul>
                        </div>
                    </li>
                @endcan

                @can('admin')
                    <li class="sidebar-dropdown">
                        <a href="#">
                            <i class="fas fa-user"></i>
                            <span class="menu-text">{{__('ui.manage') . __('ui.admin')}}</span>
                        </a>
                        <div class="sidebar-submenu">
                            <ul>
                                <li>
                                    <a href="{{route('admin.admins.index')}}">{{__('ui.admin') . __('ui.list')}}</a>
                                </li>
                            </ul>
                        </div>
                    </li>
                @endcan

                @can('bank_card')
                    <li class="sidebar-dropdown">
                        <a href="#">
                            <i class="fa fa-chart-line"></i>
                            <span class="menu-text">{{__('ui.bank_card')}}</span>
                        </a>
                        <div class="sidebar-submenu">
                            <ul>
                                <li>
                                    <a href="{{route('admin.bank-cards.index')}}">{{__('ui.bank_card') . __('ui.list')}}</a>
                                </li>
                            </ul>
                        </div>
                    </li>
                @endcan

                @can('report')
                    <li class="sidebar-dropdown ">
                        <a href="#">
                            <i class="fa fa-chart-line"></i>
                            <span class="menu-text">{{__('ui.report')}}</span>
                        </a>
                        <div class="sidebar-submenu">
                            <ul>
                                <li>
                                    <a href="{{route('admin.reports.index')}}">{{__('ui.query') . __('ui.report')}}</a>
                                </li>
                            </ul>
                        </div>
                    </li>
                @endcan

                <li class="header-menu">
                    <span>{{__('ui.other')}}</span>
                </li>
                <li>
                    <a href="{{route('admin.login-records.index')}}">
                        <i class="fas fa-clipboard-list"></i>
                        <span class="menu-text">{{__('ui.login_logs')}}</span>
                    </a>
                </li>
                <li>
                    <a href="{{route('admin.profile.index')}}">
                        <i class="fas fa-cog"></i>
                        <span class="menu-text">{{__('ui.account_settings')}}</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="sidebar-footer">
        <div class="pinned-footer"></div>
        <div>
            <form action="{{route('admin.logout')}}" method="POST">@csrf</form>
            <a class="cursor-pointer" onclick="this.previousElementSibling.submit()">
                <i class="fas fa-sign-out-alt"></i>
            </a>
        </div>
    </div>
</nav>

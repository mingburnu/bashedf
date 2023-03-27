<a id="show-sidebar" class="btn btn-sm btn-dark" href="#">
    <i class="fas fa-bars"></i>
</a>
<nav id="sidebar" class="sidebar-wrapper">
    <div class="sidebar-content">
        <!-- sidebar-brand  -->
        <div class="sidebar-item sidebar-brand">
            <a href="{{route('index')}}">{{config('app.project_name')}}</a>
            <a></a>
            <div id="close-sidebar" class="cursor-pointer">
                <i class="fas fa-times"></i>
            </div>
        </div>
        <!-- sidebar-header  -->
        <div class="sidebar-header d-flex flex-nowrap">
            <div class="user-pic">
                <img class="img-responsive img-rounded" src="{{ asset('images/profile.png') }}" alt="User picture">
            </div>
            <div class="user-info">
                <span class="user-name">
                    {{ Auth::user() != null ? Auth::user()->name : "" }}
                </span>
                <span class="user-role">
                    {{ Auth::user() != null ? Auth::user()->company : "" }}
                </span>
            </div>
        </div>

        <!-- sidebar-menu  -->
        <div class=" sidebar-item sidebar-menu">
            <ul>
                <li class="">
                    <a href="{{ route('index') }}">
                        <i class="fas fa-home"></i>
                        <span class="menu-text">{{__('ui.homepage')}}</span>
                    </a>
                </li>
                <li class="header-menu">
                    <span>{{__('ui.general')}}</span>
                </li>
                @can('use')
                    <li class="sidebar-dropdown">
                        <a href="#">
                            <i class="fas fa-money-check-alt"></i>
                            <span class="menu-text">{{__('ui.api_document')}}</span>
                        </a>
                        <div class="sidebar-submenu">
                            <ul>
                                <li>
                                    <a href="https://www.showdoc.com.cn/p/b9d53524dd5a636a3c6cf5422ec6be48"
                                       target="_blank">{{__('ui.api_information')}}</a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    <li class="sidebar-dropdown">
                        <a href="#">
                            <i class="fas fa-money-check-alt"></i>
                            <span class="menu-text">{{__('ui.deposit')}}</span>
                        </a>
                        <div class="sidebar-submenu">
                            <ul>
                                <li>
                                    <a href="{{route('deposits.create')}}">{{__('ui.apply') . __('ui.deposit')}}
                                        <span class="badge badge-pill badge-danger">Hot</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{route('deposits.index')}}">{{__('ui.deposit') . __('ui.list')}}</a>
                                </li>
                            </ul>
                        </div>
                    </li>
                @endcan

                <li class="sidebar-dropdown">
                    <a href="#">
                        <i class="fas fa-donate"></i>
                        <span class="menu-text">{{__('ui.payment')}}</span>
                    </a>
                    <div class="sidebar-submenu">
                        <ul>
                            <li>
                                <a href="{{route('payments.create')}}">{{__('ui.apply') . __('ui.payment')}}</a>
                            </li>
                            <li>
                                <a href="{{route('payments.index')}}">{{__('ui.payment') . __('ui.list')}}</a>
                            </li>
                        </ul>
                    </div>
                </li>
                @can('use')
                    <li class="sidebar-dropdown">
                        <a href="#">
                            <i class="fas fa-user"></i>
                            <span class="menu-text">{{__('ui.manage') . __('ui.clerk')}}</span>
                        </a>
                        <div class="sidebar-submenu">
                            <ul>
                                <li>
                                    <a href="{{route('children.create')}}">{{__('ui.create') . __('ui.clerk')}}</a>
                                </li>
                                <li>
                                    <a href="{{route('children.index')}}">{{__('ui.clerk') . __('ui.list')}}</a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    <li class="sidebar-dropdown">
                        <a href="#">
                            <i class="fas fa-chart-line"></i>
                            <span class="menu-text">{{__('ui.report')}}</span>
                        </a>
                        <div class="sidebar-submenu">
                            <ul>
                                <li>
                                    <a href="{{route('reports.index')}}">{{__('ui.query') . __('ui.report')}}</a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    <li class="sidebar-dropdown">
                        <a href="#">
                            <i class="fas fa-wallet"></i>
                            <span class="menu-text">{{__('ui.transaction')}}</span>
                        </a>
                        <div class="sidebar-submenu">
                            <ul>
                                <li>
                                    <a href="{{route('transactions.index')}}">{{__('ui.query') . __('ui.transaction')}}</a>
                                </li>
                            </ul>
                        </div>
                    </li>
                @endcan
                <li>
                    <a href="{{route('news.index')}}">
                        <i class="fas fa-clipboard-list"></i>
                        <span class="menu-text">{{__('ui.new')}}</span>
                    </a>
                </li>
                <li class="header-menu">
                    <span>{{__('ui.other')}}</span>
                </li>
                <li>
                    <a href="{{route('login-records.index')}}">
                        <i class="fas fa-clipboard-list"></i>
                        <span class="menu-text">{{__('ui.login_logs')}}</span>
                    </a>
                </li>
                <li>
                    <a href="{{route('profile.index')}}">
                        <i class="fas fa-cog"></i>
                        <span class="menu-text">{{__('ui.account_settings')}}</span>
                    </a>
                </li>
            </ul>
        </div>
        <!-- sidebar-menu  -->
    </div>
    <!-- sidebar-footer  -->
    <div class="sidebar-footer">
        <div class="pinned-footer"></div>
        <div>
            <form action="{{route('logout')}}" method="POST">@csrf</form>
            <a class="cursor-pointer" onclick="this.previousElementSibling.submit()">
                <i class="fas fa-sign-out-alt"></i>
            </a>
        </div>
    </div>
</nav>
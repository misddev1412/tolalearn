<div class="dropdown">
    <button type="button" class="btn btn-transparent dropdown-toggle" {{ (empty($unReadNotifications) or count($unReadNotifications) < 1) ? 'disabled' : '' }} id="navbarNotification" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i data-feather="bell" width="20" height="20" class="mr-10"></i>

        @if(!empty($unReadNotifications) and count($unReadNotifications))
            <span class="badge badge-circle-danger d-flex align-items-center justify-content-center">{{ count($unReadNotifications) }}</span>
        @endif
    </button>

    <div class="dropdown-menu pt-20" aria-labelledby="navbarNotification">
        <div class="d-flex flex-column h-100">
            <div class="mb-auto navbar-notification-card" data-simplebar>
                <div class="d-md-none border-bottom mb-20 pb-10 text-right">
                    <i class="close-dropdown" data-feather="x" width="32" height="32" class="mr-10"></i>
                </div>

                @if(!empty($unReadNotifications) and count($unReadNotifications))

                    @foreach($unReadNotifications as $unReadNotification)
                        <a href="/panel/notifications?notification={{ $unReadNotification->id }}">
                            <div class="navbar-notification-item border-bottom">
                                <h4 class="font-14 font-weight-bold text-secondary">{{ $unReadNotification->title }}</h4>
                                <span class="notify-at d-block mt-5">{{ dateTimeFormat($unReadNotification->created_at,'Y M j | H:i') }}</span>
                            </div>
                        </a>
                    @endforeach

                @else
                    <div class="d-flex align-items-center text-center py-50">
                        <i data-feather="bell" width="20" height="20" class="mr-10"></i>
                        <span class="">{{ trans('notification.empty_notifications') }}</span>
                    </div>
                @endif

            </div>

            @if(!empty($unReadNotifications) and count($unReadNotifications))
                <div class="mt-10 navbar-notification-action">
                    <a href="/panel/notifications" class="btn btn-sm btn-danger btn-block">{{ trans('notification.all_notifications') }}</a>
                </div>
            @endif
        </div>
    </div>
</div>

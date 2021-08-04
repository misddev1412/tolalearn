<div class="main-sidebar">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">
            <a href="/">
                @if(!empty($generalSettings) and $generalSettings['site_name'])
                    {{ strtoupper($generalSettings['site_name']) }}
                @endif
            </a>
        </div>
        <div class="sidebar-brand sidebar-brand-sm">
            <a href="/">
                @if(!empty($generalSettings) and $generalSettings['site_name'])
                    {{ strtoupper(substr($generalSettings['site_name'],0,2)) }}
                @endif
            </a>
        </div>

        <ul class="sidebar-menu">
            @can('admin_general_dashboard_show')
                <li class="{{ (request()->is('admin/')) ? 'active' : '' }}">
                    <a href="/admin" class="nav-link">
                        <i class="fas fa-fire"></i>
                        <span>{{ trans('admin/main.dashboard') }}</span>
                    </a>
                </li>
            @endcan

            @can('admin_marketing_dashboard')
                <li class="{{ (request()->is('admin/marketing')) ? 'active' : '' }}">
                    <a href="/admin/marketing" class="nav-link">
                        <i class="fas fa-chart-pie"></i>
                        <span>{{ trans('admin/main.marketing_dashboard_title') }}</span>
                    </a>
                </li>
            @endcan

            @if($authUser->can('admin_webinars') or
                $authUser->can('admin_categories') or
                $authUser->can('admin_filters') or
                $authUser->can('admin_quizzes') or
                $authUser->can('admin_certificate') or
                $authUser->can('admin_reviews_lists')
            )
                <li class="menu-header">{{ trans('site.education') }}</li>
            @endif

            @can('admin_webinars')
                <li class="nav-item dropdown {{ (request()->is('admin/webinars*') and !request()->is('admin/webinars/comments*')) ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-graduation-cap"></i>
                        <span>{{ trans('panel.classes') }}</span>
                    </a>
                    <ul class="dropdown-menu">
                        @can('admin_webinars_list')
                            <li class="{{ (request()->is('admin/webinars') and request()->get('type') == 'course') ? 'active' : '' }}">
                                <a class="nav-link @if(!empty($sidebarBeeps['courses']) and $sidebarBeeps['courses']) beep beep-sidebar @endif" href="/admin/webinars?type=course">{{ trans('admin/main.courses') }}</a>
                            </li>

                            <li class="{{ (request()->is('admin/webinars') and request()->get('type') == 'webinar') ? 'active' : '' }}">
                                <a class="nav-link @if(!empty($sidebarBeeps['webinars']) and $sidebarBeeps['webinars']) beep beep-sidebar @endif" href="/admin/webinars?type=webinar">{{ trans('admin/main.live_classes') }}</a>
                            </li>

                            <li class="{{ (request()->is('admin/webinars') and request()->get('type') == 'text_lesson') ? 'active' : '' }}">
                                <a class="nav-link @if(!empty($sidebarBeeps['textLessons']) and $sidebarBeeps['textLessons']) beep beep-sidebar @endif" href="/admin/webinars?type=text_lesson">{{ trans('admin/main.text_courses') }}</a>
                            </li>
                        @endcan()

                        @can('admin_webinars_create')
                            <li class="{{ (request()->is('admin/webinars/create')) ? 'active' : '' }}">
                                <a class="nav-link" href="/admin/webinars/create">{{ trans('admin/main.new') }}</a>
                            </li>
                        @endcan()

                    </ul>
                </li>
            @endcan()

            @can('admin_categories')
                <li class="nav-item dropdown {{ (request()->is('admin/categories*')) ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-th"></i>
                        <span>{{ trans('admin/main.categories') }}</span>
                    </a>
                    <ul class="dropdown-menu">
                        @can('admin_categories_list')
                            <li class="{{ (request()->is('admin/categories')) ? 'active' : '' }}">
                                <a class="nav-link" href="/admin/categories">{{ trans('admin/main.lists') }}</a>
                            </li>
                        @endcan()
                        @can('admin_categories_create')
                            <li class="{{ (request()->is('admin/categories/create')) ? 'active' : '' }}">
                                <a class="nav-link" href="/admin/categories/create">{{ trans('admin/main.new') }}</a>
                            </li>
                        @endcan()
                        @can('admin_trending_categories')
                            <li class="{{ (request()->is('admin/categories/trends')) ? 'active' : '' }}">
                                <a class="nav-link" href="/admin/categories/trends">{{ trans('admin/main.trends') }}</a>
                            </li>
                        @endcan()
                    </ul>
                </li>
            @endcan()

            @can('admin_filters')
                <li class="nav-item dropdown {{ (request()->is('admin/filters*')) ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-filter"></i>
                        <span>{{ trans('admin/main.filters') }}</span>
                    </a>
                    <ul class="dropdown-menu">
                        @can('admin_filters_list')
                            <li class="{{ (request()->is('admin/filters')) ? 'active' : '' }}">
                                <a class="nav-link" href="/admin/filters">{{ trans('admin/main.lists') }}</a>
                            </li>
                        @endcan()
                        @can('admin_filters_create')
                            <li class="{{ (request()->is('admin/filters/create')) ? 'active' : '' }}">
                                <a class="nav-link" href="/admin/filters/create">{{ trans('admin/main.new') }}</a>
                            </li>
                        @endcan()
                    </ul>
                </li>
            @endcan()

            @can('admin_quizzes')
                <li class="{{ (request()->is('admin/quizzes*')) ? 'active' : '' }}">
                    <a class="nav-link " href="/admin/quizzes">
                        <i class="fa fa-file"></i>
                        <span>{{ trans('admin/main.quizzes') }}</span>
                    </a>
                </li>
            @endcan()

            @can('admin_certificate')
                <li class="nav-item dropdown {{ (request()->is('admin/certificates*')) ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-newspaper"></i>
                        <span>{{ trans('admin/main.certificates') }}</span>
                    </a>
                    <ul class="dropdown-menu">
                        @can('admin_certificate_list')
                            <li class="{{ (request()->is('admin/certificates')) ? 'active' : '' }}">
                                <a class="nav-link" href="/admin/certificates">{{ trans('admin/main.lists') }}</a>
                            </li>
                        @endcan

                        @can('admin_certificate_template_list')
                            <li class="{{ (request()->is('admin/certificates/templates')) ? 'active' : '' }}">
                                <a class="nav-link"
                                   href="/admin/certificates/templates">{{ trans('admin/main.templates') }}</a>
                            </li>
                        @endcan

                        @can('admin_certificate_template_create')
                            <li class="{{ (request()->is('admin/certificates/templates/new')) ? 'active' : '' }}">
                                <a class="nav-link"
                                   href="/admin/certificates/templates/new">{{ trans('admin/main.new_template') }}</a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcan

            @can('admin_reviews_lists')
                <li class="{{ (request()->is('admin/reviews')) ? 'active' : '' }}">
                    <a href="/admin/reviews" class="nav-link @if(!empty($sidebarBeeps['reviews']) and $sidebarBeeps['reviews']) beep beep-sidebar @endif">
                        <i class="fas fa-star"></i>
                        <span>Reviews</span>
                    </a>
                </li>
            @endcan

            @if($authUser->can('admin_consultants_lists') or
                $authUser->can('admin_appointments_lists')
            )
                <li class="menu-header">{{ trans('site.appointments') }}</li>
            @endif

            @can('admin_consultants_lists')
                <li class="{{ (request()->is('admin/consultants')) ? 'active' : '' }}">
                    <a href="/admin/consultants" class="nav-link">
                        <i class="fas fa-id-card"></i>
                        <span>{{ trans('admin/main.consultants') }}</span>
                    </a>
                </li>
            @endcan

            @can('admin_appointments_lists')
                <li class="{{ (request()->is('admin/appointments')) ? 'active' : '' }}">
                    <a class="nav-link" href="/admin/appointments">
                        <i class="fas fa-address-book"></i>
                        <span>{{ trans('admin/main.appointments') }}</span>
                    </a>
                </li>
            @endcan

            @if($authUser->can('admin_users') or
                $authUser->can('admin_roles') or
                $authUser->can('admin_group') or
                $authUser->can('admin_users_badges') or
                $authUser->can('admin_become_instructors_list')
            )
                <li class="menu-header">{{ trans('panel.users') }}</li>
            @endif

            @can('admin_users')
                <li class="nav-item dropdown {{ (request()->is('admin/staffs') or request()->is('admin/students') or request()->is('admin/instructors') or request()->is('admin/organizations')) ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-users"></i>
                        <span>{{ trans('admin/main.users_list') }}</span>
                    </a>

                    <ul class="dropdown-menu">
                        @can('admin_staffs_list')
                            <li class="{{ (request()->is('admin/staffs')) ? 'active' : '' }}">
                                <a class="nav-link" href="/admin/staffs">{{ trans('admin/main.staff') }}</a>
                            </li>
                        @endcan()

                        @can('admin_users_list')
                            <li class="{{ (request()->is('admin/students')) ? 'active' : '' }}">
                                <a class="nav-link" href="/admin/students">{{ trans('public.students') }}</a>
                            </li>
                        @endcan()

                        @can('admin_instructors_list')
                            <li class="{{ (request()->is('admin/instructors')) ? 'active' : '' }}">
                                <a class="nav-link" href="/admin/instructors">{{ trans('home.instructors') }}</a>
                            </li>
                        @endcan()

                        @can('admin_organizations_list')
                            <li class="{{ (request()->is('admin/organizations')) ? 'active' : '' }}">
                                <a class="nav-link" href="/admin/organizations">{{ trans('admin/main.organizations') }}</a>
                            </li>
                        @endcan()

                        @can('admin_users_create')
                            <li class="{{ (request()->is('admin/users/create')) ? 'active' : '' }}">
                                <a class="nav-link" href="/admin/users/create">{{ trans('admin/main.new') }}</a>
                            </li>
                        @endcan()
                    </ul>
                </li>
            @endcan

            @can('admin_roles')
                <li class="nav-item dropdown {{ (request()->is('admin/roles*')) ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-user-circle"></i> <span>{{ trans('admin/main.roles') }}</span>
                    </a>
                    <ul class="dropdown-menu">
                        @can('admin_roles_list')
                            <li class="{{ (request()->is('admin/roles')) ? 'active' : '' }}">
                                <a class="nav-link active" href="/admin/roles">{{ trans('admin/main.lists') }}</a>
                            </li>
                        @endcan()
                        @can('admin_roles_create')
                            <li class="{{ (request()->is('admin/roles/create')) ? 'active' : '' }}">
                                <a class="nav-link" href="/admin/roles/create">{{ trans('admin/main.new') }}</a>
                            </li>
                        @endcan()
                    </ul>
                </li>
            @endcan()

            @can('admin_group')
                <li class="nav-item dropdown {{ (request()->is('admin/users/groups*')) ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-sitemap"></i>
                        <span>{{ trans('admin/main.groups') }}</span>
                    </a>
                    <ul class="dropdown-menu">
                        @can('admin_group_list')
                            <li class="{{ (request()->is('admin/users/groups')) ? 'active' : '' }}">
                                <a class="nav-link" href="/admin/users/groups">{{ trans('admin/main.lists') }}</a>
                            </li>
                        @endcan
                        @can('admin_group_create')
                            <li class="{{ (request()->is('admin/users/groups/create')) ? 'active' : '' }}">
                                <a class="nav-link" href="/admin/users/groups/create">{{ trans('admin/main.new') }}</a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcan

            @can('admin_users_badges')
                <li class="{{ (request()->is('admin/users/badges')) ? 'active' : '' }}">
                    <a class="nav-link" href="/admin/users/badges"><i class="fas fa-trophy"></i> {{ trans('admin/main.badges') }}</a>
                </li>
            @endcan()

            @can('admin_become_instructors_list')
                <li class="{{ (request()->is('admin/users/become_instructors')) ? 'active' : '' }}">
                    <a class="nav-link" href="/admin/users/become_instructors"><i class="fas fa-list-alt"></i> {{ trans('admin/main.instructor_requests') }}</a>
                </li>
            @endcan()

            @if($authUser->can('admin_supports') or
                $authUser->can('admin_comments') or
                $authUser->can('admin_reports') or
                $authUser->can('admin_contacts') or
                $authUser->can('admin_noticeboards') or
                $authUser->can('admin_notifications')
            )
                <li class="menu-header">{{ trans('admin/main.crm') }}</li>
            @endif

            @can('admin_supports')
                <li class="nav-item dropdown {{ (request()->is('admin/supports*') and request()->get('type') != 'course_conversations') ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-headphones"></i>
                        <span>{{ trans('admin/main.supports') }}</span>
                    </a>

                    <ul class="dropdown-menu">
                        @can('admin_supports_list')
                            <li class="{{ (request()->is('admin/supports')) ? 'active' : '' }}">
                                <a class="nav-link" href="/admin/supports">{{ trans('public.tickets') }}</a>
                            </li>
                        @endcan

                        @can('admin_support_send')
                            <li class="{{ (request()->is('admin/supports/create')) ? 'active' : '' }}">
                                <a class="nav-link" href="/admin/supports/create">{{ trans('admin/main.new_ticket') }}</a>
                            </li>
                        @endcan

                        @can('admin_support_departments')
                            <li class="{{ (request()->is('admin/supports/departments')) ? 'active' : '' }}">
                                <a class="nav-link" href="/admin/supports/departments">{{ trans('admin/main.departments') }}</a>
                            </li>
                        @endcan
                    </ul>
                </li>

                @can('admin_support_course_conversations')
                    <li class="{{ (request()->is('admin/supports*') and request()->get('type') == 'course_conversations') ? 'active' : '' }}">
                        <a class="nav-link" href="/admin/supports?type=course_conversations">
                            <i class="fas fa-envelope-square"></i>
                            <span>{{ trans('admin/main.classes_conversations') }}</span>
                        </a>
                    </li>
                @endcan
            @endcan

            @can('admin_comments')
                <li class="nav-item dropdown {{ (request()->is('admin/comments*') and !request()->is('admin/comments/webinars/reports')) ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown"><i class="fas fa-comments"></i> <span>{{ trans('admin/main.comments') }}</span></a>
                    <ul class="dropdown-menu">
                        @can('admin_webinar_comments')
                            <li class="{{ (request()->is('admin/comments/webinars')) ? 'active' : '' }}">
                                <a class="nav-link @if(!empty($sidebarBeeps['classesComments']) and $sidebarBeeps['classesComments']) beep beep-sidebar @endif" href="/admin/comments/webinars">{{ trans('admin/main.classes_comments') }}</a>
                            </li>
                        @endcan

                        @can('admin_blog_comments')
                            <li class="{{ (request()->is('admin/comments/blog')) ? 'active' : '' }}">
                                <a class="nav-link @if(!empty($sidebarBeeps['blogComments']) and $sidebarBeeps['blogComments']) beep beep-sidebar @endif" href="/admin/comments/blog">{{ trans('admin/main.blog_comments') }}</a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcan

            @can('admin_reports')
                <li class="nav-item dropdown {{ (request()->is('admin/reports*') or request()->is('admin/comments/webinars/reports') or request()->is('admin/comments/blog/reports')) ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown"><i class="fas fa-info-circle"></i> <span>{{ trans('admin/main.reports') }}</span></a>

                    <ul class="dropdown-menu">
                        @can('admin_webinar_reports')
                            <li class="{{ (request()->is('admin/reports/webinars')) ? 'active' : '' }}">
                                <a class="nav-link" href="/admin/reports/webinars">{{ trans('panel.classes') }}</a>
                            </li>
                        @endcan

                        @can('admin_webinar_comments_reports')
                            <li class="{{ (request()->is('admin/comments/webinars/reports')) ? 'active' : '' }}">
                                <a class="nav-link" href="/admin/comments/webinars/reports">{{ trans('admin/main.classes_comments_reports') }}</a>
                            </li>
                        @endcan

                        @can('admin_blog_comments_reports')
                            <li class="{{ (request()->is('admin/comments/blog/reports')) ? 'active' : '' }}">
                                <a class="nav-link" href="/admin/comments/blog/reports">{{ trans('admin/main.blog_comments_reports') }}</a>
                            </li>
                        @endcan

                        @can('admin_report_reasons')
                            <li class="{{ (request()->is('admin/reports/reasons')) ? 'active' : '' }}">
                                <a class="nav-link" href="/admin/reports/reasons">{{ trans('admin/main.report_reasons') }}</a>
                            </li>
                        @endcan()
                    </ul>
                </li>
            @endcan

            @can('admin_contacts')
                <li class="{{ (request()->is('admin/contacts*')) ? 'active' : '' }}">
                    <a class="nav-link" href="/admin/contacts">
                        <i class="fas fa-phone-square"></i>
                        <span>{{ trans('admin/main.contacts') }}</span>
                    </a>
                </li>
            @endcan

            @can('admin_noticeboards')
                <li class="nav-item dropdown {{ (request()->is('admin/noticeboards*')) ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown"><i class="fas fa-sticky-note"></i> <span>{{ trans('admin/main.noticeboard') }}</span></a>
                    <ul class="dropdown-menu">
                        @can('admin_noticeboards_list')
                            <li class="{{ (request()->is('admin/noticeboards')) ? 'active' : '' }}">
                                <a class="nav-link" href="/admin/noticeboards">{{ trans('admin/main.notices_list_title') }}</a>
                            </li>
                        @endcan

                        @can('admin_noticeboards_send')
                            <li class="{{ (request()->is('admin/noticeboards/send')) ? 'active' : '' }}">
                                <a class="nav-link" href="/admin/noticeboards/send">{{ trans('admin/main.new_notice_title') }}</a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcan

            @can('admin_notifications')
                <li class="nav-item dropdown {{ (request()->is('admin/notifications*')) ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-bell"></i>
                        <span>{{ trans('admin/main.notifications') }}</span>
                    </a>

                    <ul class="dropdown-menu">
                        @can('admin_notifications_list')
                            <li class="{{ (request()->is('admin/notifications')) ? 'active' : '' }}">
                                <a class="nav-link" href="/admin/notifications">{{ trans('public.history') }}</a>
                            </li>
                        @endcan

                        @can('admin_notifications_send')
                            <li class="{{ (request()->is('admin/notifications/send')) ? 'active' : '' }}">
                                <a class="nav-link" href="/admin/notifications/send">{{ trans('admin/main.new') }}</a>
                            </li>
                        @endcan

                        @can('admin_notifications_templates')
                            <li class="{{ (request()->is('admin/notifications/templates')) ? 'active' : '' }}">
                                <a class="nav-link" href="/admin/notifications/templates">{{ trans('admin/main.templates') }}</a>
                            </li>
                        @endcan

                        @can('admin_notifications_template_create')
                            <li class="{{ (request()->is('admin/notifications/templates/create')) ? 'active' : '' }}">
                                <a class="nav-link" href="/admin/notifications/templates/create">{{ trans('admin/main.new_template') }}</a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcan

            @if($authUser->can('admin_blog') or
                $authUser->can('admin_pages') or
                $authUser->can('admin_additional_pages') or
                $authUser->can('admin_testimonials') or
                $authUser->can('admin_tags')
            )
                <li class="menu-header">{{ trans('admin/main.content') }}</li>
            @endif

            @can('admin_blog')
                <li class="nav-item dropdown {{ (request()->is('admin/blog*') and !request()->is('admin/blog/comments')) ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-rss-square"></i>
                        <span>{{ trans('admin/main.blog') }}</span>
                    </a>
                    <ul class="dropdown-menu">
                        @can('admin_blog_lists')
                            <li class="{{ (request()->is('admin/blog')) ? 'active' : '' }}">
                                <a class="nav-link" href="/admin/blog">{{ trans('site.posts') }}</a>
                            </li>
                        @endcan

                        @can('admin_blog_create')
                            <li class="{{ (request()->is('admin/blog/create')) ? 'active' : '' }}">
                                <a class="nav-link" href="/admin/blog/create">{{ trans('admin/main.new_post') }}</a>
                            </li>
                        @endcan

                        @can('admin_blog_categories')
                            <li class="{{ (request()->is('admin/blog/categories')) ? 'active' : '' }}">
                                <a class="nav-link" href="/admin/blog/categories">{{ trans('admin/main.categories') }}</a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcan()

            @can('admin_pages')
                <li class="nav-item dropdown {{ (request()->is('admin/pages*')) ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-pager"></i>
                        <span>{{ trans('admin/main.pages') }}</span>
                    </a>

                    <ul class="dropdown-menu">
                        @can('admin_pages_list')
                            <li class="{{ (request()->is('admin/pages')) ? 'active' : '' }}">
                                <a class="nav-link" href="/admin/pages">{{ trans('admin/main.lists') }}</a>
                            </li>
                        @endcan()

                        @can('admin_pages_create')
                            <li class="{{ (request()->is('admin/pages/create')) ? 'active' : '' }}">
                                <a class="nav-link" href="/admin/pages/create">{{ trans('admin/main.new_page') }}</a>
                            </li>
                        @endcan()
                    </ul>
                </li>
            @endcan

            @can('admin_additional_pages')
                <li class="nav-item dropdown {{ (request()->is('admin/additional_page*')) ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown"><i class="fas fa-plus-circle"></i> <span>{{ trans('admin/main.additional_pages_title') }}</span></a>
                    <ul class="dropdown-menu">

                        @can('admin_additional_pages_404')
                            <li class="{{ (request()->is('admin/additional_page/404')) ? 'active' : '' }}">
                                <a class="nav-link" href="/admin/additional_page/404">{{ trans('admin/main.error_404') }}</a>
                            </li>
                        @endcan()

                        @can('admin_additional_pages_contact_us')
                            <li class="{{ (request()->is('admin/additional_page/contact_us')) ? 'active' : '' }}">
                                <a class="nav-link" href="/admin/additional_page/contact_us">{{ trans('admin/main.contact_us') }}</a>
                            </li>
                        @endcan()

                        @can('admin_additional_pages_footer')
                            <li class="{{ (request()->is('admin/additional_page/footer')) ? 'active' : '' }}">
                                <a class="nav-link" href="/admin/additional_page/footer">{{ trans('admin/main.footer') }}</a>
                            </li>
                        @endcan()

                        @can('admin_additional_pages_navbar_links')
                            <li class="{{ (request()->is('admin/additional_page/navbar_links')) ? 'active' : '' }}">
                                <a class="nav-link" href="/admin/additional_page/navbar_links">{{ trans('admin/main.top_navbar') }}</a>
                            </li>
                        @endcan()
                    </ul>
                </li>
            @endcan

            @can('admin_testimonials')
                <li class="nav-item dropdown {{ (request()->is('admin/testimonials*')) ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-address-card"></i>
                        <span>{{ trans('admin/main.testimonials') }}</span>
                    </a>
                    <ul class="dropdown-menu">
                        @can('admin_testimonials_list')
                            <li class="{{ (request()->is('admin/testimonials')) ? 'active' : '' }}">
                                <a class="nav-link" href="/admin/testimonials">{{ trans('admin/main.lists') }}</a>
                            </li>
                        @endcan()

                        @can('admin_testimonials_create')
                            <li class="{{ (request()->is('admin/testimonials/create')) ? 'active' : '' }}">
                                <a class="nav-link" href="/admin/testimonials/create">{{ trans('admin/main.new') }}</a>
                            </li>
                        @endcan()
                    </ul>
                </li>
            @endcan

            @can('admin_tags')
                <li class="nav-item dropdown {{ (request()->is('admin/tags*')) ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-tags"></i>
                        <span>{{ trans('admin/main.tags') }}</span>
                    </a>
                    <ul class="dropdown-menu">
                        @can('admin_tags_list')
                            <li class="{{ (request()->is('admin/tags')) ? 'active' : '' }}">
                                <a class="nav-link" href="/admin/tags">{{ trans('admin/main.lists') }}</a>
                            </li>
                        @endcan()
                        @can('admin_tags_create')
                            <li class="{{ (request()->is('admin/tags/create')) ? 'active' : '' }}">
                                <a class="nav-link" href="/admin/tags/create">{{ trans('admin/main.new') }}</a>
                            </li>
                        @endcan()
                    </ul>
                </li>
            @endcan()

            @if($authUser->can('admin_documents') or
                $authUser->can('admin_sales_list') or
                $authUser->can('admin_payouts') or
                $authUser->can('admin_offline_payments_list') or
                $authUser->can('admin_subscribe')
            )
                <li class="menu-header">{{ trans('admin/main.financial') }}</li>
            @endif

            @can('admin_documents')
                <li class="nav-item dropdown {{ (request()->is('admin/financial/documents*')) ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-file-invoice-dollar"></i>
                        <span>Balances</span>
                    </a>
                    <ul class="dropdown-menu">

                        @can('admin_documents_list')
                            <li class="{{ (request()->is('admin/financial/documents')) ? 'active' : '' }}">
                                <a class="nav-link" href="/admin/financial/documents">{{ trans('admin/main.list') }}</a>
                            </li>
                        @endcan

                        @can('admin_documents_create')
                            <li class="{{ (request()->is('admin/financial/documents/new')) ? 'active' : '' }}">
                                <a class="nav-link" href="/admin/financial/documents/new">{{ trans('admin/main.new') }}</a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcan

            @can('admin_sales_list')
                <li class="{{ (request()->is('admin/financial/sales*')) ? 'active' : '' }}">
                    <a href="/admin/financial/sales" class="nav-link">
                        <i class="fas fa-list-ul"></i>
                        <span>{{ trans('admin/main.sales_list') }}</span>
                    </a>
                </li>
            @endcan

            @can('admin_payouts')
                <li class="nav-item dropdown {{ (request()->is('admin/financial/payouts*')) ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown"><i class="fas fa-credit-card"></i> <span>{{ trans('admin/main.payout') }}</span></a>
                    <ul class="dropdown-menu">
                        @can('admin_payouts_list')
                            <li class="{{ (request()->is('admin/financial/payouts') and request()->get('payout') == 'requests') ? 'active' : '' }}">
                                <a href="/admin/financial/payouts?payout=requests" class="nav-link @if(!empty($sidebarBeeps['payoutRequest']) and $sidebarBeeps['payoutRequest']) beep beep-sidebar @endif">
                                    <span>{{ trans('panel.requests') }}</span>
                                </a>
                            </li>
                        @endcan

                        @can('admin_payouts_list')
                            <li class="{{ (request()->is('admin/financial/payouts') and request()->get('payout') == 'history') ? 'active' : '' }}">
                                <a href="/admin/financial/payouts?payout=history" class="nav-link">
                                    <span>{{ trans('public.history') }}</span>
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcan

            @can('admin_offline_payments_list')
                <li class="nav-item dropdown {{ (request()->is('admin/financial/offline_payments*')) ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown"><i class="fas fa-university"></i> <span>{{ trans('admin/main.offline_payments') }}</span></a>
                    <ul class="dropdown-menu">
                        <li class="{{ (request()->is('admin/financial/offline_payments') and request()->get('page_type') == 'requests') ? 'active' : '' }}">
                            <a href="/admin/financial/offline_payments?page_type=requests" class="nav-link @if(!empty($sidebarBeeps['offlinePayments']) and $sidebarBeeps['offlinePayments']) beep beep-sidebar @endif">
                                <span>{{ trans('panel.requests') }}</span>
                            </a>
                        </li>

                        <li class="{{ (request()->is('admin/financial/offline_payments') and request()->get('page_type') == 'history') ? 'active' : '' }}">
                            <a href="/admin/financial/offline_payments?page_type=history" class="nav-link">
                                <span>{{ trans('public.history') }}</span>
                            </a>
                        </li>
                    </ul>
                </li>
            @endcan

            @can('admin_subscribe')
                <li class="nav-item dropdown {{ (request()->is('admin/financial/subscribes*')) ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-cart-plus"></i>
                        <span>{{ trans('admin/main.subscribes') }}</span>
                    </a>
                    <ul class="dropdown-menu">
                        @can('admin_subscribe_list')
                            <li class="{{ (request()->is('admin/financial/subscribes')) ? 'active' : '' }}">
                                <a class="nav-link" href="/admin/financial/subscribes">{{ trans('admin/main.packages') }}</a>
                            </li>
                        @endcan

                        @can('admin_subscribe_create')
                            <li class="{{ (request()->is('admin/financial/subscribes/new')) ? 'active' : '' }}">
                                <a class="nav-link" href="/admin/financial/subscribes/new">{{ trans('admin/main.new_package') }}</a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcan

            @if($authUser->can('admin_discount_codes') or
                $authUser->can('admin_product_discount') or
                $authUser->can('admin_feature_webinars') or
                $authUser->can('admin_promotion') or
                $authUser->can('admin_advertising') or
                $authUser->can('admin_newsletters_lists')
            )
                <li class="menu-header">{{ trans('admin/main.marketing') }}</li>
            @endif

            @can('admin_discount_codes')
                <li class="nav-item dropdown {{ (request()->is('admin/financial/discounts*')) ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-percent"></i>
                        <span>{{ trans('admin/main.discount_codes_title') }}</span>
                    </a>
                    <ul class="dropdown-menu">
                        @can('admin_discount_codes_list')
                            <li class="{{ (request()->is('admin/financial/discounts')) ? 'active' : '' }}">
                                <a class="nav-link" href="/admin/financial/discounts">{{ trans('admin/main.lists') }}</a>
                            </li>
                        @endcan

                        @can('admin_discount_codes_create')
                            <li class="{{ (request()->is('admin/financial/discounts/new')) ? 'active' : '' }}">
                                <a class="nav-link" href="/admin/financial/discounts/new">{{ trans('admin/main.new') }}</a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcan

            @can('admin_product_discount')
                <li class="nav-item dropdown {{ (request()->is('admin/financial/special_offers*')) ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-percent"></i>
                        <span>{{ trans('admin/main.special_offers') }}</span>
                    </a>
                    <ul class="dropdown-menu">
                        @can('admin_product_discount_list')
                            <li class="{{ (request()->is('admin/financial/special_offers')) ? 'active' : '' }}">
                                <a class="nav-link" href="/admin/financial/special_offers">{{ trans('admin/main.lists') }}</a>
                            </li>
                        @endcan

                        @can('admin_product_discount_create')
                            <li class="{{ (request()->is('admin/financial/special_offers/new')) ? 'active' : '' }}">
                                <a class="nav-link" href="/admin/financial/special_offers/new">{{ trans('admin/main.new') }}</a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcan

            @can('admin_feature_webinars')
                <li class="nav-item dropdown {{ (request()->is('admin/webinars/features*')) ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-star"></i>
                        <span>{{ trans('admin/main.feature_webinars') }}</span>
                    </a>
                    <ul class="dropdown-menu">
                        @can('admin_feature_webinars')
                            <li class="{{ (request()->is('admin/webinars/features')) ? 'active' : '' }}">
                                <a class="nav-link" href="/admin/webinars/features">{{ trans('admin/main.lists') }}</a>
                            </li>
                        @endcan()

                        @can('admin_feature_webinars_create')
                            <li class="{{ (request()->is('admin/webinars/features/create')) ? 'active' : '' }}">
                                <a class="nav-link" href="/admin/webinars/features/create">{{ trans('admin/main.new') }}</a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcan

            @can('admin_promotion')
                <li class="nav-item dropdown {{ (request()->is('admin/financial/promotions*')) ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-rocket"></i>
                        <span>{{ trans('admin/main.content_promotion') }}</span>
                    </a>
                    <ul class="dropdown-menu">
                        @can('admin_promotion_list')
                            <li class="{{ (request()->is('admin/financial/promotions')) ? 'active' : '' }}">
                                <a class="nav-link" href="/admin/financial/promotions">{{ trans('admin/main.plans') }}</a>
                            </li>
                        @endcan
                        @can('admin_promotion_list')
                            <li class="{{ (request()->is('admin/financial/promotions/sales')) ? 'active' : '' }}">
                                <a class="nav-link" href="/admin/financial/promotions/sales">{{ trans('admin/main.promotion_sales') }}</a>
                            </li>
                        @endcan

                        @can('admin_promotion_create')
                            <li class="{{ (request()->is('admin/financial/promotions/new')) ? 'active' : '' }}">
                                <a class="nav-link" href="/admin/financial/promotions/new">{{ trans('admin/main.new_plan') }}</a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcan

            @can('admin_advertising')
                <li class="nav-item dropdown {{ (request()->is('admin/advertising*')) ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-file-image"></i>
                        <span>{{ trans('admin/main.ad_banners') }}</span>
                    </a>
                    <ul class="dropdown-menu">
                        @can('admin_advertising_banners')
                            <li class="{{ (request()->is('admin/advertising/banners')) ? 'active' : '' }}">
                                <a class="nav-link" href="/admin/advertising/banners">{{ trans('admin/main.list') }}</a>
                            </li>
                        @endcan

                        @can('admin_advertising_banners_create')
                            <li class="{{ (request()->is('admin/advertising/banners/new')) ? 'active' : '' }}">
                                <a class="nav-link" href="/admin/advertising/banners/new">{{ trans('admin/main.new') }}</a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcan

            @can('admin_newsletters_lists')
                <li class="nav-item {{ (request()->is('admin/newsletters*')) ? 'active' : '' }}">
                    <a href="/admin/newsletters" class="nav-link">
                        <i class="fas fa-newspaper"></i>
                        <span>{{ trans('admin/main.newsletters') }}</span>
                    </a>
                </li>
            @endcan

            @if($authUser->can('admin_settings'))
                <li class="menu-header">{{ trans('admin/main.settings') }}</li>
            @endif

            @can('admin_settings')
                @php
                    $settingClass ='';

                    if (request()->is('admin/settings*') and
                            !(
                                request()->is('admin/settings/404') or
                                request()->is('admin/settings/contact_us') or
                                request()->is('admin/settings/footer') or
                                request()->is('admin/settings/navbar_links')
                            )
                        ) {
                            $settingClass = 'active';
                        }
                @endphp

                <li class="{{ $settingClass ?? '' }}">
                    <a href="/admin/settings" class="nav-link">
                        <i class="fas fa-cogs"></i>
                        <span>{{ trans('admin/main.settings') }}</span>
                    </a>
                </li>
            @endcan()


            <li>
                <a class="nav-link" href="/admin/logout">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </li>

        </ul>
        <br><br><br>
    </aside>
</div>
